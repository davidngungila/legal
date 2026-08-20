<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AuditLogger;

class RoleController
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users']);
        $activePermissionFilter = request()->query('permission');

        if ($activePermissionFilter) {
            $roles->whereHas('permissions', function ($query) use ($activePermissionFilter) {
                $query->where('name', $activePermissionFilter);
            });
        }

        $roles = $roles->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'roles' => $roles
            ]);
        }
        
        $permissions = \App\Models\Permission::all();
        
        return view('roles.index', compact('roles', 'permissions', 'activePermissionFilter'));
    }
    
    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $role = Role::create([
                'name' => $request->get('name'),
                'display_name' => $request->get('display_name'),
                'description' => $request->get('description'),
                'is_active' => $request->get('is_active', 1),
            ]);
            
            // Assign permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->get('permissions'));
            }
            
            AuditLogger::log(
                'created',
                $role,
                'Roles',
                "Created role: {$role->display_name}",
                null,
                $role->toArray()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        return response()->json([
            'success' => true,
            'role' => $role->load('permissions')
        ]);
    }
    
    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $oldValues = $role->toArray();
            $oldPermissions = $role->permissions()->pluck('permissions.id')->toArray();
            
            $role->update([
                'name' => $request->get('name'),
                'display_name' => $request->get('display_name'),
                'description' => $request->get('description'),
                'is_active' => $request->get('is_active'),
            ]);
            
            // Always update permissions (even if empty)
            $role->permissions()->sync($request->get('permissions', []));
            
            $newValues = $role->toArray();
            $newPermissions = $request->get('permissions', []);
            
            AuditLogger::log(
                'updated',
                $role,
                'Roles',
                "Updated role: {$role->display_name}",
                array_merge($oldValues, ['permissions' => $oldPermissions]),
                array_merge($newValues, ['permissions' => $newPermissions])
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'role' => $role->load('permissions')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        try {
            // Prevent deletion of roles assigned to users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users'
                ], 403);
            }
            
            $oldValues = $role->toArray();
            $roleName = $role->display_name;
            
            $role->permissions()->detach();
            $role->delete();
            
            AuditLogger::log(
                'deleted',
                $role,
                'Roles',
                "Deleted role: {$roleName}",
                $oldValues,
                null
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available permissions
     */
    public function getPermissions()
    {
        $permissions = Permission::all();
        
        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }
}
