<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrvionClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the Orvion client
        $orvion = Client::firstOrCreate(
            ['name' => 'Orvion'],
            [
                'email' => 'info@orvion.com',
                'phone' => '+1234567890',
                'industry' => 'Technology',
                'address' => '123 Tech Street',
                'city' => 'San Francisco',
                'country' => 'USA',
                'contact_person' => 'Orvion Admin',
                'contact_title' => 'Administrator',
                'contact_email' => 'admin@orvion.com',
                'contact_phone' => '+1234567890',
                'status' => 'active',
                'subscription_plan' => 'enterprise',
            ]
        );

        // Get super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if ($superAdminRole) {
            // Attach all super admins to Orvion client
            $superAdmins = User::whereHas('roles', function ($query) {
                $query->where('name', 'super_admin');
            })->get();

            foreach ($superAdmins as $admin) {
                $admin->clients()->syncWithoutDetaching([
                    $orvion->id => [
                        'role' => 'admin',
                        'is_active' => true,
                        'joined_at' => now(),
                    ]
                ]);
            }
        }
    }
}
