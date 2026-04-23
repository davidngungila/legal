<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable([
    'user_id',
    'theme',
    'language',
    'timezone',
    'date_format',
    'time_format',
    'currency',
    'notification_email',
    'notification_push',
    'notification_sms',
    'two_factor_enabled',
    'session_timeout',
    'auto_logout',
    'privacy_profile_visibility',
    'privacy_activity_visibility',
    'data_export_format',
    'dashboard_layout',
    'preferences'
])]
#[Hidden([])]
class UserSetting extends Model
{
    use HasFactory;

    protected $casts = [
        'notification_email' => 'boolean',
        'notification_push' => 'boolean',
        'notification_sms' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'auto_logout' => 'boolean',
        'preferences' => 'json',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default settings for new users.
     */
    public static function getDefaultSettings()
    {
        return [
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'Africa/Dar_es_Salaam',
            'date_format' => 'Y-m-d',
            'time_format' => '24',
            'currency' => 'TZS',
            'notification_email' => true,
            'notification_push' => true,
            'notification_sms' => false,
            'two_factor_enabled' => false,
            'session_timeout' => 120,
            'auto_logout' => false,
            'privacy_profile_visibility' => 'team',
            'privacy_activity_visibility' => 'team',
            'data_export_format' => 'csv',
            'dashboard_layout' => 'default',
            'preferences' => json_encode([
                'sidebar_collapsed' => false,
                'compact_view' => false,
                'show_tooltips' => true,
                'auto_save' => true,
                'animations_enabled' => true
            ])
        ];
    }

    /**
     * Create settings for a new user.
     */
    public static function createForUser($userId)
    {
        $defaultSettings = self::getDefaultSettings();
        $defaultSettings['user_id'] = $userId;
        
        return self::create($defaultSettings);
    }

    /**
     * Update specific preference.
     */
    public function updatePreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
        
        return $this;
    }

    /**
     * Get specific preference.
     */
    public function getPreference($key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }
}
