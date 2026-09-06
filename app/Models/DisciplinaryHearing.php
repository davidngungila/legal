<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryHearing extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'case_id',
        'hearing_date',
        'hearing_time',
        'venue',
        'notice_sent_at',
        'committee_members',
        'employee_representative',
        'proceedings_notes',
    ];

    protected $casts = [
        'hearing_date' => 'date',
        'hearing_time' => 'datetime:H:i',
        'notice_sent_at' => 'datetime',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    /**
     * Hearings inherit their client from the parent disciplinary case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('disciplinaryCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
