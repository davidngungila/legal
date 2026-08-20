<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .page {
            padding: 36px;
        }
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header .brand {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header .client {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-top: 4px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #6b7280;
        }
        h1 {
            font-size: 22px;
            color: #111827;
            margin: 0 0 6px;
        }
        .type-badge {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 10px;
        }
        .description {
            margin: 16px 0 24px;
            color: #374151;
            font-size: 12px;
        }
        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.meta th, table.meta td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
            text-align: left;
        }
        table.meta th {
            background: #f9fafb;
            color: #374151;
            font-weight: bold;
            width: 32%;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        table.meta td {
            color: #111827;
        }
        .notice {
            border: 1px dashed #d1d5db;
            background: #f9fafb;
            border-radius: 6px;
            padding: 12px 16px;
            color: #6b7280;
            font-size: 10px;
        }
        .footer {
            position: fixed;
            bottom: 24px;
            left: 36px;
            right: 36px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
        .signatures {
            margin-top: 40px;
        }
        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }
        .signatures td {
            width: 50%;
            padding: 4px;
        }
        .signature-line {
            border-top: 1px solid #374151;
            width: 70%;
            margin-top: 44px;
        }
        .signature-label {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="brand">LegalHR Tanzania</div>
            <div class="client">{{ $client->name ?? 'Company' }}</div>
            <div class="subtitle">Human Resources - Documents &amp; Policies</div>
        </div>

        <span class="type-badge">{{ ucfirst($document->document_type) }}</span>
        <h1>{{ $document->title }}</h1>
        <p class="description">{{ $document->description }}</p>

        <table class="meta">
            <tr>
                <th>Version</th>
                <td>{{ $document->version }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $document->category ?? 'General' }}</td>
            </tr>
            <tr>
                <th>Effective Date</th>
                <td>{{ $document->effective_date ? $document->effective_date->format('F j, Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Expiry Date</th>
                <td>{{ $document->expiry_date ? $document->expiry_date->format('F j, Y') : 'No expiry' }}</td>
            </tr>
            @if(!empty($document->tags))
            <tr>
                <th>Tags</th>
                <td>{{ implode(', ', $document->tags) }}</td>
            </tr>
            @endif
            <tr>
                <th>Issued For</th>
                <td>{{ $client->name ?? 'Company' }}</td>
            </tr>
        </table>

        <p class="notice">
            This document is the official published version and is provided for information purposes.
            Employees are expected to read and comply with the policies outlined. For questions, contact your HR department.
        </p>

        <div class="signatures">
            <table>
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Authorized Officer / Date</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">HR Department / Date</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Generated {{ now()->format('F j, Y') }} &middot; LegalHR Tanzania &middot; {{ $client->name ?? '' }}
    </div>
</body>
</html>
