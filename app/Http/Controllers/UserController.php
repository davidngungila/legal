<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Client;
use App\Models\Department;
use App\Models\Position;
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
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        if ($isSuperAdmin) {
            $query = User::withoutClientFilter()->with(['roles', 'permissions', 'client', 'clients'])->orderBy('created_at', 'desc');
        } else {
            $clientId = $this->getCurrentClientId();
            $query = User::whereHas('clients', function ($q) use ($clientId) {
                $q->where('clients.id', $clientId)->where('client_user.is_active', true);
            })->with(['roles', 'permissions', 'client', 'clients'])->orderBy('created_at', 'desc');
        }

        $users = $query->get();
        
        // Add role information to each user
        $users->each(function ($user) {
            // Ensure super admin is attached to Orvion
            \App\Models\User::ensureSuperAdminBelongsToOrvion($user);
            // Refresh user to get updated client info
            $user->refresh();
            
            $user->role_display = $user->roles->first()?->display_name ?? 'No Role';
            $user->role = $user->roles->first()?->name ?? 'no_role';
            $user->company_name = $user->client?->name ?? ($user->clients->first()?->name ?? 'Orvion');
            $user->profile_photo_url = $user->profile_photo ? asset('storage/' . $user->profile_photo) : null;
        });

        // Optimized stats query - single query with conditional aggregates
        if ($isSuperAdmin) {
            $statsQuery = User::withoutClientFilter();
        } else {
            $clientId = $this->getCurrentClientId();
            $statsQuery = User::whereHas('clients', function ($q) use ($clientId) {
                $q->where('clients.id', $clientId)->where('client_user.is_active', true);
            });
        }
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('is_active', 1)->count(),
            'inactive' => (clone $statsQuery)->where('is_active', 0)->count(),
            'admin' => (clone $statsQuery)->whereHas('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'lead_hr_admin']);
            })->count(),
        ];
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'stats' => $stats
        ]);
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        
        // Define role hierarchy
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'lead_hr_admin' => ['hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'hr_officer' => ['line_manager', 'employee'],
            'finance_payroll_officer' => ['employee'],
            'line_manager' => ['employee'],
            'employee' => [],
            'external_auditor' => [],
        ];
        
        // Get current user's roles
        $userRoleNames = $currentUser->roles->pluck('name')->toArray();
        
        // Collect all allowed roles
        $allowedRoles = [];
        foreach ($userRoleNames as $roleName) {
            if (isset($roleHierarchy[$roleName])) {
                $allowedRoles = array_merge($allowedRoles, $roleHierarchy[$roleName]);
            }
        }
        $allowedRoles = array_unique($allowedRoles);
        
        // If no allowed roles (e.g., super admin has none, add all
        if (empty($allowedRoles)) {
            $allowedRoles = array_keys($roleHierarchy);
        }
        
        $userType = $request->get('user_type', 'client');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:client,orvion',
            'role' => 'required_if:user_type,client|exists:roles,name',
            'is_active' => 'required_if:user_type,client|boolean',
            'employee_id' => 'required_if:user_type,client|string|max:255|unique:users',
            'client_id' => 'required_if:user_type,client|exists:clients,id',
            'department' => 'required_if:user_type,client|string|max:255',
            'position' => 'required_if:user_type,client|string|max:255',
            'employment_type' => 'required_if:user_type,client|string',
            'permissions' => 'required_if:user_type,orvion|array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userType = $request->get('user_type', 'client');
            $clientId = $request->get('client_id');
            $roleName = $request->get('role');

            // Create user
            $user = User::create([
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'password' => Hash::make($request->get('password')),
                'is_active' => $userType === 'client' ? $request->get('is_active', 1) : 1,
                'email_verified_at' => now(),
                'current_client_id' => $userType === 'client' ? ($clientId ?? $this->getCurrentClientId()) : null,
                'employee_id' => $userType === 'client' ? $request->get('employee_id') : null,
                'department' => $userType === 'client' ? $request->get('department') : null,
                'position' => $userType === 'client' ? $request->get('position') : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Handle role assignment for client users
            if ($userType === 'client' && $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $user->roles()->attach($role->id);
                }
            }

            // Assign admin role and permissions for orvion users
            if ($userType === 'orvion') {
                // Auto-assign admin role
                $adminRole = Role::where('name', 'super_admin')->first();
                if ($adminRole) {
                    $user->roles()->attach($adminRole->id);
                }

                // Assign permissions if provided
                if ($request->has('permissions')) {
                    $permissions = Permission::whereIn('name', $request->get('permissions'))->get();
                    $user->permissions()->sync($permissions->pluck('id'));
                }
            }

            // Assign client for client users
            if ($userType === 'client' && $clientId) {
                $user->clients()->syncWithoutDetaching([
                    $clientId => [
                        'role' => $this->mapClientPivotRole($roleName),
                        'is_active' => true,
                        'joined_at' => now(),
                    ]
                ]);
            }

            // Ensure super admin belongs to Orvion
            $user->refresh();
            \App\Models\User::ensureSuperAdminBelongsToOrvion($user);
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user->load('roles', 'permissions', 'clients')
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
        $user = User::with('roles', 'permissions', 'client', 'clients')->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $user->profile_photo_url = $user->profile_photo ? asset('storage/' . $user->profile_photo) : null;
        $user->company_name = $user->client?->name ?? ($user->clients->first()?->name ?? 'Orvion');
        
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
        
        $currentUser = auth()->user();
        
        // Define role hierarchy
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'lead_hr_admin' => ['hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'hr_officer' => ['line_manager', 'employee'],
            'finance_payroll_officer' => ['employee'],
            'line_manager' => ['employee'],
            'employee' => [],
            'external_auditor' => [],
        ];
        
        // Get current user's roles
        $userRoleNames = $currentUser->roles->pluck('name')->toArray();
        
        // Collect all allowed roles
        $allowedRoles = [];
        foreach ($userRoleNames as $roleName) {
            if (isset($roleHierarchy[$roleName])) {
                $allowedRoles = array_merge($allowedRoles, $roleHierarchy[$roleName]);
            }
        }
        $allowedRoles = array_unique($allowedRoles);
        
        // If no allowed roles (e.g., super admin has none, add all
        if (empty($allowedRoles)) {
            $allowedRoles = array_keys($roleHierarchy);
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|' . Rule::unique('users')->ignore($id),
            'phone' => 'nullable|string|max:20',
            'role' => ['required', 'exists:roles,name', Rule::in($allowedRoles)],
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

            // Ensure super admin belongs to Orvion
            $user->refresh();
            \App\Models\User::ensureSuperAdminBelongsToOrvion($user);
            
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
        $currentUser = auth()->user();
        
        // Define role hierarchy
        $roleHierarchy = [
            'super_admin' => ['super_admin', 'lead_hr_admin', 'hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'lead_hr_admin' => ['hr_officer', 'finance_payroll_officer', 'line_manager', 'employee', 'external_auditor'],
            'hr_officer' => ['line_manager', 'employee'],
            'finance_payroll_officer' => ['employee'],
            'line_manager' => ['employee'],
            'employee' => [],
            'external_auditor' => [],
        ];
        
        // Get current user's roles
        $userRoleNames = $currentUser->roles->pluck('name')->toArray();
        
        // Collect all allowed roles
        $allowedRoles = [];
        foreach ($userRoleNames as $roleName) {
            if (isset($roleHierarchy[$roleName])) {
                $allowedRoles = array_merge($allowedRoles, $roleHierarchy[$roleName]);
            }
        }
        $allowedRoles = array_unique($allowedRoles);
        
        // If no allowed roles (e.g., super admin has none, add all
        if (empty($allowedRoles)) {
            $allowedRoles = array_keys($roleHierarchy);
        }
        
        // Get roles based on allowed roles
        $roles = Role::with('permissions')->whereIn('name', $allowedRoles)->get();
        $permissions = Permission::all();

        // Get all clients for selection
        $clients = \App\Models\Client::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'roles' => $roles,
            'permissions' => $permissions,
            'clients' => $clients
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
                    $isSuperAdmin = auth()->user()->hasRole('super_admin');

                    if ($isSuperAdmin) {
                        $users = User::whereIn('id', $userIds)->with('roles', 'clients')->get();
                    } else {
                        $users = User::whereIn('id', $userIds)->whereHas('clients', function ($q) use ($clientId) {
                            $q->where('clients.id', $clientId)->where('client_user.is_active', true);
                        })->with('roles', 'clients')->get();
                    }

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
                    $isSuperAdmin = auth()->user()->hasRole('super_admin');
                    $activateQuery = User::whereIn('id', $userIds);
                    if (!$isSuperAdmin) {
                        $clientId = $this->getCurrentClientId();
                        $activateQuery->whereHas('clients', function ($q) use ($clientId) {
                            $q->where('clients.id', $clientId)->where('client_user.is_active', true);
                        });
                    }
                    $activateQuery->update(['is_active' => 1]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Users activated successfully'
                    ]);
                    
                case 'deactivate':
                    $isSuperAdmin = auth()->user()->hasRole('super_admin');
                    if ($isSuperAdmin) {
                        $users = User::whereIn('id', $userIds)->with('roles')->get();
                    } else {
                        $clientId = $this->getCurrentClientId();
                        $users = User::whereIn('id', $userIds)->whereHas('clients', function ($q) use ($clientId) {
                            $q->where('clients.id', $clientId)->where('client_user.is_active', true);
                        })->with('roles')->get();
                    }
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
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        if ($isSuperAdmin) {
            $query = User::withoutClientFilter()->with('roles');
        } else {
            $clientId = $this->getCurrentClientId();
            $query = User::whereHas('clients', function ($q) use ($clientId) {
                $q->where('clients.id', $clientId)->where('client_user.is_active', true);
            })->with('roles');
        }
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $searchTerm = $request->string('search')->toString();
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
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
                'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Client(s)', 'Status', 'Created At', 'Last Login'
            ]);

            foreach ($users as $user) {
                $clientNames = $user->clients->isNotEmpty() 
                    ? $user->clients->pluck('name')->join(', ') 
                    : 'Orvion';
                
                fputcsv($handle, [
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone ?? '',
                    $user->roles->first()->display_name ?? '',
                    $clientNames,
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

    /**
     * Get next available employee ID
     */
    public function getNextEmployeeId()
    {
        $lastUser = User::whereNotNull('employee_id')
            ->orderBy('id', 'desc')
            ->first();
        
        if (!$lastUser || !$lastUser->employee_id) {
            $nextId = 'EMP-001';
        } else {
            $numPart = (int) preg_replace('/[^0-9]/', '', $lastUser->employee_id);
            $nextNum = $numPart + 1;
            $nextId = 'EMP-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        }
        
        return response()->json([
            'success' => true,
            'employee_id' => $nextId
        ]);
    }

    public function getDepartmentsByClient($clientId)
    {
        $departments = Department::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'departments' => $departments
        ]);
    }

    public function getPositionsByDepartment($departmentId)
    {
        $positions = Position::where('department_id', $departmentId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        return response()->json([
            'success' => true,
            'positions' => $positions
        ]);
    }
}
