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
    private function getCurrentClientId(): ?int
    {
        $clientId = session('current_client_id');
        return $clientId ? (int) $clientId : null;
    }

    private function mapClientPivotRole(?string $roleName): string
    {
        return match ($roleName) {
            'super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer' => 'admin',
            'line_manager' => 'manager',
            default => 'employee',
        };
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $clientId = $this->getCurrentClientId();

        $query = User::with('roles')->orderBy('created_at', 'desc');
        if ($clientId) {
            $query->where(function ($q) use ($clientId) {
                $q->where('current_client_id', $clientId)
                    ->orWhereHas('clients', function ($q2) use ($clientId) {
                        $q2->where('clients.id', $clientId);
                    });
            });
        }

        $users = $query->get();
        
        // Add role information to each user
        $users->each(function ($user) {
            $user->role_display = $user->roles->first()?->display_name ?? 'No Role';
            $user->role = $user->roles->first()?->name ?? 'no_role';
        });

        $statsQuery = User::query();
        if ($clientId) {
            $statsQuery->where(function ($q) use ($clientId) {
                $q->where('current_client_id', $clientId)
                    ->orWhereHas('clients', function ($q2) use ($clientId) {
                        $q2->where('clients.id', $clientId);
                    });
            });
        }
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'stats' => [
                'total' => (clone $statsQuery)->count(),
                'active' => (clone $statsQuery)->where('is_active', 1)->count(),
                'inactive' => (clone $statsQuery)->where('is_active', 0)->count(),
                'admin' => (clone $statsQuery)->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['super_admin', 'lead_hr_admin']);
                })->count(),
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
            $clientId = $this->getCurrentClientId();
            $roleName = $request->get('role');

            // Create user
            $user = User::create([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make($request->get('password')),
                'is_active' => $request->get('is_active', 1),
                'email_verified_at' => now(),
                'current_client_id' => $clientId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Assign role
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
            
            // Assign permissions if provided
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('name', $request->get('permissions'))->get();
                $user->permissions()->sync($permissions->pluck('id'));
            }

            if ($clientId) {
                $user->clients()->syncWithoutDetaching([
                    $clientId => [
                        'role' => $this->mapClientPivotRole($roleName),
                        'is_active' => true,
                        'joined_at' => now(),
                    ]
                ]);
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
            $clientId = $this->getCurrentClientId();
            $roleName = $request->get('role');

            // Update user
            $user->update([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'is_active' => $request->get('is_active'),
                'current_client_id' => $clientId ?: $user->current_client_id,
                'updated_at' => now()
            ]);
            
            // Update password if provided
            if ($request->has('password') && $request->get('password')) {
                $user->update([
                    'password' => Hash::make($request->get('password'))
                ]);
            }
            
            // Update role
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
            
            // Update permissions if provided
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('name', $request->get('permissions'))->get();
                $user->permissions()->sync($permissions->pluck('id'));
            }

            if ($clientId) {
                $user->clients()->syncWithoutDetaching([
                    $clientId => [
                        'role' => $this->mapClientPivotRole($roleName),
                        'is_active' => true,
                        'joined_at' => now(),
                    ]
                ]);
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
            $clientId = $this->getCurrentClientId();

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

            if ($clientId) {
                $user->clients()->detach($clientId);
                $remainingClientCount = $user->clients()->count();
                if ($remainingClientCount > 0) {
                    return response()->json([
                        'success' => true,
                        'message' => 'User removed from current client'
                    ]);
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
                    $clientId = $this->getCurrentClientId();
                    $users = User::whereIn('id', $userIds)->with('roles', 'clients')->get();

                    $superAdminCountInSelection = $users->filter(function ($user) {
                        return $user->roles->contains('name', 'super_admin');
                    })->count();

                    if ($superAdminCountInSelection > 0) {
                        $superAdminTotal = User::whereHas('roles', function ($q) {
                            $q->where('name', 'super_admin');
                        })->count();

                        if ($superAdminTotal - $superAdminCountInSelection <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Cannot delete the last Super Admin user'
                            ], 403);
                        }
                    }

                    foreach ($users as $user) {
                        if ($clientId) {
                            $user->clients()->detach($clientId);
                            if ($user->clients()->count() > 0) {
                                continue;
                            }
                        }

                        $user->roles()->detach();
                        $user->permissions()->detach();
                        $user->delete();
                    }

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
                    $users = User::whereIn('id', $userIds)->with('roles')->get();
                    $superAdminCountInSelection = $users->filter(function ($user) {
                        return $user->roles->contains('name', 'super_admin');
                    })->count();

                    if ($superAdminCountInSelection > 0) {
                        $superAdminTotal = User::whereHas('roles', function ($q) {
                            $q->where('name', 'super_admin');
                        })->count();

                        if ($superAdminTotal - $superAdminCountInSelection <= 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Cannot deactivate the last Super Admin user'
                            ], 403);
                        }
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
        $clientId = $this->getCurrentClientId();

        $query = User::with('roles');
        if ($clientId) {
            $query->where(function ($q) use ($clientId) {
                $q->where('current_client_id', $clientId)
                    ->orWhereHas('clients', function ($q2) use ($clientId) {
                        $q2->where('clients.id', $clientId);
                    });
            });
        }
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $roleName = $request->string('role')->toString();
            $query->whereHas('roles', function($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', (int) $request->get('status'));
        }
        
        $users = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Status', 'Created At', 'Last Login'
            ]);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone ?? '',
                    $user->roles->first()->display_name ?? '',
                    $user->is_active ? 'Active' : 'Inactive',
                    optional($user->created_at)->toDateTimeString(),
                    optional($user->last_login_at)->toDateTimeString() ?? 'Never',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
