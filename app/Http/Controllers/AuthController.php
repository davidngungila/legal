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

        // Add remember token if checkbox is checked
        $remember = $request->has('remember');

        // Bypass global scopes during authentication to prevent client filter interference
        $user = User::withoutGlobalScopes()->where('email', $credentials['email'])->first();

        \Log::info('Login attempt details', [
            'email' => $credentials['email'],
            'user_found' => $user ? 'yes' : 'no',
            'password_match' => $user ? (Hash::check($credentials['password'], $user->password) ? 'yes' : 'no') : 'n/a',
            'is_active' => $user ? ($user->is_active ? 'yes' : 'no') : 'n/a'
        ]);

        if ($user && Hash::check($credentials['password'], $user->password) && $user->is_active) {
            // Log successful auth attempt for debugging
            \Log::info('Authentication passed', ['email' => $user->email]);
            Auth::login($user, $remember);
            $request->session()->regenerate();

            $this->ensureClientContext($user, $request);

            // Update last login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            AuditLogger::log(
                'login',
                null,
                'Authentication',
                "User logged in: {$user->email}"
            );

            return redirect()->route('dashboard')->with('success', 'Login successful!');
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
     * Resolve and persist the active client for the authenticated user.
     */
    private function ensureClientContext(User $user, Request $request): void
    {
        $client = null;

        if ($user->current_client_id) {
            $stored = Client::find($user->current_client_id);

            // Super admins can be left attached to the system placeholder client
            // ("Orvion"), which has no tenant employees. Fall back to a real tenant
            // client so listing pages (e.g. /employees) are not empty by default.
            if ($stored) {
                $storedHasStaff = \App\Models\Employee::query()
                    ->withoutGlobalScopes()
                    ->where('client_id', $stored->id)
                    ->exists();

                if ($storedHasStaff || !$user->hasRole('super_admin')) {
                    $client = $stored;
                }
            }
        }

        if (!$client) {
            $client = $user->resolveDefaultClient();
        }

        if (!$client) {
            return;
        }

        $request->session()->put('current_client_id', $client->id);
        $request->session()->put('current_client', $client);
        $request->session()->put('current_client_name', $client->name);

        if ($user->current_client_id !== $client->id) {
            $user->update(['current_client_id' => $client->id]);
        }

        // Only super_admin is auto-synced into clients they visit
        if ($user->hasRole('super_admin') && 
            !$user->clients()->where('clients.id', $client->id)->exists()) {
            $user->clients()->syncWithoutDetaching([
                $client->id => [
                    'role' => $this->resolveClientPivotRole($user),
                    'is_active' => true,
                    'joined_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Map application roles to client pivot roles.
     */
    private function resolveClientPivotRole(User $user): string
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('lead_hr_admin') || $user->hasRole('hr_officer')) {
            return 'admin';
        }

        if ($user->hasRole('line_manager') || $user->hasRole('manager')) {
            return 'manager';
        }

        return 'employee';
    }

    /**
     * Get current authenticated user
     */
    public static function getCurrentUser()
    {
        return Auth::user();
    }
}
