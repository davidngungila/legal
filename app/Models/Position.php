<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class Position extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'department_id',
        'title',
        'job_code',
        'description',
        'requirements',
        'grade_level',
        'min_salary',
        'max_salary',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public static function forCurrentClient()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return static::where('client_id', 0); // Return empty query when no client is set
        }

        return static::where('client_id', $clientId);
    }
}
