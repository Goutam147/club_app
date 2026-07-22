<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    /**
     * Display a matrix of roles and their permissions.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('roles-permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created role in database.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles-permissions.index')->with('success', "Role '{$request->name}' created successfully.");
    }

    /**
     * Store a newly created permission in database.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('roles-permissions.index')->with('success', "Permission '{$request->name}' created successfully.");
    }

    /**
     * Sync the complete roles & permissions matrix.
     */
    public function syncMatrix(Request $request)
    {
        $roles = Role::all();
        $matrix = $request->input('matrix', []);

        foreach ($roles as $role) {
            // Get the list of checked permissions for this role, default to empty array
            $permissions = isset($matrix[$role->id]) ? $matrix[$role->id] : [];
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles-permissions.index')->with('success', 'Roles & Permissions matrix saved successfully.');
    }

    /**
     * Delete a custom role.
     */
    public function destroyRole(Role $role)
    {
        $protectedRoles = ['TH', 'President', 'Secretary', 'Cashier', 'Member'];
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('roles-permissions.index')->with('error', "Core role '{$role->name}' cannot be deleted.");
        }

        $role->delete();

        return redirect()->route('roles-permissions.index')->with('success', "Role '{$role->name}' deleted successfully.");
    }
}
