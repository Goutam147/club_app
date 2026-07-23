<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\FeeType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display the transactions page (empty shell, data loaded via JS).
     */
    public function index()
    {
        return view('transactions.index');
    }

    /**
     * API: Load transactions with cursor-based pagination.
     * Returns 15 records with id < last_id (or latest 15 if no last_id).
     */
    public function load(Request $request)
    {
        $user = Auth::user();
        $perPage = 15;

        // Dynamic permission check: users with manage_transactions OR approve_transactions can see all statuses
        $hasManage = $user->can('manage_transactions');
        $hasApprove = $user->can('approve_transactions');
        $canSeeAllTransactions = $hasManage || $hasApprove;

        $query = Transaction::with(['user', 'approvedBy', 'rejectedBy', 'items.feeType']);

        // Filtering rule:
        if (!$canSeeAllTransactions) {
            $query->where('status', 'approved');
        } else if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Cursor: get records with id < last_id
        if ($request->filled('last_id')) {
            $query->where('id', '<', $request->last_id);
        }

        // Optional type filter (credit / debit)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('id', 'desc')->limit($perPage + 1)->get();

        // Check if there are more records beyond the 15 we return
        $hasMore = $transactions->count() > $perPage;
        if ($hasMore) {
            $transactions = $transactions->take($perPage);
        }

        $data = $transactions->map(function ($t) use ($canSeeAllTransactions) {
            return [
                'id' => $t->id,
                'transaction_id' => $t->transaction_id,
                'amount' => number_format($t->amount, 2),
                'type' => $t->type,
                'method' => $t->method,
                'document_url' => $t->document_url ? asset($t->document_url) : null,
                'status' => $t->status,
                'remark' => $t->remark,
                'created_at' => $t->created_at->format('M d, y g:i A'),
                'user_name' => $t->user ? $t->user->name : ($t->type === 'debit' ? 'Club Expense (Cashier)' : 'General User'),
                'approved_by_name' => $t->approvedBy ? $t->approvedBy->name : null,
                'approved_at' => $t->approved_at ? $t->approved_at->format('M d') : null,
                'rejected_by_name' => $t->rejectedBy ? $t->rejectedBy->name : null,
                'rejected_at' => $t->rejected_at ? $t->rejected_at->format('M d') : null,
                'items' => $t->items->map(function ($item) {
                    $monthName = $item->month ? date('F', mktime(0, 0, 0, $item->month, 10)) : null;
                    return [
                        'id' => $item->id,
                        'title' => $item->title ?? ($item->feeType ? $item->feeType->title : 'General'),
                        'month' => $item->month,
                        'month_name' => $monthName,
                        'year' => $item->year,
                        'amount' => number_format($item->amount, 2),
                    ];
                }),
            ];
        });

        // Calculate totals based on approved transactions
        $totalCredit = Transaction::where('status', 'approved')->where('type', 'credit')->sum('amount');
        $totalDebit = Transaction::where('status', 'approved')->where('type', 'debit')->sum('amount');
        $currentBalance = $totalCredit - $totalDebit;

        return response()->json([
            'transactions' => $data->values(),
            'has_more' => $hasMore,
            'can_manage' => $canSeeAllTransactions,
            'total_credit' => number_format($totalCredit, 2),
            'total_debit' => number_format($totalDebit, 2),
            'current_balance' => number_format($currentBalance, 2),
        ]);
    }

    /**
     * Show form to create a transaction.
     */
    public function create()
    {
        $user = Auth::user();
        $users = [];
        if ($user->can('manage_transactions')) {
            $users = User::where('status', 'active')->orderBy('name', 'asc')->get();
        }

        $feeTypes = FeeType::where('status', 'active')->get();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('transactions.create', compact('users', 'feeTypes', 'months'));
    }

    /**
     * Store transaction details.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $hasManageTransactions = $user->can('manage_transactions');
        $hasApproveTransactions = $user->can('approve_transactions');

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank',
            'remark' => 'nullable|string|max:500',
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
            'items' => 'nullable|array',
            'items.*.fee_type_id' => 'nullable|exists:fee_types,id',
            'items.*.month' => 'nullable|integer|between:1,12',
            'items.*.year' => 'nullable|integer|min:2020|max:2099',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.amount' => 'required_with:items|numeric|min:0.01',
        ];

        if ($hasManageTransactions) {
            $rules['type'] = 'required|in:credit,debit';
            if ($request->input('type') === 'debit') {
                $rules['user_id'] = 'nullable|exists:users,id';
            } else {
                $rules['user_id'] = 'required|exists:users,id';
            }
        }

        $request->validate($rules);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'receipt_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/transactions'), $fileName);
            $documentPath = 'uploads/transactions/' . $fileName;
        }

        $type = $hasManageTransactions ? $request->type : 'credit';
        $userId = $hasManageTransactions ? ($request->filled('user_id') ? $request->user_id : null) : $user->id;

        $status = $hasApproveTransactions ? 'approved' : 'pending';
        $approvedAt = $hasApproveTransactions ? now() : null;
        $approvedBy = $hasApproveTransactions ? $user->id : null;

        DB::transaction(function () use ($request, $userId, $type, $status, $approvedAt, $approvedBy, $documentPath, $user) {
            $transaction = Transaction::create([
                'user_id' => $userId,
                'amount' => $request->amount,
                'type' => $type,
                'method' => $request->method,
                'remark' => $request->remark,
                'document_url' => $documentPath,
                'status' => $status,
                'approved_at' => $approvedAt,
                'approved_by' => $approvedBy,
                'created_by' => $user->id,
            ]);

            // Save line items if provided
            if ($request->filled('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['amount']) && $itemData['amount'] > 0) {
                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'fee_type_id' => $itemData['fee_type_id'] ?? null,
                            'month' => $itemData['month'] ?? null,
                            'year' => $itemData['year'] ?? null,
                            'title' => $itemData['title'] ?? null,
                            'amount' => $itemData['amount'],
                        ]);
                    }
                }
            } else {
                // Default single line item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'title' => $type === 'credit' ? 'General Deposit / Dues' : 'Expense / Purchase',
                    'amount' => $request->amount,
                ]);
            }
        });

        $message = $status === 'approved' 
            ? 'Transaction recorded and approved successfully.'
            : 'Transaction submitted successfully and is pending approval.';

        return redirect()->route('transactions.index')->with('success', $message);
    }

    /**
     * Display the approvals page (only for users with approve_transactions permission).
     */
    public function approvals()
    {
        return view('transactions.approvals');
    }

    /**
     * API: Load pending transactions for approval page.
     */
    public function loadPending(Request $request)
    {
        $user = Auth::user();
        $perPage = 15;

        $query = Transaction::with(['user', 'creator'])
            ->where('status', 'pending');

        // Cursor pagination
        if ($request->filled('last_id')) {
            $query->where('id', '<', $request->last_id);
        }

        $transactions = $query->orderBy('id', 'desc')->limit($perPage + 1)->get();

        $hasMore = $transactions->count() > $perPage;
        if ($hasMore) {
            $transactions = $transactions->take($perPage);
        }

        $data = $transactions->map(function ($t) {
            return [
                'id' => $t->id,
                'transaction_id' => $t->transaction_id,
                'amount' => number_format($t->amount, 2),
                'type' => $t->type,
                'method' => $t->method,
                'document_url' => $t->document_url ? asset($t->document_url) : null,
                'remark' => $t->remark,
                'created_at' => $t->created_at->format('M d, y g:i A'),
                'user_name' => $t->user->name ?? 'Deleted User',
                'user_profile' => $t->user && $t->user->profile ? asset($t->user->profile) : null,
                'created_by_name' => $t->creator ? $t->creator->name : null,
            ];
        });

        $pendingCount = Transaction::where('status', 'pending')->count();

        return response()->json([
            'transactions' => $data->values(),
            'has_more' => $hasMore,
            'pending_count' => $pendingCount,
        ]);
    }

    /**
     * Approve transaction.
     */
    public function approve(Transaction $transaction)
    {
        $user = Auth::user();
        if (!$user->can('approve_transactions')) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to approve transactions.');
        }

        $transaction->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction approved successfully.']);
        }
        return redirect()->back()->with('success', 'Transaction approved successfully.');
    }

    /**
     * Reject transaction.
     */
    public function reject(Transaction $transaction, Request $request)
    {
        $user = Auth::user();
        if (!$user->can('approve_transactions')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'You do not have permission to reject transactions.');
        }

        $rejectReason = $request->input('reject_reason', 'N/A');
        $remark = $transaction->remark ? $transaction->remark . ' | Rejection: ' . $rejectReason : 'Rejection: ' . $rejectReason;

        $transaction->update([
            'status' => 'rejected',
            'remark' => $remark,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Transaction rejected successfully.']);
        }
        return redirect()->back()->with('success', 'Transaction rejected successfully.');
    }
}
