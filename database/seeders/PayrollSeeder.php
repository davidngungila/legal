<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payroll;
use App\Models\Employee;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            // Create payroll records for the last 3 months
            for ($month = 1; $month <= 3; $month++) {
                $payrollDate = Carbon::now()->subMonths($month);
                $payrollPeriod = $payrollDate->format('Y-m');
                
                $basicSalary = $employee->salary ?? 1500000;
                $overtimeHours = rand(0, 20);
                $overtimeRate = $basicSalary / 160; // Hourly rate
                $overtimePay = $overtimeHours * $overtimeRate * 1.5; // 1.5x for overtime
                $allowances = rand(100000, 300000);
                $bonuses = $month == 1 ? rand(50000, 200000) : 0; // Bonus in first month
                
                $grossPay = $basicSalary + $overtimePay + $allowances + $bonuses;
                $taxDeductions = $grossPay * 0.15; // 15% tax
                $socialSecurity = $grossPay * 0.10; // 10% social security
                $pension = $grossPay * 0.05; // 5% pension
                $otherDeductions = rand(10000, 50000);
                
                $totalDeductions = $taxDeductions + $socialSecurity + $pension + $otherDeductions;
                $netPay = $grossPay - $totalDeductions;
                
                Payroll::firstOrCreate(
                    [
                        'client_id' => $employee->client_id,
                        'employee_id' => $employee->id,
                        'payroll_period' => $payrollPeriod,
                    ],
                    [
                        'pay_date' => $payrollDate->endOfMonth(),
                        'basic_salary' => $basicSalary,
                        'overtime_hours' => $overtimeHours,
                        'overtime_rate' => $overtimeRate,
                        'overtime_pay' => $overtimePay,
                        'allowances' => $allowances,
                        'bonuses' => $bonuses,
                        'gross_pay' => $grossPay,
                        'tax_deductions' => $taxDeductions,
                        'social_security' => $socialSecurity,
                        'pension' => $pension,
                        'other_deductions' => $otherDeductions,
                        'total_deductions' => $totalDeductions,
                        'net_pay' => $netPay,
                        'status' => 'paid',
                        'notes' => 'Monthly salary payment - ' . $payrollDate->format('F Y'),
                    ]
                );
            }
        }

        $this->command->info('Payroll records seeded successfully for all employees!');
    }
}
