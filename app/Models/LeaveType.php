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

    /**
     * Calculate monthly accrual rate for Tanzania annual leave
     * BR-LV-001: 28 days per year = 2.333 days per month
     */
    public function calculateMonthlyAccrual()
    {
        if ($this->type_name === 'Annual Leave' || $this->type_name === 'annual_leave') {
            return 28 / 12; // 2.333 days per month
        }
        
        return $this->accrual_rate ?? 0;
    }

    /**
     * Check if employee is eligible for this leave type
     * BR-LV-002: Annual leave only after 6 months of service
     */
    public function isEmployeeEligible($employee)
    {
        if (!$employee->hire_date) {
            return false;
        }

        $monthsOfService = \Carbon\Carbon::parse($employee->hire_date)->diffInMonths(now());

        // Annual leave requires 6 months of service
        if (($this->type_name === 'Annual Leave' || $this->type_name === 'annual_leave') && $monthsOfService < 6) {
            return false;
        }

        // Check eligibility months for other leave types
        if ($this->eligibility_months > 0 && $monthsOfService < $this->eligibility_months) {
            return false;
        }

        return true;
    }

    /**
     * Get maximum leave allowed at 6 months of service for annual leave
     * BR-LV-002: Maximum 14 days at 6 months
     */
    public function getMaxAt6Months()
    {
        if ($this->type_name === 'Annual Leave' || $this->type_name === 'annual_leave') {
            return 14;
        }
        
        return $this->entitlement_days;
    }

    /**
     * Scope to only include active leave types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get Tanzania default leave types
     */
    public static function getTanzaniaDefaults()
    {
        return [
            [
                'type_name' => 'Annual Leave',
                'entitlement_days' => 28,
                'accrual_rate' => 2.333,
                'eligibility_months' => 6,
                'cycle_months' => 12,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'type_name' => 'Sick Leave - Full Pay',
                'entitlement_days' => 63,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'type_name' => 'Sick Leave - Half Pay',
                'entitlement_days' => 63,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 50,
                'is_active' => true,
            ],
            [
                'type_name' => 'Compassionate Leave',
                'entitlement_days' => 4,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 36,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
            [
                'type_name' => 'Unpaid Leave',
                'entitlement_days' => 0,
                'accrual_rate' => 0,
                'eligibility_months' => 0,
                'cycle_months' => 12,
                'is_paid' => false,
                'pay_rate' => 0,
                'is_active' => true,
            ],
            [
                'type_name' => 'Maternity Leave',
                'entitlement_days' => 84,
                'accrual_rate' => 0,
                'eligibility_months' => 6,
                'cycle_months' => 12,
                'is_paid' => true,
                'pay_rate' => 100,
                'is_active' => true,
            ],
        ];
    }
}
