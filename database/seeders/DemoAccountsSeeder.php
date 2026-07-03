<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountsSeeder extends Seeder
{
    public function run()
    {
        // Get the first client to assign all demo accounts to
        $client = Client::first();

        if (!$client) {
            $this->command->error("No clients found! Please seed clients first.");
            return;
        }

        $this->command->info("Creating demo accounts for client: {$client->name}");

        // HR Admin (hr_officer role)
        $this->createUser($client, 'hr_officer', 'hr@legalhr.com', 'hr123', 'HR', 'Admin');

        // Department Manager (line_manager role)
        $this->createUser($client, 'line_manager', 'manager@legalhr.com', 'manager123', 'Department', 'Manager');

        // Employee (employee role)
        $this->createUser($client, 'employee', 'employee@legalhr.com', 'emp123', 'Employee', 'User');
    }

    private function createUser(Client $client, string $roleName, string $email, string $password, string $firstName, string $lastName)
    {
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->command->warn("Role {$roleName} not found, skipping user {$email}");
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => Hash::make($password),
                'phone' => '+2557' . rand(10000000, 99999999),
                'is_active' => true,
                'email_verified_at' => now(),
                'current_client_id' => $client->id,
            ]
        );

        $user->roles()->sync([$role->id]);

        $pivotRole = match($roleName) {
            'hr_officer', 'lead_hr_admin', 'admin' => 'admin',
            'line_manager' => 'manager',
            default => 'employee',
        };

        $user->clients()->syncWithoutDetaching([
            $client->id => [
                'role' => $pivotRole,
                'is_active' => true,
                'joined_at' => now(),
            ]
        ]);

        $this->command->info("  - Created user: {$email} ({$roleName}) with password: {$password}");
    }
}
