<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToCurrentClient;

class EmployeeRegistration extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $table = 'employee_registrations';

    protected $fillable = [
        'client_id',
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
        'employee_signature_path',
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
        'age' => 'integer',
        'job_descriptions' => 'array',
        'terms_conditions' => 'array',
        'ranking_details' => 'array',
        'employment_history' => 'array',
    ];

    /**
     * Get the HR interview associated with the registration.
     */
    public function hrInterview(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HrCompetencyInterview::class, 'hr_interview_id');
    }

    /**
     * Get the technical interview associated with the registration.
     */
    public function technicalInterview(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TechnicalInterview::class, 'technical_interview_id');
    }

    /**
     * Get the user who created the registration.
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the registration.
     */
    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_registration_id');
    }

    public function socialRecord(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SocialRecord::class, 'employee_registration_id');
    }

    public function inductionTrainings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InductionTraining::class, 'employee_registration_id');
    }

    public function personnelIdApplications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PersonnelIdApplication::class, 'employee_registration_id');
    }

    /**
     * Filter by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('employee_registrations.client_id', $clientId);
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->middle_name} {$this->surname}";
    }
}

