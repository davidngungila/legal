<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
                'name' => 'Super Admin',
                'email' => 'admin@legalhr.com',
                'password' => 'admin123',
                'phone' => '+255 712 345 678',
                'role' => 'super_admin',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@legalhr.com',
                'password' => 'hr123',
                'phone' => '+255 712 345 679',
                'role' => 'hr_admin',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@legalhr.com',
                'password' => 'hr123',
                'phone' => '+255 712 345 680',
                'role' => 'hr_admin',
                'status' => 'active',
            ],
            [
                'name' => 'David Kamau',
                'email' => 'david.kamau@legalhr.com',
                'password' => 'hr123',
                'phone' => '+255 712 345 681',
                'role' => 'hr_staff',
                'status' => 'active',
            ],
            [
                'name' => 'Grace Mwangi',
                'email' => 'grace.mwangi@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 682',
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 683',
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Fatuma Hassan',
                'email' => 'fatuma.hassan@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 684',
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Peter Wilson',
                'email' => 'peter.wilson@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 685',
                'role' => 'employee',
                'status' => 'active',
            ],
            [
                'name' => 'Anna Mkapa',
                'email' => 'anna.mkapa@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 686',
                'role' => 'employee',
                'status' => 'inactive',
            ],
            [
                'name' => 'James Moyo',
                'email' => 'james.moyo@legalhr.com',
                'password' => 'emp123',
                'phone' => '+255 712 345 687',
                'role' => 'employee',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'phone' => $userData['phone'] ?? null,
                    'role' => $userData['role'],
                    'status' => $userData['status'],
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
