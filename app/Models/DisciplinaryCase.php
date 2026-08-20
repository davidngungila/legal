<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DisciplinaryCase extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'case_number',
        'case_type',
        'incident_date',
        'incident_description',
        'reported_by',
        'status',
        'investigator',
        'investigation_started_at',
        'investigation_findings',
        'recommendation',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'investigation_started_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(DisciplinaryWarning::class, 'case_id');
    }

    public function showCauseNotice(): HasOne
    {
        return $this->hasOne(ShowCauseNotice::class, 'case_id');
    }

    public function hearing(): HasOne
    {
        return $this->hasOne(DisciplinaryHearing::class, 'case_id');
    }

    public function outcome(): HasOne
    {
        return $this->hasOne(DisciplinaryOutcome::class, 'case_id');
    }

    public function appeal(): HasOne
    {
        return $this->hasOne(DisciplinaryAppeal::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DisciplinaryDocument::class, 'case_id');
    }

    /**
     * Get active warnings for this employee
     * BR-DISC-001 to BR-DISC-005
     */
    public function getActiveWarnings()
    {
        return DisciplinaryWarning::where('employee_id', $this->employee_id)
            ->where('is_active', true)
            ->where('expiry_date', '>', now())
            ->orderBy('issued_date', 'desc')
            ->get();
    }

    /**
     * Determine recommended warning level based on active warnings
     * BR-DISC-002, BR-DISC-003, BR-DISC-004
     */
    public function getRecommendedWarningLevel()
    {
        $activeWarnings = $this->getActiveWarnings();
        
        if ($activeWarnings->isEmpty()) {
            return 'first'; // BR-DISC-001
        }

        $hasFirstWarning = $activeWarnings->contains('warning_type', 'first');
        $hasSecondWarning = $activeWarnings->contains('warning_type', 'second');
        $hasFinalWarning = $activeWarnings->contains('warning_type', 'final');

        if ($hasFinalWarning) {
            return 'escalate_to_major'; // BR-DISC-004
        } elseif ($hasSecondWarning) {
            return 'final'; // BR-DISC-003
        } elseif ($hasFirstWarning) {
            return 'second'; // BR-DISC-002
        }

        return 'first';
    }

    /**
     * Check if this case should escalate to major misconduct
     * BR-DISC-004
     */
    public function shouldEscalateToMajor()
    {
        return $this->getRecommendedWarningLevel() === 'escalate_to_major';
    }

    /**
     * Create show cause notice with 48-hour minimum deadline
     * BR-DISC-007
     */
    public function createShowCauseNotice()
    {
        $responseDeadline = now()->addHours(48);

        return ShowCauseNotice::create([
            'case_id' => $this->id,
            'sent_date' => now(),
            'response_deadline' => $responseDeadline,
            'response_received_at' => null,
            'response_text' => null,
            'status' => 'pending',
        ]);
    }

    /**
     * Schedule disciplinary hearing with 48-hour minimum notice
     * BR-DISC-008
     */
    public function scheduleHearing($hearingDate, $hearingTime, $venue, $committeeMembers = null)
    {
        $proposedDateTime = \Carbon\Carbon::parse($hearingDate . ' ' . $hearingTime);
        $minimumNotice = now()->addHours(48);

        if ($proposedDateTime->lt($minimumNotice)) {
            throw new \Exception('Hearing must be scheduled at least 48 hours from now (BR-DISC-008)');
        }

        $noticeSentAt = now();

        return DisciplinaryHearing::create([
            'case_id' => $this->id,
            'hearing_date' => $hearingDate,
            'hearing_time' => $hearingTime,
            'venue' => $venue,
            'notice_sent_at' => $noticeSentAt,
            'committee_members' => $committeeMembers,
            'employee_representative' => null,
            'proceedings_notes' => null,
        ]);
    }

    /**
     * Record disciplinary outcome with 5-day communication deadline
     * BR-DISC-009
     */
    public function recordOutcome($outcomeType, $rationale, $issuedBy)
    {
        $outcomeDate = now();
        $appealDeadline = now()->addDays(5); // BR-DISC-010

        return DisciplinaryOutcome::create([
            'case_id' => $this->id,
            'outcome_type' => $outcomeType,
            'outcome_date' => $outcomeDate,
            'issued_by' => $issuedBy,
            'rationale' => $rationale,
            'appeal_deadline' => $appealDeadline,
        ]);
    }

    /**
     * File appeal within 5-day window
     * BR-DISC-010
     */
    public function fileAppeal($appealBy, $appealGrounds)
    {
        $outcome = $this->outcome;
        
        if (!$outcome) {
            throw new \Exception('Cannot file appeal: No outcome recorded for this case');
        }

        if (now()->gt($outcome->appeal_deadline)) {
            throw new \Exception('Appeal deadline has passed (BR-DISC-010)');
        }

        return DisciplinaryAppeal::create([
            'case_id' => $this->id,
            'appeal_filed_at' => now(),
            'appeal_by' => $appealBy,
            'appeal_grounds' => $appealGrounds,
            'appeal_decision' => null,
            'decision_date' => null,
            'appeal_authority_id' => null,
        ]);
    }

    /**
     * Record appeal decision within 15 working days
     * BR-DISC-011
     */
    public function recordAppealDecision($decision, $appealAuthorityId)
    {
        $appeal = $this->appeal;
        
        if (!$appeal) {
            throw new \Exception('No appeal found for this case');
        }

        $decisionDate = now();
        $appealFiledDate = \Carbon\Carbon::parse($appeal->appeal_filed_at);
        $workingDays = $this->calculateWorkingDays($appealFiledDate, $decisionDate);

        if ($workingDays > 15) {
            // Log warning but still allow decision
            \Log::warning("Appeal decision exceeds 15 working days (BR-DISC-011): {$workingDays} days");
        }

        $appeal->update([
            'appeal_decision' => $decision,
            'decision_date' => $decisionDate,
            'appeal_authority_id' => $appealAuthorityId,
        ]);

        return $appeal;
    }

    /**
     * Calculate working days between two dates (excluding weekends)
     */
    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $workingDays = 0;

        while ($start->lte($end)) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    /**
     * Archive expired warnings
     * BR-DISC-005
     */
    public function archiveExpiredWarnings()
    {
        DisciplinaryWarning::where('employee_id', $this->employee_id)
            ->where('is_active', true)
            ->where('expiry_date', '<', now())
            ->update(['is_active' => false]);
    }

    /**
     * Check if investigation is overdue (5 working days minimum, 48 hours minimum)
     * BR-DISC-006
     */
    public function isInvestigationOverdue()
    {
        if (!$this->investigation_started_at) {
            return false;
        }

        $startedAt = \Carbon\Carbon::parse($this->investigation_started_at);
        $minimumTime = now()->subHours(48);
        $workingDaysDeadline = now()->subWeekdays(5);

        return now()->gt($minimumTime) && now()->gt($workingDaysDeadline);
    }

    /**
     * Scope to only include minor misconduct cases
     */
    public function scopeMinor($query)
    {
        return $query->where('case_type', 'minor');
    }

    /**
     * Scope to only include major misconduct cases
     */
    public function scopeMajor($query)
    {
        return $query->where('case_type', 'major');
    }

    /**
     * Scope to only include open cases
     */
    public function scopeOpen($query)
    {
        return $query->where('status', '!=', 'resolved');
    }

    /**
     * Scope to only include resolved cases
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
