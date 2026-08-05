<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employment Contract - {{ $contract->formatted_contract_number }}</title>
    <style>
        @page {
            margin: 18mm 15mm 22mm 15mm;
        }

        body {
            font-family: 'Helvetica', 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.55;
            color: #1f2937;
        }

        .org-header {
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            position: relative;
        }

        .org-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }

        .org-meta {
            font-size: 9.5px;
            color: #6b7280;
            margin-top: 3px;
        }

        .contract-title {
            text-align: center;
            margin: 28px 0 6px 0;
        }

        .contract-title h1 {
            font-size: 24px;
            color: #1e3a8a;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .contract-title .contract-no {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            letter-spacing: 2px;
        }

        .doc-reference {
            text-align: right;
            font-size: 9.5px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #ffffff;
            background-color: #1e3a8a;
            padding: 6px 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
            width: 30%;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .info-value {
            color: #111827;
            font-size: 11px;
        }

        .content-block {
            background-color: #f8fafc;
            border-left: 3px solid #1e3a8a;
            padding: 10px 14px;
            font-size: 10.5px;
            color: #334155;
            white-space: pre-line;
        }

        ul.terms {
            margin: 0;
            padding-left: 16px;
        }

        ul.terms li {
            margin-bottom: 6px;
            font-size: 10.5px;
            color: #334155;
        }

        .signature-section {
            margin-top: 42px;
            width: 100%;
        }

        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-section td {
            width: 50%;
            vertical-align: bottom;
            padding: 0 16px;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 8px;
            margin-top: 60px;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11px;
        }

        .signature-role {
            font-size: 9.5px;
            color: #6b7280;
        }

        .signature-date {
            font-size: 9.5px;
            color: #6b7280;
            margin-top: 2px;
        }

        .closing {
            margin-top: 24px;
            font-size: 10.5px;
            color: #334155;
        }

        .footer {
            position: fixed;
            bottom: -20mm;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <!-- Organisation Header -->
    <div class="org-header">
        <div class="org-name">{{ $currentClient->name }}</div>
        <div class="org-meta">
            {{ collect([$currentClient->address, $currentClient->city, $currentClient->country])->filter()->implode(', ') }}
            @if($currentClient->phone) | {{ $currentClient->phone }} @endif
            @if($currentClient->email) | {{ $currentClient->email }} @endif
        </div>
    </div>

    <!-- Document Title -->
    <div class="contract-title">
        <h1>Employment Contract</h1>
        <div class="contract-no">{{ $contract->formatted_contract_number }}</div>
    </div>

    <div class="doc-reference">
        Contract No: <strong>{{ $contract->formatted_contract_number }}</strong><br>
        Date: <strong>{{ now()->format('F j, Y') }}</strong>
    </div>

    <!-- Parties -->
    <div class="section">
        <div class="section-title">Parties to the Agreement</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Employer</td>
                <td class="info-value">
                    <strong>{{ $currentClient->name }}</strong>
                    @if($currentClient->address || $currentClient->city)
                        <br>{{ collect([$currentClient->address, $currentClient->city, $currentClient->country])->filter()->implode(', ') }}
                    @endif
                    @if($currentClient->contact_person)
                        <br>Contact Person: {{ $currentClient->contact_person }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="info-label">Employee</td>
                <td class="info-value">
                    <strong>{{ $contract->employee->full_name }}</strong>
                    @if($contract->employee->employee_id)
                        <br>Employee ID: {{ $contract->employee->employee_id }}
                    @endif
                    @if($contract->employee->email)
                        <br>Email: {{ $contract->employee->email }}
                    @endif
                    @if($contract->employee->phone)
                        <br>Phone: {{ $contract->employee->phone }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Position & Terms -->
    <div class="section">
        <div class="section-title">Position and Terms of Employment</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Position</td>
                <td class="info-value">{{ $contract->job_title ?: 'N/A' }}</td>
                <td class="info-label">Department</td>
                <td class="info-value">{{ $contract->department ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Contract Type</td>
                <td class="info-value">{{ \App\Models\EmploymentContract::CONTRACT_TYPES[$contract->contract_type] ?? str_replace('_', ' ', $contract->contract_type) }}</td>
                <td class="info-label">Status</td>
                <td class="info-value">{{ \App\Models\EmploymentContract::STATUSES[$contract->effective_status] ?? ucfirst($contract->effective_status) }}</td>
            </tr>
            <tr>
                <td class="info-label">Commencement Date</td>
                <td class="info-value">{{ $contract->effective_date?->format('F j, Y') ?? 'N/A' }}</td>
                <td class="info-label">Expiry Date</td>
                <td class="info-value">{{ $contract->expiry_date ? $contract->expiry_date->format('F j, Y') : 'Open-ended' }}</td>
            </tr>
            <tr>
                <td class="info-label">Probation Period</td>
                <td class="info-value">{{ $contract->probation_end_date ? 'Until ' . $contract->probation_end_date->format('F j, Y') : 'N/A' }}</td>
                <td class="info-label">Reporting Line</td>
                <td class="info-value">{{ $contract->reporting_line ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Work Location</td>
                <td class="info-value">{{ $contract->work_location ?: 'N/A' }}</td>
                <td class="info-label">Work Schedule</td>
                <td class="info-value">{{ $contract->work_schedule ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Remuneration -->
    <div class="section">
        <div class="section-title">Remuneration</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Basic Salary</td>
                <td class="info-value"><strong>{{ $contract->formatted_basic_salary }}</strong></td>
                <td class="info-label">Total Compensation</td>
                <td class="info-value"><strong>{{ $contract->formatted_total_compensation }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Housing Allowance</td>
                <td class="info-value">{{ number_format((float) $contract->housing_allowance, 2) }} {{ $contract->salary_currency }}</td>
                <td class="info-label">Transport Allowance</td>
                <td class="info-value">{{ number_format((float) $contract->transport_allowance, 2) }} {{ $contract->salary_currency }}</td>
            </tr>
            <tr>
                <td class="info-label">Meal Allowance</td>
                <td class="info-value">{{ number_format((float) $contract->meal_allowance, 2) }} {{ $contract->salary_currency }}</td>
                <td class="info-label">Other Allowances</td>
                <td class="info-value">{{ number_format((float) $contract->other_allowances, 2) }} {{ $contract->salary_currency }}</td>
            </tr>
            <tr>
                <td class="info-label">Payment Frequency</td>
                <td class="info-value">{{ \App\Models\EmploymentContract::PAYMENT_FREQUENCIES[$contract->payment_frequency] ?? ucfirst($contract->payment_frequency) }}</td>
                <td class="info-label">Payment Method</td>
                <td class="info-value">{{ $contract->payment_method ? ucfirst(str_replace('_', ' ', $contract->payment_method)) : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Leave & Benefits -->
    <div class="section">
        <div class="section-title">Leave and Benefits</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Annual Leave</td>
                <td class="info-value">{{ $contract->leave_entitlement_days }} days</td>
                <td class="info-label">Sick Leave</td>
                <td class="info-value">{{ $contract->sick_leave_days }} days</td>
            </tr>
            <tr>
                <td class="info-label">Public Holidays</td>
                <td class="info-value">{{ $contract->public_holidays }} days</td>
                <td class="info-label">Maternity / Paternity Leave</td>
                <td class="info-value">{{ $contract->maternity_leave_weeks }}w / {{ $contract->paternity_leave_weeks }}w</td>
            </tr>
            <tr>
                <td class="info-label">Notice Period</td>
                <td class="info-value">{{ $contract->notice_period_days }} days</td>
                <td class="info-label">Working Hours</td>
                <td class="info-value">{{ $contract->working_hours_per_week }} hrs/week @if($contract->overtime_rate) (overtime x{{ $contract->overtime_rate }}) @endif</td>
            </tr>
        </table>
        @if($contract->benefits_package)
            <div class="content-block" style="margin-top: 10px;">{{ $contract->benefits_package }}</div>
        @endif
    </div>

    <!-- Contractual Clauses -->
    @if($contract->confidentiality_clause || $contract->non_compete_clause || $contract->intellectual_property_clause
        || $contract->data_protection_clause || $contract->health_and_safety_clause || $contract->training_development_clause
        || $contract->company_policies_acknowledgment)
    <div class="section">
        <div class="section-title">Contractual Clauses</div>
        <ul class="terms">
            @if($contract->confidentiality_clause)
                <li>The Employee agrees to maintain confidentiality of all proprietary and business information of the Employer during and after employment.</li>
            @endif
            @if($contract->non_compete_clause)
                <li>
                    Non-Compete: The Employee shall not engage in competing business for
                    {{ $contract->non_compete_duration_months ?: 'a specified' }} month(s) after termination
                    @if($contract->non_compete_restriction) within {{ $contract->non_compete_restriction }} @endif.
                </li>
            @endif
            @if($contract->intellectual_property_clause)
                <li>Intellectual property created during the course of employment shall be assigned to the Employer.</li>
            @endif
            @if($contract->data_protection_clause)
                <li>The Employee shall comply with applicable data protection regulations when processing personal data.</li>
            @endif
            @if($contract->health_and_safety_clause)
                <li>The Employee shall comply with all health and safety policies and regulations of the workplace.</li>
            @endif
            @if($contract->training_development_clause)
                <li>The Employee may be entitled to training and development opportunities per company policy.</li>
            @endif
            @if($contract->company_policies_acknowledgment)
                <li>The Employee acknowledges receipt of and agrees to comply with all company policies and procedures.</li>
            @endif
        </ul>
    </div>
    @endif

    @if($contract->termination_clause)
    <div class="section">
        <div class="section-title">Termination</div>
        <div class="content-block">{{ $contract->termination_clause }}</div>
    </div>
    @endif

    @if($contract->grievance_procedure)
    <div class="section">
        <div class="section-title">Grievance Procedure</div>
        <div class="content-block">{{ $contract->grievance_procedure }}</div>
    </div>
    @endif

    @if($contract->disciplinary_procedure)
    <div class="section">
        <div class="section-title">Disciplinary Procedure</div>
        <div class="content-block">{{ $contract->disciplinary_procedure }}</div>
    </div>
    @endif

    <!-- Closing -->
    <div class="closing">
        This agreement sets out the terms and conditions of employment between the parties named above.
        By signing below, the parties acknowledge that they have read, understood and agreed to be bound by
        the terms of this contract.
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <table>
            <tr>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">{{ $contract->employee->full_name }}</div>
                        <div class="signature-role">Employee</div>
                        <div class="signature-date">{{ $contract->signed_at ? 'Signed: ' . $contract->signed_at->format('F j, Y') : 'Date: ______________' }}</div>
                    </div>
                </td>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">{{ $currentClient->contact_person ?: $currentClient->name }}</div>
                        <div class="signature-role">Authorized Representative - {{ $currentClient->name }}</div>
                        <div class="signature-date">Date: ______________</div>
                    </div>
                </td>
            </tr>
        </table>
        @if($contract->witness_name)
        <table style="margin-top: 40px;">
            <tr>
                <td>
                    <div class="signature-line">
                        <div class="signature-name">{{ $contract->witness_name }}</div>
                        <div class="signature-role">Witness{{ $contract->witness_title ? ' - ' . $contract->witness_title : '' }}</div>
                        <div class="signature-date">Date: ______________</div>
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        {{ $currentClient->name }} - Employment Contract {{ $contract->formatted_contract_number }}
        &nbsp;&nbsp;|&nbsp;&nbsp; Page <span class="page"></span> of <span class="topage"></span>
    </div>
</body>
</html>
