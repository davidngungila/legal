<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToCurrentClient;

class Attendance extends Model
{
    use HasFactory, BelongsToCurrentClient;

    protected $fillable = [
        'client_id',
        'employee_id',
        'attendance_date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'overtime_hours',
        'status',
        'status_code',
        'ordinary_hours',
        'rest_day_hours',
        'ph_hours',
        'night_hours',
        'source',
        'manual_entry',
        'workflow_status',
        'approved_by',
        'approved_at',
        'late_minutes',
        'early_departure_minutes',
        'violation_flags',
        'shift_pattern_id',
        'notes',
        'location',
        'ip_address',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'ordinary_hours' => 'decimal:2',
        'rest_day_hours' => 'decimal:2',
        'ph_hours' => 'decimal:2',
        'night_hours' => 'decimal:2',
        'manual_entry' => 'boolean',
        'approved_at' => 'datetime',
        'violation_flags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the attendance.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that owns the attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shiftPattern(): BelongsTo
    {
        return $this->belongsTo(ShiftPattern::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(AttendanceViolation::class);
    }

    /**
     * Get the formatted status badge.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Present</span>',
            'absent' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Absent</span>',
            'late' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Late</span>',
            'half_day' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Half Day</span>',
            'on_leave' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">On Leave</span>',
            'holiday' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Holiday</span>',
        ];

        return $badges[$this->status] ?? $badges['present'];
    }

    public function getStatusCodeLabelAttribute(): string
    {
        return match ($this->status_code) {
            'A' => 'Absent',
            'AL' => 'Annual Leave',
            'SLF' => 'Sick Leave Full Pay',
            'SLH' => 'Sick Leave Half Pay',
            'UL' => 'Unpaid Leave',
            'M' => 'Official Mission',
            '9' => 'Ordinary Hours',
            '12' => '12-Hour Shift',
            default => strtoupper((string) ($this->status_code ?? $this->status ?? '')),
        };
    }

    /**
     * Filter attendances by current client.
     */
    protected static function filterByClient(\Illuminate\Database\Eloquent\Builder $builder, $clientId)
    {
        $builder->where('client_id', $clientId);
    }

    /**
     * Get attendance records for current client.
     */
    public static function forCurrentClient()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return static::where('client_id', 0); // Return empty query when no client is set
        }

        return static::where('client_id', $clientId);
    }

    /**
     * Scope to only include attendances for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    /**
     * Scope to only include attendances in a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Scope to only include attendances with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to only include present attendances.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to only include absent attendances.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Calculate total hours automatically.
     */
    public function calculateTotalHours()
    {
        if ($this->clock_in && $this->clock_out) {
            $clockIn = \Carbon\Carbon::parse($this->clock_in);
            $clockOut = \Carbon\Carbon::parse($this->clock_out);
            
            $totalMinutes = $clockOut->diffInMinutes($clockIn);
            
            // Subtract break time if both break start and end are set
            if ($this->break_start && $this->break_end) {
                $breakStart = \Carbon\Carbon::parse($this->break_start);
                $breakEnd = \Carbon\Carbon::parse($this->break_end);
                $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                $totalMinutes -= $breakMinutes;
            }
            
            $this->total_hours = $totalMinutes / 60;
            
            // Calculate overtime (anything over 8 hours)
            if ($this->total_hours > 8) {
                $this->overtime_hours = $this->total_hours - 8;
            } else {
                $this->overtime_hours = 0;
            }
        }
        
        return $this;
    }

    /**
     * Detect and record late arrival
     * BR-WT rules enforcement
     */
    public function detectLateArrival($shiftStartTime = '08:00')
    {
        if ($this->clock_in) {
            $clockIn = \Carbon\Carbon::parse($this->clock_in);
            $shiftStart = \Carbon\Carbon::parse($shiftStartTime);
            
            if ($clockIn->gt($shiftStart)) {
                $this->late_minutes = $clockIn->diffInMinutes($shiftStart);
                
                // Add to violation flags
                $flags = $this->violation_flags ?? [];
                $flags[] = 'late_arrival';
                $this->violation_flags = $flags;
            }
        }
        
        return $this;
    }

    /**
     * Detect and record early departure
     * BR-WT rules enforcement
     */
    public function detectEarlyDeparture($shiftEndTime = '17:00')
    {
        if ($this->clock_out) {
            $clockOut = \Carbon\Carbon::parse($this->clock_out);
            $shiftEnd = \Carbon\Carbon::parse($shiftEndTime);
            
            if ($clockOut->lt($shiftEnd)) {
                $this->early_departure_minutes = $shiftEnd->diffInMinutes($clockOut);
                
                // Add to violation flags
                $flags = $this->violation_flags ?? [];
                $flags[] = 'early_departure';
                $this->violation_flags = $flags;
            }
        }
        
        return $this;
    }

    /**
     * Check 12-hour daily maximum violation
     * BR-WT-003
     */
    public function check12HourLimit()
    {
        if ($this->total_hours > 12) {
            $flags = $this->violation_flags ?? [];
            $flags[] = 'exceeds_12_hour_limit';
            $this->violation_flags = $flags;
        }
        
        return $this;
    }

