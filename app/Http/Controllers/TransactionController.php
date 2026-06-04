<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     * Members see only their own. Admins (TH, President, Secretary) see all.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::with(['user', 'approvedBy', 'rejectedBy', 'creator']);

        // Check if user has admin privileges
        if (!$user->hasAnyRole(['TH', 'President', 'Secretary'])) {
            $query->where('user_id', $user->id);
        }

        // Apply filters if present
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show form to create a transaction.
     */
    public function create()
    {
        $users = User::where('status', 'active')->orderBy('name', 'asc')->get();
        return view('transactions.create', compact('users'));
    }

    /**
     * Store transaction details.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['TH', 'President', 'Secretary']);

        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'method' => 'required|in:cash,bank',
            'remark' => 'nullable|string|max:500',
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096', // 4MB max
        ];

        // If admin, they can select any user, else it's for the logged in user
        if ($isAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'receipt_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/transactions'), $fileName);
            $documentPath = 'uploads/transactions/' . $fileName;
        }

        Transaction::create([
            'user_id' => $isAdmin ? $request->user_id : $user->id,
            'amount' => $request->amount,
            'type' => $request->type,
            'method' => $request->method,
            'remark' => $request->remark,
            'document_url' => $documentPath,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction submitted successfully and is pending approval.');
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
