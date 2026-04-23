<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'status',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'two_factor_recovery_codes' => 'array',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the clients associated with the user.
     */
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_user')
            ->withPivot('role', 'is_active', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the user settings.
     */
    public function settings()
    {
        return $this->hasOne(UserSettings::class);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is an HR admin.
     */
    public function isHrAdmin()
    {
        return $this->role === 'hr_admin';
    }

    /**
     * Check if user is an HR staff.
     */
    public function isHrStaff()
    {
        return $this->role === 'hr_staff';
    }

    /**
     * Check if user is an employee.
     */
    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    /**
     * Check if user has access to client management.
     */
    public function canManageClients()
    {
        return $this->isSuperAdmin() || $this->isHrAdmin();
    }

    /**
     * Check if user has access to payroll management.
     */
    public function canManagePayroll()
    {
        return $this->isSuperAdmin() || $this->isHrAdmin() || $this->isHrStaff();
    }

    /**
     * Check if user has access to employee management.
     */
    public function canManageEmployees()
    {
        return $this->isSuperAdmin() || $this->isHrAdmin() || $this->isHrStaff();
    }

    /**
     * Check if user has access to reports.
     */
    public function canViewReports()
    {
        return $this->isSuperAdmin() || $this->isHrAdmin() || $this->isHrStaff();
    }

    /**
     * Check if user has two-factor authentication enabled.
     */
    public function hasTwoFactorEnabled()
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        // Generate default avatar based on user's initials
        $initials = collect(explode(' ', $this->name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');

        return "https://ui-avatars.com/api/?name={$initials}&color=7F9CF5&background=EBF8FF&size=128";
    }

    /**
     * Get the user's full name with title.
     */
    public function getFullNameWithTitleAttribute()
    {
        $title = match($this->role) {
            'super_admin' => 'Super Admin',
            'hr_admin' => 'HR Admin',
            'hr_staff' => 'HR Staff',
            'employee' => 'Employee',
            default => 'User'
        };

        return "{$this->name} ({$title})";
    }

    /**
     * Scope a query to only include super admins.
     */
    public function scopeSuperAdmin($query)
    {
        return $query->where('role', 'super_admin');
    }

    /**
     * Scope a query to only include HR admins.
     */
    public function scopeHrAdmin($query)
    {
        return $query->where('role', 'hr_admin');
    }

    /**
     * Scope a query to only include HR staff.
     */
    public function scopeHrStaff($query)
    {
        return $query->where('role', 'hr_staff');
    }

    /**
     * Scope a query to only include employees.
     */
    public function scopeEmployee($query)
    {
        return $query->where('role', 'employee');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include verified users.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope a query to only include unverified users.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Get the user's role badge HTML.
     */
    public function getRoleBadgeAttribute()
    {
        $badges = [
            'super_admin' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Super Admin</span>',
            'hr_admin' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">HR Admin</span>',
            'hr_staff' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">HR Staff</span>',
            'employee' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Employee</span>',
        ];

        return $badges[$this->role] ?? $badges['employee'];
    }

    /**
     * Get the user's status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
            'inactive' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
            'suspended' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>',
        ];

        return $badges[$this->status] ?? $badges['inactive'];
    }
}
