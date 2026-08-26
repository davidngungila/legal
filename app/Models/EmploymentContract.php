<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class EmploymentContract extends Model
{
    use HasFactory, BelongsToCurrentClient;

    const CONTRACT_TYPES = [
        'unspecified' => 'Unspecified (Permanent)',
        'fixed_term' => 'Fixed Term',
        'specific_task' => 'Specific Task',
        'commission' => 'Commission',
        'internship' => 'Internship',
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'active' => 'Active',
        'expired' => 'Expired',
        'terminated' => 'Terminated',
        'renewed' => 'Renewed',
    ];

    const PAYMENT_FREQUENCIES = [
        'weekly' => 'Weekly',
        'biweekly' => 'Bi-Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually' => 'Annually',
    ];

    const REVIEW_FREQUENCIES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'semi_annually' => 'Semi-Annually',
        'annually' => 'Annually',
    ];

    protected $fillable = [
        'client_id',
        'employee_id',
        'contract_number',
        'contract_title',
        'contract_type',
        'effective_date',
        'expiry_date',
        'probation_end_date',
        'job_title',
        'department',
        'reporting_line',
        'work_location',
        'work_schedule',
        'salary_currency',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'meal_allowance',
        'other_allowances',
        'total_compensation',
        'payment_frequency',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'working_hours_per_week',
        'overtime_rate',
        'leave_entitlement_days',
        'sick_leave_days',
        'public_holidays',
        'maternity_leave_weeks',
        'paternity_leave_weeks',
        'notice_period_days',
        'confidentiality_clause',
        'non_compete_clause',
        'non_compete_duration_months',
        'non_compete_restriction',
        'intellectual_property_clause',
        'data_protection_clause',
        'health_and_safety_clause',
        'training_development_clause',
        'company_policies_acknowledgment',
        'termination_clause',
        'grievance_procedure',
        'disciplinary_procedure',
        'benefits_package',
        'performance_review_frequency',
        'contract_document_path',
        'signed_contract_path',
        'employee_signature_path',
        'employer_signature_path',
        'witness_name',
        'witness_title',
        'witness_signature_path',
        'status',
        'notes',
        'signed_at',
        'activated_at',
        'terminated_at',
        'termination_reason',
        'termination_type',
        'renewal_count',
        'last_renewal_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'probation_end_date' => 'date',
        'signed_at' => 'datetime',
        'activated_at' => 'datetime',
        'terminated_at' => 'datetime',
        'last_renewal_date' => 'date',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'total_compensation' => 'decimal:2',
        'working_hours_per_week' => 'decimal:1',
        'overtime_rate' => 'decimal:2',
        'confidentiality_clause' => 'boolean',
        'non_compete_clause' => 'boolean',
        'intellectual_property_clause' => 'boolean',
        'data_protection_clause' => 'boolean',
        'health_and_safety_clause' => 'boolean',
        'training_development_clause' => 'boolean',
        'company_policies_acknowledgment' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFormattedContractNumberAttribute(): string
    {
        return $this->contract_number ?: ('EMP-' . str_pad($this->id, 6, '0', STR_PAD_LEFT));
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'active' && $this->expiry_date && $this->expiry_date->isPast()) {
            return 'expired';
        }

        return $this->status;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->effective_status) {
            'active' => 'green',
            'renewed' => 'emerald',
            'draft' => 'gray',
            'expired' => 'red',
            'terminated' => 'red',
            default => 'gray',
        };
    }

    public function getRemainingDaysAttribute(): int
    {
        if (! $this->expiry_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->expiry_date));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->effective_status === 'expired';
    }

    public function getFormattedBasicSalaryAttribute(): string
    {
        return number_format((float) $this->basic_salary, 2) . ' ' . ($this->salary_currency ?: 'TZS');
    }

    public function getFormattedTotalCompensationAttribute(): string
    {
        return number_format((float) $this->total_compensation, 2) . ' ' . ($this->salary_currency ?: 'TZS');
    }

    public function getDurationMonthsAttribute(): int
    {
        if (! $this->effective_date) {
            return 0;
        }

        $end = $this->expiry_date ?: now();

        return max(0, $this->effective_date->diffInMonths($end));
    }

    public function isExpiringSoon(int $days = 60): bool
    {
        return in_array($this->effective_status, ['active', 'renewed'])
            && $this->expiry_date
            && $this->expiry_date->between(now(), now()->addDays($days));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('contract_number', 'like', "%{$term}%")
                ->orWhere('job_title', 'like', "%{$term}%")
                ->orWhere('department', 'like', "%{$term}%")
                ->orWhereHas('employee', function (Builder $eq) use ($term) {
                    $eq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('employee_id', 'like', "%{$term}%");
                });
        });
    }

    public static function generateContractNumber(): string
    {
        $clientId = session('current_client_id');
        $year = date('Y');

        $query = self::whereYear('created_at', $year);
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        $base = 'EMP-' . $year . '-' . str_pad($query->count() + 1, 4, '0', STR_PAD_LEFT);

        $number = $base;
        $suffix = 1;
        while (self::where('contract_number', $number)->exists()) {
            $number = $base . '-' . ++$suffix;
        }

        return $number;
    }

    public static function getContractStats(): array
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return self::emptyStats();
        }

        $contracts = self::with('employee')->where('client_id', $clientId)->get();

        return [
            'total' => $contracts->count(),
            'draft' => $contracts->where('status', 'draft')->count(),
            'active' => $contracts->filter(fn ($c) => $c->effective_status === 'active')->count(),
            'renewed' => $contracts->where('status', 'renewed')->count(),
            'expired' => $contracts->filter(fn ($c) => $c->effective_status === 'expired')->count(),
            'terminated' => $contracts->where('status', 'terminated')->count(),
            'expiring_soon' => $contracts->filter(fn ($c) => $c->isExpiringSoon(60))->count(),
            'total_compensation' => round($contracts->sum('total_compensation'), 2),
            'average_salary' => $contracts->count() > 0 ? round($contracts->sum('basic_salary') / $contracts->count(), 2) : 0,
            'renewal_rate' => $contracts->count() > 0
                ? round(($contracts->where('renewal_count', '>', 0)->count() / $contracts->count()) * 100, 1)
                : 0,
            'termination_rate' => $contracts->count() > 0
                ? round(($contracts->where('status', 'terminated')->count() / $contracts->count()) * 100, 1)
                : 0,
            'by_type' => $contracts->count() > 0
                ? $contracts->groupBy('contract_type')->map(fn ($group) => $group->count())
                : collect(),
            'employees_covered' => $contracts->pluck('employee_id')->unique()->count(),
        ];
    }

    public static function getRequiringAttention(): array
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return [];
        }

        $contracts = self::with('employee')->where('client_id', $clientId)->get();

        $expiringSoon = $contracts->filter(fn ($c) => $c->isExpiringSoon(60))->sortBy('expiry_date')->values();
        $expired = $contracts->filter(fn ($c) => $c->effective_status === 'expired')->sortByDesc('expiry_date')->values();
        $pendingSignature = $contracts->where('status', 'draft')->values();
        $probationEnding = $contracts->filter(function ($c) {
            return in_array($c->effective_status, ['active', 'renewed'])
                && $c->probation_end_date
                && $c->probation_end_date->between(now(), now()->addDays(30));
        })->sortBy('probation_end_date')->values();

        return [
            'expiring_soon' => $expiringSoon,
            'expired' => $expired,
            'pending_signature' => $pendingSignature,
            'probation_ending' => $probationEnding,
            'total' => $expiringSoon->count() + $expired->count() + $pendingSignature->count() + $probationEnding->count(),
        ];
    }

    public static function getCalendarEvents(): array
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return [];
        }

        $contracts = self::with('employee')->where('client_id', $clientId)->get();
        $events = [];

        foreach ($contracts as $contract) {
            $name = $contract->employee?->full_name ?? 'Unknown Employee';

            if ($contract->effective_date) {
                $events[] = [
                    'id' => $contract->id . '-start',
                    'title' => 'Contract Start - ' . $name,
                    'start' => $contract->effective_date->format('Y-m-d'),
                    'type' => 'start',
                    'contract' => $contract->formatted_contract_number,
                ];
            }

            if ($contract->expiry_date) {
                $events[] = [
                    'id' => $contract->id . '-expiry',
                    'title' => 'Contract Expiry - ' . $name,
                    'start' => $contract->expiry_date->format('Y-m-d'),
                    'type' => 'expiry',
                    'contract' => $contract->formatted_contract_number,
                ];
            }

            if ($contract->probation_end_date) {
                $events[] = [
                    'id' => $contract->id . '-probation',
                    'title' => 'Probation End - ' . $name,
                    'start' => $contract->probation_end_date->format('Y-m-d'),
                    'type' => 'probation_end',
                    'contract' => $contract->formatted_contract_number,
                ];
            }

            if ($contract->last_renewal_date) {
                $events[] = [
                    'id' => $contract->id . '-renewal',
                    'title' => 'Contract Renewal - ' . $name,
                    'start' => $contract->last_renewal_date->format('Y-m-d'),
                    'type' => 'renewal',
                    'contract' => $contract->formatted_contract_number,
                ];
            }
        }

        return $events;
    }

    private static function emptyStats(): array
    {
        return [
            'total' => 0,
            'draft' => 0,
            'active' => 0,
            'renewed' => 0,
            'expired' => 0,
            'terminated' => 0,
            'expiring_soon' => 0,
            'total_compensation' => 0,
            'average_salary' => 0,
            'renewal_rate' => 0,
            'termination_rate' => 0,
            'by_type' => collect(),
            'employees_covered' => 0,
        ];
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where((new static)->getTable() . '.client_id', $clientId);
    }
}
