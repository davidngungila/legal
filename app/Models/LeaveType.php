<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class LeaveType extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'type_name',
        'entitlement_days',
        'accrual_rate',
        'eligibility_months',
        'cycle_months',
        'is_paid',
        'pay_rate',
        'is_active',
    ];

    protected $casts = [
        'entitlement_days' => 'decimal:2',
        'accrual_rate' => 'decimal:2',
        'is_paid' => 'boolean',
        'pay_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function leaveEntitlements(): HasMany
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
