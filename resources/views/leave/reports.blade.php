@extends('layouts.app')

@section('title', 'Leave Reports - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Leave Reports</h1>
            <p class="text-gray-600 mt-2">Analyze leave trends, utilization, and patterns</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="mt-4 md:mt-0">
            <button type="button" onclick="exportCSV()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i data-feather="download" class="w-4 h-4 inline mr-1"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" action="{{ route('leave.reports') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">From</label>
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">To</label>
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Employee</label>
                <select name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Leave Type</label>
                <select name="leave_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">All Leave Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->type_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="filter" class="w-4 h-4 inline mr-1"></i>Apply
                </button>
                <a href="{{ route('leave.reports') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total'] }}</p>
            <p class="text-sm text-gray-500">Total Requests</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['approved'] }}</p>
            <p class="text-sm text-gray-500">Approved</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['pending'] }}</p>
            <p class="text-sm text-gray-500">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['rejected'] }}</p>
            <p class="text-sm text-gray-500">Rejected</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_days'], 1) }}</p>
            <p class="text-sm text-gray-500">Total Days</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-5 h-5 text-teal-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['approved_days'], 1) }}</p>
            <p class="text-sm text-gray-500">Approved Days</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Leave by Type</h3>
            <p class="text-sm text-gray-500 mb-4">Distribution of requests</p>
            <div class="relative h-64">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Monthly Trend</h3>
            <p class="text-sm text-gray-500 mb-4">Requests per month</p>
            <div class="relative h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">By Department</h3>
            <p class="text-sm text-gray-500 mb-4">Requests per department</p>
            <div class="relative h-64">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Employee Utilization -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Employee Utilization</h3>
                <p class="text-sm text-gray-500">Leave consumption by employee</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved Days</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employeeSummary as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ strtoupper(substr($row['name'], 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $row['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['employee_code'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['department'] ?: 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row['total_requests'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $row['approved_requests'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">{{ $row['pending_requests'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($row['total_days'], 1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">{{ number_format($row['approved_days'], 1) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">No leave data available for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Leave Requests</h3>
                <p class="text-sm text-gray-500">{{ $from->format('M d, Y') }} - {{ $to->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="requestsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $request->employee->first_name ?? '' }} {{ $request->employee->last_name ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->leaveType->type_name ?? $request->getOriginal('leave_type') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->days }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($request->status == 'approved') bg-green-100 text-green-800
                                @elseif($request->status == 'pending') bg-amber-100 text-amber-800
                                @elseif($request->status == 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">{{ ucfirst($request->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $request->reason ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">No leave requests found for the selected filters.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const byTypeData = @json($byType);
    const monthlyData = @json($monthly);
    const byDepartmentData = @json($byDepartment);
    const requestsData = @json($requestsData);

    const CHART_COLORS = ['#6366f1', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#3b82f6', '#10b981'];

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }
        Chart.helpers.each(Chart.instances, function(instance) { instance.destroy(); });

        // Leave by Type (doughnut)
        const typeCtx = document.getElementById('typeChart');
        if (typeCtx) {
            const labels = Object.keys(byTypeData);
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: labels.map(l => byTypeData[l].count),
                        backgroundColor: CHART_COLORS,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // Monthly trend (bar)
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            const labels = Object.keys(monthlyData);
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Approved',
                            data: labels.map(l => monthlyData[l].approved),
                            backgroundColor: '#10b981',
                            borderRadius: 4
                        },
                        {
                            label: 'Pending',
                            data: labels.map(l => monthlyData[l].pending),
                            backgroundColor: '#f59e0b',
                            borderRadius: 4
                        },
                        {
                            label: 'Rejected',
                            data: labels.map(l => monthlyData[l].rejected),
                            backgroundColor: '#ef4444',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true, ticks: { font: { size: 10 } } },
                        y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } }
                    }
                }
            });
        }

        // By department (horizontal bar)
        const deptCtx = document.getElementById('departmentChart');
        if (deptCtx) {
            const labels = Object.keys(byDepartmentData);
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Requests',
                        data: labels.map(l => byDepartmentData[l].count),
                        backgroundColor: labels.map((_, i) => CHART_COLORS[i % CHART_COLORS.length]),
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                        y: { ticks: { font: { size: 10 } } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterLabel: (ctx) => {
                                    const l = labels[ctx.dataIndex];
                                    const d = byDepartmentData[l];
                                    return 'Days: ' + d.days + '\nApproved Days: ' + d.approved_days;
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    window.exportCSV = function() {
        if (!requestsData.length) {
            showNotification('No data to export', 'warning');
            return;
        }
        const headers = ['Employee', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status', 'Reason'];
        const rows = requestsData.map(r => [
            r.employee, r.leave_type, r.start, r.end, r.days, r.status, r.reason || ''
        ]);
        const csv = [headers, ...rows]
            .map(row => row.map(cell => '"' + String(cell).replace(/"/g, '""') + '"').join(','))
            .join('\n');
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'leave-reports.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showNotification('Report exported successfully', 'success');
    };
})();
</script>
@endsection
