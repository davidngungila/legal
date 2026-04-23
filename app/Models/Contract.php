<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'employee_id',
        'contract_type',
        'contract_number',
        'start_date',
        'end_date',
        'probation_end_date',
        'salary',
        'currency',
        'payment_frequency',
        'work_schedule',
        'work_location',
        'job_description',
        'responsibilities',
        'benefits',
        'terms_and_conditions',
        'status',
        'created_by',
        'updated_by',
        'signed_at',
        'terminated_at',
        'termination_reason',
        'renewal_count',
        'last_renewal_date',
        'auto_renewal',
        'documents',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'probation_end_date' => 'date',
        'signed_at' => 'datetime',
        'terminated_at' => 'datetime',
        'last_renewal_date' => 'date',
        'benefits' => 'array',
        'responsibilities' => 'array',
        'terms_and_conditions' => 'array',
        'documents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Contract types.
     */
    const CONTRACT_TYPES = [
        'permanent' => 'Permanent Employment',
        'fixed_term' => 'Fixed-Term Contract',
        'probation' => 'Probation Contract',
        'internship' => 'Internship Agreement',
        'consultant' => 'Consultant Agreement',
        'part_time' => 'Part-Time Contract',
        'temporary' => 'Temporary Contract',
    ];

    /**
     * Contract statuses.
     */
    const STATUSES = [
        'draft' => 'Draft',
        'pending_signature' => 'Pending Signature',
        'active' => 'Active',
        'probation' => 'On Probation',
        'suspended' => 'Suspended',
        'terminated' => 'Terminated',
        'expired' => 'Expired',
        'renewed' => 'Renewed',
    ];

    /**
     * Get the client that owns the contract.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee for the contract.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who created the contract.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the contract.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the documents for the contract.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ContractDocument::class);
    }

    /**
     * Get the amendments for the contract.
     */
    public function amendments(): HasMany
    {
        return $this->hasMany(ContractAmendment::class);
    }

    /**
     * Get the formatted contract number.
     */
    public function getFormattedContractNumberAttribute(): string
    {
        return $this->contract_number ? "CTR-{$this->contract_number}" : 'N/A';
    }

    /**
     * Get the contract duration in days.
     */
    public function getDurationInDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get the remaining days until contract expiry.
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->end_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->end_date));
    }

    /**
     * Get the formatted salary.
     */
    public function getFormattedSalaryAttribute(): string
    {
        $currency = $this->currency ?? 'TZS';
        $symbol = match($currency) {
            'TZS' => 'TSh',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency
        };

        return "{$symbol}" . number_format($this->salary, 2);
    }

    /**
     * Check if contract is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if contract is expiring soon.
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date && $this->days_until_expiry <= $days;
    }

    /**
     * Check if contract is on probation.
     */
    public function isOnProbation(): bool
    {
        return $this->probation_end_date && $this->probation_end_date->isFuture();
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'probation' => 'yellow',
            'suspended' => 'red',
            'terminated' => 'gray',
            'expired' => 'red',
            'pending_signature' => 'blue',
            'draft' => 'gray',
            'renewed' => 'green',
            default => 'gray'
        };
    }

    /**
     * Scope a query to only include contracts for the current client.
     */
    public function scopeForCurrentClient($query)
    {
        $clientId = session('current_client_id');
        
        if ($clientId) {
            return $query->where('client_id', $clientId);
        }
        
        return $query;
    }

    /**
     * Scope a query to only include active contracts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include contracts expiring soon.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                   ->where('status', 'active');
    }

    /**
     * Generate contract document content.
     */
    public function generateContractContent(): string
    {
        $template = $this->getContractTemplate();
        
        $replacements = [
            '{{contract_number}}' => $this->formatted_contract_number,
            '{{employee_name}}' => $this->employee->full_name,
            '{{employee_id}}' => $this->employee->employee_id,
            '{{position}}' => $this->employee->position,
            '{{department}}' => $this->employee->department,
            '{{start_date}}' => $this->start_date->format('F j, Y'),
            '{{end_date}}' => $this->end_date ? $this->end_date->format('F j, Y') : 'Open-ended',
            '{{salary}}' => $this->formatted_salary,
            '{{payment_frequency}}' => $this->payment_frequency,
            '{{work_schedule}}' => $this->work_schedule,
            '{{work_location}}' => $this->work_location,
            '{{job_description}}' => $this->job_description,
            '{{client_name}}' => $this->client->name,
            '{{current_date}}' => now()->format('F j, Y'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Get contract template based on contract type.
     */
    public function getContractTemplate(): string
    {
        return match($this->contract_type) {
            'permanent' => $this->getPermanentContractTemplate(),
            'fixed_term' => $this->getFixedTermContractTemplate(),
            'probation' => $this->getProbationContractTemplate(),
            'internship' => $this->getInternshipContractTemplate(),
            'consultant' => $this->getConsultantContractTemplate(),
            'part_time' => $this->getPartTimeContractTemplate(),
            'temporary' => $this->getTemporaryContractTemplate(),
            default => $this->getStandardContractTemplate(),
        };
    }

    /**
     * Get permanent contract template.
     */
    private function getPermanentContractTemplate(): string
    {
        return '
EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Start Date: {{start_date}}
End Date: {{end_date}}
Salary: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This is a permanent employment contract between {{client_name}} (hereinafter referred to as "Employer") and {{employee_name}} (hereinafter referred to as "Employee").

1. EMPLOYMENT: Employee agrees to work as {{position}} in the {{department}} department, reporting to the designated manager, and performing such duties as may be assigned by the Employer.

2. COMPENSATION: Employee shall receive a salary of {{salary}} paid {{payment_frequency}}, subject to applicable deductions and withholdings as required by law.

3. WORK HOURS: Employee shall work {{work_schedule}} hours as required by the Employer, with reasonable overtime compensation as per Tanzanian labor laws.

4. BENEFITS: Employee shall be entitled to all benefits as outlined in the company handbook and as required by Tanzanian employment law.

5. TERMINATION: Either party may terminate this employment with written notice as specified in the employee handbook or as required by law.

This contract is governed by the laws of Tanzania and any disputes shall be resolved according to Tanzanian labor regulations.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get fixed-term contract template.
     */
    private function getFixedTermContractTemplate(): string
    {
        return '
FIXED-TERM EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Contract Period: {{start_date}} to {{end_date}}
Salary: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This is a fixed-term employment contract for a period from {{start_date}} to {{end_date}} between {{client_name}} (hereinafter referred to as "Employer") and {{employee_name}} (hereinafter referred to as "Employee").

1. EMPLOYMENT: Employee agrees to work as {{position}} in the {{department}} department for the specified contract period.

2. COMPENSATION: Employee shall receive a salary of {{salary}} paid {{payment_frequency}}, subject to applicable deductions and withholdings.

3. CONTRACT TERM: This contract shall automatically expire on {{end_date}} unless renewed in writing by both parties.

4. TERMINATION: Either party may terminate this contract with written notice as specified in Tanzanian labor laws.

This contract is governed by the laws of Tanzania.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get probation contract template.
     */
    private function getProbationContractTemplate(): string
    {
        return '
PROBATION EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Probation Period: {{start_date}} to {{probation_end_date}}
Salary: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This is a probationary employment contract for a period from {{start_date}} to {{probation_end_date}} between {{client_name}} (hereinafter referred to as "Employer") and {{employee_name}} (hereinafter referred to as "Employee").

1. PROBATIONARY PERIOD: Employee shall be on probation for the specified period. During this time, performance will be regularly assessed.

2. COMPENSATION: Employee shall receive a salary of {{salary}} paid {{payment_frequency}}.

3. ASSESSMENT: Employee\'s performance will be assessed at the end of the probationary period to determine suitability for permanent employment.

4. TERMINATION: This contract may be terminated by either party with written notice during the probationary period.

This contract is governed by the laws of Tanzania.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get internship contract template.
     */
    private function getInternshipContractTemplate(): string
    {
        return '
INTERNSHIP AGREEMENT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. COMPANY: {{client_name}}
2. INTERN: {{employee_name}} (ID: {{employee_id}})

INTERNSHIP DETAILS

Position: {{position}}
Department: {{department}}
Internship Period: {{start_date}} to {{end_date}}
Stipend: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This internship agreement is for the period specified above between {{client_name}} and {{employee_name}}.

1. PURPOSE: To provide practical work experience to the intern in the field of {{position}}.

2. RESPONSIBILITIES: Intern will perform duties as assigned by the supervisor in the {{department}} department.

3. STIPEND: Intern will receive a stipend of {{salary}} paid {{payment_frequency}}.

4. SUPERVISION: Intern will be supervised by designated company personnel and will receive regular feedback.

5. COMPLETION: Upon successful completion, company may provide certificate of internship.

This agreement is governed by the laws of Tanzania.

SIGNATURES

Company Representative: _______________________ Date: _________

Intern: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get consultant contract template.
     */
    private function getConsultantContractTemplate(): string
    {
        return '
CONSULTANT AGREEMENT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. CLIENT: {{client_name}}
2. CONSULTANT: {{employee_name}} (ID: {{employee_id}})

SERVICES

Position: {{position}}
Department: {{department}}
Engagement Period: {{start_date}} to {{end_date}}
Consulting Fee: {{salary}} ({{payment_frequency}})
Work Location: {{work_location}}

TERMS AND CONDITIONS

This consultant agreement is for professional services between {{client_name}} and {{employee_name}}.

1. SERVICES: Consultant shall provide {{position}} services to the client as specified in the Statement of Work attached hereto.

2. COMPENSATION: Client shall pay consultant {{salary}} for services rendered, payable {{payment_frequency}}.

3. DELIVERABLES: Consultant shall deliver professional services as agreed upon by both parties.

4. CONFIDENTIALITY: Consultant shall maintain confidentiality of all client information.

5. TERMINATION: Either party may terminate this agreement with written notice as specified.

This agreement is governed by the laws of Tanzania.

SIGNATURES

Client: _______________________ Date: _________

Consultant: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get part-time contract template.
     */
    private function getPartTimeContractTemplate(): string
    {
        return '
PART-TIME EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Contract Period: {{start_date}} to {{end_date}}
Hours: {{work_schedule}}
Hourly Rate: {{salary}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This is a part-time employment contract between {{client_name}} and {{employee_name}}.

1. EMPLOYMENT: Employee agrees to work part-time as {{position}} for {{work_schedule}}.

2. COMPENSATION: Employee shall be paid {{salary}} per hour for hours worked.

3. BENEFITS: Part-time employees may be eligible for certain benefits as per company policy and Tanzanian law.

4. TERMINATION: Either party may terminate this employment with written notice.

This contract is governed by the laws of Tanzania.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get temporary contract template.
     */
    private function getTemporaryContractTemplate(): string
    {
        return '
TEMPORARY EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Contract Period: {{start_date}} to {{end_date}}
Salary: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

This is a temporary employment contract for the period specified above between {{client_name}} and {{employee_name}}.

1. EMPLOYMENT: Employee agrees to work as {{position}} for the specified temporary period.

2. COMPENSATION: Employee shall receive {{salary}} paid {{payment_frequency}}.

3. TEMPORARY NATURE: This employment is temporary and will terminate automatically on {{end_date}}.

4. BENEFITS: Temporary employees may be eligible for certain benefits as per company policy.

This contract is governed by the laws of Tanzania.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Get standard contract template.
     */
    private function getStandardContractTemplate(): string
    {
        return '
EMPLOYMENT CONTRACT

Contract Number: {{contract_number}}
Date: {{current_date}}

PARTIES

1. EMPLOYER: {{client_name}}
2. EMPLOYEE: {{employee_name}} (ID: {{employee_id}})

This employment contract is between {{client_name}} and {{employee_name}}.

EMPLOYMENT DETAILS

Position: {{position}}
Department: {{department}}
Start Date: {{start_date}}
End Date: {{end_date}}
Salary: {{salary}} ({{payment_frequency}})
Work Schedule: {{work_schedule}}
Work Location: {{work_location}}

TERMS AND CONDITIONS

1. EMPLOYMENT: Employee agrees to work as {{position}} and perform such duties as may be assigned.

2. COMPENSATION: Employee shall receive {{salary}} paid {{payment_frequency}}.

3. TERMS: This contract is subject to Tanzanian employment laws and company policies.

SIGNATURES

Employer: _______________________ Date: _________

Employee: {{employee_name}} _____________________ Date: _________
        ';
    }

    /**
     * Create a new contract.
     */
    public static function createContract(array $data): self
    {
        $contract = new self();
        $contract->fill($data);
        $contract->contract_number = self::generateContractNumber();
        $contract->status = 'draft';
        $contract->created_by = auth()->id();
        $contract->save();

        return $contract;
    }

    /**
     * Generate unique contract number.
     */
    private static function generateContractNumber(): string
    {
        $prefix = 'CTR';
        $year = date('Y');
        $sequence = self::whereYear('created_at', $year)->count() + 1;
        
        return "{$prefix}-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get contracts expiring soon for current client.
     */
    public static function getExpiringSoonContracts(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return collect();
        }

        return self::where('client_id', $clientId)
                   ->where('end_date', '<=', now()->addDays($days))
                   ->where('status', 'active')
                   ->with('employee')
                   ->orderBy('end_date', 'asc')
                   ->get();
    }

    /**
     * Get contract statistics for current client.
     */
    public static function getContractStats()
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return [
                'total' => 0,
                'active' => 0,
                'expiring_soon' => 0,
                'by_type' => [],
                'renewals_due' => 0,
            ];
        }

        $contracts = self::where('client_id', $clientId)->get();

        return [
            'total' => $contracts->count(),
            'active' => $contracts->where('status', 'active')->count(),
            'expiring_soon' => $contracts->where('end_date', '<=', now()->addDays(30))->where('status', 'active')->count(),
            'by_type' => $contracts->groupBy('contract_type')->map->count(),
            'renewals_due' => $contracts->where('end_date', '<=', now()->addDays(60))->where('status', 'active')->count(),
        ];
    }
}
