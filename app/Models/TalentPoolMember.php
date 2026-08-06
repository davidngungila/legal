<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class TalentPoolMember extends Model
{
    use BelongsToCurrentClient;

    const READINESS = [
        'ready_now' => 'Ready Now',
        'ready_1_2' => 'Ready 1-2 Years',
        'developing' => 'Developing',
        'not_ready' => 'Not Ready',
    ];

    protected $fillable = [
        'client_id',
        'talent_pool_id',
        'employee_id',
        'readiness',
        'notes',
        'added_by',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(TalentPool::class, 'talent_pool_id');
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
