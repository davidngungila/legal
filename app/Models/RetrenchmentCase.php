<?php

namespace App\Models;

use App\Models\Traits\BelongsToCurrentClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RetrenchmentCase extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'business_justification',
        'consultation_notice_date',
        'selection_criteria',
        'status',
        'initiated_by',
    ];

    protected $casts = [
        'consultation_notice_date' => 'date',
    ];

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(RetrenchmentEmployee::class);
    }

    /**
     * Calculate selection score for employee based on criteria
     * BR-EXIT-004
     */
    public function calculateSelectionScore($employee, $criteria)
    {
        $score = 0;

        switch ($criteria) {
            case 'lifo': // Last In First Out
                $yearsOfService = \Carbon\Carbon::parse($employee->hire_date)->diffInYears(now());
                $score = -$yearsOfService; // Lower score = more recent hire = higher priority
                break;

            case 'fifo': // First In First Out
                $yearsOfService = \Carbon\Carbon::parse($employee->hire_date)->diffInYears(now());
                $score = $yearsOfService; // Higher score = longer service = higher priority
                break;

            case 'attendance':
                // Calculate attendance percentage (inverse - lower attendance = higher score)
                $last6Months = now()->subMonths(6);
                $totalDays = \App\Models\Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', '>=', $last6Months)
                    ->count();
                
                $presentDays = \App\Models\Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', '>=', $last6Months)
                    ->where('status', 'present')
                    ->count();
                
                $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
                $score = 100 - $attendanceRate; // Lower attendance = higher score
                break;

            case 'performance':
                // Get average performance rating (inverse - lower rating = higher score)
                $reviews = \App\Models\PerformanceReview::where('employee_id', $employee->id)
                    ->where('status', 'completed')
                    ->whereNotNull('final_rating')
                    ->orderBy('review_date', 'desc')
                    ->take(4)
                    ->get();
                
                if ($reviews->isNotEmpty()) {
                    $avgRating = $reviews->avg('final_rating');
                    $score = 5 - $avgRating; // Lower rating = higher score (assuming 1-5 scale)
                } else {
                    $score = 2.5; // Default mid score
                }
                break;

            case 'disciplinary':
                // Count active disciplinary cases
                $activeCases = \App\Models\DisciplinaryCase::where('employee_id', $employee->id)
                    ->where('status', '!=', 'resolved')
                    ->count();
                
                $activeWarnings = \App\Models\DisciplinaryWarning::where('employee_id', $employee->id)
                    ->where('is_active', true)
                    ->count();
                
                $score = ($activeCases * 10) + ($activeWarnings * 5);
                break;

            case 'combined':
                // Combine all criteria with weights
                $lifoScore = $this->calculateSelectionScore($employee, 'lifo');
                $attendanceScore = $this->calculateSelectionScore($employee, 'attendance');
                $performanceScore = $this->calculateSelectionScore($employee, 'performance');
                $disciplinaryScore = $this->calculateSelectionScore($employee, 'disciplinary');
                
                // Weighted average
                $score = ($lifoScore * 0.3) + ($attendanceScore * 0.25) 
                       + ($performanceScore * 0.25) + ($disciplinaryScore * 0.2);
                break;

            default:
                $score = 0;
        }

        return $score;
    }

    /**
     * Select employees for retrenchment based on criteria
     * BR-EXIT-004
     */
    public function selectEmployees($targetCount, $criteria = 'combined')
    {
        $employees = Employee::where('client_id', $this->client_id)
            ->where('status', 'active')
            ->get();

        $scoredEmployees = [];

        foreach ($employees as $employee) {
            $score = $this->calculateSelectionScore($employee, $criteria);
            $scoredEmployees[] = [
                'employee' => $employee,
                'score' => $score,
            ];
        }

        // Sort by score (descending - higher score = more likely to be selected)
        usort($scoredEmployees, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Select top N employees
        $selected = array_slice($scoredEmployees, 0, $targetCount);

        // Create retrenchment employee records
        foreach ($selected as $item) {
            RetrenchmentEmployee::create([
                'retrenchment_case_id' => $this->id,
                'employee_id' => $item['employee']->id,
                'selection_score' => $item['score'],
                'selected' => true,
                'redundancy_pay' => null,
                'final_settlement' => null,
            ]);
        }

        return $selected;
    }

    /**
     * Calculate severance pay for employee
     * BR-EXIT-005: Minimum 7 days per completed year of service
     */
    public function calculateSeverancePay($employee)
    {
        $hireDate = \Carbon\Carbon::parse($employee->hire_date);
        $yearsOfService = $hireDate->diffInYears(now());

        // Minimum 7 days per completed year
        $minimumDays = $yearsOfService * 7;
        
        // Calculate daily rate from basic salary
        $dailyRate = $employee->basic_salary / (4.333 * 6); // Using same formula as payroll

        $severancePay = $minimumDays * $dailyRate;

        return [
            'years_of_service' => $yearsOfService,
            'minimum_days' => $minimumDays,
            'daily_rate' => $dailyRate,
            'severance_pay' => $severancePay,
        ];
    }

    /**
     * Calculate final settlement for employee
     * BR-EXIT-006
     */
    public function calculateFinalSettlement($employee, $exitDate)
    {
        $severance = $this->calculateSeverancePay($employee);
        
        // Calculate notice pay (if applicable)
        $noticePay = 0;
        $noticePeriod = $employee->notice_period ?? 30; // Default 30 days
        $noticeDays = \Carbon\Carbon::parse($employee->hire_date)->diffInDays($exitDate);
        
        if ($noticeDays < $noticePeriod) {
            $dailyRate = $severance['daily_rate'];
            $remainingNoticeDays = $noticePeriod - $noticeDays;
            $noticePay = $remainingNoticeDays * $dailyRate;
        }

        // Calculate leave pay
        $leaveBalance = $employee->leave_balance ?? 0;
        $dailyRate = $severance['daily_rate'];
        $leavePay = $leaveBalance * $dailyRate;

        // Calculate pro-rated bonus (if applicable)
        $bonusPay = 0;
        if ($employee->bonus_eligible) {
            $monthsWorked = \Carbon\Carbon::parse($employee->hire_date)->diffInMonths($exitDate);
            $annualBonus = $employee->annual_bonus ?? 0;
            $bonusPay = ($annualBonus / 12) * $monthsWorked;
        }

        // Calculate unpaid salary (if any)
        $unpaidSalary = 0;
        $lastPayDate = $employee->last_pay_date;
        if ($lastPayDate && \Carbon\Carbon::parse($lastPayDate)->lt($exitDate)) {
            $daysUnpaid = \Carbon\Carbon::parse($lastPayDate)->diffInDays($exitDate);
            $dailyRate = $severance['daily_rate'];
            $unpaidSalary = $daysUnpaid * $dailyRate;
        }

        $totalSettlement = $severance['severance_pay'] + $noticePay + $leavePay + $bonusPay + $unpaidSalary;

        return [
            'severance_pay' => $severance['severance_pay'],
            'notice_pay' => $noticePay,
            'leave_pay' => $leavePay,
            'bonus_pay' => $bonusPay,
            'unpaid_salary' => $unpaidSalary,
            'total_settlement' => $totalSettlement,
        ];
    }

    /**
     * Check if 28-day minimum consultation period has been met
     * BR-EXIT-003
     */
    public function isConsultationPeriodComplete()
    {
        if (!$this->consultation_notice_date) {
            return false;
        }

        $noticeDate = \Carbon\Carbon::parse($this->consultation_notice_date);
        $minimumPeriod = now()->subDays(28);

        return now()->gte($minimumPeriod);
    }

    /**
     * Scope to only include initiated cases
     */
    public function scopeInitiated($query)
    {
        return $query->where('status', 'initiated');
    }

    /**
     * Scope to only include completed cases
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to only include cases with complete consultation period
     */
    public function scopeConsultationComplete($query)
    {
        return $query->where('consultation_notice_date', '<=', now()->subDays(28));
    }
}
