<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrCompetencyInterview extends Model
{
    use HasFactory;

    protected $table = 'hr_competency_interviews';

    protected $fillable = [
        'interview_number',
        'job_title',
        'interview_date',
        'candidate_name',
        'interviewer_name',
        'interviewer_id',
        'military_service_status',
        'military_certificate_path',
        'place_of_recruitment',
        'total_years_experience',
        'education_job_knowledge',
        'education_job_knowledge_comment',
        'relevant_job_experience',
        'major_previous_achievement',
        'language_fluency',
        'language_fluency_comment',
        'interactive_communication',
        'interactive_communication_comment',
        'accountability',
        'accountability_comment',
        'work_excellence',
        'work_excellence_comment',
        'functional_competencies',
        'functional_competencies_comment',
        'planning_organizing',
        'planning_organizing_comment',
        'problem_solving',
        'problem_solving_comment',
        'attention_to_details',
        'attention_to_details_comment',
        'multitasking',
        'multitasking_comment',
        'continuous_improvement',
        'continuous_improvement_comment',
        'compliance',
        'compliance_comment',
        'creative_innovation',
        'creative_innovation_comment',
        'negotiation',
        'negotiation_comment',
        'teamwork',
        'teamwork_comment',
        'adaptability_flexibility',
        'adaptability_flexibility_comment',
        'leadership',
        'managing_developing_people',
        'managing_change',
        'making_decisions',
        'overall_rating',
        'main_strength',
        'main_weakness',
        'relative_inside_client',
        'relative_name',
        'birthplace',
        'residence',
        'employed_before',
        'reference_checking',
        'current_salary',
        'required_notice_days',
        'current_employer_entity',
        'recruiter_recommendation',
        'recommended_job_title',
        'interviewer_signature',
        'hr_manager_id',
        'hr_manager_approved_at',
        'signed_file_path',
        'status',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'total_years_experience' => 'integer',
        'current_salary' => 'decimal:2',
        'required_notice_days' => 'integer',
        'hr_manager_approved_at' => 'datetime',
        'status' => 'string',
    ];

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function hrManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_manager_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJobTitle($query, $jobTitle)
    {
        return $query->where('job_title', $jobTitle);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'hr_approved');
    }

    public function scopeByRecommendation($query, $recommendation)
    {
        return $query->where('recruiter_recommendation', $recommendation);
    }

    public static function generateInterviewNumber()
    {
        $prefix = 'HRINT';
        $year = date('Y');
        $sequence = str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$sequence}";
    }

    public function getCompetencyScore($competency)
    {
        return $this->{$competency} ?? 0;
    }

    public function getCompetencyAverage()
    {
        $competencies = [
            'education_job_knowledge', 'relevant_job_experience', 'major_previous_achievement',
            'language_fluency', 'interactive_communication', 'accountability', 'work_excellence',
            'functional_competencies', 'planning_organizing', 'problem_solving', 'attention_to_details',
            'multitasking', 'continuous_improvement', 'compliance', 'creative_innovation',
            'negotiation', 'teamwork', 'adaptability_flexibility', 'leadership',
            'managing_developing_people', 'managing_change', 'making_decisions'
        ];

        $total = 0;
        $count = 0;

        foreach ($competencies as $competency) {
            $score = $this->{$competency};
            if ($score > 0) {
                $total += $score;
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 1) : 0;
    }

    public function getRecommendationLabel()
    {
        return match($this->recruiter_recommendation) {
            'accepted' => 'Accepted',
            'not_accepted' => 'Not Accepted',
            'waiting_list' => 'Waiting List',
            default => 'Pending'
        };
    }

    public function getRecommendationColor()
    {
        return match($this->recruiter_recommendation) {
            'accepted' => 'green',
            'not_accepted' => 'red',
            'waiting_list' => 'yellow',
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
        $score = $this->overall_rating;
        
        if ($score >= 4.5) return 'Outstanding';
        if ($score >= 3.5) return 'Very Good';
        if ($score >= 2.5) return 'Good';
        if ($score >= 1.5) return 'Average';
        if ($score >= 0.5) return 'Below Average';
        return 'N/A';
    }
}
