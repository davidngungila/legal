<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController
{
    /**
     * Sample users for demonstration
     */
    private $sampleUsers = [
        [
            'email' => 'admin@legalhr.com',
            'password' => 'admin123',
            'name' => 'Admin User',
            'role' => 'super_admin'
        ],
        [
            'email' => 'hr@legalhr.com',
            'password' => 'hr123',
            'name' => 'HR Manager',
            'role' => 'hr_admin'
        ],
        [
            'email' => 'manager@legalhr.com',
            'password' => 'manager123',
            'name' => 'Department Manager',
            'role' => 'manager'
        ],
        [
            'email' => 'employee@legalhr.com',
            'password' => 'emp123',
            'name' => 'John Employee',
            'role' => 'employee'
        ]
    ];

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check against sample users
        $user = collect($this->sampleUsers)->firstWhere('email', $credentials['email']);

        if ($user && $user['password'] === $credentials['password']) {
            // Convert array to object for consistent access
            $userObject = (object) $user;
            
            // Store user object in session
            Session::put('user', $userObject);
            
            return redirect('/dashboard')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ]);
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        Session::forget('user');
        return redirect('/login')->with('success', 'Logged out successfully!');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // For demo purposes, just show success message
        return back()->with('success', 'Password reset instructions sent to your email!');
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        return Session::get('user');
    }
}
