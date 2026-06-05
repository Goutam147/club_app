<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * - Users with manage_transactions: see all records.
     * - Users without: see only approved records + their own.
     */
    public function load(Request $request)
    {
        $user = Auth::user();
        $perPage = 15;

        $query = Transaction::with(['user', 'approvedBy', 'rejectedBy']);

        // Permission-based filtering
        if (!$user->can('manage_transactions')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('status', 'approved');
            });
        }

        // Cursor: get records with id < last_id
        if ($request->filled('last_id')) {
            $query->where('id', '<', $request->last_id);
        }

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('id', 'desc')->limit($perPage + 1)->get();

        // Check if there are more records beyond the 15 we return
        $hasMore = $transactions->count() > $perPage;
        if ($hasMore) {
            $transactions = $transactions->take($perPage);
        }

        $canManage = $user->can('manage_transactions');

        $data = $transactions->map(function ($t) use ($canManage) {
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
                'user_name' => $t->user->name ?? 'Deleted User',
                'approved_by_name' => $t->approvedBy ? $t->approvedBy->name : null,
                'approved_at' => $t->approved_at ? $t->approved_at->format('M d') : null,
                'rejected_by_name' => $t->rejectedBy ? $t->rejectedBy->name : null,
                'rejected_at' => $t->rejected_at ? $t->rejected_at->format('M d') : null,
            ];
        });

        return response()->json([
            'transactions' => $data->values(),
            'has_more' => $hasMore,
            'can_manage' => $canManage,
        ]);
    }

    /**
     * Show form to create a transaction.
     */
    public function create()
    {
        $users = [];
        if (Auth::user()->can('manage_transactions')) {
            $users = User::where('status', 'active')->orderBy('name', 'asc')->get();
        }
        return view('transactions.create', compact('users'));
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
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096', // 4MB max
        ];

        if ($hasManageTransactions) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['type'] = 'required|in:credit,debit';
        }

        $request->validate($rules);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'receipt_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/transactions'), $fileName);
            $documentPath = 'uploads/transactions/' . $fileName;
        }

        $userId = $hasManageTransactions ? $request->user_id : $user->id;
        $type = $hasManageTransactions ? $request->type : 'credit';

        $status = $hasApproveTransactions ? 'approved' : 'pending';
        $approvedAt = $hasApproveTransactions ? now() : null;
        $approvedBy = $hasApproveTransactions ? $user->id : null;

        Transaction::create([
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

        $message = $status === 'approved' 
            ? 'Transaction recorded and approved successfully.'
            : 'Transaction submitted successfully and is pending approval.';

        return redirect()->route('transactions.index')->with('success', $message);
    }

    /**
     * Approve transaction.
     */
    public function approve(Transaction $transaction)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['TH', 'President'])) {
            return redirect()->back()->with('error', 'Only Technical Head or President can approve transactions.');
        }

        $transaction->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Transaction approved successfully.');
    }

    /**
     * Reject transaction.
     */
    public function reject(Transaction $transaction, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['TH', 'President'])) {
            return redirect()->back()->with('error', 'Only Technical Head or President can reject transactions.');
        }

        $transaction->update([
            'status' => 'rejected',
            'remark' => $transaction->remark . ' (Rejected comment: ' . $request->input('reject_reason', 'N/A') . ')',
            'rejected_at' => now(),
            'rejected_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Transaction rejected successfully.');
    }
}
