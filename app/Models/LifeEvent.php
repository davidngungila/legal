<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\BelongsToCurrentClient;

class LifeEvent extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'event_type',
        'event_date',
        'description',
        'documents',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
