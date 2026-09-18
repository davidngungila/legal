<?php

use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayslipService;

function makePayroll(array $attributes): Payroll
{
    return new Payroll($attributes);
}

test('payslip service decomposes statutory deductions from stored columns', function () {
    $employee = new Employee([
        'employee_id' => 'EMP001',
        'first_name' => 'Asha',
        'last_name' => 'Mushi',
        'department' => 'Finance',
        'position' => 'Accountant',
    ]);

    $payroll = makePayroll([
        'payroll_period' => '2026-09',
        'basic_salary' => 1000000,
        'allowances' => 200000,
        'gross_pay' => 1200000,
        'taxable_income' => 1080000,
        'tax_deductions' => 100000,
        'nssf_employee' => 120000,
        'nssf_employer' => 120000,
        'heslb' => 50000,
        'trade_union' => 20000,
        'other_deductions' => 80000,
        'total_deductions' => 290000,
        'net_pay' => 910000,
        'sdl' => 54000,
        'wcf' => 6000,
        'status' => 'paid',
        'notes' => json_encode([
            'payroll_meta' => [
                'loanDeductions' => 10000,
                'heslb' => 50000,
                'tradeUnion' => 20000,
            ],
        ]),
    ]);

    $data = app(PayslipService::class)->build($payroll, $employee);

    expect($data['deductions']['paye'])->toBe(100000.0)
        ->and($data['deductions']['nssf_employee'])->toBe(120000.0)
        ->and($data['deductions']['heslb'])->toBe(50000.0)
        ->and($data['deductions']['trade_union'])->toBe(20000.0)
        ->and($data['deductions']['loan'])->toBe(10000.0)
        ->and($data['deductions']['other'])->toBe(0.0)
        ->and($data['deductions']['total'])->toBe(290000.0)
        ->and($data['earnings']['gross'])->toBe(1200000.0)
        ->and($data['employer_contributions']['nssf_employer'])->toBe(120000.0)
        ->and($data['employer_contributions']['sdl'])->toBe(54000.0)
        ->and($data['employer_contributions']['wcf'])->toBe(6000.0)
        ->and($data['net_pay'])->toBe(910000.0)
        ->and($data['period_label'])->toBe('September 2026');
});

test('payslip service falls back to legacy columns and meta values', function () {
    $payroll = makePayroll([
        'payroll_period' => '2026-08',
        'basic_salary' => 600000,
        'gross_pay' => 600000,
        'tax_deductions' => 30000,
        'nssf_employee' => 0,
        'social_security' => 60000,
        'nssf_employer' => 0,
        'pension' => 60000,
        'other_deductions' => 0,
        'total_deductions' => 0,
        'net_pay' => 0,
        'status' => 'draft',
        'notes' => json_encode([
            'payroll_meta' => [
                'sdl' => 27000,
                'wcf' => 3000,
                'heslb' => 15000,
                'tradeUnion' => 0,
                'loanDeductions' => 5000,
            ],
        ]),
    ]);

    $data = app(PayslipService::class)->build($payroll);

    expect($data['deductions']['nssf_employee'])->toBe(60000.0)
        ->and($data['deductions']['heslb'])->toBe(15000.0)
        ->and($data['deductions']['loan'])->toBe(5000.0)
        ->and($data['deductions']['total'])->toBe(110000.0)
        ->and($data['net_pay'])->toBe(490000.0)
        ->and($data['employer_contributions']['nssf_employer'])->toBe(60000.0)
        ->and($data['employer_contributions']['sdl'])->toBe(27000.0)
        ->and($data['employer_contributions']['wcf'])->toBe(3000.0);
});
