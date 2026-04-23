<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
            return static::query();
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
}
