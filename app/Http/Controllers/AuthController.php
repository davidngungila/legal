<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;
use App\Models\Role;
use App\Helpers\AuditLogger;

class AuthController
{
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Update last login
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
            
            // Set current client for the user
            if ($user->current_client_id) {
                $request->session()->put('current_client_id', $user->current_client_id);
            } else {
                $firstClient = $user->clients()->first();
                if ($firstClient) {
                    $request->session()->put('current_client_id', $firstClient->id);
                    $user->update(['current_client_id' => $firstClient->id]);
                }
            }
            
            AuditLogger::log(
                'login',
                null,
                'Authentication',
                "User logged in: {$user->email}"
            );
            
            return redirect('/dashboard')->with('success', 'Login successful!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.',
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            AuditLogger::log(
                'logout',
                null,
                'Authentication',
                "User logged out: {$user->email}"
            );
        }
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
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
     * Handle register request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:50',
            'company' => 'required|string|max:255',
            'role' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ]);

        $requestedRole = $validated['role'];
        $mappedRole = match ($requestedRole) {
            'hr_admin' => 'lead_hr_admin',
            'manager' => 'line_manager',
            default => $requestedRole,
        };

        $pivotRole = match ($mappedRole) {
            'lead_hr_admin', 'super_admin', 'hr_officer', 'finance_payroll_officer' => 'admin',
            'line_manager' => 'manager',
            default => 'employee',
        };

        return DB::transaction(function () use ($validated, $mappedRole, $pivotRole, $request) {
            $client = Client::create([
                'name' => $validated['company'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'industry' => 'General',
                'address' => 'N/A',
                'city' => 'N/A',
                'country' => 'Tanzania',
                'postal_code' => null,
                'website' => null,
                'contact_person' => $validated['first_name'] . ' ' . $validated['last_name'],
                'contact_title' => 'Administrator',
                'contact_email' => $validated['email'],
                'contact_phone' => $validated['phone'],
                'status' => 'active',
                'subscription_plan' => 'basic',
                'employee_count' => 1,
                'notes' => null,
            ]);

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
                'email_verified_at' => now(),
                'current_client_id' => $client->id,
            ]);

            $role = Role::where('name', $mappedRole)->first();
            if (!$role) {
                $role = Role::where('name', 'employee')->first();
            }
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            $user->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => $pivotRole,
                    'is_active' => true,
                    'joined_at' => now(),
                ],
            ]);

            $request->session()->put('current_client_id', $client->id);

            Auth::login($user);
            $request->session()->regenerate();
            
            AuditLogger::log(
                'registered',
                $user,
                'Users',
                "New user registered: {$user->email}",
                null,
                $user->toArray()
            );

            return redirect('/dashboard')->with('success', 'Account created successfully!');
        });
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        return Auth::user();
    }
}
