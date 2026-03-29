<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@legalhr.com',
                'password' => 'admin123',
                'role' => 'super_admin',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@legalhr.com',
                'password' => 'hr123',
                'role' => 'hr_admin',
            ],
            [
                'name' => 'Department Manager',
                'email' => 'manager@legalhr.com',
                'password' => 'manager123',
                'role' => 'manager',
            ],
            [
                'name' => 'John Employee',
                'email' => 'employee@legalhr.com',
                'password' => 'emp123',
                'role' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'is_active' => true,
                ]
            );

            // Assign role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }
    }
}
