<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
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
                'permissions' => json_encode($request->get('permissions', [])),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Assign permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->get('permissions'));
            }
            
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
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'role' => $role
        ]);
    }
    
    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
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
            $role->update([
                'name' => $request->get('name'),
                'display_name' => $request->get('display_name'),
                'description' => $request->get('description'),
                'is_active' => $request->get('is_active'),
                'permissions' => json_encode($request->get('permissions', [])),
                'updated_at' => now()
            ]);
            
            // Update permissions if provided
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->get('permissions'));
            }
            
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
    public function destroy($id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }
        
        try {
            // Prevent deletion of roles assigned to users
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users'
                ], 403);
            }
            
            $role->permissions()->detach();
            $role->delete();
            
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
