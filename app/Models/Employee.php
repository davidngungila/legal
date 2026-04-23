<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;
use App\Models\Traits\BelongsToCurrentClient;

class Employee extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'national_id',
        'passport_number',
        'tin_number',
        'nssf_number',
        'nhif_number',
        'position',
        'department',
        'manager_id',
        'hire_date',
        'termination_date',
        'probation_end_date',
        'employment_type',
        'status',
        'salary',
        'currency',
        'payment_frequency',
        'bank_account',
        'bank_name',
        'bank_branch',
        'address',
        'city',
        'region',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'work_schedule',
        'reporting_to',
        'education_level',
        'professional_qualifications',
        'certifications',
        'skills',
        'languages',
        'profile_photo',
        'documents',
        'contracts_count',
        'last_performance_review',
        'next_performance_review',
        'leave_balance',
        'overtime_hours',
        'benefits',
        'tax_information',
        'social_security_info',
        'health_insurance_info',
        'pension_info',
        'created_by',
        'updated_by',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'probation_end_date' => 'date',
        'last_performance_review' => 'date',
        'next_performance_review' => 'date',
        'salary' => 'decimal:2',
        'documents' => 'array',
        'professional_qualifications' => 'array',
        'certifications' => 'array',
        'skills' => 'array',
        'languages' => 'array',
        'benefits' => 'array',
        'tax_information' => 'array',
        'social_security_info' => 'array',
        'health_insurance_info' => 'array',
        'pension_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the employee.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the manager who oversees this employee.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the employees that report to this manager.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Get attendance records for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get payroll records for the employee.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get self-service requests for the employee.
     */
    public function selfServiceRequests(): HasMany
    {
        return $this->hasMany(SelfService::class);
    }

    
    /**
     * Get the formatted status badge.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
            'inactive' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
            'terminated' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Terminated</span>',
            'on_leave' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">On Leave</span>',
        ];

        return $badges[$this->status] ?? $badges['inactive'];
    }

    /**
     * Filter employees by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }

    /**
     * Get employees for the current client.
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
     * Scope to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to only include employees in a specific department.
     */
    public function scopeInDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to only include employees with a specific position.
     */
    public function scopeInPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get the contracts for the employee.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the active contract for the employee.
     */
    public function activeContract(): HasMany
    {
        return $this->hasMany(Contract::class)->where('status', 'active');
    }

    /**
     * Get the documents for the employee.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Get the performance reviews for the employee.
     */
    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    /**
     * Get the leave requests for the employee.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the user who created the employee.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the employee.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the age of the employee.
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    /**
     * Get the employment duration in years and months.
     */
    public function getEmploymentDurationAttribute(): string
    {
        if (!$this->hire_date) {
            return 'N/A';
        }

        $endDate = $this->termination_date ?: now();
        $duration = $this->hire_date->diff($endDate);

        $years = $duration->y;
        $months = $duration->m;

        if ($years > 0) {
            return "{$years} year" . ($years > 1 ? 's' : '') . ($months > 0 ? " {$months} month" . ($months > 1 ? 's' : '') : '');
        } elseif ($months > 0) {
            return "{$months} month" . ($months > 1 ? 's' : '');
        }

        return 'Less than 1 month';
    }

    /**
     * Check if the employee is on probation.
     */
    public function isOnProbation(): bool
    {
        return $this->probation_end_date && $this->probation_end_date->isFuture();
    }

    /**
     * Check if the employee is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the employee is on leave.
     */
    public function isOnLeave(): bool
    {
        return $this->status === 'on_leave';
    }

    /**
     * Check if the employee is terminated.
     */
    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'on_leave' => 'yellow',
            'suspended' => 'red',
            'terminated' => 'gray',
            'probation' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get the formatted salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        $currency = $this->currency ?? 'TZS';
        $symbol = match($currency) {
            'TZS' => 'TSh',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency
        };

        return "{$symbol}" . number_format($this->salary, 2);
    }

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get employee statistics for the current client.
     */
    public static function getEmployeeStats()
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return [
                'total' => 0,
                'active' => 0,
                'on_leave' => 0,
                'probation' => 0,
                'terminated' => 0,
                'by_department' => [],
                'by_employment_type' => [],
                'by_position' => [],
                'avg_salary' => 0,
                'total_payroll' => 0
            ];
        }

        $employees = self::where('client_id', $clientId)->get();

        return [
            'total' => $employees->count(),
            'active' => $employees->where('status', 'active')->count(),
            'on_leave' => $employees->where('status', 'on_leave')->count(),
            'probation' => $employees->where('status', 'probation')->count(),
            'terminated' => $employees->where('status', 'terminated')->count(),
            'by_department' => $employees->groupBy('department')->map->count(),
            'by_employment_type' => $employees->groupBy('employment_type')->map->count(),
            'by_position' => $employees->groupBy('position')->map->count(),
            'avg_salary' => $employees->avg('salary'),
            'total_payroll' => $employees->sum('salary'),
        ];
    }

    /**
     * Get compliance information for the employee.
     */
    public function getComplianceInfo(): array
    {
        return [
            'has_national_id' => !empty($this->national_id),
            'has_tin_number' => !empty($this->tin_number),
            'has_nssf_number' => !empty($this->nssf_number),
            'has_nhif_number' => !empty($this->nhif_number),
            'has_bank_account' => !empty($this->bank_account),
            'has_valid_contract' => $this->activeContract()->exists(),
            'is_compliant' => !empty($this->national_id) && !empty($this->tin_number) && !empty($this->nssf_number),
        ];
    }

    /**
     * Get documents that need attention.
     */
    public function getDocumentsNeedingAttention(): array
    {
        $needed = [];
        
        if (empty($this->national_id)) {
            $needed[] = 'National ID';
        }
        
        if (empty($this->tin_number)) {
            $needed[] = 'TIN Certificate';
        }
        
        if (empty($this->nssf_number)) {
            $needed[] = 'NSSF Card';
        }
        
        if (empty($this->nhif_number)) {
            $needed[] = 'NHIF Card';
        }
        
        if (!$this->activeContract()->exists()) {
            $needed[] = 'Employment Contract';
        }
        
        return $needed;
    }

    /**
     * Generate employee profile for export.
     */
    public function toExportArray(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'department' => $this->department,
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'hire_date' => $this->hire_date->format('Y-m-d'),
            'salary' => $this->formatted_salary,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'emergency_contact' => $this->emergency_contact_name,
            'emergency_phone' => $this->emergency_contact_phone,
            'compliance' => $this->getComplianceInfo(),
            'documents_needed' => $this->getDocumentsNeedingAttention(),
        ];
    }
}
