<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'industry',
        'address',
        'city',
        'country',
        'postal_code',
        'website',
        'contact_person',
        'contact_title',
        'contact_email',
        'contact_phone',
        'status',
        'subscription_plan',
        'employee_count',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'employee_count' => 'integer',
    ];

    /**
     * Get the users associated with the client.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'client_user')
            ->withPivot('role', 'is_active', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the employees for the client.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the departments for the client.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the positions for the client.
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the contracts for the client.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the formatted status badge.
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

    /**
     * Get the formatted subscription plan badge.
     */
    public function getSubscriptionBadgeAttribute()
    {
        $badges = [
            'basic' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Basic</span>',
            'premium' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Premium</span>',
            'enterprise' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Enterprise</span>',
        ];

        return $badges[$this->subscription_plan] ?? $badges['basic'];
    }

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive clients.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include suspended clients.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
}
