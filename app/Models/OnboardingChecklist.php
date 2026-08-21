<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class OnboardingChecklist extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'task_key',
        'task_name',
        'category',
        'order',
        'is_required',
        'is_completed',
        'completed_at',
        'completed_by',
        'notes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('task_name');
    }

    public static function getDefaultChecklist(): array
    {
        return [
            ['task_key' => 'welcome_kit', 'task_name' => 'Welcome kit and company materials', 'category' => 'orientation', 'order' => 1, 'is_required' => true],
            ['task_key' => 'it_setup', 'task_name' => 'IT setup and system access', 'category' => 'orientation', 'order' => 2, 'is_required' => true],
            ['task_key' => 'office_tour', 'task_name' => 'Office tour and introductions', 'category' => 'orientation', 'order' => 3, 'is_required' => true],
            ['task_key' => 'hr_policies', 'task_name' => 'HR policy review and acknowledgment', 'category' => 'compliance', 'order' => 4, 'is_required' => true],
            
            ['task_key' => 'dept_training', 'task_name' => 'Department-specific training', 'category' => 'training', 'order' => 5, 'is_required' => true],
            ['task_key' => 'team_intro', 'task_name' => 'Team meetings and introductions', 'category' => 'orientation', 'order' => 6, 'is_required' => true],
            ['task_key' => 'mentor_assigned', 'task_name' => 'Assign mentor/buddy', 'category' => 'training', 'order' => 7, 'is_required' => false],
            ['task_key' => 'first_project', 'task_name' => 'First project assignment', 'category' => 'training', 'order' => 8, 'is_required' => true],
            
            ['task_key' => 'goals_setting', 'task_name' => 'Performance goals setting', 'category' => 'documentation', 'order' => 9, 'is_required' => true],
            ['task_key' => 'review_30_day', 'task_name' => '30-day review meeting', 'category' => 'documentation', 'order' => 10, 'is_required' => true],
            ['task_key' => 'benefits_enrollment', 'task_name' => 'Benefits enrollment completion', 'category' => 'compliance', 'order' => 11, 'is_required' => true],
            ['task_key' => 'training_plan', 'task_name' => 'Training plan finalization', 'category' => 'training', 'order' => 12, 'is_required' => false],
            
            ['task_key' => 'nida_card', 'task_name' => 'NIDA/National ID submission', 'category' => 'compliance', 'order' => 13, 'is_required' => true],
            ['task_key' => 'tin_certificate', 'task_name' => 'TIN certificate submission', 'category' => 'compliance', 'order' => 14, 'is_required' => true],
            ['task_key' => 'nssf_card', 'task_name' => 'NSSF card submission', 'category' => 'compliance', 'order' => 15, 'is_required' => true],
            ['task_key' => 'nhif_card', 'task_name' => 'NHIF card submission', 'category' => 'compliance', 'order' => 16, 'is_required' => true],
            ['task_key' => 'employment_contract', 'task_name' => 'Employment contract signing', 'category' => 'documentation', 'order' => 17, 'is_required' => true],
            ['task_key' => 'bank_details', 'task_name' => 'Bank account details submission', 'category' => 'documentation', 'order' => 18, 'is_required' => true],
        ];
    }
}