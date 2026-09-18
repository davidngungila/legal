@php
    $documentLabels = $labels;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payslips - {{ $employee->first_name }} {{ $employee->last_name }}</title>
    @include('selfservice.partials.payslip-styles')
</head>
<body class="ps-batch">
    @foreach($rows as $row)
        @include('selfservice.partials.payslip-document', ['data' => $row, 'labels' => $documentLabels])
        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>
