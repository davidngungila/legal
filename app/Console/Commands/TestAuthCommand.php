<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

#[Signature('test:auth')]
#[Description('Test authentication')]
class TestAuthCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Test authentication with the admin user
        $credentials = [
            'email' => 'admin@legalhr.com',
            'password' => 'password',
        ];

        if (Auth::attempt($credentials)) {
            $this->info('Authentication: SUCCESS');
            $user = Auth::user();
            $this->info('Authenticated user: ' . $user->first_name . ' ' . $user->last_name);
            Auth::logout();
        } else {
            $this->error('Authentication: FAILED');
        }

        return 0;
    }
}
