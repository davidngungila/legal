<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .certificate {
            width: 100%;
            border: 6px solid #4f46e5;
            padding: 40px 50px;
            box-sizing: border-box;
            text-align: center;
        }
        .org {
            font-size: 13px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 34px;
            color: #4f46e5;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        .certified {
            font-size: 14px;
            color: #6b7280;
            margin: 24px 0 6px 0;
        }
        .name {
            font-size: 34px;
            font-weight: bold;
            color: #111827;
            margin: 6px 0;
        }
        .statement {
            font-size: 13px;
            color: #374151;
            line-height: 1.7;
            margin: 18px 0;
        }
        .details {
            margin: 24px 0;
            font-size: 13px;
            color: #374151;
        }
        .details span {
            display: inline-block;
            padding: 6px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin: 4px;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7280;
        }
        .signature-line {
            width: 220px;
            border-top: 1px solid #9ca3af;
            padding-top: 8px;
        }
        .date {
            margin-top: 30px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="org">{{ $clientName }}</div>
        <h1>Certificate of Completion</h1>
        <div class="certified">This certificate is proudly presented to</div>
        <div class="name">{{ $employee->full_name }}</div>
        <div class="statement">
            for successfully completing the training program
            <strong>{{ $program?->name }}</strong>
        </div>
        <div class="details">
            <span>{{ $session?->title }}</span>
            @if($session?->instructor)
            <span>Trainer: {{ $session->instructor }}</span>
            @endif
            @if($program?->duration_hours)
            <span>{{ $program->duration_hours }} hours</span>
            @endif
            @if($session?->start_at)
            <span>{{ $session->start_at->format('F d, Y') }}</span>
            @endif
            @if($enrollment->assessment_score)
            <span>Score: {{ $enrollment->assessment_score }}%</span>
            @endif
        </div>
        <div class="date">
            Certificate No: {{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp;
            Issued: {{ $enrollment->completed_at ? $enrollment->completed_at->format('F d, Y') : now()->format('F d, Y') }} &nbsp;|&nbsp;
            Employee ID: {{ $employee->employee_id }}
        </div>
        <div class="signatures">
            @if($session?->instructor)
            <div class="signature-line">Trainer: {{ $session->instructor }}</div>
            @endif
            <div class="signature-line">HR Manager</div>
        </div>
    </div>
</body>
</html>
