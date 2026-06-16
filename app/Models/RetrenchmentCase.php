<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RetrenchmentCase extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'business_justification',
        'consultation_notice_date',
        'selection_criteria',
        'status',
        'initiated_by',
    ];

    protected $casts = [
        'consultation_notice_date' => 'date',
    ];

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(RetrenchmentEmployee::class);
    }
}
