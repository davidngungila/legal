<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $currentClient = session('current_client');
        $currentUser = $user; // Add currentUser for layout
        
        // Get user statistics
        $stats = [
            'member_since' => $user->created_at->format('F Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('M j, Y g:i A') : 'Never',
            'profile_completion' => $this->calculateProfileCompletion($user),
            'security_score' => $this->calculateSecurityScore($user),
        ];

        // Get user settings
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        return view('profile.index', compact('user', 'settings', 'stats', 'currentClient', 'currentUser'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Upload new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        
        $user->update(['profile_photo' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully!',
            'photo_url' => Storage::url($path)
        ]);
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile photo deleted successfully!'
        ]);
    }

    /**
     * Get user's profile completion percentage.
     */
    private function calculateProfileCompletion($user)
    {
        $fields = [
            'first_name',
            'last_name', 
            'email',
            'phone',
            'profile_photo'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if ($user->$field) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    /**
     * Calculate user's security score.
     */
    private function calculateSecurityScore($user)
    {
        $score = 100;

        // Check if password is strong (basic check)
        if (strlen($user->password) < 8) {
            $score -= 20;
        }

        // Check if 2FA is enabled
        $settings = $user->settings;
        if (!$settings || !$settings->two_factor_enabled) {
            $score -= 30;
        }

        // Check last login time (suspicious if too recent or too old)
        if (!$user->last_login_at || $user->last_login_at->diffInDays(now()) > 30) {
            $score -= 10;
        }

        return max(0, $score);
    }

    /**
     * Get user activity log.
     */
    public function activityLog()
    {
        $user = Auth::user();
        
        // This would typically come from a dedicated activity log table
        $activities = [
            [
                'action' => 'Login',
                'description' => 'Logged in from ' . ($user->last_login_ip ?? 'Unknown IP'),
                'timestamp' => $user->last_login_at ?? now(),
                'ip_address' => $user->last_login_ip ?? 'Unknown'
            ],
            // Add more activities as needed
        ];

        return response()->json([
            'success' => true,
            'activities' => $activities
        ]);
    }

    /**
     * Update user settings.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,auto'],
            'language' => ['required', 'string', 'in:en,sw'],
            'timezone' => ['required', 'string'],
            'notification_email' => ['boolean'],
            'notification_push' => ['boolean'],
            'notification_sms' => ['boolean'],
            'two_factor_enabled' => ['boolean'],
            'auto_logout' => ['boolean'],
            'session_timeout' => ['integer', 'min:5', 'max:480'],
        ]);

        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully!',
            'settings' => $settings
        ]);
    }

    /**
     * Export user profile data.
     */
    public function export()
    {
        $user = Auth::user();
        $settings = $user->settings;

        $profileData = [
            'personal_information' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
            ],
            'settings' => $settings ? [
                'theme' => $settings->theme,
                'language' => $settings->language,
                'timezone' => $settings->timezone,
                'notifications' => [
                    'email' => $settings->notification_email,
                    'push' => $settings->notification_push,
                    'sms' => $settings->notification_sms,
                ]
            ] : [],
            'exported_at' => now()
        ];

        return response()->json($profileData);
    }
}
