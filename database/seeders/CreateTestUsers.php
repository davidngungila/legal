<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create roles
        $adminRole = Role::where('name', 'admin')->first();
        $managerRole = Role::where('name', 'manager')->first();
        $employeeRole = Role::where('name', 'employee')->first();

        // Create HR Admin
        $hrAdmin = User::updateOrCreate(
            ['email' => 'hr@legalhr.com'],
            [
                'first_name' => 'HR',
                'last_name' => 'Admin',
                'password' => Hash::make('hr123'),
                'is_active' => true,
                'phone' => '+255 123 456 789',
            ]
        );

        if ($adminRole && !$hrAdmin->roles()->where('role_id', $adminRole->id)->exists()) {
            $hrAdmin->roles()->attach($adminRole);
        }

        // Create Department Manager
        $manager = User::updateOrCreate(
            ['email' => 'manager@legalhr.com'],
            [
                'first_name' => 'Department',
                'last_name' => 'Manager',
                'password' => Hash::make('manager123'),
                'is_active' => true,
                'phone' => '+255 123 456 790',
            ]
        );

        if ($managerRole && !$manager->roles()->where('role_id', $managerRole->id)->exists()) {
            $manager->roles()->attach($managerRole);
        }

        // Create Employee
        $employee = User::updateOrCreate(
            ['email' => 'employee@legalhr.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'Employee',
                'password' => Hash::make('emp123'),
                'is_active' => true,
                'phone' => '+255 123 456 791',
            ]
        );

        if ($employeeRole && !$employee->roles()->where('role_id', $employeeRole->id)->exists()) {
            $employee->roles()->attach($employeeRole);
        }

        $this->command->info('Test users created/updated successfully!');
        $this->command->info('HR Admin: hr@legalhr.com / hr123');
        $this->command->info('Manager: manager@legalhr.com / manager123');
        $this->command->info('Employee: employee@legalhr.com / emp123');
    }
}
