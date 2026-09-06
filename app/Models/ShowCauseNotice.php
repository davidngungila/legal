<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowCauseNotice extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'case_id',
        'sent_date',
        'response_deadline',
        'response_received_at',
        'response_text',
        'status',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'response_deadline' => 'date',
        'response_received_at' => 'datetime',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    /**
     * Notices inherit their client from the parent disciplinary case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('disciplinaryCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
