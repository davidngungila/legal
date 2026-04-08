<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);
        $currentClient = session('current_client');
        $currentUser = $user; // Add currentUser for layout

        return view('settings.index', compact('user', 'settings', 'currentClient', 'currentUser'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'in:en,sw'],
            'timezone' => ['required', 'string', 'timezone'],
            'date_format' => ['required', 'string', 'in:Y-m-d,d/m/Y,m/d/Y'],
            'time_format' => ['required', 'string', 'in:12,24'],
            'currency' => ['required', 'string', 'in:TZS,USD,EUR'],
        ]);

        // Update user display name
        $user->update([
            'display_name' => $validated['display_name']
        ]);

        // Update settings
        $settings->update([
            'language' => $validated['language'],
            'timezone' => $validated['timezone'],
            'date_format' => $validated['date_format'],
            'time_format' => $validated['time_format'],
            'currency' => $validated['currency'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'General settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'notification_email' => ['boolean'],
            'notification_push' => ['boolean'],
            'notification_sms' => ['boolean'],
            'email_notifications' => ['array'],
            'push_notifications' => ['array'],
            'sms_notifications' => ['array'],
        ]);

        $settings->update([
            'notification_email' => $validated['notification_email'] ?? false,
            'notification_push' => $validated['notification_push'] ?? false,
            'notification_sms' => $validated['notification_sms'] ?? false,
        ]);

        // Update notification preferences
        $preferences = $settings->preferences ?? [];
        $preferences['email_notifications'] = $validated['email_notifications'] ?? [];
        $preferences['push_notifications'] = $validated['push_notifications'] ?? [];
        $preferences['sms_notifications'] = $validated['sms_notifications'] ?? [];
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update privacy settings.
     */
    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'privacy_profile_visibility' => ['required', 'string', 'in:public,team,private'],
            'privacy_activity_visibility' => ['required', 'string', 'in:public,team,private'],
            'data_sharing' => ['boolean'],
            'analytics_tracking' => ['boolean'],
            'marketing_emails' => ['boolean'],
        ]);

        $settings->update([
            'privacy_profile_visibility' => $validated['privacy_profile_visibility'],
            'privacy_activity_visibility' => $validated['privacy_activity_visibility'],
        ]);

        // Update privacy preferences
        $preferences = $settings->preferences ?? [];
        $preferences['data_sharing'] = $validated['data_sharing'] ?? false;
        $preferences['analytics_tracking'] = $validated['analytics_tracking'] ?? false;
        $preferences['marketing_emails'] = $validated['marketing_emails'] ?? false;
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Privacy settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update appearance settings.
     */
    public function updateAppearance(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,auto'],
            'dashboard_layout' => ['required', 'string', 'in:default,compact,detailed'],
            'sidebar_collapsed' => ['boolean'],
            'compact_view' => ['boolean'],
            'show_tooltips' => ['boolean'],
            'animations_enabled' => ['boolean'],
        ]);

        $settings->update([
            'theme' => $validated['theme'],
            'dashboard_layout' => $validated['dashboard_layout'],
        ]);

        // Update appearance preferences
        $preferences = $settings->preferences ?? [];
        $preferences['sidebar_collapsed'] = $validated['sidebar_collapsed'] ?? false;
        $preferences['compact_view'] = $validated['compact_view'] ?? false;
        $preferences['show_tooltips'] = $validated['show_tooltips'] ?? true;
        $preferences['animations_enabled'] = $validated['animations_enabled'] ?? true;
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Appearance settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update security settings.
     */
    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'two_factor_enabled' => ['boolean'],
            'session_timeout' => ['required', 'integer', 'min:15', 'max:480'],
            'auto_logout' => ['boolean'],
            'login_notifications' => ['boolean'],
            'password_expiry' => ['integer', 'min:0', 'max:365'],
        ]);

        $settings->update([
            'two_factor_enabled' => $validated['two_factor_enabled'] ?? false,
            'session_timeout' => $validated['session_timeout'],
            'auto_logout' => $validated['auto_logout'] ?? false,
        ]);

        // Update security preferences
        $preferences = $settings->preferences ?? [];
        $preferences['login_notifications'] = $validated['login_notifications'] ?? true;
        $preferences['password_expiry'] = $validated['password_expiry'] ?? 90;
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Security settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update data and storage settings.
     */
    public function updateDataStorage(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'data_export_format' => ['required', 'string', 'in:csv,xlsx,json'],
            'auto_backup' => ['boolean'],
            'data_retention_days' => ['integer', 'min:30', 'max:3650'],
            'cache_clear_frequency' => ['string', 'in:daily,weekly,monthly,never'],
        ]);

        $settings->update([
            'data_export_format' => $validated['data_export_format'],
        ]);

        // Update data preferences
        $preferences = $settings->preferences ?? [];
        $preferences['auto_backup'] = $validated['auto_backup'] ?? false;
        $preferences['data_retention_days'] = $validated['data_retention_days'] ?? 365;
        $preferences['cache_clear_frequency'] = $validated['cache_clear_frequency'] ?? 'weekly';
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Data and storage settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Update integration settings.
     */
    public function updateIntegrations(Request $request)
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        $validated = $request->validate([
            'integrations' => ['array'],
            'api_keys' => ['array'],
            'webhook_urls' => ['array'],
        ]);

        // Update integration preferences
        $preferences = $settings->preferences ?? [];
        $preferences['integrations'] = $validated['integrations'] ?? [];
        $preferences['api_keys'] = $validated['api_keys'] ?? [];
        $preferences['webhook_urls'] = $validated['webhook_urls'] ?? [];
        
        $settings->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Integration settings updated successfully!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Reset all settings to default.
     */
    public function resetToDefault()
    {
        $user = Auth::user();
        $settings = $user->settings;

        if ($settings) {
            $defaultSettings = UserSetting::getDefaultSettings();
            $settings->update($defaultSettings);
        }

        return response()->json([
            'success' => true,
            'message' => 'All settings have been reset to default values!',
            'settings' => $settings->fresh()
        ]);
    }

    /**
     * Export settings data.
     */
    public function export()
    {
        $user = Auth::user();
        $settings = $user->settings;

        $exportData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ],
            'settings' => $settings ? [
                'theme' => $settings->theme,
                'language' => $settings->language,
                'timezone' => $settings->timezone,
                'notifications' => [
                    'email' => $settings->notification_email,
                    'push' => $settings->notification_push,
                    'sms' => $settings->notification_sms,
                ],
                'privacy' => [
                    'profile_visibility' => $settings->privacy_profile_visibility,
                    'activity_visibility' => $settings->privacy_activity_visibility,
                ],
                'security' => [
                    'two_factor_enabled' => $settings->two_factor_enabled,
                    'session_timeout' => $settings->session_timeout,
                ],
                'preferences' => $settings->preferences,
            ] : [],
            'exported_at' => now()
        ];

        return response()->json($exportData);
    }

    /**
     * Get all settings for the current user.
     */
    public function getSettings()
    {
        $user = Auth::user();
        $settings = $user->settings ?? UserSetting::createForUser($user->id);

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }
}
