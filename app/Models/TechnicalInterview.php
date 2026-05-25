<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCurrentClient;

class TechnicalInterview extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $table = 'technical_interviews';

    protected $fillable = [
        'client_id',
        'interview_number',
        'hr_interview_id',
        'candidate_name',
        'job_title',
        'interview_date',
        'interviewer_name',
        'interviewer_id',
        'department_manager_id',
        'business_process_knowledge',
        'technical_skills_assessment',
        'physical_capabilities',
        'practical_test_results',
        'other_technical_areas',
        'technical_result',
        'technical_comments',
        'manager_approval',
        'manager_comments',
        'assessment_report_path',
        'signed_file_path',
        'status',
        'interviewer_completed_at',
        'manager_approved_at',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'interviewer_completed_at' => 'datetime',
        'manager_approved_at' => 'datetime',
        'status' => 'string',
    ];

    public function hrInterview(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HrCompetencyInterview::class, 'hr_interview_id');
    }

    public function interviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function departmentManager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'department_manager_id');
    }

    /**
     * Filter by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('technical_interviews.client_id', $clientId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', ['submitted', 'interviewer_completed']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'manager_approved');
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('technical_result', $result);
    }

    public static function generateInterviewNumber()
    {
        $prefix = 'TECHINT';
        $year = date('Y');
        $sequence = str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$sequence}";
    }

    public function getResultLabel()
    {
        return match($this->technical_result) {
            'pass' => 'Pass',
            'fail' => 'Fail',
            'na' => 'N/A',
            default => 'Pending'
        };
    }

    public function getResultColor()
    {
        return match($this->technical_result) {
            'pass' => 'green',
            'fail' => 'red',
            'na' => 'gray',
            default => 'yellow'
        };
    }

    public function getApprovalLabel()
    {
        return match($this->manager_approval) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'pending' => 'Pending',
            default => 'Not Required'
        };
    }

    public function getApprovalColor()
    {
        return match($this->manager_approval) {
            'approved' => 'green',
            'rejected' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }

    public function canBeEdited()
    {
        return $this->status === 'draft' || 
               ($this->status === 'submitted' && now()->diffInDays($this->created_at) <= 7);
    }

    public function getOverallAssessment()
    {
        if ($this->technical_result === 'pass') {
            return 'Technically Competent';
        } elseif ($this->technical_result === 'fail') {
            return 'Not Suitable';
        } else {
            return 'Assessment Pending';
        }
    }

    public function isComplete()
    {
        return $this->business_process_knowledge && 
               $this->technical_skills_assessment && 
               $this->technical_result;
    }
}
