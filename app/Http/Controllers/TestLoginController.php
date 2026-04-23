<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestLoginController
{
    /**
     * Test login functionality
     */
    public function testLogin()
    {
        // Find or create a test user
        $user = User::firstOrCreate(
            ['email' => 'test@legalhr.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Login the user
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Logged in as test user successfully!');
    }
}
