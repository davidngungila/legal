<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitChecklist extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'exit_case_id',
        'item_name',
        'category',
        'completed',
        'completed_by',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function exitCase(): BelongsTo
    {
        return $this->belongsTo(ExitCase::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Checklist items inherit their client from the parent exit case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('exitCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
