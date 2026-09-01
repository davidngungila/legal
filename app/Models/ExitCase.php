<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExitCase extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'disciplinary_case_id',
        'employment_contract_id',
        'exit_number',
        'exit_type',
        'exit_date',
        'notice_date',
        'reason',
        'status',
        'initiated_by',
    ];

    protected $casts = [
        'exit_date' => 'date',
        'notice_date' => 'date',
    ];

    const EXIT_TYPES = [
        'resignation' => 'Resignation',
        'misconduct_termination' => 'Termination (Misconduct)',
        'retrenchment' => 'Retrenchment',
        'mutual_separation' => 'Mutual Separation',
        'retirement' => 'Retirement',
        'death_in_service' => 'Death in Service',
        'capacity' => 'Capacity',
    ];

    const STATUSES = [
        'initiated' => 'Initiated',
        'pending_clearance' => 'Pending Clearance',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ExitChecklist::class);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(ExitSettlement::class);
    }

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class);
    }

    public function employmentContract(): BelongsTo
    {
        return $this->belongsTo(EmploymentContract::class);
    }
}
