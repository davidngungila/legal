<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Interview Assessment</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            border-bottom: 3px solid #1e40af;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .header-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .info-value {
            color: #333;
            font-size: 11px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        
        .assessment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .assessment-item {
            background-color: #f9fafb;
            padding: 10px;
            border-left: 3px solid #1e40af;
        }
        
        .assessment-label {
            font-weight: bold;
            font-size: 10px;
            color: #555;
            margin-bottom: 5px;
        }
        
        .assessment-content {
            font-size: 10px;
            color: #333;
            white-space: pre-wrap;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-submitted {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .status-draft {
            background-color: #fef9c3;
            color: #854d0e;
        }
        
        .status-manager_approved {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .result-pass {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .result-fail {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .result-na {
            background-color: #f3f4f6;
            color: #4b5563;
        }
        
        .footer {
            position: fixed;
            bottom: -20mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
        }
        
        .signature-section {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }
        
        .signature-box {
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }
        
        .signature-label {
            font-size: 10px;
            color: #555;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .signature-date {
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">Technical Interview Assessment</div>
        <div class="header-subtitle">Orvion HRIS - Candidate Evaluation Report</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">Basic Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Interview Number</span>
                <span class="info-value">{{ $technicalInterview->interview_number }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="status-badge status-{{ $technicalInterview->status }}">
                    {{ ucfirst(str_replace('_', ' ', $technicalInterview->status)) }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Candidate Name</span>
                <span class="info-value">{{ $technicalInterview->candidate_name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Job Title</span>
                <span class="info-value">{{ $technicalInterview->job_title }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Interview Date</span>
                <span class="info-value">{{ $technicalInterview->interview_date ? $technicalInterview->interview_date->format('d M, Y') : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Interviewer</span>
                <span class="info-value">{{ $technicalInterview->interviewer_name }}</span>
            </div>
        </div>
    </div>

    <!-- Technical Assessment -->
    <div class="section">
        <div class="section-title">Technical Assessment Areas</div>
        <div class="assessment-grid">
            <div class="assessment-item">
                <div class="assessment-label">Business Process Knowledge</div>
                <div class="assessment-content">{{ $technicalInterview->business_process_knowledge ?? 'N/A' }}</div>
            </div>
            <div class="assessment-item">
                <div class="assessment-label">Technical Skills Assessment</div>
                <div class="assessment-content">{{ $technicalInterview->technical_skills_assessment ?? 'N/A' }}</div>
            </div>
            <div class="assessment-item">
                <div class="assessment-label">Physical Capabilities</div>
                <div class="assessment-content">{{ $technicalInterview->physical_capabilities ?? 'N/A' }}</div>
            </div>
            <div class="assessment-item">
                <div class="assessment-label">Practical Test Results</div>
                <div class="assessment-content">{{ $technicalInterview->practical_test_results ?? 'N/A' }}</div>
            </div>
            @if($technicalInterview->other_technical_areas)
            <div class="assessment-item" style="grid-column: span 2;">
                <div class="assessment-label">Other Technical Areas</div>
                <div class="assessment-content">{{ $technicalInterview->other_technical_areas }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Assessment Results -->
    <div class="section">
        <div class="section-title">Assessment Results</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Technical Result</span>
                <span class="status-badge result-{{ $technicalInterview->technical_result }}">
                    {{ ucfirst($technicalInterview->technical_result ?? 'N/A') }}
                </span>
            </div>
            @if($technicalInterview->technical_comments)
            <div class="info-item">
                <span class="info-label">Technical Comments</span>
                <span class="info-value">{{ $technicalInterview->technical_comments }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Manager Approval -->
    @if($technicalInterview->manager_approval)
    <div class="section">
        <div class="section-title">Department Manager Approval</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Approval Status</span>
                <span class="info-value">{{ ucfirst($technicalInterview->manager_approval) }}</span>
            </div>
            @if($technicalInterview->departmentManager)
            <div class="info-item">
                <span class="info-label">Approved By</span>
                <span class="info-value">{{ $technicalInterview->departmentManager->name }}</span>
            </div>
            @endif
            @if($technicalInterview->manager_approved_at)
            <div class="info-item">
                <span class="info-label">Approval Date</span>
                <span class="info-value">{{ $technicalInterview->manager_approved_at->format('d M, Y H:i') }}</span>
            </div>
            @endif
            @if($technicalInterview->manager_comments)
            <div class="info-item">
                <span class="info-label">Manager Comments</span>
                <span class="info-value">{{ $technicalInterview->manager_comments }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            @if($technicalInterview->interviewer_signature_path)
                <img src="{{ Storage::url($technicalInterview->interviewer_signature_path) }}" alt="Interviewer Signature" style="max-height: 45px; margin-bottom: 4px;">
            @endif
            <div class="signature-name">{{ $technicalInterview->interviewer_name }}</div>
            <div class="signature-label">Technical Interviewer</div>
            @if($technicalInterview->interviewer_completed_at)
            <div class="signature-date">Signed: {{ $technicalInterview->interviewer_completed_at->format('d M, Y') }}</div>
            @endif
        </div>
        @if($technicalInterview->departmentManager)
        <div class="signature-box">
            <div class="signature-name">{{ $technicalInterview->departmentManager->name }}</div>
            <div class="signature-label">Department Manager</div>
            @if($technicalInterview->manager_approved_at)
            <div class="signature-date">Approved: {{ $technicalInterview->manager_approved_at->format('d M, Y') }}</div>
            @endif
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <span>Orvion HRIS - Technical Interview Assessment</span>
            <span>Generated: {{ now()->format('d M, Y H:i') }}</span>
            <span>Page <span class="page"></span> of <span class="topage"></span></span>
        </div>
    </div>
</body>
</html>
