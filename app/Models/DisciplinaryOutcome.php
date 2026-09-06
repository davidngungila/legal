<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryOutcome extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'case_id',
        'outcome_type',
        'outcome_date',
        'issued_by',
        'rationale',
        'appeal_deadline',
    ];

    protected $casts = [
        'outcome_date' => 'date',
        'appeal_deadline' => 'date',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Outcomes inherit their client from the parent disciplinary case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('disciplinaryCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
