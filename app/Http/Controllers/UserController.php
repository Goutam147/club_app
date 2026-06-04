<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of active/inactive users (Member Directory).
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('name', 'asc')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show list of pending users.
     */
    public function pending()
    {
        $users = User::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        return view('users.pending', compact('users'));
    }

    /**
     * Update user status (Approve, Activate, Deactivate).
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,active,inactive',
        ]);

        // Prevent admin deactivating themselves
        if ($user->id === Auth::id() && $request->status !== 'active') {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', "User status updated to {$request->status} successfully.");
    }

    /**
     * Assign role to user (Admins only).
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // Super Admin (TH) check: prevent changing own role to avoid lockout
        if ($user->id === Auth::id() && $user->hasRole('TH') && $request->role !== 'TH') {
            return redirect()->back()->with('error', 'You cannot demote yourself from the Technical Head role.');
        }

        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', "Role for {$user->name} updated to {$request->role} successfully.");
    }
}
