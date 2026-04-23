<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController
{
    /**
     * Display roles index
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show create role form
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->get()->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store new role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->permissions()->attach($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Show role details
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('roles.show', compact('role'));
    }

    /**
     * Show edit role form
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->get()->groupBy('group');
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only(['name', 'description']));
        $role->permissions()->sync($request->permissions);

        return redirect()->route('roles.show', $role)
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        // Check if role is being used
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete role that is assigned to users']);
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }

    /**
     * Get role permissions
     */
    public function permissions(Role $role)
    {
        return response()->json($role->permissions);
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully!'
        ]);
    }
}
