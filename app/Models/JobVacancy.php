<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Traits\BelongsToCurrentClient;

class JobVacancy extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $table = 'job_vacancies';

    protected $fillable = [
        'client_id',
        'company_name',
        'job_title',
        'vacancy_type',
        'position_vacant_date',
        'application_date',
        'application_deadline',
        'department',
        'workstation',
        'replacement_reason',
        'job_description',
        'min_age',
        'academic_qualifications',
        'professional_qualifications',
        'other_qualifications',
        'salary_range_min',
        'salary_range_max',
        'additional_comments',
        'status',
        'initiated_by',
        'supervisor_id',
        'manager_id',
        'hr_manager_id',
        'supervisor_approved_at',
        'manager_recommended_at',
        'hr_approved_at',
        'shortlisted_file_path',
        'signed_file_path',
    ];

    protected $casts = [
        'position_vacant_date' => 'date',
        'application_date' => 'date',
        'application_deadline' => 'date',
        'min_age' => 'integer',
        'salary_range_min' => 'decimal:2',
        'salary_range_max' => 'decimal:2',
        'supervisor_approved_at' => 'datetime',
        'manager_recommended_at' => 'datetime',
        'hr_approved_at' => 'datetime',
    ];

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function hrManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }

    /**
     * Filter by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('job_vacancies.client_id', $clientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', ['submitted', 'supervisor_approved', 'manager_recommended']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'hr_approved');
    }

    public function getSalaryRangeAttribute()
    {
        if ($this->salary_range_min && $this->salary_range_max) {
            return number_format($this->salary_range_min, 0) . ' - ' . number_format($this->salary_range_max, 0);
        }
        return 'Not specified';
    }

    public function getDaysUntilDeadlineAttribute()
    {
        if ($this->application_deadline) {
            return now()->diffInDays($this->application_deadline, false);
        }
        return null;
    }

    public function isExpired()
    {
        return $this->application_deadline && now()->isAfter($this->application_deadline);
    }

    public function canBeApprovedBy($user, $level)
    {
        switch ($level) {
            case 'supervisor':
                return $this->status === 'submitted' && $this->supervisor_id === $user->id;
            case 'manager':
                return $this->status === 'supervisor_approved' && $this->manager_id === $user->id;
            case 'hr_manager':
                return $this->status === 'manager_recommended' && $this->hr_manager_id === $user->id;
            default:
                return false;
        }
    }
}
