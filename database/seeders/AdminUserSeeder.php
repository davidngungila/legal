<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@legalhr.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'is_active' => true,
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]
        );

        // Get first active client
        $firstClient = Client::where('status', 'active')->first();
        
        if ($firstClient) {
            // Attach admin user to the first active client
            $adminUser->clients()->syncWithoutDetaching([
                $firstClient->id => [
                    'role' => 'admin',
                    'is_active' => true,
                    'joined_at' => now(),
                ]
            ]);
            
            $this->command->info("Admin user assigned to client: {$firstClient->name}");
        } else {
            $this->command->warn('No active clients found. Please run ClientSeeder first.');
        }

        // Create additional sample users for testing
        $sampleUsers = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign to first active client
            if ($firstClient) {
                $user->clients()->syncWithoutDetaching([
                    $firstClient->id => [
                        'role' => 'employee',
                        'is_active' => true,
                        'joined_at' => now(),
                    ]
                ]);
            }
        }

        $this->command->info('Admin user and sample users created successfully!');
        $this->command->info('Login credentials: admin@legalhr.com / password');
    }
}
