<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryDocument extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'case_id',
        'doc_type',
        'generated_at',
        'file_path',
        'served_at',
        'served_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    public function disciplinaryCase(): BelongsTo
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    /**
     * Documents inherit their client from the parent disciplinary case.
     */
    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->whereHas('disciplinaryCase', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        });
    }
}
