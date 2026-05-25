<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCurrentClient;

class InductionTraining extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'employee_registration_id',
        'training_date',
        'training_type',
        'training_title',
        'training_description',
        'trainer_name',
        'training_duration_hours',
        'training_materials_path',
        'completion_certificate_path',
        'assessment_score',
        'assessment_passed',
        'feedback_comments',
        'next_training_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'training_date' => 'date',
        'next_training_date' => 'date',
        'assessment_passed' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function registration()
    {
        return $this->belongsTo(EmployeeRegistration::class, 'employee_registration_id');
    }
}
