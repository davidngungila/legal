<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;

class TrainingSession extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'program_id',
        'plan_id',
        'title',
        'instructor',
        'venue',
        'start_at',
        'end_at',
        'capacity',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'program_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class, 'plan_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(TrainingEnrollment::class, 'session_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where((new static)->getTable() . '.client_id', $clientId);
    }
}
