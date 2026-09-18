<style>
    * { box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        background: #eef2f7;
        color: #1e293b;
        margin: 0;
        padding: 24px 12px;
        font-size: 12px;
        line-height: 1.45;
    }
    .ps-toolbar {
        max-width: 800px;
        margin: 0 auto 16px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    .ps-toolbar a, .ps-toolbar button {
        font: inherit;
        cursor: pointer;
        border-radius: 8px;
        padding: 8px 14px;
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #334155;
        text-decoration: none;
    }
    .ps-toolbar .primary { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .ps-doc {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #dbe3ec;
        border-radius: 10px;
        padding: 28px 30px;
        page-break-inside: avoid;
    }
    .ps-head { width: 100%; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; }
    .ps-head-left { width: 60%; vertical-align: top; }
    .ps-head-right { width: 40%; vertical-align: top; text-align: right; }
    .ps-company { font-size: 18px; font-weight: bold; color: #1e293b; }
    .ps-title { font-size: 20px; font-weight: bold; letter-spacing: 3px; color: #4f46e5; }
    .ps-muted { color: #64748b; font-size: 11px; }
    .ps-info { width: 100%; border-collapse: collapse; margin-top: 14px; }
    .ps-info td { padding: 5px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
    .ps-info-label { background: #f8fafc; color: #475569; width: 18%; font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; }
    .ps-info-value { width: 32%; }
    .ps-grid { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .ps-grid > tr > td.ps-col { width: 50%; vertical-align: top; padding: 0 6px; }
    .ps-grid > tr > td.ps-col:first-child { padding-left: 0; }
    .ps-grid > tr > td.ps-col:last-child { padding-right: 0; }
    .ps-section-title {
        background: #4f46e5;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .6px;
        padding: 6px 10px;
        border-radius: 6px 6px 0 0;
    }
    .ps-table { width: 100%; border-collapse: collapse; }
    .ps-table td { padding: 5px 8px; border: 1px solid #e2e8f0; }
    .ps-amount { text-align: right; white-space: nowrap; }
    .ps-negative td { color: #b91c1c; }
    .ps-total td { background: #f1f5f9; font-weight: bold; }
    .ps-net { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .ps-net td { border: 2px solid #4f46e5; padding: 10px 14px; }
    .ps-net-label { font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
    .ps-net-amount { text-align: right; font-size: 20px; font-weight: bold; color: #4f46e5; white-space: nowrap; }
    .ps-alert { margin-top: 12px; padding: 10px 12px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 6px; }
    .ps-employer { margin-top: 16px; }
    .ps-employer .ps-info-label { width: 22%; }
    .ps-employer .ps-info-value { width: 11%; }
    .ps-foot { margin-top: 16px; text-align: center; color: #94a3b8; font-size: 10px; }
    .ps-batch .ps-doc { margin-bottom: 20px; }

    @media print {
        body { background: #fff; padding: 0; }
        .ps-toolbar { display: none !important; }
        .ps-doc { border: none; border-radius: 0; max-width: none; padding: 0; }
    }
</style>
