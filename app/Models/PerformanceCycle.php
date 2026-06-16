<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class PerformanceCycle extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'cycle_type',
        'cycle_name',
        'period_start',
        'period_end',
        'employee_category',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function employeeGoals(): HasMany
    {
        return $this->hasMany(EmployeeGoal::class, 'cycle_id');
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'cycle_id');
    }
}
