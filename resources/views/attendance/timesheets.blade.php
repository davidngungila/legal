@extends('layouts.app')

@section('title', 'Timesheets - LegalHR Tanzania')

@section('content')
<div class="p-6" id="timesheetsPage" data-month="{{ $monthDate->format('Y-m') }}">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Timesheets</h1>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">{{ $monthLabel }}</span>
                @if($currentClient)
                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
                @endif
            </div>
            <p class="text-gray-600 mt-2">Monthly attendance summaries computed live from attendance records for all employees</p>
        </div>
    </div>

    <!-- Filters + Month Navigation -->
    <form method="GET" action="{{ route('attendance.timesheets') }}" id="timesheetFilterForm"
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6" data-no-transition>
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" data-month-step="-1" title="Previous month"
                    class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                    <i data-feather="chevron-left" class="w-4 h-4"></i>
                </button>
                <input type="month" name="month" id="monthInput" value="{{ $monthDate->format('Y-m') }}"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" data-current-month="{{ $monthDate->format('Y-m') }}">
                <button type="button" data-month-step="1" title="Next month"
                    class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600 transition-colors">
                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                </button>
                <button type="button" data-month-today
                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    This Month
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                    Apply Filters
                </button>
                <a href="{{ route('attendance.timesheets.export', request()->query()) }}" data-no-transition
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Name or Employee ID" autocomplete="off"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>
            <div>
                <label for="departmentFilter" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="departmentFilter" name="department" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->name }}" {{ request('department') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Attendance</label>
                <select id="statusFilter" name="status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Attendance Statuses</option>
                    <option value="has_absence" {{ request('status') === 'has_absence' ? 'selected' : '' }}>With Absences</option>
                    <option value="has_leave" {{ request('status') === 'has_leave' ? 'selected' : '' }}>On Leave (any)</option>
                    <option value="has_late" {{ request('status') === 'has_late' ? 'selected' : '' }}>Late Arrivals</option>
                    <option value="has_overtime" {{ request('status') === 'has_overtime' ? 'selected' : '' }}>With Overtime</option>
                    <option value="low_attendance" {{ request('status') === 'low_attendance' ? 'selected' : '' }}>Attendance &lt; 90%</option>
                    <option value="no_records" {{ request('status') === 'no_records' ? 'selected' : '' }}>No Records This Month</option>
                </select>
            </div>
            <div>
                <label for="employmentStatusFilter" class="block text-sm font-medium text-gray-700 mb-2">Employee Status</label>
                <select id="employmentStatusFilter" name="employment_status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employee Statuses</option>
                    @php($empStatuses = $employees->pluck('status')->filter()->unique()->sort()->values())
                    @foreach($empStatuses as $empStatus)
                        <option value="{{ $empStatus }}" {{ request('employment_status') === $empStatus ? 'selected' : '' }}>{{ ucfirst($empStatus) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            @if(request()->hasAny(['search', 'department', 'status', 'employment_status']))
            <a href="{{ route('attendance.timesheets') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Clear all filters</a>
            @else
            <span></span>
            @endif
            <span id="resultCount" class="text-sm text-gray-500"></span>
        </div>
    </form>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] }}</p>
                </div>
                <div class="w-11 h-11 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $monthLabel }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Worked Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_worked_days'] }}</p>
                </div>
                <div class="w-11 h-11 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <p class="text-xs {{ $stats['employees_with_absence'] > 0 ? 'text-red-400' : 'text-gray-400' }} mt-2">
                {{ $stats['employees_with_absence'] }} with absences
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Absent Days</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['total_absent_days'] }}</p>
                </div>
                <div class="w-11 h-11 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Unauthorised absence</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Leave Days</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_leave_days'] }}</p>
                </div>
                <div class="w-11 h-11 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-5 h-5 text-yellow-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">AL / SLF / SLH / UL</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Late Arrivals</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['total_late_days'] }}</p>
                </div>
                <div class="w-11 h-11 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-circle" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Days clocked in late</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Attendance Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['attendance_rate'], 1) }}%</p>
                </div>
                <div class="w-11 h-11 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-5 h-5 text-teal-600"></i>
                </div>
            </div>
            <p class="text-xs {{ $stats['employees_with_low_attendance'] > 0 ? 'text-red-400' : 'text-gray-400' }} mt-2">
                {{ $stats['employees_with_low_attendance'] }} below 90%
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Overtime</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_overtime_hours'], 1) }}<span class="text-sm text-gray-400 font-normal"> hrs</span></p>
                </div>
                <div class="w-11 h-11 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ number_format($stats['total_paid_hours'], 1) }} total paid hrs</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Night Hours</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_night_hours'], 1) }}<span class="text-sm text-gray-400 font-normal"> hrs</span></p>
                </div>
                <div class="w-11 h-11 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="moon" class="w-5 h-5 text-indigo-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">20:00 - 06:00 window</p>
        </div>
    </div>

    <!-- Timesheet Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-semibold text-gray-900">{{ $monthLabel }} Overview</h2>
            <span class="text-xs text-gray-500">Click a column header to sort · use page size to paginate</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="timesheetsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th data-sort="name" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Employee</th>
                        <th data-sort="department" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Department</th>
                        <th data-sort="position" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Position</th>
                        <th data-sort="worked" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Worked</th>
                        <th data-sort="absent" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Absent</th>
                        <th data-sort="leave" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Leave</th>
                        <th data-sort="late" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Late</th>
                        <th data-sort="rate" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Rate</th>
                        <th data-sort="hours" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Paid Hrs</th>
                        <th data-sort="overtime" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">OT</th>
                        <th data-sort="night" class="sortable px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer select-none">Night</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="timesheetBody">
                    @php($i = 0)
                    @forelse($timesheetData as $row)
                    @php($e = $row['employee'])
                    @php($lb = $row['leave_breakdown'])
                    <tr class="ts-row hover:bg-indigo-50/40 transition-colors"
                        data-sort-name="{{ strtolower(trim($e->first_name . ' ' . $e->last_name)) }}"
                        data-sort-department="{{ strtolower($e->department ?? '') }}"
                        data-sort-position="{{ strtolower($e->position ?? '') }}"
                        data-sort-worked="{{ $row['worked_days'] }}"
                        data-sort-absent="{{ $row['absent_days'] }}"
                        data-sort-leave="{{ $row['leave_days'] }}"
                        data-sort-late="{{ $row['late_days'] }}"
                        data-sort-rate="{{ $row['attendance_rate'] ?? -1 }}"
                        data-sort-hours="{{ $row['total_paid_hours'] }}"
                        data-sort-overtime="{{ $row['overtime_hours'] }}"
                        data-sort-night="{{ $row['night_hours'] }}"
                        style="{{ $i++ >= 15 ? 'display: none;' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium">
                                        {{ substr($e->first_name ?? '?', 0, 1) }}{{ substr($e->last_name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $e->first_name }} {{ $e->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $e->employee_id ?: 'ID: ' . $e->id }} · {{ ucfirst($e->status ?? 'unknown') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $e->department ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $e->position ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $row['worked_days'] }} / {{ $row['total_days'] }}
                            </span>
                            @if($row['recorded_days'] === 0)
                            <p class="text-[11px] text-gray-400 mt-0.5">No records</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row['absent_days'] > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $row['absent_days'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row['leave_days'] > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}"
                                @if($row['leave_days'] > 0)
                                title="AL {{ $lb['AL'] }} · SLF {{ $lb['SLF'] }} · SLH {{ $lb['SLH'] }} · UL {{ $lb['UL'] }}"
                                @endif>
                                {{ $row['leave_days'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $row['late_days'] > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $row['late_days'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($row['attendance_rate'] === null)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">—</span>
                            @elseif($row['attendance_rate'] >= 95)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ number_format($row['attendance_rate'], 1) }}%</span>
                            @elseif($row['attendance_rate'] >= 90)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">{{ number_format($row['attendance_rate'], 1) }}%</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ number_format($row['attendance_rate'], 1) }}%</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ number_format($row['total_paid_hours'], 2) }}</div>
                            <div class="text-[11px] text-gray-400">Ord {{ number_format($row['ordinary_hours'], 2) }} · Rest {{ number_format($row['rest_day_hours'], 2) }} · PH {{ number_format($row['ph_hours'], 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $row['overtime_hours'] > 0 ? 'font-semibold text-purple-700' : 'text-gray-400' }}">
                            {{ number_format($row['overtime_hours'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $row['night_hours'] > 0 ? 'font-semibold text-indigo-700' : 'text-gray-400' }}">
                            {{ number_format($row['night_hours'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button type="button" data-detail-id="{{ $e->id }}"
                                    class="text-indigo-600 hover:text-indigo-900 p-1.5 hover:bg-indigo-50 rounded-lg transition-colors" title="View daily breakdown">
                                    <i data-feather="calendar" class="w-4 h-4"></i>
                                </button>
                                <a href="{{ route('attendance.index', ['employee_id' => $e->id]) }}"
                                    class="text-gray-400 hover:text-gray-700 p-1.5 hover:bg-gray-100 rounded-lg transition-colors" title="Open daily log">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="tsEmptyRow">
                        <td colspan="12" class="px-6 py-14 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="file-text" class="w-12 h-12 text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No timesheet data available</p>
                                <p class="text-sm text-gray-600 mt-2">Select a different month, adjust your filters, or add employees to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($timesheetData->isNotEmpty())
        <div class="px-6 py-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3" id="tsPaginationBar">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select id="tsPageSize" class="px-2 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10">10</option>
                    <option value="15" selected>15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
                <span>per page</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="tsPrevPage"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">Previous</button>
                <span id="tsPageInfo" class="text-sm text-gray-600"></span>
                <button type="button" id="tsNextPage"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">Next</button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Daily Breakdown Modal -->
<x-advanced-modal id="timesheetDetailModal" title="Timesheet Detail" description="Daily attendance breakdown for the selected month" icon="calendar" color="indigo" size="4xl">
    <div id="tsDetailContent">
        <div class="flex flex-col items-center justify-center py-10">
            <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            <p class="text-sm text-gray-500 mt-3">Loading attendance detail...</p>
        </div>
    </div>
    <x-slot:footer>
        <div class="flex justify-end">
            <button type="button" onclick="closeModal('timesheetDetailModal')"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Close</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();

    // --- Month navigation (preserves filters, submits the GET form) ---
    function shiftMonth(offset) {
        const input = document.getElementById('monthInput');
        const parts = input.value.split('-').map(Number);
        let y = parts[0], m = parts[1];
        m += offset;
        if (m < 1) { m += 12; y--; }
        if (m > 12) { m -= 12; y++; }
        input.value = y + '-' + String(m).padStart(2, '0');
        document.getElementById('timesheetFilterForm').submit();
    }

    document.querySelectorAll('[data-month-step]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            shiftMonth(parseInt(btn.dataset.monthStep, 10));
        });
    });
    document.querySelector('[data-month-today]').addEventListener('click', function() {
        const input = document.getElementById('monthInput');
        input.value = input.dataset.currentMonth;
        document.getElementById('timesheetFilterForm').submit();
    });
    document.getElementById('searchInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });

    // --- Client-side sorting + pagination over the server-filtered rows ---
    const rows = Array.from(document.querySelectorAll('#timesheetBody .ts-row'));
    const emptyRow = document.getElementById('tsEmptyRow');
    const perPageSelect = document.getElementById('tsPageSize');
    const prevBtn = document.getElementById('tsPrevPage');
    const nextBtn = document.getElementById('tsNextPage');
    const pageInfo = document.getElementById('tsPageInfo');
    const countEl = document.getElementById('resultCount');

    const NUMERIC_KEYS = ['worked', 'absent', 'leave', 'late', 'rate', 'hours', 'overtime', 'night'];
    const state = { key: 'name', dir: 1, page: 1, per: perPageSelect ? parseInt(perPageSelect.value, 10) : 15 };

    function sortValue(row) {
        const key = state.key;
        if (key === 'name' || key === 'department' || key === 'position') {
            return row.dataset['sort' + key.charAt(0).toUpperCase() + key.slice(1)] || '';
        }
        return parseFloat(row.dataset['sort' + key.charAt(0).toUpperCase() + key.slice(1)]) || 0;
    }

    function visibleRows() {
        return rows.slice().sort(function(a, b) {
            const av = sortValue(a);
            const bv = sortValue(b);
            let cmp;
            if (typeof av === 'string' || typeof bv === 'string') {
                cmp = String(av).localeCompare(String(bv));
            } else {
                cmp = av - bv;
            }
            return cmp * state.dir;
        });
    }

    function applyView() {
        const sorted = visibleRows();
        const total = sorted.length;
        const perPage = state.per === 'all' ? total : Math.max(1, state.per);
        const pages = Math.max(1, Math.ceil(total / perPage));
        if (state.page > pages) state.page = pages;
        if (state.page < 1) state.page = 1;

        const start = (state.page - 1) * perPage;
        const end = Math.min(start + perPage, total);

        sorted.forEach(function(row, idx) {
            row.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        if (emptyRow) emptyRow.style.display = total === 0 ? '' : 'none';

        if (countEl) {
            countEl.textContent = 'Showing ' + total + (total === 1 ? ' employee' : ' employees');
        }
        if (pageInfo) {
            pageInfo.textContent = total === 0
                ? '0 of 0 pages'
                : 'Page ' + state.page + ' of ' + pages + ' · ' + (start + 1) + '–' + end + ' of ' + total;
        }
        if (prevBtn) prevBtn.disabled = state.page <= 1;
        if (nextBtn) nextBtn.disabled = state.page >= pages;

        // Update sort indicator arrows on th
        document.querySelectorAll('#timesheetsTable th.sortable').forEach(function(th) {
            const active = th.dataset.sort === state.key;
            th.classList.toggle('text-indigo-600', active);
            th.classList.toggle('bg-indigo-50', active);
            th.innerHTML = th.innerHTML.replace(/\s*(↑|↓|↕)\s*$/i, '');
            th.innerHTML += active ? (state.dir === 1 ? ' ↑' : ' ↓') : ' ↕';
        });
    }

    document.querySelectorAll('#timesheetsTable th.sortable').forEach(function(th) {
        th.addEventListener('click', function() {
            const key = th.dataset.sort;
            if (state.key === key) {
                state.dir *= -1;
            } else {
                state.key = key;
                state.dir = 1;
            }
            state.page = 1;
            applyView();
        });
    });

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            state.per = parseInt(perPageSelect.value, 10);
            if (isNaN(state.per)) state.per = 'all';
            state.page = 1;
            applyView();
        });
    }
    if (prevBtn) prevBtn.addEventListener('click', function() { state.page--; applyView(); });
    if (nextBtn) nextBtn.addEventListener('click', function() { state.page++; applyView(); });

    applyView();

    // --- Per-employee daily breakdown modal ---
    function esc(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function statusChip(code, label, color) {
        color = color || (({ '9': 'green', '12': 'green', 'M': 'green', 'A': 'red' })[code]
            || (['AL', 'SLF', 'SLH', 'UL'].indexOf(code) !== -1 ? 'blue' : 'gray'));
        const cls = {
            green: 'bg-green-100 text-green-800',
            red: 'bg-red-100 text-red-800',
            blue: 'bg-blue-100 text-blue-800',
            gray: 'bg-gray-100 text-gray-500'
        }[color] || 'bg-gray-100 text-gray-500';
        return '<span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full ' + cls + '">' + esc(label) + '</span>';
    }

    function fmt(n) {
        return Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderDetail(data) {
        const emp = data.employee;
        const s = data.summary;

        const dayRows = data.days.map(function(d) {
            const hours = d.total_hours > 0 ? fmt(d.total_hours) : '—';
            const clock = (d.clock_in && d.clock_out) ? d.clock_in + ' – ' + d.clock_out : (d.clock_in || d.clock_out || '—');
            const badges = [];
            if (d.late_minutes > 0) badges.push('<span class="text-[11px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">Late ' + d.late_minutes + 'm</span>');
            if (d.early_departure_minutes > 0) badges.push('<span class="text-[11px] font-semibold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded">Early ' + d.early_departure_minutes + 'm</span>');
            if (d.overtime_hours > 0) badges.push('<span class="text-[11px] font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded">OT ' + fmt(d.overtime_hours) + '</span>');
            if (d.night_hours > 0) badges.push('<span class="text-[11px] font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded">Night ' + fmt(d.night_hours) + '</span>');
            return '<div class="flex items-center justify-between px-4 py-2.5 rounded-lg ' +
                (d.is_weekend && d.status_code === null ? 'bg-gray-50/60' : (d.date === new Date().toISOString().slice(0,10) ? 'bg-gray-50' : 'hover:bg-gray-50 transition-colors')) +
                '">' +
                '<div class="flex items-center gap-3">' +
                    '<div class="text-right">' +
                        '<div class="text-sm font-semibold text-gray-900 w-6">' + d.day + '</div>' +
                        '<div class="text-[10px] text-gray-400 uppercase tracking-wide w-16">' + esc(d.weekday.slice(0, 3)) + '</div>' +
                    '</div>' +
                    statusChip(d.status_code, d.status_label, d.color) +
                    '<div class="text-sm text-gray-500 font-mono">' + clock + '</div>' +
                '</div>' +
                '<div class="flex items-center gap-2">' +
                    '<div class="text-xs text-gray-600 w-10 text-right">' + hours + 'h</div>' +
                    badges.join('') +
                    '</div>' +
                '</div>' +
                (d.notes ? '<div class="px-4 pb-2 -mt-1 text-xs text-gray-500 italic">' + esc(d.notes) + '</div>' : '');
        }).join('');

        return '' +
        '<div class="border-b border-gray-100 pb-4 mb-4">' +
            '<div class="flex items-center gap-3">' +
                '<div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">' +
                    '<span class="text-lg font-semibold text-indigo-600">' + esc((emp.name || '??').charAt(0)) + '</span>' +
                '</div>' +
                '<div>' +
                    '<div class="flex items-center gap-2">' +
                        '<h3 class="text-lg font-bold text-gray-900">' + esc(emp.name) + '</h3>' +
                        '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">' + esc(emp.employee_id || (emp.id ? 'ID: ' + emp.id : '')) + '</span>' +
                    '</div>' +
                    '<p class="text-sm text-gray-500">' + esc(emp.position || '-') + ' · ' + esc(emp.department || '-') + '</p>' +
                '</div>' +
            '</div>' +
        '</div>' +
        '<div class="grid grid-cols-4 gap-2 mb-4">' +
            ['Worked', 'Absent', 'Leave', 'Late', 'Rate', 'Paid Hrs', 'Overtime', 'Night'].map(function(label, i) {
                const vals = [s.worked_days + '/' + s.total_days, s.absent_days, s.leave_days, s.late_days,
                    (s.attendance_rate == null ? '—' : s.attendance_rate + '%'), fmt(s.total_paid_hours), fmt(s.overtime_hours), fmt(s.night_hours)];
                return '<div class="bg-gray-50 rounded-lg px-3 py-2">' +
                    '<p class="text-[11px] text-gray-500 uppercase tracking-wide">' + label + '</p>' +
                    '<p class="text-sm font-bold text-gray-900">' + esc(vals[i]) + '</p>' +
                '</div>';
            }).join('') +
        '</div>' +
        '<div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Daily Breakdown - ' + esc(data.month_label) + '</div>' +
        '<div class="max-h-[380px] overflow-y-auto space-y-1 bg-gray-50/50 rounded-xl p-2">' +
            (dayRows || '<p class="text-sm text-gray-500 py-8 text-center">No attendance records for this month.</p>') +
        '</div>';
    }

    async function openTimesheetDetail(employeeId) {
        openModal('timesheetDetailModal');
        const wrap = document.getElementById('tsDetailContent');
        const month = document.getElementById('timesheetsPage').dataset.month || '';
        wrap.innerHTML =
            '<div class="flex flex-col items-center justify-center py-10">' +
                '<div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>' +
                '<p class="text-sm text-gray-500 mt-3">Loading attendance detail...</p>' +
            '</div>';

        try {
            const response = await fetch('{{ route("attendance.timesheets.employee", "__ID__") }}'.replace('__ID__', employeeId) + '?month=' + encodeURIComponent(month), {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Request failed (' + response.status + ')');
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            wrap.innerHTML = renderDetail(data);
            if (typeof feather !== 'undefined' && feather.replace) feather.replace();
        } catch (err) {
            wrap.innerHTML =
                '<div class="flex flex-col items-center justify-center py-10 text-center">' +
                    '<i data-feather="alert-circle" class="w-10 h-10 text-red-400 mb-3"></i>' +
                    '<p class="text-sm font-semibold text-gray-900">Could not load timesheet detail</p>' +
                    '<p class="text-xs text-gray-500 mt-1">' + esc(err.message) + '</p>' +
                '</div>';
            if (typeof feather !== 'undefined' && feather.replace) feather.replace();
        }
    }

    // Event delegation for detail buttons
    document.getElementById('timesheetBody').addEventListener('click', function(e) {
        const btn = e.target.closest('[data-detail-id]');
        if (btn) {
            openTimesheetDetail(btn.dataset.detailId);
        }
    });
});
</script>
@endpush