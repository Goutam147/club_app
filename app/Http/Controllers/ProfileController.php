<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $totalDonated = \App\Models\Transaction::where('user_id', $request->user()->id)
            ->where('type', 'credit')
            ->where('status', 'approved')
            ->sum('amount');

        return view('profile.edit', [
            'user' => $request->user(),
            'totalDonated' => $totalDonated,
        ]);
    }

    /**
     * Get paginated transactions for the authenticated user.
     */
    public function transactions(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $transactions = \App\Models\Transaction::where('user_id', $user->id)
            ->with(['approvedBy:id,name', 'rejectedBy:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $formatted = [];
        foreach ($transactions as $t) {
            $formatted[] = [
                'id' => $t->id,
                'transaction_id' => $t->transaction_id,
                'amount' => number_format($t->amount, 2),
                'type' => $t->type,
                'method' => ucfirst($t->method),
                'status' => $t->status,
                'created_at' => $t->created_at->format('M d, Y g:i A'),
                'approved_by' => $t->approvedBy ? $t->approvedBy->name : null,
                'approved_at' => $t->approved_at ? $t->approved_at->format('M d, Y g:i A') : null,
                'rejected_by' => $t->rejectedBy ? $t->rejectedBy->name : null,
                'rejected_at' => $t->rejected_at ? $t->rejected_at->format('M d, Y g:i A') : null,
            ];
        }

        return response()->json([
            'transactions' => $formatted,
            'has_more' => $transactions->hasMorePages(),
            'current_page' => $transactions->currentPage(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except(['profile']));

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $fileName = 'profile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profiles'), $fileName);
            $user->profile = 'uploads/profiles/' . $fileName;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
