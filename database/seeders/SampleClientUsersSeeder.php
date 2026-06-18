<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleClientUsersSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::whereIn('name', [
            'ABC Manufacturing Ltd',
            'Arusha Tech Solutions',
        ])->get();

        foreach ($clients as $client) {
            $this->command->info("Creating users for {$client->name}");
            
            $clientSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($client->name)));
            $clientSlug = trim($clientSlug, '-');

            $this->createUserForClient($client, 'hr_officer', "hr.{$clientSlug}@example.com", "{$client->name} HR Admin");
            $this->createUserForClient($client, 'line_manager', "manager.{$clientSlug}@example.com", "{$client->name} Department Manager");
            $this->createUserForClient($client, 'employee', "employee.{$clientSlug}@example.com", "{$client->name} Employee");
        }
    }

    private function createUserForClient(Client $client, string $roleName, string $email, string $name)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->command->warn("Role {$roleName} not found, skipping user {$email}");
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'first_name' => explode(' ', $name)[0],
                'last_name' => implode(' ', array_slice(explode(' ', $name), 1)),
                'password' => Hash::make('password123'),
                'phone' => '+2557' . rand(10000000, 99999999),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->roles()->sync([$role->id]);

        $pivotRole = match($roleName) {
            'hr_officer', 'lead_hr_admin' => 'admin',
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

        if (!$user->current_client_id) {
            $user->update(['current_client_id' => $client->id]);
        }

        $this->command->info("  - Created user: {$email} ({$roleName})");
    }
}