<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSettings;

class SettingsController
{
    /**
     * Display settings page
     */
    public function index()
    {
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings();
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'language' => $request->language,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
            'time_format' => $request->time_format,
        ]);

        return redirect()->back()
            ->with('success', 'General settings updated successfully!');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'notification_types' => 'array',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'notification_preferences' => json_encode($request->notification_types ?? []),
        ]);

        return redirect()->back()
            ->with('success', 'Notification settings updated successfully!');
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request)
    {
        $request->validate([
            'profile_visibility' => 'required|string',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'allow_direct_messages' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'profile_visibility' => $request->profile_visibility,
            'show_email' => $request->boolean('show_email'),
            'show_phone' => $request->boolean('show_phone'),
            'allow_direct_messages' => $request->boolean('allow_direct_messages'),
        ]);

        return redirect()->back()
            ->with('success', 'Privacy settings updated successfully!');
    }

    /**
     * Update appearance settings
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
            'sidebar_style' => 'required|string',
            'font_size' => 'required|string',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'theme' => $request->theme,
            'sidebar_style' => $request->sidebar_style,
            'font_size' => $request->font_size,
        ]);

        return redirect()->back()
            ->with('success', 'Appearance settings updated successfully!');
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'two_factor_enabled' => 'boolean',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'require_password_change' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'session_timeout' => $request->session_timeout,
            'require_password_change' => $request->boolean('require_password_change'),
        ]);

        return redirect()->back()
            ->with('success', 'Security settings updated successfully!');
    }

    /**
     * Update data storage settings
     */
    public function updateDataStorage(Request $request)
    {
        $request->validate([
            'data_retention_period' => 'required|integer|min:30|max:3650',
            'auto_backup' => 'boolean',
            'backup_frequency' => 'required|string',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'data_retention_period' => $request->data_retention_period,
            'auto_backup' => $request->boolean('auto_backup'),
            'backup_frequency' => $request->backup_frequency,
        ]);

        return redirect()->back()
            ->with('success', 'Data storage settings updated successfully!');
    }

    /**
     * Update integrations settings
     */
    public function updateIntegrations(Request $request)
    {
        $request->validate([
            'calendar_integration' => 'boolean',
            'email_integration' => 'boolean',
            'slack_integration' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings(['user_id' => $user->id]);
        
        $settings->update([
            'calendar_integration' => $request->boolean('calendar_integration'),
            'email_integration' => $request->boolean('email_integration'),
            'slack_integration' => $request->boolean('slack_integration'),
        ]);

        return redirect()->back()
            ->with('success', 'Integration settings updated successfully!');
    }

    /**
     * Reset settings to default
     */
    public function resetToDefault()
    {
        $user = auth()->user();
        $settings = $user->settings;
        
        if ($settings) {
            $settings->delete();
        }

        return redirect()->back()
            ->with('success', 'Settings reset to default successfully!');
    }

    /**
     * Export settings
     */
    public function export()
    {
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings();
        
        return response()->json($settings->toArray());
    }

    /**
     * Get settings data
     */
    public function getSettings()
    {
        $user = auth()->user();
        $settings = $user->settings ?? new UserSettings();
        
        return response()->json($settings->toArray());
    }
}
