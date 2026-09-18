@php
    $employee = $data['employee'];
    $employer = $data['employer'];
    $ear = $data['earnings'];
    $ded = $data['deductions'];
    $con = $data['employer_contributions'];
    $att = $data['attendance'];
@endphp

<div class="ps-doc">
    <table class="ps-head">
        <tr>
            <td class="ps-head-left">
                <div class="ps-company">{{ $employer['name'] ?: config('app.name') }}</div>
                @if($employer['address'])
                    <div class="ps-muted">{{ $employer['address'] }}</div>
                @endif
                <div class="ps-muted">
                    @if($employer['phone'] && $employer['phone'] !== '-'){{ $employer['phone'] }}@endif
                    @if($employer['email'] && $employer['email'] !== '-') &middot; {{ $employer['email'] }}@endif
                </div>
            </td>
            <td class="ps-head-right">
                <div class="ps-title">PAYSLIP</div>
                <div class="ps-muted">{{ $data['period_label'] }}</div>
                <div class="ps-muted">Pay Date: {{ $data['pay_date'] ?: '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="ps-info">
        <tr>
            <td class="ps-info-label">Employee Name</td>
            <td class="ps-info-value">{{ $employee['name'] }}</td>
            <td class="ps-info-label">Employee ID</td>
            <td class="ps-info-value">{{ $employee['code'] }}</td>
        </tr>
        <tr>
            <td class="ps-info-label">Department</td>
            <td class="ps-info-value">{{ $employee['department'] }}</td>
            <td class="ps-info-label">Position</td>
            <td class="ps-info-value">{{ $employee['position'] }}</td>
        </tr>
        <tr>
            <td class="ps-info-label">TIN</td>
            <td class="ps-info-value">{{ $employee['tin'] }}</td>
            <td class="ps-info-label">NSSF No.</td>
            <td class="ps-info-value">{{ $employee['nssf'] }}</td>
        </tr>
        <tr>
            <td class="ps-info-label">HESLB No.</td>
            <td class="ps-info-value">{{ $employee['heslb_number'] }}</td>
            <td class="ps-info-label">Bank Account</td>
            <td class="ps-info-value">{{ $employee['bank_name'] }} {{ $employee['bank_account'] !== '-' ? '/ ' . $employee['bank_account'] : '' }}</td>
        </tr>
        <tr>
            <td class="ps-info-label">Pay Period</td>
            <td class="ps-info-value">{{ $data['period'] }}</td>
            <td class="ps-info-label">Status</td>
            <td class="ps-info-value">{{ ucfirst($data['status'] ?? '-') }}</td>
        </tr>
    </table>

    <table class="ps-grid">
        <tr>
            <td class="ps-col">
                <div class="ps-section-title">Earnings</div>
                <table class="ps-table">
                    <tr>
                        <td>Basic Salary</td>
                        <td class="ps-amount">{{ number_format($ear['basic'], 2) }}</td>
                    </tr>
                    @if($ear['allowances'] > 0)
                        <tr>
                            <td>Allowances</td>
                            <td class="ps-amount">{{ number_format($ear['allowances'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ear['bonuses'] > 0)
                        <tr>
                            <td>Bonuses</td>
                            <td class="ps-amount">{{ number_format($ear['bonuses'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ear['overtime'] > 0)
                        <tr>
                            <td>Overtime ({{ number_format($att['overtime_hours'], 2) }} hrs)</td>
                            <td class="ps-amount">{{ number_format($ear['overtime'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ear['rest_day'] > 0)
                        <tr>
                            <td>Rest Day Pay ({{ number_format($att['rest_day_hours'], 2) }} hrs)</td>
                            <td class="ps-amount">{{ number_format($ear['rest_day'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ear['public_holiday'] > 0)
                        <tr>
                            <td>Public Holiday Pay ({{ number_format($att['public_holiday_hours'], 2) }} hrs)</td>
                            <td class="ps-amount">{{ number_format($ear['public_holiday'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ear['night_allowance'] > 0)
                        <tr>
                            <td>Night Shift Allowance ({{ number_format($att['night_hours'], 2) }} hrs)</td>
                            <td class="ps-amount">{{ number_format($ear['night_allowance'], 2) }}</td>
                        </tr>
                    @endif
                    @if($att['unpaid_leave_deduction'] > 0)
                        <tr class="ps-negative">
                            <td>Unpaid Leave ({{ number_format($att['unpaid_leave_days'], 2) }} days)</td>
                            <td class="ps-amount">-{{ number_format($att['unpaid_leave_deduction'], 2) }}</td>
                        </tr>
                    @endif
                    <tr class="ps-total">
                        <td>Gross Pay</td>
                        <td class="ps-amount">{{ number_format($ear['gross'], 2) }}</td>
                    </tr>
                </table>
            </td>
            <td class="ps-col">
                <div class="ps-section-title">Deductions</div>
                <table class="ps-table">
                    <tr>
                        <td>{{ $labels['paye'] }}</td>
                        <td class="ps-amount">{{ number_format($ded['paye'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>{{ $labels['nssf_employee'] }}</td>
                        <td class="ps-amount">{{ number_format($ded['nssf_employee'], 2) }}</td>
                    </tr>
                    @if($ded['heslb'] > 0)
                        <tr>
                            <td>{{ $labels['heslb'] }}</td>
                            <td class="ps-amount">{{ number_format($ded['heslb'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ded['trade_union'] > 0)
                        <tr>
                            <td>{{ $labels['trade_union'] }}</td>
                            <td class="ps-amount">{{ number_format($ded['trade_union'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ded['loan'] > 0)
                        <tr>
                            <td>{{ $labels['loan'] }}</td>
                            <td class="ps-amount">{{ number_format($ded['loan'], 2) }}</td>
                        </tr>
                    @endif
                    @if($ded['other'] > 0)
                        <tr>
                            <td>{{ $labels['other'] }}</td>
                            <td class="ps-amount">{{ number_format($ded['other'], 2) }}</td>
                        </tr>
                    @endif
                    <tr class="ps-total">
                        <td>Total Deductions</td>
                        <td class="ps-amount">{{ number_format($ded['total'], 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="ps-net">
        <tr>
            <td class="ps-net-label">
                NET PAY
                <div class="ps-muted">Taxable Income: TZS {{ number_format($data['taxable_income'], 2) }}</div>
            </td>
            <td class="ps-net-amount">TZS {{ number_format($data['net_pay'], 2) }}</td>
        </tr>
    </table>

    @if($data['salary_hold'])
        <div class="ps-alert">
            <strong>Salary Hold:</strong> {{ $data['salary_hold_reason'] ?: 'This payslip has an active salary hold.' }}
        </div>
    @endif

    <table class="ps-info ps-employer">
        <tr>
            <td class="ps-info-label">Employer NSSF (10%)</td>
            <td class="ps-info-value">{{ number_format($con['nssf_employer'], 2) }}</td>
            <td class="ps-info-label">SDL (4.5%)</td>
            <td class="ps-info-value">{{ number_format($con['sdl'], 2) }}</td>
            <td class="ps-info-label">WCF (0.5%)</td>
            <td class="ps-info-value">{{ number_format($con['wcf'], 2) }}</td>
        </tr>
        <tr>
            <td class="ps-info-label">Total Employer Cost</td>
            <td class="ps-info-value" colspan="4">TZS {{ number_format($ear['gross'] + $con['total'], 2) }}</td>
        </tr>
    </table>

    <div class="ps-foot">
        This is a computer-generated payslip and does not require a signature.
        Generated on {{ $data['generated_at'] }}.
    </div>
</div>
