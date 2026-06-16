@extends('layouts.app')

@section('title', 'Attendance & Timesheet - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Attendance & Timesheet</h1>
            <p class="text-gray-600 mt-2">Track employee attendance and manage timesheets</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Tracking attendance for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button type="button" onclick="openTimesheetImport()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Import Timesheet
            </button>
        </div>
    </div>

    <!-- Attendance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">94.2%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">234</h3>
            <p class="text-gray-600 text-sm">Present Today</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">+2</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">8</h3>
            <p class="text-gray-600 text-sm">Late Arrivals</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">12</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">6</h3>
            <p class="text-gray-600 text-sm">On Leave</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">-1</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">2</h3>
            <p class="text-gray-600 text-sm">Absent</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="check-in" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Quick Action</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Check In/Out</h3>
            <p class="text-sm opacity-90 mb-4">Record attendance for employees</p>
            <button class="bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Manage Attendance
            </button>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="file-text" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Timesheet</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Timesheet Entry</h3>
            <p class="text-sm opacity-90 mb-4">Submit weekly timesheets</p>
            <button class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Enter Timesheet
            </button>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="bar-chart-2" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Reports</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Attendance Report</h3>
            <p class="text-sm opacity-90 mb-4">Generate monthly reports</p>
            <button class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Generate Report
            </button>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Attendance</h3>
                @if(isset($summary))
                    <p class="text-sm text-gray-600 mt-1">
                        Present: <span class="font-medium">{{ $summary['present'] ?? 0 }}</span>,
                        Late: <span class="font-medium">{{ $summary['late'] ?? 0 }}</span>,
                        Absent: <span class="font-medium">{{ $summary['absent'] ?? 0 }}</span>,
                        On Leave: <span class="font-medium">{{ $summary['on_leave'] ?? 0 }}</span>
                    </p>
                @endif
            </div>
            <form method="GET" action="{{ route('attendance.index') }}" class="flex space-x-3">
                <input type="date" name="date" class="form-input" value="{{ $date ?? now()->format('Y-m-d') }}">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="search" class="w-4 h-4 inline mr-2"></i>
                    Load
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(($rows ?? []) as $row)
                    @php($employee = $row['employee'])
                    @php($att = $row['attendance'])
                    @php($status = $att?->status ?? 'absent')
                    <tr class="hover:bg-gray-50" data-att-row="{{ $employee->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr(trim($employee->first_name.' '.$employee->last_name), 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $employee->employee_id ?: ('#'.$employee->id) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="clock-in-{{ $employee->id }}">{{ $att?->clock_in ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="clock-out-{{ $employee->id }}">{{ $att?->clock_out ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($status === 'present') bg-green-100 text-green-800
                                @elseif($status === 'late' || $status === 'half_day') bg-yellow-100 text-yellow-800
                                @elseif($status === 'on_leave' || $status === 'holiday') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif"
                                id="status-{{ $employee->id }}">
                                {{ strtoupper(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="hours-{{ $employee->id }}">{{ $att?->total_hours ? number_format((float)$att->total_hours, 2) : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button type="button"
                                        onclick="setAttendanceStatus({{ $employee->id }}, 'present')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-50 text-green-700 hover:bg-green-100"
                                        title="Mark Present">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                </button>
                                <button type="button"
                                        onclick="setAttendanceStatus({{ $employee->id }}, 'late')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                                        title="Mark Late">
                                    <i data-feather="clock" class="w-4 h-4"></i>
                                </button>
                                <button type="button"
                                        onclick="setAttendanceStatus({{ $employee->id }}, 'absent')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-700 hover:bg-red-100"
                                        title="Mark Absent">
                                    <i data-feather="x-circle" class="w-4 h-4"></i>
                                </button>
                                <button type="button"
                                        onclick="setAttendanceStatus({{ $employee->id }}, 'on_leave')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100"
                                        title="Mark On Leave">
                                    <i data-feather="calendar" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">No employees found for this client.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Weekly Timesheet Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Weekly Timesheet Summary</h3>
            <div class="flex space-x-3">
                <select class="form-select">
                    <option>Week 12 (Mar 25-31, 2024)</option>
                    <option>Week 11 (Mar 18-24, 2024)</option>
                    <option>Week 10 (Mar 11-17, 2024)</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Department Summary</h4>
                <div class="space-y-3">
                    @foreach([
                        ['dept' => 'IT', 'employees' => 45, 'hours' => 405, 'avg' => 9.0],
                        ['dept' => 'HR', 'dept' => 'HR', 'employees' => 12, 'hours' => 108, 'avg' => 9.0],
                        ['dept' => 'Finance', 'employees' => 28, 'hours' => 252, 'avg' => 9.0],
                        ['dept' => 'Operations', 'employees' => 89, 'hours' => 801, 'avg' => 9.0],
                        ['dept' => 'Sales', 'employees' => 56, 'hours' => 504, 'avg' => 9.0]
                    ] as $dept)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="briefcase" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $dept['dept'] }}</p>
                                <p class="text-xs text-gray-500">{{ $dept['employees'] }} employees</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $dept['hours'] }} hrs</p>
                            <p class="text-xs text-gray-500">{{ $dept['avg'] }} avg</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Overtime Summary</h4>
                <div class="space-y-3">
                    @foreach([
                        ['name' => 'John Doe', 'dept' => 'IT', 'regular' => 45, 'overtime' => 5, 'total' => 50],
                        ['name' => 'Sarah Smith', 'dept' => 'HR', 'regular' => 40, 'overtime' => 2, 'total' => 42],
                        ['name' => 'Mike Johnson', 'dept' => 'Finance', 'regular' => 40, 'overtime' => 8, 'total' => 48],
                        ['name' => 'David Wilson', 'dept' => 'Operations', 'regular' => 45, 'overtime' => 10, 'total' => 55],
                        ['name' => 'Lisa Brown', 'dept' => 'Sales', 'regular' => 40, 'overtime' => 3, 'total' => 43]
                    ] as $overtime)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $overtime['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $overtime['dept'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $overtime['total'] }} hrs</p>
                            <p class="text-xs text-orange-600">+{{ $overtime['overtime'] }} OT</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Calendar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Calendar</h3>
            <div class="flex space-x-3">
                <a id="attendanceCalendarPrev" data-date="{{ $calendar['prev'] ?? ($date ?? now()->toDateString()) }}"
                   href="{{ route('attendance.index', ['date' => $calendar['prev'] ?? ($date ?? now()->toDateString())]) }}"
                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors inline-flex items-center">
                    <i data-feather="chevron-left" class="w-4 h-4"></i>
                </a>
                <span id="attendanceCalendarLabel" class="px-4 py-1 bg-indigo-100 text-indigo-700 rounded font-medium">{{ $calendar['label'] ?? 'Calendar' }}</span>
                <a id="attendanceCalendarNext" data-date="{{ $calendar['next'] ?? ($date ?? now()->toDateString()) }}"
                   href="{{ route('attendance.index', ['date' => $calendar['next'] ?? ($date ?? now()->toDateString())]) }}"
                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors inline-flex items-center">
                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
        <div id="attendanceCalendarGrid" class="grid grid-cols-7 gap-2">
            <!-- Week days -->
            <div class="text-center text-xs font-medium text-gray-500 py-2">Sun</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Mon</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Tue</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Wed</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Thu</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Fri</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Sat</div>
            
            @foreach(($calendar['days'] ?? []) as $day)
                @php($counts = $day['counts'] ?? [])
                @php($total = array_sum($counts))
                <a href="{{ route('attendance.index', ['date' => $day['date']]) }}"
                   class="aspect-square border border-gray-200 rounded-lg p-2 transition-colors cursor-pointer block
                        {{ ($day['in_month'] ?? false) ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}
                        {{ ($day['is_weekend'] ?? false) ? 'ring-1 ring-yellow-100' : '' }}
                        {{ ($day['is_selected'] ?? false) ? 'ring-2 ring-indigo-500 border-indigo-300' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="text-sm font-medium {{ ($day['in_month'] ?? false) ? 'text-gray-900' : 'text-gray-400' }}">{{ $day['day'] }}</div>
                        @if(($day['is_weekend'] ?? false))
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700">W</span>
                        @endif
                    </div>
                    <div class="mt-2 space-y-1">
                        @if($total > 0)
                            @php($p = (int) round((($counts['present'] ?? 0) / $total) * 100))
                            @php($l = (int) round((($counts['late'] ?? 0) / $total) * 100))
                            @php($lv = (int) round((($counts['on_leave'] ?? 0) / $total) * 100))
                            @php($a = 100 - $p - $l - $lv)
                            <div class="w-full bg-gray-200 rounded-full h-1 overflow-hidden flex">
                                <div class="h-1 bg-green-500" style="width: {{ $p }}%"></div>
                                <div class="h-1 bg-yellow-500" style="width: {{ $l }}%"></div>
                                <div class="h-1 bg-blue-500" style="width: {{ $lv }}%"></div>
                                <div class="h-1 bg-red-500" style="width: {{ max(0, $a) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-gray-500">
                                <span>P {{ $counts['present'] ?? 0 }}</span>
                                <span>L {{ $counts['late'] ?? 0 }}</span>
                                <span>LV {{ $counts['on_leave'] ?? 0 }}</span>
                                <span>A {{ ($counts['absent'] ?? 0) + ($counts['holiday'] ?? 0) }}</span>
                            </div>
                        @else
                            <div class="w-full bg-gray-100 rounded-full h-1"></div>
                            <div class="text-[10px] text-gray-400">No data</div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Present</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Late</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-gray-600">On Leave</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Absent/Holiday</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.__attendanceSelectedDate = '{{ $date ?? now()->format('Y-m-d') }}';

async function loadAttendanceCalendar(monthDate) {
    const prev = document.getElementById('attendanceCalendarPrev');
    const next = document.getElementById('attendanceCalendarNext');
    const label = document.getElementById('attendanceCalendarLabel');
    const grid = document.getElementById('attendanceCalendarGrid');

    if (!prev || !next || !label || !grid) return;

    prev.classList.add('opacity-50', 'pointer-events-none');
    next.classList.add('opacity-50', 'pointer-events-none');

    try {
        const url = new URL('{{ route('attendance.calendar') }}', window.location.origin);
        url.searchParams.set('date', monthDate);
        url.searchParams.set('selected_date', window.__attendanceSelectedDate || '');

        const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const result = await response.json();
        if (!result?.success || !result?.calendar) return;

        const cal = result.calendar;
        label.textContent = cal.label || 'Calendar';

        prev.dataset.date = cal.prev;
        next.dataset.date = cal.next;
        prev.href = `{{ route('attendance.index') }}?date=${encodeURIComponent(cal.prev)}`;
        next.href = `{{ route('attendance.index') }}?date=${encodeURIComponent(cal.next)}`;

        grid.innerHTML = renderCalendarGrid(cal);
    } finally {
        prev.classList.remove('opacity-50', 'pointer-events-none');
        next.classList.remove('opacity-50', 'pointer-events-none');
    }
}

function renderCalendarGrid(cal) {
    const week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
        .map((d) => `<div class="text-center text-xs font-medium text-gray-500 py-2">${d}</div>`)
        .join('');

    const days = (cal.days || []).map((day) => {
        const counts = day.counts || {};
        const total = (counts.present || 0) + (counts.late || 0) + (counts.absent || 0) + (counts.on_leave || 0) + (counts.holiday || 0);

        const inMonth = !!day.in_month;
        const isWeekend = !!day.is_weekend;
        const isSelected = !!day.is_selected;

        const baseClass = [
            'aspect-square border border-gray-200 rounded-lg p-2 transition-colors cursor-pointer block',
            inMonth ? 'bg-white hover:bg-gray-50' : 'bg-gray-50 text-gray-400 hover:bg-gray-100',
            isWeekend ? 'ring-1 ring-yellow-100' : '',
            isSelected ? 'ring-2 ring-indigo-500 border-indigo-300' : '',
        ].filter(Boolean).join(' ');

        const dayTextClass = inMonth ? 'text-gray-900' : 'text-gray-400';
        const weekendBadge = isWeekend ? '<span class="text-[10px] px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-700">W</span>' : '';

        let statsHtml = '';
        if (total > 0) {
            const p = Math.round(((counts.present || 0) / total) * 100);
            const l = Math.round(((counts.late || 0) / total) * 100);
            const lv = Math.round(((counts.on_leave || 0) / total) * 100);
            const a = Math.max(0, 100 - p - l - lv);

            const absentLabel = (counts.absent || 0) + (counts.holiday || 0);

            statsHtml = `
                <div class="w-full bg-gray-200 rounded-full h-1 overflow-hidden flex">
                    <div class="h-1 bg-green-500" style="width:${p}%"></div>
                    <div class="h-1 bg-yellow-500" style="width:${l}%"></div>
                    <div class="h-1 bg-blue-500" style="width:${lv}%"></div>
                    <div class="h-1 bg-red-500" style="width:${a}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-gray-500">
                    <span>P ${counts.present || 0}</span>
                    <span>L ${counts.late || 0}</span>
                    <span>LV ${counts.on_leave || 0}</span>
                    <span>A ${absentLabel}</span>
                </div>
            `;
        } else {
            statsHtml = `
                <div class="w-full bg-gray-100 rounded-full h-1"></div>
                <div class="text-[10px] text-gray-400">No data</div>
            `;
        }

        return `
            <a href="{{ route('attendance.index') }}?date=${encodeURIComponent(day.date)}" class="${baseClass}">
                <div class="flex items-start justify-between">
                    <div class="text-sm font-medium ${dayTextClass}">${day.day}</div>
                    ${weekendBadge}
                </div>
                <div class="mt-2 space-y-1">
                    ${statsHtml}
                </div>
            </a>
        `;
    }).join('');

    return week + days;
}

document.addEventListener('DOMContentLoaded', () => {
    const prev = document.getElementById('attendanceCalendarPrev');
    const next = document.getElementById('attendanceCalendarNext');

    if (prev) {
        prev.addEventListener('click', (e) => {
            e.preventDefault();
            loadAttendanceCalendar(prev.dataset.date);
        });
    }

    if (next) {
        next.addEventListener('click', (e) => {
            e.preventDefault();
            loadAttendanceCalendar(next.dataset.date);
        });
    }
});

function openTimesheetImport() {
    const input = document.getElementById('timesheetFile');
    if (!input) return;
    input.value = '';
    input.click();
}

function submitTimesheetImport() {
    const form = document.getElementById('timesheetImportForm');
    if (!form) return;
    form.submit();
}

async function setAttendanceStatus(employeeId, status) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const date = document.querySelector('input[name="date"]')?.value || '{{ $date ?? now()->format('Y-m-d') }}';

    try {
        const response = await fetch('{{ route('attendance.upsert') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                employee_id: employeeId,
                attendance_date: date,
                status: status,
            }),
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json') ? await response.json() : null;

        if (!response.ok || !result?.success) {
            return;
        }

        const badge = document.getElementById(`status-${employeeId}`);
        if (badge) {
            badge.textContent = String(status).toUpperCase().replaceAll('_', ' ');
            badge.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + (
                status === 'present' ? 'bg-green-100 text-green-800' :
                (status === 'late' || status === 'half_day') ? 'bg-yellow-100 text-yellow-800' :
                (status === 'on_leave' || status === 'holiday') ? 'bg-blue-100 text-blue-800' :
                'bg-red-100 text-red-800'
            );
        }

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    } catch (e) {
    }
}
</script>

<form id="timesheetImportForm" method="POST" action="{{ route('attendance.import') }}" enctype="multipart/form-data" class="hidden">
    @csrf
    <input id="timesheetFile" type="file" name="timesheet" accept=".csv,text/csv" onchange="submitTimesheetImport()">
</form>
@endpush
