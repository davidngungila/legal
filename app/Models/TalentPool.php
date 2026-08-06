<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class TalentPool extends Model
{
    use BelongsToCurrentClient;

    const TYPES = [
        'high_potential' => 'High Potentials',
        'future_leader' => 'Future Leaders',
        'leadership' => 'Leadership',
        'key_role' => 'Key Roles',
        'technical' => 'Technical Specialists',
        'emerging' => 'Emerging Leaders',
        'custom' => 'Custom Pool',
    ];

    protected $fillable = [
        'client_id',
        'name',
        'type',
        'description',
        'status',
        'created_by',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(TalentPoolMember::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
