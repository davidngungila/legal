<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToCurrentClient;

class MeritReview extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'review_period',
        'rating',
        'old_salary',
        'new_salary',
        'increment_amount',
        'increment_percent',
        'reviewer_notes',
        'status',
        'reviewed_by',
        'review_date',
    ];

    protected $casts = [
        'rating' => 'integer',
        'old_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'increment_percent' => 'decimal:2',
        'review_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
