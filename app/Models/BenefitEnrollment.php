<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToCurrentClient;

class BenefitEnrollment extends Model
{
    use BelongsToCurrentClient;

    const STATUSES = [
        'enrolled' => 'Enrolled',
        'pending' => 'Pending',
        'waived' => 'Waived',
        'terminated' => 'Terminated',
    ];

    protected $fillable = [
        'client_id',
        'employee_id',
        'plan_id',
        'effective_date',
        'employee_cost',
        'employer_cost',
        'status',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'employee_cost' => 'decimal:2',
        'employer_cost' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BenefitPlan::class);
    }

    protected static function filterByClient(Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }
}
