<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncEmployeesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:sync-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create User records for existing Employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync...');

        $employees = Employee::all();
        $count = 0;

        foreach ($employees as $employee) {
            $existingUser = User::where('email', $employee->email)->first();

            if (!$existingUser) {
                $this->info("Creating User for Employee: {$employee->full_name} ({$employee->email})");

                $user = User::create([
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'email' => $employee->email,
                    'password' => Hash::make('password'),
                    'is_active' => $employee->status === 'active',
                    'current_client_id' => $employee->client_id,
                ]);

                // Attach the user to the client
                $user->clients()->attach($employee->client_id, [
                    'role' => 'employee',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);

                // Attach employee role
                $employeeRole = \App\Models\Role::where('name', 'employee')->first();
                if ($employeeRole) {
                    $user->roles()->attach($employeeRole);
                }

                // Copy profile photo
                if ($employee->profile_photo) {
                    $user->profile_photo = $employee->profile_photo;
                    $user->save();
                }

                $count++;
            }
        }

        $this->info("Sync complete! Created {$count} User records.");
    }
}
