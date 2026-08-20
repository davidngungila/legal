<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class Payroll extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'payroll_period',
        'pay_date',
        'basic_salary',
        'hourly_rate',
        'daily_rate',
        'overtime_hours',
        'overtime_rate',
        'overtime_pay',
        'rest_day_hours',
        'rest_day_pay',
        'ph_hours',
        'ph_pay',
        'night_hours',
        'night_allowance',
        'unpaid_leave_days',
        'unpaid_leave_deduction',
        'allowances',
        'bonuses',
        'gross_pay',
        'taxable_income',
        'tax_deductions',
        'nssf_employee',
        'nssf_employer',
        'wcf',
        'sdl',
        'heslb',
        'trade_union',
        'pension',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'status',
        'workflow_state',
        'initiated_by',
        'initiated_at',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'locked_at',
        'locked_by',
        'performance_complete',
        'salary_hold',
        'salary_hold_reason',
        'notes',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'rest_day_hours' => 'decimal:2',
        'rest_day_pay' => 'decimal:2',
        'ph_hours' => 'decimal:2',
        'ph_pay' => 'decimal:2',
        'night_hours' => 'decimal:2',
        'night_allowance' => 'decimal:2',
        'unpaid_leave_days' => 'decimal:2',
        'unpaid_leave_deduction' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'tax_deductions' => 'decimal:2',
        'nssf_employee' => 'decimal:2',
        'nssf_employer' => 'decimal:2',
        'wcf' => 'decimal:2',
        'sdl' => 'decimal:2',
        'heslb' => 'decimal:2',
        'trade_union' => 'decimal:2',
        'pension' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'initiated_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'locked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the payroll.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that owns the payroll.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the formatted status badge.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Prepared</span>',
            'processed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Reviewed</span>',
            'paid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Reversed</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    /**
     * Filter payrolls by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }

    /**
     * Get payroll records for current client.
     */
    public static function forCurrentClient()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return static::where('client_id', 0); // Return empty query when no client is set
        }

        return static::where('client_id', $clientId);
    }

    /**
     * Scope to only include payrolls in a specific period.
     */
    public function scopeInPeriod($query, $period)
    {
        return $query->where('payroll_period', $period);
    }

    /**
     * Scope to only include payrolls with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to only include paid payrolls.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Calculate hourly rate: Monthly Salary ÷ (4.333 × 45)
     * BR-PAY-007
     */
    public function calculateHourlyRate()
    {
        if ($this->basic_salary > 0) {
            $this->hourly_rate = $this->basic_salary / (4.333 * 45);
        }
        return $this;
    }

    /**
     * Calculate daily rate: Monthly Salary ÷ (4.333 × 6)
     * BR-PAY-008
     */
    public function calculateDailyRate()
    {
        if ($this->basic_salary > 0) {
            $this->daily_rate = $this->basic_salary / (4.333 * 6);
        }
        return $this;
    }

    /**
     * Calculate overtime pay: Hourly Rate × 1.5 × Overtime Hours
     * BR-PAY-004 (capped at 50 hours per month - enforced in controller)
     */
    public function calculateOvertimePay()
    {
        if ($this->hourly_rate > 0 && $this->overtime_hours > 0) {
            $this->overtime_pay = $this->hourly_rate * 1.5 * $this->overtime_hours;
            $this->overtime_rate = $this->hourly_rate * 1.5;
        }
        return $this;
    }

    /**
     * Calculate rest day pay: Hourly Rate × 2.0 × Hours Worked
     * BR-PAY-005
     */
    public function calculateRestDayPay()
    {
        if ($this->hourly_rate > 0 && $this->rest_day_hours > 0) {
            $this->rest_day_pay = $this->hourly_rate * 2.0 * $this->rest_day_hours;
        }
        return $this;
    }

    /**
     * Calculate public holiday pay: Hourly Rate × 2.0 × Hours Worked
     * BR-PAY-006
     */
    public function calculatePublicHolidayPay()
    {
        if ($this->hourly_rate > 0 && $this->ph_hours > 0) {
            $this->ph_pay = $this->hourly_rate * 2.0 * $this->ph_hours;
        }
        return $this;
    }

    /**
     * Calculate night shift allowance: 5% of hourly rate for hours worked between 20:00 and 06:00
     * BR-PAY-007
     */
    public function calculateNightShiftAllowance()
    {
        if ($this->hourly_rate > 0 && $this->night_hours > 0) {
            $this->night_allowance = $this->night_hours * $this->hourly_rate * 0.05;
        }
        return $this;
    }

    /**
     * Calculate employee NSSF contribution: 10% of Gross Salary
     * BR-PAY-008
     */
    public function calculateNssfEmployee()
    {
        if ($this->gross_pay > 0) {
            $this->nssf_employee = $this->gross_pay * 0.10;
        }
        return $this;
    }

    /**
     * Calculate employer NSSF contribution: 10% of Gross Salary (employer-borne)
     * BR-PAY-009
     */
    public function calculateNssfEmployer()
    {
        if ($this->gross_pay > 0) {
            $this->nssf_employer = $this->gross_pay * 0.10;
        }
        return $this;
    }

    /**
     * Calculate taxable income: Gross Salary minus Employee NSSF Contribution
     * BR-PAY-010
     */
    public function calculateTaxableIncome()
    {
        $this->taxable_income = max(0, $this->gross_pay - $this->nssf_employee);
        return $this;
    }

    /**
     * Calculate PAYE using TRA tax bands
     * BR-PAY-011
     */
    public function calculatePAYE()
    {
        if ($this->taxable_income <= 0) {
            $this->tax_deductions = 0;
            return $this;
        }

        $taxBands = \DB::table('paye_tax_bands')
            ->where('is_active', true)
            ->orderBy('lower_limit')
            ->get();

        $paye = 0;
        $taxableIncome = $this->taxable_income;

        foreach ($taxBands as $band) {
            if ($taxableIncome > $band->lower_limit) {
                $upperLimit = $band->upper_limit ?? $taxableIncome;
                $taxableInBand = min($taxableIncome, $upperLimit) - $band->lower_limit;
                
                if ($taxableInBand > 0) {
                    $paye += $taxableInBand * ($band->rate / 100);
                }
            }
        }

        $this->tax_deductions = $paye;
        return $this;
    }

    /**
     * Calculate WCF: 0.5% of Gross Salary (employer contribution)
     * BR-PAY-012
     */
    public function calculateWCF()
    {
        if ($this->gross_pay > 0) {
            $this->wcf = $this->gross_pay * 0.005;
        }
        return $this;
    }

    /**
     * Calculate SDL: 4.5% of Gross Wage Bill (employer levy)
     * BR-PAY-013 (calculated at payroll run level, not per employee)
     */
    public function calculateSDL()
    {
        if ($this->gross_pay > 0) {
            $this->sdl = $this->gross_pay * 0.045;
        }
        return $this;
    }

    /**
     * Calculate gross pay with all components
     * BR-PAY-001
     */
    public function calculateGrossPay()
    {
        $this->gross_pay = $this->basic_salary 
            + $this->overtime_pay 
            + $this->rest_day_pay 
            + $this->ph_pay 
            + $this->night_allowance 
            + $this->allowances 
            + $this->bonuses 
            - $this->unpaid_leave_deduction;
        return $this;
    }

    /**
     * Calculate total deductions with all statutory deductions
     */
    public function calculateTotalDeductions()
    {
        $this->total_deductions = $this->tax_deductions 
            + $this->nssf_employee 
            + $this->heslb 
            + $this->trade_union 
            + $this->pension 
            + $this->other_deductions;
        return $this;
    }

    /**
     * Calculate net pay automatically.
     */
    public function calculateNetPay()
    {
        $this->net_pay = $this->gross_pay - $this->total_deductions;
        return $this;
    }

    /**
     * Execute complete payroll calculation sequence
     */
    public function calculateCompletePayroll()
    {
        return $this
            ->calculateHourlyRate()
            ->calculateDailyRate()
            ->calculateOvertimePay()
            ->calculateRestDayPay()
            ->calculatePublicHolidayPay()
            ->calculateNightShiftAllowance()
            ->calculateGrossPay()
            ->calculateNssfEmployee()
            ->calculateNssfEmployer()
            ->calculateTaxableIncome()
            ->calculatePAYE()
            ->calculateWCF()
            ->calculateSDL()
            ->calculateTotalDeductions()
            ->calculateNetPay();
    }

    /**
     * Check if payroll is locked
     */
    public function isLocked()
    {
        return $this->workflow_state === 'locked' || $this->locked_at !== null;
    }

    /**
     * Scope to only include prepared payrolls
     */
    public function scopePrepared($query)
    {
        return $query->where('workflow_state', 'prepared');
    }

    /**
     * Scope to only include reviewed payrolls
     */
    public function scopeReviewed($query)
    {
        return $query->where('workflow_state', 'reviewed');
    }

    /**
     * Scope to only include approved payrolls
     */
    public function scopeApproved($query)
    {
        return $query->where('workflow_state', 'approved');
    }

    /**
     * Scope to only include locked payrolls
     */
    public function scopeLocked($query)
    {
        return $query->where('workflow_state', 'locked');
    }

    /**
     * Scope to only include payrolls with salary hold
     */
    public function scopeWithSalaryHold($query)
    {
        return $query->where('salary_hold', true);
    }
}
