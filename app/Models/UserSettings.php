<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'timezone',
        'notifications',
        'email_notifications',
        'push_notifications',
        'two_factor_enabled',
        'auto_save_interval',
        'default_client_id',
        'preferences',
    ];

    protected $casts = [
        'notifications' => 'array',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'auto_save_interval' => 'integer',
        'preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default client.
     */
    public function defaultClient()
    {
        return $this->belongsTo(Client::class, 'default_client_id');
    }

    /**
     * Scope a query to only include users with two-factor enabled.
     */
    public function scopeTwoFactorEnabled($query)
    {
        return $query->where('two_factor_enabled', true);
    }

    /**
     * Scope a query to only include users with email notifications enabled.
     */
    public function scopeEmailNotificationsEnabled($query)
    {
        return $query->where('email_notifications', true);
    }

    /**
     * Scope a query to only include users with push notifications enabled.
     */
    public function scopePushNotificationsEnabled($query)
    {
        return $query->where('push_notifications', true);
    }

    /**
     * Get notification preference for a specific type.
     */
    public function getNotificationPreference($type)
    {
        return $this->notifications[$type] ?? true;
    }

    /**
     * Set notification preference for a specific type.
     */
    public function setNotificationPreference($type, $enabled)
    {
        $notifications = $this->notifications ?? [];
        $notifications[$type] = $enabled;
        $this->notifications = $notifications;
    }

    /**
     * Get preference value.
     */
    public function getPreference($key, $default = null)
    {
        return $this->preferences[$key] ?? $default;
    }

    /**
     * Set preference value.
     */
    public function setPreference($key, $value)
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
    }
}
