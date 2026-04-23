<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'break_start',
        'break_end',
        'total_hours',
        'overtime_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the client that owns the attendance.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the employee that the attendance belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include present attendance.
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope a query to only include absent attendance.
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope a query to only include late attendance.
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Calculate work hours.
     */
    public function calculateWorkHours()
    {
        if ($this->check_in && $this->check_out) {
            $totalMinutes = $this->check_out->diffInMinutes($this->check_in);
            
            // Subtract break time if both break start and end exist
            if ($this->break_start && $this->break_end) {
                $breakMinutes = $this->break_end->diffInMinutes($this->break_start);
                $totalMinutes -= $breakMinutes;
            }
            
            return $totalMinutes / 60;
        }
        
        return 0;
    }

    /**
     * Calculate overtime hours.
     */
    public function calculateOvertime()
    {
        $workHours = $this->calculateWorkHours();
        $regularHours = 8; // Assuming 8 hours regular work day
        
        return $workHours > $regularHours ? $workHours - $regularHours : 0;
    }
}
