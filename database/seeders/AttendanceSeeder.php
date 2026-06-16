<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\AttendanceViolation;
use App\Models\AttendanceMonthlySummary;
use App\Models\Employee;
use App\Models\ShiftPattern;
use App\Models\PublicHoliday;
use App\Models\EmployeeShift;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Attendance Module data...');

        // 1. Create Default Shift Patterns
        $this->createShiftPatterns();

        // 2. Create Public Holidays (Tanzania)
        $this->createPublicHolidays();

        // 3. Assign Employees to Shifts
        $this->assignEmployeeShifts();

        // 4. Create Attendance Records
        $this->createAttendanceRecords();

        $this->command->info('✅ Attendance Module seeded successfully!');
    }

    private function createShiftPatterns(): void
    {
        $shiftPatterns = [
            [
                'shift_name' => 'Day Shift (9-5)',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_duration' => 60,
                'is_night_shift' => false,
                'allowance_rate' => 0,
                'is_active' => true,
            ],
            [
                'shift_name' => 'Morning Shift (6-2)',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'break_duration' => 60,
                'is_night_shift' => false,
                'allowance_rate' => 0,
                'is_active' => true,
            ],
            [
                'shift_name' => 'Night Shift (10pm-6am)',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'break_duration' => 60,
                'is_night_shift' => true,
                'allowance_rate' => 5000, // Example allowance in TZS
                'is_active' => true,
            ],
            [
                'shift_name' => '12-Hour Shift (8-8)',
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'break_duration' => 90,
                'is_night_shift' => false,
                'allowance_rate' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($shiftPatterns as $pattern) {
            ShiftPattern::firstOrCreate($pattern);
        }

        $this->command->info('  ✔️ Shift patterns created');
    }

    private function createPublicHolidays(): void
    {
        $year = Carbon::now()->year;
        $holidays = [
            // Fixed Public Holidays
            ['holiday_date' => "$year-01-01", 'holiday_name' => 'New Year\'s Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-01-12", 'holiday_name' => 'Zanzibar Revolution Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-04-26", 'holiday_name' => 'Union Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-05-01", 'holiday_name' => 'Labour Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-07-07", 'holiday_name' => 'Saba Saba Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-08-08", 'holiday_name' => 'Nane Nane Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-10-14", 'holiday_name' => 'Nyerere Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-12-09", 'holiday_name' => 'Independence Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-12-25", 'holiday_name' => 'Christmas Day', 'is_recurring' => true, 'active_year' => null],
            ['holiday_date' => "$year-12-26", 'holiday_name' => 'Boxing Day', 'is_recurring' => true, 'active_year' => null],
            // Variable holidays (for example)
            ['holiday_date' => "$year-06-16", 'holiday_name' => 'Eid al-Fitr (Approx)', 'is_recurring' => false, 'active_year' => $year],
            ['holiday_date' => "$year-08-21", 'holiday_name' => 'Eid al-Adha (Approx)', 'is_recurring' => false, 'active_year' => $year],
        ];

        foreach ($holidays as $holiday) {
            PublicHoliday::firstOrCreate($holiday);
        }

        $this->command->info('  ✔️ Public holidays created');
    }

    private function assignEmployeeShifts(): void
    {
        $employees = Employee::all();
        $shiftPatterns = ShiftPattern::where('is_active', true)->get();

        foreach ($employees as $employee) {
            $randomShift = $shiftPatterns->random();
            EmployeeShift::firstOrCreate([
                'client_id' => $employee->client_id,
                'employee_id' => $employee->id,
                'shift_pattern_id' => $randomShift->id,
            ], [
                'effective_from' => $employee->hire_date ?? now()->subMonths(2),
                'effective_to' => null,
            ]);
        }

        $this->command->info('  ✔️ Employees assigned to shifts');
    }

    private function createAttendanceRecords(): void
    {
        $employees = Employee::with('employeeShifts.shiftPattern')->get();
        
        foreach ($employees as $employee) {
            $this->command->info('  📅 Generating attendance for: ' . $employee->first_name . ' ' . $employee->last_name);

            $employeeShift = $employee->employeeShifts->first();
            $shift = $employeeShift?->shiftPattern;

            // Create attendance records for the last 30 days
            for ($day = 0; $day < 30; $day++) {
                $attendanceDate = Carbon::now()->subDays($day)->startOfDay();
                
                // Skip weekends (Saturday and Sunday) unless it's a public holiday
                $isPublicHoliday = PublicHoliday::where('holiday_date', $attendanceDate->toDateString())
                    ->where(function($query) use ($employee) {
                        $query->where('client_id', $employee->client_id)->orWhereNull('client_id');
                    })
                    ->exists();

                $statusCode = $this->getRandomStatusCode($attendanceDate, $isPublicHoliday);

                $this->generateAttendanceRecord($employee, $attendanceDate, $statusCode, $shift);
            }
        }
        
        // Generate monthly summaries
        $this->refreshMonthlySummaries();

        $this->command->info('  ✔️ Attendance records and violations created');
    }

    private function generateAttendanceRecord(
        Employee $employee,
        Carbon $date,
        string $statusCode,
        ?ShiftPattern $shift = null
    ): void {
        $clockIn = null;
        $clockOut = null;
        $lateMinutes = 0;
        $earlyDepartureMinutes = 0;
        $ordinaryHours = 0;
        $overtimeHours = 0;
        $nightHours = 0;
        $restDayHours = 0;
        $phHours = 0;

        if (in_array($statusCode, ['9', '12', 'M'])) { // Present/Mission
            if ($shift) {
                $startTime = Carbon::parse($shift->start_time)->addMinutes(rand(0, 30));
                $endTime = Carbon::parse($shift->end_time)->addMinutes(rand(0, 60));

                $clockIn = $startTime->format('H:i');
                $clockOut = $endTime->format('H:i');

                $scheduledStart = Carbon::parse($shift->start_time);
                if ($startTime->greaterThan($scheduledStart)) {
                    $lateMinutes = $scheduledStart->diffInMinutes($startTime);
                }

                $totalMinutes = $startTime->diffInMinutes($endTime);
                $totalMinutes -= $shift->break_duration;
                $totalHours = round($totalMinutes / 60, 2);

                // Calculate hours types
                if ($date->isWeekend()) {
                    $restDayHours = $totalHours;
                } else {
                    $ordinaryHours = min(8, $totalHours);
                    $overtimeHours = max(0, $totalHours - 8);
                }
                
                $nightHours = $this->calculateNightHours($date, $clockIn, $clockOut, $totalHours);
            } else {
                $clockIn = '08:00';
                $clockOut = '17:00';
                $ordinaryHours = 8;
            }
        }

        Attendance::firstOrCreate(
            [
                'client_id' => $employee->client_id,
                'employee_id' => $employee->id,
                'attendance_date' => $date->toDateString(),
            ],
            [
                'status' => $this->mapStatusCodeToStatus($statusCode),
                'status_code' => $statusCode,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'total_hours' => $ordinaryHours + $overtimeHours,
                'ordinary_hours' => $ordinaryHours,
                'overtime_hours' => $overtimeHours,
                'rest_day_hours' => $restDayHours,
                'ph_hours' => $phHours,
                'night_hours' => $nightHours,
                'late_minutes' => $lateMinutes,
                'early_departure_minutes' => $earlyDepartureMinutes,
                'source' => 'manual',
                'manual_entry' => true,
                'workflow_status' => 'approved',
                'approved_by' => 1, // Default admin user id
                'approved_at' => now(),
                'shift_pattern_id' => $shift?->id,
                'notes' => $this->getRandomNotes($statusCode),
                'location' => 'Main Office',
                'ip_address' => '192.168.1.' . rand(1, 254),
            ]
        );
    }

    private function calculateNightHours(Carbon $date, string $clockIn, string $clockOut, float $totalHours): float
    {
        $start = $date->copy()->setTimeFromTimeString($clockIn);
        $end = $date->copy()->setTimeFromTimeString($clockOut);
        if ($end->lte($start)) $end->addDay();

        $nightStart = $start->copy()->setTime(20,0,0);
        $nightEnd = $start->copy()->addDay()->setTime(6,0,0);
        
        $overlapStart = $start->max($nightStart);
        $overlapEnd = $end->min($nightEnd);
        $nightMinutes = $overlapStart->lt($overlapEnd) ? $overlapStart->diffInMinutes($overlapEnd) : 0;
        return round($nightMinutes / 60, 2);
    }

    private function getRandomStatusCode(Carbon $date, bool $isPublicHoliday): string
    {
        if ($isPublicHoliday) {
            return '9'; // Could also leave as holiday, let's simulate work on holiday
        }

        $statuses = [
            '9' => 75,
            '12' => 10,
            'A' => 3,
            'AL' => 4,
            'SLF' => 2,
            'SLH' => 1,
            'UL' => 2,
            'M' => 3,
        ];
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) return $status;
        }

        return '9';
    }

    private function mapStatusCodeToStatus(string $statusCode): string
    {
        $map = [
            'A' => 'absent',
            'AL' => 'on_leave',
            'SLF' => 'on_leave',
            'SLH' => 'on_leave',
            'UL' => 'on_leave',
            'M' => 'present',
            '9' => 'present',
            '12' => 'present',
        ];
        return $map[$statusCode] ?? 'present';
    }

    private function getRandomNotes(string $statusCode): string
    {
        $notes = [
            '9' => ['Regular work day', 'Completed tasks', 'Attended meeting', 'Productive work'],
            '12' => ['12-hour shift', 'Covered overtime', 'Night shift', 'Long day'],
            'A' => ['Sick leave', 'Personal emergency', 'Unapproved absence'],
            'AL' => ['Annual leave', 'Vacation'],
            'SLF' => ['Sick leave (full pay)', 'Medical appointment'],
            'SLH' => ['Sick leave (half day)', 'Medical checkup'],
            'UL' => ['Unpaid leave', 'Personal time off'],
            'M' => ['Official mission', 'Field work', 'Client meeting'],
        ];
        $list = $notes[$statusCode] ?? $notes['9'];
        return $list[array_rand($list)];
    }

    private function refreshMonthlySummaries(): void
    {
        $this->command->info('  📊 Generating monthly summaries...');
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $monthStart = now()->startOfMonth();
            $monthEnd = now()->endOfMonth();
            
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get();

            AttendanceMonthlySummary::updateOrCreate(
                [
                    'client_id' => $employee->client_id,
                    'employee_id' => $employee->id,
                    'month' => $monthStart->month,
                    'year' => $monthStart->year,
                ],
                [
                    'total_days' => $monthStart->daysInMonth,
                    'worked_days' => $attendanceRecords->whereIn('status_code', ['9','12','M'])->count(),
                    'absent_days' => $attendanceRecords->where('status_code', 'A')->count(),
                    'leave_days' => $attendanceRecords->whereIn('status_code', ['AL','SLF','SLH','UL'])->count(),
                    'overtime_hours' => round($attendanceRecords->sum('overtime_hours'), 2),
                    'night_hours' => round($attendanceRecords->sum('night_hours'), 2),
                ]
            );
        }
    }
}
