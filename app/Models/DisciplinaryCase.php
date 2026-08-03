<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DisciplinaryCase extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'case_number',
        'case_type',
        'incident_date',
        'incident_description',
        'reported_by',
        'status',
        'investigator',
        'investigation_started_at',
        'investigation_findings',
        'recommendation',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'investigation_started_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(DisciplinaryWarning::class, 'case_id');
    }

    public function showCauseNotice(): HasOne
    {
        return $this->hasOne(ShowCauseNotice::class, 'case_id');
    }

    public function hearing(): HasOne
    {
        return $this->hasOne(DisciplinaryHearing::class, 'case_id');
    }

    public function outcome(): HasOne
    {
        return $this->hasOne(DisciplinaryOutcome::class, 'case_id');
    }

    public function appeal(): HasOne
    {
        return $this->hasOne(DisciplinaryAppeal::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DisciplinaryDocument::class, 'case_id');
    }
}
