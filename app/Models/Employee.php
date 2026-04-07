<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'position',
        'department',
        'manager_id',
        'hire_date',
        'termination_date',
        'employment_type',
        'status',
        'salary',
        'bank_account',
        'bank_name',
        'address',
        'city',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
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
     * Get the full name of the employee.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
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
        $clientId = app('current_client_id');
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
}
