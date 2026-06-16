<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoPayrollAttendanceCompensationSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $client = Client::orderBy('id')->first();
        if (!$client) {
            return;
        }

        $employeeColumns = array_flip(Schema::getColumnListing('employees'));
        $hasBenefitsColumn = isset($employeeColumns['benefits']);

        $existing = Employee::where('client_id', $client->id)->count();
        if ($existing > 0) {
            return;
        }

        $employees = collect([
            [
                'employee_id' => 'EMP001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe.demo@legalhr.co.tz',
                'phone' => '255700000001',
                'department' => 'IT',
                'position' => 'Developer',
                'salary' => 2500000,
                'benefits' => ['Health Insurance', 'Phone / Internet', 'Transport Allowance'],
            ],
            [
                'employee_id' => 'EMP002',
                'first_name' => 'Sarah',
                'last_name' => 'Smith',
                'email' => 'sarah.smith.demo@legalhr.co.tz',
                'phone' => '255700000002',
                'department' => 'HR',
                'position' => 'HR Manager',
                'salary' => 2200000,
                'benefits' => ['Health Insurance', 'Training Support'],
            ],
            [
                'employee_id' => 'EMP003',
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'email' => 'mike.johnson.demo@legalhr.co.tz',
                'phone' => '255700000003',
                'department' => 'Finance',
                'position' => 'Accountant',
                'salary' => 1800000,
                'benefits' => ['Transport Allowance'],
            ],
            [
                'employee_id' => 'EMP004',
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis.demo@legalhr.co.tz',
                'phone' => '255700000004',
                'department' => 'Marketing',
                'position' => 'Analyst',
                'salary' => 1600000,
                'benefits' => ['Phone / Internet'],
            ],
            [
                'employee_id' => 'EMP005',
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson.demo@legalhr.co.tz',
                'phone' => '255700000005',
                'department' => 'Operations',
                'position' => 'Operations Manager',
                'salary' => 3000000,
                'benefits' => ['Health Insurance', 'Transport Allowance'],
            ],
            [
                'employee_id' => 'EMP006',
                'first_name' => 'Lisa',
                'last_name' => 'Brown',
                'email' => 'lisa.brown.demo@legalhr.co.tz',
                'phone' => '255700000006',
                'department' => 'Sales',
                'position' => 'Sales Manager',
                'salary' => 2400000,
                'benefits' => ['Transport Allowance', 'Phone / Internet'],
            ],
        ])->map(function ($row) use ($client, $hasBenefitsColumn) {
            $payload = [
                'client_id' => $client->id,
                'employee_id' => $row['employee_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'position' => $row['position'],
                'department' => $row['department'],
                'hire_date' => now()->subMonths(6)->toDateString(),
                'status' => 'active',
                'employment_type' => 'full_time',
                'salary' => $row['salary'],
                'currency' => 'TZS',
                'payment_frequency' => 'monthly',
            ];

            if ($hasBenefitsColumn) {
                $payload['benefits'] = $row['benefits'];
            }

            $employeeColumns = array_flip(Schema::getColumnListing('employees'));
            $filteredPayload = array_intersect_key($payload, $employeeColumns);

            return Employee::create($filteredPayload);
        });

        $period = now()->subMonth()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        DB::transaction(function () use ($client, $employees, $start, $end, $period) {
            foreach ($employees as $employee) {
                $d = $start->copy();
                while ($d->lte($end)) {
                    if ($d->isWeekend()) {
                        $d->addDay();
                        continue;
                    }

                    $seed = crc32($employee->id . '_' . $d->format('Y-m-d'));
                    $r = $seed % 100;

                    $status = 'present';
                    if ($r < 6) $status = 'absent';
                    else if ($r < 12) $status = 'late';
                    else if ($r < 15) $status = 'on_leave';

                    $clockIn = null;
                    $clockOut = null;
                    $totalHours = 0;
                    $overtime = 0;

                    if (in_array($status, ['present', 'late'], true)) {
                        $clockIn = $status === 'late' ? '09:15' : '08:30';
                        $clockOut = '17:30';
                        $totalHours = 8.0;
                        $overtime = ($r % 4 === 0) ? 2.0 : 0.0;
                    }

                    Attendance::updateOrCreate(
                        [
                            'client_id' => $client->id,
                            'employee_id' => $employee->id,
                            'attendance_date' => $d->toDateString(),
                        ],
                        [
                            'status' => $status,
                            'clock_in' => $clockIn,
                            'clock_out' => $clockOut,
                            'total_hours' => $totalHours,
                            'overtime_hours' => $overtime,
                        ]
                    );

                    $d->addDay();
                }
            }

            foreach ($employees as $employee) {
                $attendance = Attendance::where('client_id', $client->id)
                    ->where('employee_id', $employee->id)
                    ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                    ->get();

                $workingDays = $this->countWorkingDays($start, $end);
                $presentDays = $attendance->whereIn('status', ['present', 'late', 'half_day'])->count();
                $attendanceRatio = $workingDays > 0 ? min(1, $presentDays / $workingDays) : 1;

                $baseMonthly = (float) ($employee->salary ?? 0);
                $basicSalary = round($baseMonthly * $attendanceRatio, 2);

                $overtimeHours = (float) $attendance->sum('overtime_hours');
                $hourlyRate = $workingDays > 0 ? ($baseMonthly / ($workingDays * 8)) : 0;
                $overtimeRate = $hourlyRate * 1.5;
                $overtimePay = round($overtimeHours * $overtimeRate, 2);

                $allowances = $this->calculateAllowancesFromBenefits($employee->benefits ?? []);
                $bonuses = 0;
                $grossPay = round($basicSalary + $overtimePay + $allowances + $bonuses, 2);

                $tax = round($grossPay * 0.10, 2);
                $socialSecurity = round($grossPay * 0.05, 2);
                $pension = round($grossPay * 0.05, 2);
                $other = 0;
                $totalDeductions = round($tax + $socialSecurity + $pension + $other, 2);
                $netPay = round(max(0, $grossPay - $totalDeductions), 2);

                Payroll::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'employee_id' => $employee->id,
                        'payroll_period' => $period,
                    ],
                    [
                        'pay_date' => Carbon::createFromFormat('Y-m', $period)->endOfMonth()->toDateString(),
                        'basic_salary' => $basicSalary,
                        'overtime_hours' => $overtimeHours,
                        'overtime_rate' => $overtimeRate,
                        'overtime_pay' => $overtimePay,
                        'allowances' => $allowances,
                        'bonuses' => $bonuses,
                        'gross_pay' => $grossPay,
                        'tax_deductions' => $tax,
                        'social_security' => $socialSecurity,
                        'pension' => $pension,
                        'other_deductions' => $other,
                        'total_deductions' => $totalDeductions,
                        'net_pay' => $netPay,
                        'status' => 'draft',
                    ]
                );
            }
        });
    }

    private function countWorkingDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $d = $start->copy();
        while ($d->lte($end)) {
            if (!$d->isWeekend()) {
                $count++;
            }
            $d->addDay();
        }
        return $count;
    }

    private function calculateAllowancesFromBenefits($benefits): float
    {
        $list = is_array($benefits) ? $benefits : [];
        $list = array_map(fn ($v) => strtolower(trim((string) $v)), $list);

        $map = [
            'transport allowance' => 50000,
            'phone / internet' => 30000,
            'health insurance' => 100000,
            'training support' => 20000,
        ];

        $total = 0;
        foreach ($map as $key => $amount) {
            if (in_array($key, $list, true)) {
                $total += $amount;
            }
        }

        return (float) $total;
    }
}
