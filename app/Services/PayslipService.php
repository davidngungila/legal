<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class PayslipService
{
    /**
     * Build a normalised, display-ready payslip breakdown from a payroll
     * record. Every statutory component (PAYE, NSSF, HESLB, trade union,
     * loans, SDL, WCF) is decomposed from the persisted columns, falling back
     * to the payroll meta blob for legacy rows that predate those columns.
     */
    public function build(Payroll $payroll, ?Employee $employee = null, ?Client $client = null): array
    {
        $employee = $employee ?: $payroll->employee;
        $client = $client ?: ($employee?->client ?: $payroll->client);
        $meta = $this->extractMeta($payroll->notes);

        $basic = (float) ($payroll->basic_salary ?? 0);
        $allowances = (float) ($payroll->allowances ?? 0);
        $bonuses = (float) ($payroll->bonuses ?? 0);
        $overtimePay = (float) ($payroll->overtime_pay ?? 0);
        $restDayPay = (float) ($payroll->rest_day_pay ?? 0);
        $holidayPay = (float) ($payroll->ph_pay ?? 0);
        $nightAllowance = (float) ($payroll->night_allowance ?? 0);
        $unpaidLeaveDeduction = (float) ($payroll->unpaid_leave_deduction ?? 0);

        $gross = (float) ($payroll->gross_pay ?? 0);
        if ($gross <= 0) {
            $gross = max(0, $basic + $allowances + $bonuses + $overtimePay
                + $restDayPay + $holidayPay + $nightAllowance - $unpaidLeaveDeduction);
        }

        $paye = (float) ($payroll->tax_deductions ?? 0);

        $nssfEmployee = (float) ($payroll->nssf_employee ?? 0);
        if ($nssfEmployee <= 0) {
            $nssfEmployee = (float) ($payroll->social_security ?? 0);
        }

        $nssfEmployer = (float) ($payroll->nssf_employer ?? 0);
        if ($nssfEmployer <= 0) {
            $nssfEmployer = (float) ($payroll->pension ?? 0);
        }

        $heslb = (float) ($payroll->heslb ?? 0);
        if ($heslb <= 0 && isset($meta['heslb'])) {
            $heslb = (float) $meta['heslb'];
        }

        $tradeUnion = (float) ($payroll->trade_union ?? 0);
        if ($tradeUnion <= 0 && isset($meta['tradeUnion'])) {
            $tradeUnion = (float) $meta['tradeUnion'];
        }

        $loanDeduction = (float) ($meta['loanDeductions'] ?? 0);

        $otherDeductionsColumn = (float) ($payroll->other_deductions ?? 0);
        $otherVoluntary = (float) ($meta['otherDed'] ?? 0);
        if ($otherVoluntary <= 0) {
            // other_deductions aggregates HESLB + trade union + loans + voluntary.
            $otherVoluntary = (float) max(0, $otherDeductionsColumn - ($heslb + $tradeUnion + $loanDeduction));
        }

        $totalDeductions = (float) ($payroll->total_deductions ?? 0);
        if ($totalDeductions <= 0) {
            $totalDeductions = $paye + $nssfEmployee + $heslb + $tradeUnion
                + $loanDeduction + $otherVoluntary;
        }

        $net = (float) ($payroll->net_pay ?? 0);
        if ($net <= 0 && $gross > 0) {
            $net = max(0, $gross - $totalDeductions);
        }

        $sdl = (float) ($payroll->sdl ?? 0);
        if ($sdl <= 0 && isset($meta['sdl'])) {
            $sdl = (float) $meta['sdl'];
        }

        $wcf = (float) ($payroll->wcf ?? 0);
        if ($wcf <= 0 && isset($meta['wcf'])) {
            $wcf = (float) $meta['wcf'];
        }

        $taxableIncome = (float) ($payroll->taxable_income ?? 0);
        if ($taxableIncome <= 0) {
            $taxableIncome = max(0, $gross - $nssfEmployee);
        }

        return [
            'id' => $payroll->id,
            'period' => $payroll->payroll_period,
            'period_label' => $this->formatPeriod($payroll->payroll_period),
            'pay_date' => optional($payroll->pay_date)->format('d M Y'),
            'status' => $payroll->status,
            'status_badge' => $payroll->status_badge,
            'employee' => [
                'id' => $employee?->id,
                'code' => $employee?->employee_id ?? '-',
                'name' => $employee ? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) : '-',
                'department' => $employee?->department ?? '-',
                'position' => $employee?->position ?? '-',
                'email' => $employee?->email ?? '-',
                'phone' => $employee?->phone ?? '-',
                'tin' => $employee?->tin_number ?? '-',
                'nssf' => $employee?->nssf_number ?? '-',
                'heslb_number' => $employee?->heslb_number ?? '-',
                'bank_name' => $employee?->bank_name ?? '-',
                'bank_account' => $employee?->bank_account ?? '-',
                'hire_date' => optional($employee?->hire_date)->format('d M Y'),
            ],
            'employer' => [
                'name' => $client?->name ?? config('app.name'),
                'address' => trim(($client?->address ?? '') . ' ' . ($client?->city ?? '')),
                'email' => $client?->email ?? '-',
                'phone' => $client?->phone ?? '-',
            ],
            'earnings' => [
                'basic' => $basic,
                'allowances' => $allowances,
                'bonuses' => $bonuses,
                'overtime' => $overtimePay,
                'rest_day' => $restDayPay,
                'public_holiday' => $holidayPay,
                'night_allowance' => $nightAllowance,
                'gross' => $gross,
            ],
            'deductions' => [
                'paye' => $paye,
                'nssf_employee' => $nssfEmployee,
                'heslb' => $heslb,
                'trade_union' => $tradeUnion,
                'loan' => $loanDeduction,
                'other' => $otherVoluntary,
                'total' => $totalDeductions,
            ],
            'employer_contributions' => [
                'nssf_employer' => $nssfEmployer,
                'sdl' => $sdl,
                'wcf' => $wcf,
                'total' => $nssfEmployer + $sdl + $wcf,
            ],
            'attendance' => [
                'overtime_hours' => (float) ($payroll->overtime_hours ?? 0),
                'rest_day_hours' => (float) ($payroll->rest_day_hours ?? 0),
                'public_holiday_hours' => (float) ($payroll->ph_hours ?? 0),
                'night_hours' => (float) ($payroll->night_hours ?? 0),
                'unpaid_leave_days' => (float) ($payroll->unpaid_leave_days ?? 0),
                'unpaid_leave_deduction' => $unpaidLeaveDeduction,
            ],
            'taxable_income' => $taxableIncome,
            'net_pay' => $net,
            'salary_hold' => (bool) ($payroll->salary_hold ?? ($meta['salaryHold'] ?? false)),
            'salary_hold_reason' => (string) ($payroll->salary_hold_reason ?? ($meta['salaryHoldReason'] ?? '')),
            'generated_at' => now()->format('d M Y H:i'),
        ];
    }

    /**
     * Extract the payroll_meta blob from a notes column.
     */
    public function extractMeta(?string $notes): array
    {
        if (!$notes) {
            return [];
        }

        $decoded = json_decode($notes, true);
        if (!is_array($decoded)) {
            return [];
        }

        $meta = $decoded['payroll_meta'] ?? $decoded;

        return is_array($meta) ? $meta : [];
    }

    /**
     * Turn a YYYY-MM period into a human readable month label.
     */
    public function formatPeriod(?string $period): string
    {
        if (!$period) {
            return '-';
        }

        try {
            return Carbon::createFromFormat('Y-m', $period)->format('F Y');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($period)->format('F Y');
            } catch (\Exception $e) {
                return (string) $period;
            }
        }
    }

    /**
     * Human readable label for an employer contribution / employee deduction.
     */
    public function labels(): array
    {
        return [
            'paye' => 'PAYE (Income Tax)',
            'nssf_employee' => 'NSSF (Employee 10%)',
            'heslb' => 'HESLB (Student Loan 15%)',
            'trade_union' => 'Trade Union Dues',
            'loan' => 'Loan Repayment',
            'other' => 'Other Voluntary Deductions',
            'nssf_employer' => 'NSSF (Employer 10%)',
            'sdl' => 'SDL (Skills & Development Levy 4.5%)',
            'wcf' => 'WCF (Workers Compensation 0.5%)',
        ];
    }
}
