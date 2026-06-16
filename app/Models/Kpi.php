<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'kpi_description',
        'target',
        'weight',
        'measurement_unit',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'date',
        'weight' => 'decimal:2',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(EmployeeGoal::class);
    }
}
