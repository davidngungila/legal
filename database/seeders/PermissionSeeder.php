<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard.view', 'display_name' => 'View Dashboard', 'group' => 'Dashboard', 'description' => 'View dashboard and analytics'],
            
            // Users
            ['name' => 'users.view', 'display_name' => 'View Users', 'group' => 'Users', 'description' => 'View user list'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'group' => 'Users', 'description' => 'Create new users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'group' => 'Users', 'description' => 'Edit existing users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'group' => 'Users', 'description' => 'Delete users'],
            
            // Employees
            ['name' => 'employees.view', 'display_name' => 'View Employees', 'group' => 'Employees', 'description' => 'View employee list'],
            ['name' => 'employees.create', 'display_name' => 'Create Employees', 'group' => 'Employees', 'description' => 'Create new employees'],
            ['name' => 'employees.edit', 'display_name' => 'Edit Employees', 'group' => 'Employees', 'description' => 'Edit existing employees'],
            ['name' => 'employees.delete', 'display_name' => 'Delete Employees', 'group' => 'Employees', 'description' => 'Delete employees'],
            
            // Recruitment
            ['name' => 'recruitment.view', 'display_name' => 'View Recruitment', 'group' => 'Recruitment', 'description' => 'View recruitment data'],
            ['name' => 'recruitment.manage', 'display_name' => 'Manage Recruitment', 'group' => 'Recruitment', 'description' => 'Manage recruitment process'],
            
            // Attendance
            ['name' => 'attendance.view', 'display_name' => 'View Attendance', 'group' => 'Attendance', 'description' => 'View attendance records'],
            ['name' => 'attendance.manage', 'display_name' => 'Manage Attendance', 'group' => 'Attendance', 'description' => 'Manage attendance data'],
            
            // Payroll
            ['name' => 'payroll.view', 'display_name' => 'View Payroll', 'group' => 'Payroll', 'description' => 'View payroll information'],
            ['name' => 'payroll.manage', 'display_name' => 'Manage Payroll', 'group' => 'Payroll', 'description' => 'Manage payroll processing'],
            
            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'group' => 'Reports', 'description' => 'View reports and analytics'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'group' => 'Reports', 'description' => 'Export reports'],
            
            // Settings
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'group' => 'Settings', 'description' => 'View system settings'],
            ['name' => 'settings.manage', 'display_name' => 'Manage Settings', 'group' => 'Settings', 'description' => 'Manage system settings'],
            
            // Client Management
            ['name' => 'clients.view', 'display_name' => 'View Clients', 'group' => 'Clients', 'description' => 'View client list'],
            ['name' => 'clients.create', 'display_name' => 'Create Clients', 'group' => 'Clients', 'description' => 'Create new clients'],
            ['name' => 'clients.edit', 'display_name' => 'Edit Clients', 'group' => 'Clients', 'description' => 'Edit existing clients'],
            ['name' => 'clients.delete', 'display_name' => 'Delete Clients', 'group' => 'Clients', 'description' => 'Delete clients'],
            
            // Self Service
            ['name' => 'selfservice.view', 'display_name' => 'View Self Service', 'group' => 'Self Service', 'description' => 'View self service portal'],
            ['name' => 'selfservice.profile', 'display_name' => 'Edit Profile', 'group' => 'Self Service', 'description' => 'Edit own profile'],
            ['name' => 'selfservice.leave', 'display_name' => 'Manage Leave', 'group' => 'Self Service', 'description' => 'Manage leave requests'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
