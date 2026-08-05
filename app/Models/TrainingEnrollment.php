<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;

class TrainingEnrollment extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'session_id',
        'employee_id',
        'enrollment_date',
        'status',
        'attendance_status',
        'assessment_score',
        'passed',
        'completion_certificate_path',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'assessment_score' => 'decimal:2',
        'passed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function program()
    {
        return $this->hasOneThrough(TrainingProgram::class, TrainingSession::class, 'id', 'id', 'session_id', 'program_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where((new static)->getTable() . '.client_id', $clientId);
    }
}
