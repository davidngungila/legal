<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;

class TrainingProgram extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'category',
        'provider',
        'description',
        'cost',
        'currency',
        'duration_hours',
        'is_certification',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'duration_hours' => 'decimal:2',
        'is_certification' => 'boolean',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'program_id');
    }

    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(TrainingEnrollment::class, TrainingSession::class, 'program_id', 'session_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where((new static)->getTable() . '.client_id', $clientId);
    }
}