    /**
     * Calculate night shift hours (20:00 - 06:00)
     * BR-PAY-007
     */
    public function calculateNightShiftHours()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return $this;
        }

        $clockIn = \Carbon\Carbon::parse($this->clock_in);
        $clockOut = \Carbon\Carbon::parse($this->clock_out);
        
        $nightStart = \Carbon\Carbon::parse($clockIn->format('Y-m-d') . ' 20:00');
        $nightEnd = \Carbon\Carbon::parse($clockOut->format('Y-m-d') . ' 06:00')->addDay();
        
        $nightHours = 0;
        
        // Calculate overlap with night shift period
        if ($clockIn->lt($nightEnd) && $clockOut->gt($nightStart)) {
            $effectiveStart = max($clockIn, $nightStart);
            $effectiveEnd = min($clockOut, $nightEnd);
            $nightHours = $effectiveEnd->diffInHours($effectiveStart);
        }
        
        $this->night_hours = max(0, $nightHours);
        
        return $this;
    }

    /**
     * Auto-classify attendance status code based on data
     * FR-ATT-006
     */
    public function autoClassifyStatusCode()
    {
        if ($this->status_code) {
            return $this; // Already set
        }

        if ($this->total_hours >= 11 && $this->total_hours <= 12) {
            $this->status_code = '12'; // 12-Hour Shift
        } elseif ($this->total_hours >= 7 && $this->total_hours <= 9) {
            $this->status_code = '9'; // Ordinary Hours
        } elseif ($this->status === 'absent') {
            $this->status_code = 'A'; // Absent
        }

        return $this;
    }

    /**
     * Check if this is a rest day (Saturday or Sunday by default)
     */
    public function isRestDay()
    {
        if (!$this->attendance_date) {
            return false;
        }
        
        $date = \Carbon\Carbon::parse($this->attendance_date);
        return $date->isWeekend();
    }

    /**
     * Check if this is a public holiday
     */
    public function isPublicHoliday()
    {
        if (!$this->attendance_date) {
            return false;
        }
        
        return \App\Models\PublicHoliday::where('holiday_date', $this->attendance_date)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Create violation record for detected violations
     */
    public function createViolationRecords()
    {
        if (empty($this->violation_flags)) {
            return $this;
        }

        foreach ($this->violation_flags as $flag) {
            \App\Models\AttendanceViolation::create([
                'client_id' => $this->client_id,
                'employee_id' => $this->employee_id,
                'attendance_id' => $this->id,
                'violation_date' => $this->attendance_date,
                'violation_type' => $flag,
                'details' => $this->getViolationDescription($flag),
                'status' => 'open',
                'action_triggered' => false,
            ]);
        }

        return $this;
    }

    /**
     * Get human-readable violation description
     */
    private function getViolationDescription($flag)
    {
        $descriptions = [
            'late_arrival' => "Late arrival by {$this->late_minutes} minutes",
            'early_departure' => "Early departure by {$this->early_departure_minutes} minutes",
            'exceeds_12_hour_limit' => "Worked {$this->total_hours} hours, exceeds 12-hour daily limit",
            'exceeds_45_hour_weekly' => "Weekly hours exceed 45-hour limit",
            'exceeds_50_hour_monthly_overtime' => "Monthly overtime exceeds 50-hour cap",
            'missing_rest_period' => "Missing mandatory 24-hour rest period",
        ];

        return $descriptions[$flag] ?? 'Attendance violation detected';
    }

    /**
     * Check employee's weekly hours (45-hour limit)
     * BR-WT-001
     */
    public function checkWeeklyHourLimit()
    {
        if (!$this->attendance_date || !$this->employee_id) {
            return $this;
        }

        $weekStart = \Carbon\Carbon::parse($this->attendance_date)->startOfWeek();
        $weekEnd = \Carbon\Carbon::parse($this->attendance_date)->endOfWeek();

        $weeklyHours = self::where('employee_id', $this->employee_id)
            ->whereBetween('attendance_date', [$weekStart, $weekEnd])
            ->where('id', '!=', $this->id) // Exclude current record
            ->sum('total_hours');

        $totalWeeklyHours = $weeklyHours + $this->total_hours;

        if ($totalWeeklyHours > 45) {
            $flags = $this->violation_flags ?? [];
            $flags[] = 'exceeds_45_hour_weekly';
            $this->violation_flags = $flags;
        }

        return $this;
    }

    /**
     * Check employee's monthly overtime (50-hour cap)
     * BR-WT-006
     */
    public function checkMonthlyOvertimeCap()
    {
        if (!$this->attendance_date || !$this->employee_id) {
            return $this;
        }

        $monthStart = \Carbon\Carbon::parse($this->attendance_date)->startOfMonth();
        $monthEnd = \Carbon\Carbon::parse($this->attendance_date)->endOfMonth();

        $monthlyOvertime = self::where('employee_id', $this->employee_id)
            ->whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->where('id', '!=', $this->id)
            ->sum('overtime_hours');

        $totalMonthlyOvertime = $monthlyOvertime + $this->overtime_hours;

        if ($totalMonthlyOvertime > 50) {
            // Cap overtime at 50 hours
            $this->overtime_hours = max(0, 50 - $monthlyOvertime);
            
            $flags = $this->violation_flags ?? [];
            $flags[] = 'exceeds_50_hour_monthly_overtime';
            $this->violation_flags = $flags;
        }

        return $this;
    }

    /**
     * Execute complete attendance processing with violation detection
     */
    public function processAttendance($shiftStartTime = '08:00', $shiftEndTime = '17:00')
    {
        return $this
            ->calculateTotalHours()
            ->calculateNightShiftHours()
            ->detectLateArrival($shiftStartTime)
            ->detectEarlyDeparture($shiftEndTime)
            ->check12HourLimit()
            ->checkWeeklyHourLimit()
            ->checkMonthlyOvertimeCap()
            ->autoClassifyStatusCode();
    }
}
