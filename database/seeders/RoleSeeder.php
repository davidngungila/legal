<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => [
                    'dashboard.view',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                    'recruitment.view', 'recruitment.manage',
                    'attendance.view', 'attendance.manage',
                    'payroll.view', 'payroll.manage',
                    'reports.view', 'reports.export',
                    'settings.view', 'settings.manage',
                    'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
                    'selfservice.view', 'selfservice.profile', 'selfservice.leave',
                ],
            ],
            [
                'name' => 'hr_admin',
                'display_name' => 'HR Admin',
                'description' => 'HR management access',
                'permissions' => [
                    'dashboard.view',
                    'employees.view', 'employees.create', 'employees.edit',
                    'recruitment.view', 'recruitment.manage',
                    'attendance.view', 'attendance.manage',
                    'payroll.view',
                    'reports.view',
                    'selfservice.view',
                ],
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Department management access',
                'permissions' => [
                    'dashboard.view',
                    'employees.view',
                    'attendance.view',
                    'reports.view',
                    'selfservice.view',
                ],
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Self-service access',
                'permissions' => [
                    'dashboard.view',
                    'selfservice.view', 'selfservice.profile', 'selfservice.leave',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_active' => true,
                ]
            );

            // Attach permissions
            if (isset($roleData['permissions'])) {
                $permissions = Permission::whereIn('name', $roleData['permissions'])->get();
                $role->permissions()->sync($permissions);
            }
        }
    }
}
