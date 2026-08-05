<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employment Contracts Report - {{ $currentClient->name }}</title>
    <style>
        @page {
            margin: 16mm 14mm 18mm 14mm;
        }

        body {
            font-family: 'Helvetica', 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
        }

        .header {
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .org-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .meta {
            font-size: 9px;
            color: #6b7280;
            margin-top: 3px;
        }

        .title {
            text-align: center;
            margin: 20px 0 4px 0;
        }

        .title h1 {
            font-size: 18px;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .title p {
            font-size: 10px;
            color: #6b7280;
            margin: 4px 0 0 0;
        }

        .stats-grid {
            display: flex;
            width: 100%;
            margin: 16px 0;
            border: 1px solid #e5e7eb;
        }

        .stat-cell {
            flex: 1;
            padding: 8px 6px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
        }

        .stat-cell:last-child {
            border-right: none;
        }

        .stat-value {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .stat-label {
            font-size: 8.5px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 2px;
        }

        table.list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        table.list th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 6px 8px;
            text-align: left;
        }

        table.list td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9.5px;
        }

        table.list tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .status {
            font-size: 8.5px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-renewed { background-color: #d1fae5; color: #047857; }
        .status-draft { background-color: #e5e7eb; color: #374151; }
        .status-expired { background-color: #fee2e2; color: #991b1b; }
        .status-terminated { background-color: #fee2e2; color: #991b1b; }

        .summary {
            margin-top: 16px;
            padding: 10px 12px;
            background-color: #f8fafc;
            border-left: 3px solid #1e3a8a;
            font-size: 9.5px;
            color: #334155;
        }

        .footer {
            position: fixed;
            bottom: -16mm;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-size: 8.5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="org-name">{{ $currentClient->name }}</div>
        <div class="meta">
            {{ collect([$currentClient->address, $currentClient->city, $currentClient->country])->filter()->implode(', ') }}
            @if($currentClient->email) | {{ $currentClient->email }} @endif
        </div>
    </div>

    <div class="title">
        <h1>Employment Contracts Report</h1>
        <p>Generated {{ now()->format('F j, Y') }} &middot; {{ $contracts->count() }} contract(s)</p>
    </div>

    <div class="stats-grid">
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['active'] }}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['renewed'] }}</div>
            <div class="stat-label">Renewed</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['expiring_soon'] }}</div>
            <div class="stat-label">Expiring Soon</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['expired'] }}</div>
            <div class="stat-label">Expired</div>
        </div>
        <div class="stat-cell">
            <div class="stat-value">{{ $stats['terminated'] }}</div>
            <div class="stat-label">Terminated</div>
        </div>
    </div>

    <table class="list">
        <thead>
            <tr>
                <th>Contract #</th>
                <th>Employee</th>
                <th>Type</th>
                <th>Start</th>
                <th>Expiry</th>
                <th>Basic Salary</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
                <tr>
                    <td>{{ $contract->formatted_contract_number }}</td>
                    <td>{{ $contract->employee?->full_name ?? 'Unknown' }}<br><small>{{ $contract->job_title }}</small></td>
                    <td>{{ \App\Models\EmploymentContract::CONTRACT_TYPES[$contract->contract_type] ?? $contract->contract_type }}</td>
                    <td>{{ $contract->effective_date?->format('d M, Y') ?? 'N/A' }}</td>
                    <td>{{ $contract->expiry_date?->format('d M, Y') ?? 'Open-ended' }}</td>
                    <td>{{ $contract->formatted_basic_salary }}</td>
                    <td>
                        <span class="status status-{{ $contract->effective_status }}">{{ ucfirst($contract->effective_status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280;">No contracts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <strong>Summary:</strong> {{ $stats['active'] }} active, {{ $stats['renewed'] }} renewed, {{ $stats['expiring_soon'] }} expiring within 60 days,
        {{ $stats['expired'] }} expired, {{ $stats['terminated'] }} terminated out of {{ $stats['total'] }} total contract(s).
        Total monthly compensation across active contracts: <strong>{{ number_format($stats['total_compensation'], 2) }}</strong>.
        Renewal rate: {{ $stats['renewal_rate'] }}%. Termination rate: {{ $stats['termination_rate'] }}%.
    </div>

    <div class="footer">
        {{ $currentClient->name }} - Employment Contracts Report
        &nbsp;&nbsp;|&nbsp;&nbsp; Page <span class="page"></span> of <span class="topage"></span>
    </div>
</body>
</html>
