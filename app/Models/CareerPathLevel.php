<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class CareerPathLevel extends Model
{
    use BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'career_path_id',
        'level_order',
        'title',
        'typical_time',
        'competencies',
        'responsibilities',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(CareerPath::class, 'career_path_id');
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
