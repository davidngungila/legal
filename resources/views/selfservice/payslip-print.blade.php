@php
    $documentData = $data;
    $documentLabels = $labels;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payslip - {{ $data['employee']['name'] }} - {{ $data['period_label'] }}</title>
    @include('selfservice.partials.payslip-styles')
</head>
<body>
    @unless($forPdf)
        <div class="ps-toolbar">
            <a href="{{ route('selfservice.payslip') }}">&larr; Back</a>
            <button type="button" onclick="window.print()">Print</button>
            <a class="primary" href="{{ route('selfservice.payslip.download', $data['id']) }}">Download PDF</a>
        </div>
    @endunless

    @include('selfservice.partials.payslip-document', ['data' => $documentData, 'labels' => $documentLabels])
</body>
</html>
