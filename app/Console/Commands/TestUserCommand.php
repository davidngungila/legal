<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;

#[Signature('test:user')]
#[Description('Test user authentication')]
class TestUserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('email', 'admin@legalhr.com')->first();
        
        if ($user) {
            $this->info('User found: ' . $user->first_name . ' ' . $user->last_name);
            $this->info('Email: ' . $user->email);
            $this->info('Active: ' . ($user->is_active ? 'Yes' : 'No'));
            $this->info('Password hash exists: ' . (!empty($user->password) ? 'Yes' : 'No'));
            
            // Test password verification
            if (\Hash::check('password', $user->password)) {
                $this->info('Password verification: SUCCESS');
            } else {
                $this->error('Password verification: FAILED');
                
                // Reset password
                $user->password = \Hash::make('password');
                $user->save();
                $this->info('Password has been reset');
            }
        } else {
            $this->error('User not found');
            
            // Create user
            $user = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@legalhr.com',
                'password' => \Hash::make('password'),
                'is_active' => true,
            ]);
            
            $this->info('User created successfully');
        }
        
        return 0;
    }
}
