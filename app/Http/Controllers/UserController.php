<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->get('status'));
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem()
            ]
        ]);
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Create user
            $user = User::create([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make($request->get('password')),
                'is_active' => $request->get('is_active', 1),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Assign role
            $role = Role::where('name', $request->get('role'))->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
            
            // Assign permissions if provided
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('name', $request->get('permissions'))->get();
                $user->permissions()->sync($permissions->pluck('id'));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user->load('roles', 'permissions')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('roles', 'permissions')->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
    
    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|' . Rule::unique('users')->ignore($id),
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Update user
            $user->update([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'is_active' => $request->get('is_active'),
                'updated_at' => now()
            ]);
            
            // Update password if provided
            if ($request->has('password') && $request->get('password')) {
                $user->update([
                    'password' => Hash::make($request->get('password'))
                ]);
            }
            
            // Update role
            $role = Role::where('name', $request->get('role'))->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
            
            // Update permissions if provided
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('name', $request->get('permissions'))->get();
                $user->permissions()->sync($permissions->pluck('id'));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user->load('roles', 'permissions')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        try {
            // Prevent deletion of the last super admin
            if ($user->hasRole('super_admin')) {
                $superAdminCount = User::whereHas('roles', function($q) {
                    $q->where('name', 'super_admin');
                })->count();
                
                if ($superAdminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete the last Super Admin user'
                    ], 403);
                }
            }
            
            $user->roles()->detach();
            $user->permissions()->detach();
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get available roles and permissions
     */
    public function getRolesAndPermissions()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return response()->json([
            'success' => true,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
    
    /**
     * Bulk operations on users
     */
    public function bulkOperations(Request $request)
    {
        $operation = $request->get('operation');
        $userIds = $request->get('user_ids', []);
        
        if (empty($userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No users selected'
            ], 422);
        }
        
        try {
            switch ($operation) {
                case 'delete':
                    // Prevent deletion of all super admins
                    $users = User::whereIn('id', $userIds)->with('roles')->get();
                    $superAdminCount = $users->filter(function($user) {
                        return $user->roles->contains('name', 'super_admin');
                    })->count();
                    
                    if ($superAdminCount >= $users->count()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete all Super Admin users'
                        ], 403);
                    }
                    
                    User::whereIn('id', $userIds)->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Users deleted successfully'
                    ]);
                    
                case 'activate':
                    User::whereIn('id', $userIds)->update(['is_active' => 1]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Users activated successfully'
                    ]);
                    
                case 'deactivate':
                    // Prevent deactivation of all super admins
                    $users = User::whereIn('id', $userIds)->with('roles')->get();
                    $superAdminCount = $users->filter(function($user) {
                        return $user->roles->contains('name', 'super_admin');
                    })->count();
                    
                    if ($superAdminCount >= $users->count()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot deactivate all Super Admin users'
                        ], 403);
                    }
                    
                    User::whereIn('id', $userIds)->update(['is_active' => 0]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Users deactivated successfully'
                    ]);
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid operation'
                    ], 422);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::with('roles');
        
        // Apply same filters as index
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }
        
        if ($request->has('status')) {
            $query->where('is_active', $request->get('status'));
        }
        
        $users = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($handle, [
            'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Status', 'Created At', 'Last Login'
        ]);
        
        // CSV data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->first_name,
                $user->last_name,
                $user->email,
                $user->phone ?? '',
                $user->roles->first()->display_name ?? '',
                $user->is_active ? 'Active' : 'Inactive',
                $user->created_at,
                $user->last_login_at ?? 'Never'
            ]);
        }
        
        fclose($handle);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];
        
        return response()->stream(
            function () use ($filename) {
                readfile($filename);
                unlink($filename);
            },
            200,
            $headers
        );
    }
}
