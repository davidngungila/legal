@extends('layouts.app')

@section('title', 'Payroll Reports - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Payroll Reports</h1>
            <p class="text-gray-600 mt-2">Comprehensive payroll analytics and reports</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Viewing reports for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex flex-wrap gap-3 lg:justify-end">
            @if(Auth::user()->hasPermission('reports.export'))
                <button onclick="exportReport('csv')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i>
                    Export CSV
                </button>
                <button onclick="exportReport('pdf')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center justify-center">
                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                    Export PDF
                </button>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalEmployees) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Gross Pay</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalGrossPay, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Net Pay</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalNetPay, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Deductions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalDeductions, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="minus-circle" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Breakdown Cards -->
    @if(Auth::user()->hasPermission('payroll.view') || Auth::user()->hasPermission('reports.view'))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">PAYE Contributions</h3>
            <div class="text-3xl font-bold text-blue-600 mb-2">TZS {{ number_format($totalPAYE, 2) }}</div>
            <p class="text-sm text-gray-600">Total PAYE tax deductions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">NSSF Contributions</h3>
            <div class="text-3xl font-bold text-green-600 mb-2">TZS {{ number_format($totalNSSF, 2) }}</div>
            <p class="text-sm text-gray-600">Total employee NSSF deductions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employer Pension</h3>
            <div class="text-3xl font-bold text-purple-600 mb-2">TZS {{ number_format($totalPension, 2) }}</div>
            <p class="text-sm text-gray-600">Total employer pension contributions</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Department-wise Summary -->
        @if(Auth::user()->hasPermission('payroll.view') || Auth::user()->hasPermission('reports.view'))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Department-wise Summary</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left text-sm font-medium text-gray-500 py-3">Department</th>
                            <th class="text-center text-sm font-medium text-gray-500 py-3">Employees</th>
                            <th class="text-right text-sm font-medium text-gray-500 py-3">Total Gross</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($departmentSummary) > 0)
                            @foreach($departmentSummary as $dept => $data)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 text-gray-900">{{ $dept }}</td>
                                <td class="py-3 text-center text-gray-700">{{ number_format($data['total_employees']) }}</td>
                                <td class="py-3 text-right text-gray-700 font-medium">TZS {{ number_format($data['total_gross'], 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-500">No department data available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payroll Periods -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payroll Periods</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left text-sm font-medium text-gray-500 py-3">Period</th>
                            <th class="text-right text-sm font-medium text-gray-500 py-3">Total Gross</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($periods) > 0)
                            @foreach($periods->reverse() as $period)
                            @php
                                $periodGross = $payrolls->where('payroll_period', $period)->sum('gross_pay');
                            @endphp
                            <tr class="border-b border-gray-100">
                                <td class="py-3 text-gray-900">{{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') }}</td>
                                <td class="py-3 text-right text-gray-700 font-medium">TZS {{ number_format($periodGross, 2) }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2" class="py-8 text-center text-gray-500">No payroll periods available</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Latest Period Payroll Details -->
    @if(Auth::user()->hasPermission('payroll.view'))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            {{ $latestPeriod ? \Carbon\Carbon::createFromFormat('Y-m', $latestPeriod)->format('F Y') : 'Latest Period' }} Payroll Details
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left text-sm font-medium text-gray-500 py-3">Employee</th>
                        <th class="text-right text-sm font-medium text-gray-500 py-3">Basic Salary</th>
                        <th class="text-right text-sm font-medium text-gray-500 py-3">Allowances</th>
                        <th class="text-right text-sm font-medium text-gray-500 py-3">Gross Pay</th>
                        <th class="text-right text-sm font-medium text-gray-500 py-3">Deductions</th>
                        <th class="text-right text-sm font-medium text-gray-500 py-3">Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($latestPeriodPayrolls) > 0)
                        @foreach($latestPeriodPayrolls as $payroll)
                        <tr class="border-b border-gray-100">
                            <td class="py-3">
                                <p class="text-gray-900 font-medium">{{ optional($payroll->employee)->first_name ?? 'Unknown' }} {{ optional($payroll->employee)->last_name ?? 'Employee' }}</p>
                                <p class="text-xs text-gray-500">{{ optional($payroll->employee)->employee_id ?? '-' }}</p>
                            </td>
                            <td class="py-3 text-right text-gray-700">TZS {{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="py-3 text-right text-gray-700">TZS {{ number_format($payroll->allowances, 2) }}</td>
                            <td class="py-3 text-right text-gray-900 font-medium">TZS {{ number_format($payroll->gross_pay, 2) }}</td>
                            <td class="py-3 text-right text-gray-700">TZS {{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="py-3 text-right text-gray-900 font-semibold text-green-600">TZS {{ number_format($payroll->net_pay, 2) }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">No payroll data for latest period</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payroll Audit Trail (Admin Only) -->
    @if(Auth::user()->hasPermission('payroll.audit') || Auth::user()->hasRole('super_admin'))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i data-feather="shield" class="w-5 h-5 mr-2"></i>
            Payroll Audit Trail
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left text-sm font-medium text-gray-500 py-3">Date</th>
                        <th class="text-left text-sm font-medium text-gray-500 py-3">Employee</th>
                        <th class="text-left text-sm font-medium text-gray-500 py-3">Period</th>
                        <th class="text-left text-sm font-medium text-gray-500 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($payrolls) > 0)
                        @foreach($payrolls->take(10) as $payroll)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-700">{{ optional($payroll->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="py-3 text-gray-900">{{ optional($payroll->employee)->first_name ?? 'Unknown' }} {{ optional($payroll->employee)->last_name ?? 'Employee' }}</td>
                            <td class="py-3 text-gray-700">{{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->payroll_period)->format('F Y') }}</td>
                            <td class="py-3">{!! $payroll->status_badge ?? '' !!}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">No audit trail available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
    function exportReport(type) {
        if (type === 'csv') {
            // Create CSV content
            let csvContent = 'Employee,Period,Basic Salary,Allowances,Gross Pay,Deductions,Net Pay\n';
            
            @foreach($payrolls as $payroll)
                csvContent += '{{ optional($payroll->employee)->first_name ?? 'Unknown' }} {{ optional($payroll->employee)->last_name ?? '' }},{{ $payroll->payroll_period }},{{ $payroll->basic_salary }},{{ $payroll->allowances }},{{ $payroll->gross_pay }},{{ $payroll->total_deductions }},{{ $payroll->net_pay }}\n';
            @endforeach
            
            // Download CSV
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'payroll_report.csv';
            link.click();
            showNotification('CSV report exported successfully!', 'success');
        } else if (type === 'pdf') {
            showNotification('PDF export coming soon!', 'info');
        }
    }
</script>
@endsection
