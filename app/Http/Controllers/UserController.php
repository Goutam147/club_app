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
    public function index(Request $request)
    {
        // Month filter: default to current month (1-12)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = now()->year;

        $users = User::with('roles')
            ->withSum(['transactions as total_donated' => function ($query) {
                $query->where('type', 'credit')->where('status', 'approved');
            }], 'amount')
            ->withSum(['transactions as monthly_credit' => function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('type', 'credit')
                      ->where('status', 'approved')
                      ->whereMonth('created_at', $selectedMonth)
                      ->whereYear('created_at', $selectedYear);
            }], 'amount')
            ->withSum(['transactions as monthly_debit' => function ($query) use ($selectedMonth, $selectedYear) {
                $query->where('type', 'debit')
                      ->where('status', 'approved')
                      ->whereMonth('created_at', $selectedMonth)
                      ->whereYear('created_at', $selectedYear);
            }], 'amount')
            ->orderBy('name', 'asc')
            ->get();

        // Calculate monthly balance for each user
        $users->each(function ($user) {
            $user->monthly_balance = ($user->monthly_credit ?? 0) - ($user->monthly_debit ?? 0);
        });

        $roles = Role::all();
        return view('users.index', compact('users', 'roles', 'selectedMonth'));
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

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'profile' => 'nullable|image|max:2048',
        ]);

        $profilePath = null;
        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $fileName = 'profile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profiles'), $fileName);
            $profilePath = 'uploads/profiles/' . $fileName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'profile' => $profilePath,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', "User '{$user->name}' created successfully with role '{$request->role}'.");
    }
}
