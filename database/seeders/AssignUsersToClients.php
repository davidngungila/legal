<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;

class AssignUsersToClients extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first available client
        $client = Client::first();
        
        if (!$client) {
            $this->command->error('No clients found in the database!');
            return;
        }

        // Get the test users
        $hrAdmin = User::where('email', 'hr@legalhr.com')->first();
        $manager = User::where('email', 'manager@legalhr.com')->first();
        $employee = User::where('email', 'employee@legalhr.com')->first();

        // Assign HR Admin to client with admin role
        if ($hrAdmin) {
            $hrAdmin->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => 'admin',
                    'is_active' => true,
                    'joined_at' => now()
                ]
            ]);
            $this->command->info('HR Admin assigned to client: ' . $client->name);
        }

        // Assign Manager to client with manager role
        if ($manager) {
            $manager->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => 'manager',
                    'is_active' => true,
                    'joined_at' => now()
                ]
            ]);
            $this->command->info('Manager assigned to client: ' . $client->name);
        }

        // Assign Employee to client with employee role
        if ($employee) {
            $employee->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => 'employee',
                    'is_active' => true,
                    'joined_at' => now()
                ]
            ]);
            $this->command->info('Employee assigned to client: ' . $client->name);
        }

        $this->command->info('All test users have been assigned to client: ' . $client->name);
        $this->command->info('Users can now log in and access the system!');
    }
}
