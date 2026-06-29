@extends('layouts.app')

@section('title', 'Timesheets - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Timesheets</h1>
            <p class="text-gray-600 mt-2">Monthly attendance summaries for all employees</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Managing timesheets for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0 items-center">
            <!-- Month Selector -->
            <form method="GET" id="monthForm" class="flex items-center space-x-2">
                <label for="monthSelect" class="text-sm font-medium text-gray-700">Select Month:</label>
                <input type="month" id="monthSelect" name="month" value="{{ $monthDate->format('Y-m') }}" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Go
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Worked Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_worked_days'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Absent Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_absent_days'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Leave Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_leave_days'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Overtime (hrs)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_overtime_hours'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Employees</label>
                <div class="relative">
                    <input type="text" id="employeeSearch" placeholder="Search by name or employee ID..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="departmentFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Show</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees</option>
                    <option value="has_absence">With Absences</option>
                    <option value="has_overtime">With Overtime</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Timesheets Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="timesheetsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worked</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime (hrs)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Night Shift (hrs)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($timesheetData as $data)
                    <tr class="timesheet-row" 
                        data-employee-name="{{ strtolower($data['employee']->first_name . ' ' . $data['employee']->last_name) }}"
                        data-employee-id="{{ strtolower($data['employee']->employee_id ?? '') }}"
                        data-department-id="{{ $data['employee']->department_id ?? '' }}"
                        data-has-absence="{{ $data['summary']->absent_days > 0 ? 'true' : 'false' }}"
                        data-has-overtime="{{ $data['summary']->overtime_hours > 0 ? 'true' : 'false' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium">
                                            {{ substr($data['employee']->first_name, 0, 1) }}{{ substr($data['employee']->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $data['employee']->first_name }} {{ $data['employee']->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $data['employee']->employee_id ?? 'ID: ' . $data['employee']->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $data['employee']->department->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $data['employee']->position->title ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $data['summary']->worked_days }} / {{ $data['summary']->total_days }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $data['summary']->absent_days > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $data['summary']->absent_days }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $data['summary']->leave_days > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $data['summary']->leave_days }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ number_format($data['summary']->overtime_hours, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ number_format($data['summary']->night_hours, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('attendance.index') }}?employee_id={{ $data['employee']->id }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="file-text" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No timesheet data available</p>
                                <p class="text-sm text-gray-600 mt-2">Select a different month or add employees to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter functionality
    function filterTimesheets() {
        const search = document.getElementById('employeeSearch').value.toLowerCase();
        const deptId = document.getElementById('departmentFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('.timesheet-row').forEach(row => {
            const nameMatch = row.dataset.employeeName.includes(search) || row.dataset.employeeId.includes(search);
            const deptMatch = !deptId || row.dataset.departmentId === deptId;
            const statusMatch = !status || 
                (status === 'has_absence' && row.dataset.hasAbsence === 'true') ||
                (status === 'has_overtime' && row.dataset.hasOvertime === 'true');
            
            if (nameMatch && deptMatch && statusMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Event listeners for filters
    document.getElementById('employeeSearch').addEventListener('input', filterTimesheets);
    document.getElementById('departmentFilter').addEventListener('change', filterTimesheets);
    document.getElementById('statusFilter').addEventListener('change', filterTimesheets);
});
</script>
@endpush
