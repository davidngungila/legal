<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class SuccessionReadiness extends Model
{
    use BelongsToCurrentClient;

    const READINESS = [
        'ready_now' => 'Ready Now',
        'ready_1_2' => '1-2 Years',
        'ready_2_3' => '2-3 Years',
        'development' => 'Development Needed',
    ];

    protected $table = 'succession_readiness';

    protected $fillable = [
        'client_id',
        'employee_id',
        'pool_id',
        'current_role',
        'target_role',
        'readiness',
        'development_needs',
        'assessment_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(TalentPool::class, 'pool_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
