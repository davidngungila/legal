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
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@legalhr.com',
                'password' => 'admin123',
                'phone' => '+255 712 345 678',
                'is_active' => true,
                'role' => 'super_admin',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@legalhr.com',
                'password' => 'hr123',
                'phone' => '+255 712 345 679',
                'is_active' => true,
                'role' => 'lead_hr_admin',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@legalhr.com',
                'password' => 'hr123',
                'phone' => '+255 712 345 680',
                'is_active' => true,
                'role' => 'lead_hr_admin',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Kamau',
                'email' => 'david.kamau@legalhr.com',
                'password' => 'manager123',
                'phone' => '+255 712 345 681',
                'role' => 'manager',
                'is_active' => true,
            ],
            [
                'first_name' => 'Grace',
                'last_name' => 'Mwangi',
                'email' => 'grace.mwangi@legalhr.com',
                'password' => 'manager123',
                'phone' => '+255 712 345 682',
                'role' => 'manager',
                'is_active' => true,
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 683',
                'role' => 'employee',
                'is_active' => true,
            ],
            [
                'first_name' => 'Fatuma',
                'last_name' => 'Hassan',
                'email' => 'fatuma.hassan@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 684',
                'role' => 'employee',
                'is_active' => true,
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Wilson',
                'email' => 'peter.wilson@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 685',
                'role' => 'employee',
                'is_active' => true,
            ],
            [
                'first_name' => 'Anna',
                'last_name' => 'Mkapa',
                'email' => 'anna.mkapa@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 686',
                'role' => 'employee',
                'is_active' => false,
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Moyo',
                'email' => 'james.moyo@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 687',
                'role' => 'employee',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'password' => Hash::make($userData['password']),
                    'phone' => $userData['phone'] ?? null,
                    'is_active' => $userData['is_active'],
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
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
