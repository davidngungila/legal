<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRegistration extends Model
{
    use HasFactory;

    protected $table = 'employee_registrations';

    protected $fillable = [
        'employee_number',
        'hr_interview_id',
        'technical_interview_id',
        'surname',
        'first_name',
        'middle_name',
        'birthplace',
        'date_of_birth',
        'age',
        'gender',
        'residence_area',
        'permanent_residence',
        'postal_address',
        'email_address',
        'phone_number',
        'place_of_recruitment',
        'work_station',
        'type_of_contract',
        'job_descriptions',
        'date_employed',
        'terms_conditions',
        'information_consent',
        'employee_signature',
        'signature_date',
        'signed_document_path',
        'ranking_details',
        'employment_history',
        'created_by',
        'approved_by',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_employed' => 'date',
        'signature_date' => 'date',
        'information_consent' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function hrInterview(): BelongsTo
    {
        return $this->belongsTo(HrCompetencyInterview::class, 'hr_interview_id');
    }

    public function technicalInterview(): BelongsTo
    {
        return $this->belongsTo(TechnicalInterview::class, 'technical_interview_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_registration_id');
    }
}

