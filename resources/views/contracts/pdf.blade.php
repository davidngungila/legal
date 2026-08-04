<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employment Contract - {{ $contract->contract_number }}</title>
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
        <div class="contract-no">{{ $contract->contract_number }}</div>
    </div>

    <div class="doc-reference">
        Contract No: <strong>{{ $contract->contract_number }}</strong><br>
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
                <td class="info-value">{{ $contract->employee->position ?: 'N/A' }}</td>
                <td class="info-label">Department</td>
                <td class="info-value">{{ $contract->employee->department ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Contract Type</td>
                <td class="info-value">{{ \App\Models\Contract::CONTRACT_TYPES[$contract->contract_type] ?? str_replace('_', ' ', $contract->contract_type) }}</td>
                <td class="info-label">Status</td>
                <td class="info-value">{{ \App\Models\Contract::STATUSES[$contract->status] ?? ucfirst($contract->status) }}</td>
            </tr>
            <tr>
                <td class="info-label">Commencement Date</td>
                <td class="info-value">{{ $contract->start_date->format('F j, Y') }}</td>
                <td class="info-label">Expiry Date</td>
                <td class="info-value">{{ $contract->end_date ? $contract->end_date->format('F j, Y') : 'Open-ended' }}</td>
            </tr>
            <tr>
                <td class="info-label">Probation Period</td>
                <td class="info-value">{{ $contract->probation_end_date ? 'Until ' . $contract->probation_end_date->format('F j, Y') : 'N/A' }}</td>
                <td class="info-label">Auto Renewal</td>
                <td class="info-value">{{ $contract->auto_renewal ? 'Yes' : 'No' }}</td>
            </tr>
        </table>
    </div>

    <!-- Remuneration -->
    <div class="section">
        <div class="section-title">Remuneration</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Basic Salary</td>
                <td class="info-value"><strong>{{ $contract->formatted_salary }}</strong></td>
                <td class="info-label">Payment Frequency</td>
                <td class="info-value">{{ ucfirst(str_replace('_', ' ', $contract->payment_frequency ?? 'monthly')) }}</td>
            </tr>
            <tr>
                <td class="info-label">Work Location</td>
                <td class="info-value">{{ $contract->work_location ?: 'N/A' }}</td>
                <td class="info-label">Work Schedule</td>
                <td class="info-value">{{ $contract->work_schedule ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Job Description -->
    <div class="section">
        <div class="section-title">Job Description</div>
        <div class="content-block">{{ $contract->job_description ?: 'No specific job description provided.' }}</div>
    </div>

    <!-- Responsibilities -->
    @if($contract->responsibilities)
    <div class="section">
        <div class="section-title">Key Responsibilities</div>
        <ul class="terms">
            @foreach((array) $contract->responsibilities as $responsibility)
                <li>{{ $responsibility }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Terms and Conditions -->
    @if($contract->terms_and_conditions)
    <div class="section">
        <div class="section-title">Terms and Conditions</div>
        <ul class="terms">
            @foreach((array) $contract->terms_and_conditions as $term)
                <li>{{ $term }}</li>
            @endforeach
        </ul>
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
    </div>

    <!-- Footer -->
    <div class="footer">
        {{ $currentClient->name }} - Employment Contract {{ $contract->contract_number }}
        &nbsp;&nbsp;|&nbsp;&nbsp; Page <span class="page"></span> of <span class="topage"></span>
    </div>
</body>
</html>
