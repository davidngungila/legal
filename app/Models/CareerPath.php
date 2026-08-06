<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class CareerPath extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'name',
        'department',
        'description',
        'status',
        'created_by',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(CareerPathLevel::class)->orderBy('level_order');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CareerPathMember::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
