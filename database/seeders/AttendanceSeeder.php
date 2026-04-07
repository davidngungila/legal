<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create attendance records for the last 30 days
            for ($day = 0; $day < 30; $day++) {
                $attendanceDate = Carbon::now()->subDays($day);
                
                // Skip weekends (Saturday and Sunday)
                if ($attendanceDate->isWeekend()) {
                    continue;
                }
                
                // Randomly determine attendance status
                $status = $this->getRandomAttendanceStatus();
                
                $clockIn = null;
                $clockOut = null;
                $breakStart = null;
                $breakEnd = null;
                $totalHours = 0;
                $overtimeHours = 0;
                
                if ($status === 'present') {
                    // Generate realistic work hours
                    $clockIn = Carbon::createFromTime(8 + rand(0, 1), rand(0, 30), 0);
                    $clockOut = Carbon::createFromTime(17 + rand(0, 2), rand(0, 30), 0);
                    $breakStart = Carbon::createFromTime(12, rand(0, 30), 0);
                    $breakEnd = Carbon::createFromTime(13, rand(0, 30), 0);
                    
                    $totalMinutes = $clockOut->diffInMinutes($clockIn) - $breakEnd->diffInMinutes($breakStart);
                    $totalHours = $totalMinutes / 60;
                    
                    if ($totalHours > 8) {
                        $overtimeHours = $totalHours - 8;
                    }
                } elseif ($status === 'late') {
                    $clockIn = Carbon::createFromTime(9 + rand(0, 1), rand(0, 30), 0);
                    $clockOut = Carbon::createFromTime(17 + rand(0, 1), rand(0, 30), 0);
                    $breakStart = Carbon::createFromTime(12, rand(0, 30), 0);
                    $breakEnd = Carbon::createFromTime(13, rand(0, 30), 0);
                    
                    $totalMinutes = $clockOut->diffInMinutes($clockIn) - $breakEnd->diffInMinutes($breakStart);
                    $totalHours = $totalMinutes / 60;
                }
                
                Attendance::firstOrCreate(
                    [
                        'client_id' => $employee->client_id,
                        'employee_id' => $employee->id,
                        'attendance_date' => $attendanceDate->format('Y-m-d'),
                    ],
                    [
                        'clock_in' => $clockIn ? $clockIn->format('H:i') : null,
                        'clock_out' => $clockOut ? $clockOut->format('H:i') : null,
                        'break_start' => $breakStart ? $breakStart->format('H:i') : null,
                        'break_end' => $breakEnd ? $breakEnd->format('H:i') : null,
                        'total_hours' => $totalHours,
                        'overtime_hours' => $overtimeHours,
                        'status' => $status,
                        'notes' => $this->getRandomNotes($status),
                        'location' => 'Office',
                        'ip_address' => '192.168.1.' . rand(1, 254),
                    ]
                );
            }
        }

        $this->command->info('Attendance records seeded successfully for all employees!');
    }
    
    /**
     * Get random attendance status with realistic distribution
     */
    private function getRandomAttendanceStatus()
    {
        $statuses = [
            'present' => 85, // 85% present
            'late' => 8,     // 8% late
            'absent' => 3,   // 3% absent
            'on_leave' => 4, // 4% on leave
        ];
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $status;
            }
        }
        
        return 'present';
    }
    
    /**
     * Get random notes based on status
     */
    private function getRandomNotes($status)
    {
        $notes = [
            'present' => [
                'Regular work day',
                'Completed assigned tasks',
                'Attended team meeting',
                'Productive day',
            ],
            'late' => [
                'Traffic delay',
                'Transport issues',
                'Personal emergency',
                'Overslept',
            ],
            'absent' => [
                'Sick leave',
                'Personal emergency',
                'Family emergency',
                'Unapproved absence',
            ],
            'on_leave' => [
                'Annual leave',
                'Sick leave',
                'Maternity leave',
                'Personal leave',
            ],
        ];
        
        $statusNotes = $notes[$status] ?? $notes['present'];
        return $statusNotes[array_rand($statusNotes)];
    }
}
