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
        'overtime_hours',
        'overtime_rate',
        'overtime_pay',
        'allowances',
        'bonuses',
        'gross_pay',
        'tax_deductions',
        'social_security',
        'pension',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'status',
        'notes',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'basic_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'tax_deductions' => 'decimal:2',
        'social_security' => 'decimal:2',
        'pension' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
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
            'draft' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'processed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Processed</span>',
            'paid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>',
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
            return static::query();
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
     * Calculate gross pay automatically.
     */
    public function calculateGrossPay()
    {
        $this->gross_pay = $this->basic_salary + $this->overtime_pay + $this->allowances + $this->bonuses;
        return $this;
    }

    /**
     * Calculate total deductions automatically.
     */
    public function calculateTotalDeductions()
    {
        $this->total_deductions = $this->tax_deductions + $this->social_security + $this->pension + $this->other_deductions;
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
}
