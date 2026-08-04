@extends('layouts.app')

@section('title', 'Leave Calendar - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Leave Calendar</h1>
            <p class="text-gray-600 mt-2">Visualize employee leave across the organization</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
        <form method="GET" action="{{ route('leave.calendar') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                    <i data-feather="filter" class="w-4 h-4 inline mr-1"></i>Apply Filters
                </button>
                <a href="{{ route('leave.calendar') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                    <i data-feather="x" class="w-4 h-4 inline mr-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <!-- Calendar Toolbar -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 font-manrope" id="calendarTitle">Month Year</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="eventCount">0</span> leave event(s) this month
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" id="prevMonth" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" title="Previous Month">
                        <i data-feather="chevron-left" class="w-4 h-4 text-gray-600"></i>
                    </button>
                    <button type="button" id="todayBtn" class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">Today</button>
                    <button type="button" id="nextMonth" class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" title="Next Month">
                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-600"></i>
                    </button>
                </div>
            </div>

            <!-- Weekday Header -->
            <div class="grid grid-cols-7 mb-2">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="text-center text-xs font-semibold text-gray-500 uppercase tracking-wider py-2">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Calendar Grid -->
            <div id="calendarGrid" class="grid grid-cols-7 gap-1.5"></div>

            <!-- Legend -->
            <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-4">
                <span class="text-sm font-medium text-gray-700">Legend:</span>
                <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span><span class="text-xs text-gray-600">Approved</span></span>
                <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span><span class="text-xs text-gray-600">Pending</span></span>
                <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="text-xs text-gray-600">Rejected</span></span>
                <span class="flex items-center space-x-1.5"><span class="w-3 h-3 rounded-full bg-indigo-500"></span><span class="text-xs text-gray-600">Today</span></span>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Overview</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-blue-700" id="statTotal">0</p>
                        <p class="text-xs text-blue-600">Events</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-green-700" id="statApproved">0</p>
                        <p class="text-xs text-green-600">Approved</p>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-amber-700" id="statPending">0</p>
                        <p class="text-xs text-amber-600">Pending</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-indigo-700" id="statOnLeave">0</p>
                        <p class="text-xs text-indigo-600">On Leave Today</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Leave -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Leave</h3>
                    <span class="text-xs text-gray-500">Next 14 days</span>
                </div>
                <div id="upcomingList" class="space-y-3">
                    <p class="text-sm text-gray-500">Loading...</p>
                </div>
            </div>

            <!-- Leave Type Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">By Leave Type</h3>
                <div id="typeLegend" class="space-y-3">
                    <p class="text-sm text-gray-500">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<x-advanced-modal id="eventDetailModal" title="Leave Request Details" icon="calendar" color="indigo" size="lg">
    <div class="space-y-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium" id="detailInitials">--</span>
            </div>
            <div>
                <p class="font-semibold text-gray-900" id="detailName">--</p>
                <p class="text-sm text-gray-500" id="detailEmployeeCode">--</p>
            </div>
            <span id="detailStatusBadge" class="ml-auto px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">--</span>
        </div>

        <div class="border-t pt-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Leave Type</p>
                    <p class="font-medium text-gray-900" id="detailType">--</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Department</p>
                    <p class="font-medium text-gray-900" id="detailDepartment">--</p>
                </div>
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Start Date</p>
                    <p class="font-medium text-gray-900" id="detailStart">--</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">End Date</p>
                    <p class="font-medium text-gray-900" id="detailEnd">--</p>
                </div>
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Duration</p>
                    <p class="font-medium text-gray-900" id="detailDays">--</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applied At</p>
                    <p class="font-medium text-gray-900" id="detailAppliedAt">--</p>
                </div>
            </div>
        </div>

        <div class="border-t pt-4">
            <p class="text-sm text-gray-500">Reason</p>
            <p class="font-medium text-gray-900" id="detailReason">--</p>
        </div>
    </div>
    <x-slot:footer>
        <div class="flex justify-end">
            <button type="button" onclick="closeModal('eventDetailModal')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const ALL_EVENTS = @json($events);
    const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const STATUS_STYLES = {
        approved: { dot: 'bg-green-500', badge: 'bg-green-100 text-green-800', bar: 'bg-green-500' },
        pending:  { dot: 'bg-amber-500', badge: 'bg-amber-100 text-amber-800', bar: 'bg-amber-500' },
        rejected: { dot: 'bg-red-500',   badge: 'bg-red-100 text-red-800',     bar: 'bg-red-400' }
    };

    let currentYear, currentMonth;
    const grid = document.getElementById('calendarGrid');
    const title = document.getElementById('calendarTitle');

    function parseDate(str) {
        if (!str) return null;
        const parts = str.split('-').map(Number);
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }

    function dateKey(date) {
        return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
    }

    function formatDate(str) {
        const d = parseDate(str);
        return d ? d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }) : '--';
    }

    function isSameDate(a, b) {
        return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    }

    function eventDates(event) {
        const start = parseDate(event.start);
        const end = parseDate(event.end);
        const keys = [];
        if (!start || !end) return keys;
        const cur = new Date(start);
        while (cur <= end) {
            keys.push(dateKey(cur));
            cur.setDate(cur.getDate() + 1);
        }
        return keys;
    }

    function statusStyle(status) {
        return STATUS_STYLES[status] || STATUS_STYLES.pending;
    }

    function initials(name) {
        const parts = name.trim().split(/\s+/);
        if (parts.length >= 2) return (parts[0][0] || '') + (parts[parts.length - 1][0] || '');
        return parts[0] ? parts[0].slice(0, 2) : '--';
    }

    function todayKey() {
        const now = new Date();
        return dateKey(now);
    }

    function renderCalendar() {
        const firstOfMonth = new Date(currentYear, currentMonth, 1);
        const startOffset = firstOfMonth.getDay();
        const gridStart = new Date(firstOfMonth);
        gridStart.setDate(1 - startOffset);

        const today = todayKey();
        const eventsByDay = {};
        let monthEventIds = new Set();

        ALL_EVENTS.forEach(event => {
            const dates = eventDates(event);
            dates.forEach(key => {
                if (!eventsByDay[key]) eventsByDay[key] = [];
                eventsByDay[key].push(event);
            });
            if (dates.some(key => key.startsWith(currentYear + '-' + String(currentMonth + 1).padStart(2, '0')))) {
                monthEventIds.add(event.id);
            }
        });

        document.getElementById('eventCount').textContent = monthEventIds.size;

        const cells = [];
        for (let i = 0; i < 42; i++) {
            const day = new Date(gridStart);
            day.setDate(gridStart.getDate() + i);
            const key = dateKey(day);
            const inMonth = day.getMonth() === currentMonth;
            const isToday = key === today;
            const events = eventsByDay[key] || [];

            let cellClasses = 'bg-gray-50/50 border border-gray-100 rounded-lg min-h-[88px] p-1.5 flex flex-col transition-colors hover:bg-gray-50';
            if (!inMonth) cellClasses += ' opacity-40';
            if (isToday) cellClasses += ' ring-2 ring-indigo-500';

            let eventsHtml = '';
            const visible = events.slice(0, 3);
            visible.forEach(event => {
                const style = statusStyle(event.status);
                eventsHtml += `
                    <button type="button" data-event-id="${event.id}" onclick="window.showEventDetail(${event.id})"
                        class="cal-event text-left w-full rounded px-1 py-0.5 mb-0.5 text-[10px] leading-tight font-medium ${style.bar} text-white hover:opacity-90 transition-opacity truncate">
                        ${event.employee_name}
                    </button>`;
            });
            if (events.length > 3) {
                eventsHtml += `<button type="button" onclick="window.showDayEvents('${key}')" class="text-[10px] text-indigo-600 font-medium hover:text-indigo-800 text-left px-1">+${events.length - 3} more</button>`;
            }

            cells.push(`
                <div class="${cellClasses}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold ${isToday ? 'text-indigo-700' : inMonth ? 'text-gray-700' : 'text-gray-400'}">${day.getDate()}</span>
                        ${isToday ? '<span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>' : ''}
                    </div>
                    <div class="overflow-hidden">${eventsHtml || '<span class="text-[10px] text-gray-300">&nbsp;</span>'}</div>
                </div>
            `);
        }

        grid.innerHTML = cells.join('');
        title.textContent = MONTH_NAMES[currentMonth] + ' ' + currentYear;
        if (typeof feather !== 'undefined') feather.replace();

        renderSidebar();
    }

    function renderSidebar() {
        const now = new Date();
        const monthEvents = ALL_EVENTS.filter(e => {
            return eventDates(e).some(k => k.startsWith(currentYear + '-' + String(currentMonth + 1).padStart(2, '0')));
        });

        document.getElementById('statTotal').textContent = monthEvents.length;
        document.getElementById('statApproved').textContent = monthEvents.filter(e => e.status === 'approved').length;
        document.getElementById('statPending').textContent = monthEvents.filter(e => e.status === 'pending').length;
        document.getElementById('statOnLeave').textContent = ALL_EVENTS.filter(e => e.status === 'approved' && eventDates(e).includes(todayKey())).length;

        // Upcoming (next 14 days, approved or pending)
        const upcoming = ALL_EVENTS
            .filter(e => e.status !== 'rejected')
            .filter(e => eventDates(e).some(key => {
                const d = parseDate(key);
                const diff = (d - new Date(now.getFullYear(), now.getMonth(), now.getDate())) / 86400000;
                return diff >= 0 && diff <= 14;
            }))
            .sort((a, b) => a.start.localeCompare(b.start))
            .slice(0, 6);

        const upcomingEl = document.getElementById('upcomingList');
        if (!upcoming.length) {
            upcomingEl.innerHTML = '<p class="text-sm text-gray-500">No upcoming leave in the next 14 days.</p>';
        } else {
            upcomingEl.innerHTML = upcoming.map(event => {
                const style = statusStyle(event.status);
                return `
                    <button type="button" onclick="window.showEventDetail(${event.id})" class="w-full flex items-start space-x-3 p-2.5 rounded-lg hover:bg-gray-50 transition-colors text-left">
                        <span class="w-2 h-2 rounded-full ${style.dot} mt-1.5 flex-shrink-0"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">${event.employee_name}</p>
                            <p class="text-xs text-gray-500 truncate">${event.leave_type} &middot; ${event.days} day(s)</p>
                            <p class="text-xs text-gray-400">${formatDate(event.start)} - ${formatDate(event.end)}</p>
                        </div>
                    </button>`;
            }).join('');
        }

        // Leave type distribution
        const typeCounts = {};
        ALL_EVENTS.forEach(e => {
            typeCounts[e.leave_type] = (typeCounts[e.leave_type] || 0) + 1;
        });
        const entries = Object.entries(typeCounts).sort((a, b) => b[1] - a[1]);
        const total = ALL_EVENTS.length || 1;
        const colors = ['bg-indigo-500', 'bg-purple-500', 'bg-pink-500', 'bg-teal-500', 'bg-orange-500', 'bg-cyan-500', 'bg-blue-500', 'bg-emerald-500'];
        const typeLegendEl = document.getElementById('typeLegend');
        if (!entries.length) {
            typeLegendEl.innerHTML = '<p class="text-sm text-gray-500">No leave data available.</p>';
        } else {
            typeLegendEl.innerHTML = entries.map(([type, count], i) => `
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="flex items-center space-x-2 text-sm text-gray-700">
                            <span class="w-2.5 h-2.5 rounded-full ${colors[i % colors.length]}"></span>
                            ${type}
                        </span>
                        <span class="text-sm font-semibold text-gray-900">${count}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="${colors[i % colors.length]} h-1.5 rounded-full" style="width: ${Math.round((count / total) * 100)}%"></div>
                    </div>
                </div>
            `).join('');
        }
    }

    function navigate(offset) {
        currentMonth += offset;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar();
    }

    window.showEventDetail = function(id) {
        const event = ALL_EVENTS.find(e => e.id === id);
        if (!event) return;
        document.getElementById('detailInitials').textContent = initials(event.employee_name);
        document.getElementById('detailName').textContent = event.employee_name || 'Unknown';
        document.getElementById('detailEmployeeCode').textContent = event.employee_id || 'Employee #' + event.employee_code;
        document.getElementById('detailType').textContent = event.leave_type;
        document.getElementById('detailDepartment').textContent = event.department || 'N/A';
        document.getElementById('detailStart').textContent = formatDate(event.start);
        document.getElementById('detailEnd').textContent = formatDate(event.end);
        document.getElementById('detailDays').textContent = event.days + ' day(s)';
        document.getElementById('detailAppliedAt').textContent = event.applied_at ? formatDate(event.applied_at) : 'N/A';
        document.getElementById('detailReason').textContent = event.reason || 'No reason provided';

        const badge = document.getElementById('detailStatusBadge');
        const style = statusStyle(event.status);
        badge.className = 'ml-auto px-2.5 py-1 text-xs font-semibold rounded-full ' + style.badge;
        badge.textContent = event.status.charAt(0).toUpperCase() + event.status.slice(1);

        openModal('eventDetailModal');
        if (typeof feather !== 'undefined') feather.replace();
    };

    window.showDayEvents = function(key) {
        const events = ALL_EVENTS.filter(e => eventDates(e).includes(key));
        showNotification(events.map(e => e.employee_name + ' (' + e.leave_type + ')').join(' | '), 'info');
    };

    document.getElementById('prevMonth').addEventListener('click', () => navigate(-1));
    document.getElementById('nextMonth').addEventListener('click', () => navigate(1));
    document.getElementById('todayBtn').addEventListener('click', () => {
        const now = new Date();
        currentYear = now.getFullYear();
        currentMonth = now.getMonth();
        renderCalendar();
    });

    const now = new Date();
    currentYear = now.getFullYear();
    currentMonth = now.getMonth();
    renderCalendar();
})();
</script>
@endsection
