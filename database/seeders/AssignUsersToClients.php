<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignUsersToClients extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = Client::orderBy('name')->first();

        if (!$client) {
            $this->command->error('No clients found in the database!');
            return;
        }

        $users = User::withoutGlobalScopes()->get();

        foreach ($users as $user) {
            if ($user->hasRole('super_admin') && $user->clients()->count() === 0) {
                continue;
            }

            $pivotRole = match (true) {
                $user->hasRole('super_admin'),
                $user->hasRole('lead_hr_admin'),
                $user->hasRole('hr_officer') => 'admin',
                $user->hasRole('line_manager') => 'manager',
                default => 'employee',
            };

            $user->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => $pivotRole,
                    'is_active' => true,
                    'joined_at' => now(),
                ],
            ]);

            if (!$user->current_client_id) {
                $user->update(['current_client_id' => $client->id]);
            }

            $this->command->info("Assigned {$user->email} to client: {$client->name}");
        }

        $this->command->info('All users have been linked to client: ' . $client->name);
    }
}
