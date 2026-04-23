<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'month_of_payment',
        'basic_salary',
        'ot_hours',
        'ot_rate',
        'ot_pay',
        'holiday_pay',
        'gross_pay',
        'nssf',
        'taxable_pay',
        'paye',
        'heslb',
        'other_ded',
        'total_deduction',
        'net_pay',
        'employer_nssf',
        'sdl',
        'wcf',
        'total_cost',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'ot_rate' => 'decimal:2',
        'ot_pay' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'nssf' => 'decimal:2',
        'taxable_pay' => 'decimal:2',
        'paye' => 'decimal:2',
        'heslb' => 'decimal:2',
        'other_ded' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'employer_nssf' => 'decimal:2',
        'sdl' => 'decimal:2',
        'wcf' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the payroll.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that the payroll belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include processed payrolls.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Scope a query to only include pending payrolls.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter by month of payment.
     */
    public function scopeByMonth($query, $month)
    {
        return $query->where('month_of_payment', $month);
    }

    /**
     * Get formatted currency values.
     */
    public function getFormattedBasicSalaryAttribute()
    {
        return 'TZS ' . number_format($this->basic_salary, 2);
    }

    public function getFormattedNetPayAttribute()
    {
        return 'TZS ' . number_format($this->net_pay, 2);
    }

    public function getFormattedGrossPayAttribute()
    {
        return 'TZS ' . number_format($this->gross_pay, 2);
    }
}
