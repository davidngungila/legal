@extends('layouts.app')

@section('title', 'Dashboard - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()?->name ?? 'User' }}! Here's your HR overview.</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Current Client</p>
                        <p class="text-sm font-semibold text-gray-900" data-client-display>{{ $currentClient?->name ?? 'No Client Selected' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Employees -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+{{ $stats['new_hires'] }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] }}</h3>
            <p class="text-gray-600 text-sm">Total Employees</p>
        </div>

        <!-- Active Cases -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">Medium</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active_cases'] }}</h3>
            <p class="text-gray-600 text-sm">Active Disciplinary Cases</p>
        </div>

        <!-- Payroll Processed -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="credit-card" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-gray-600 font-medium">{{ now()->format('M Y') }}</span>
            </div>
            @if($stats['monthly_payroll'] > 0)
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['monthly_payroll_formatted'] }}</h3>
            <p class="text-gray-600 text-sm">Monthly Payroll{{ $stats['monthly_payroll_count'] ? ' · ' . $stats['monthly_payroll_count'] . ' records' : '' }}</p>
            @else
            <h3 class="text-2xl font-bold text-gray-400">—</h3>
            <p class="text-gray-600 text-sm">No payroll for {{ now()->format('M Y') }}</p>
            @endif
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">{{ $stats['present_today'] }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['attendance_rate'] }}%</h3>
            <p class="text-gray-600 text-sm">Attendance Rate</p>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Organization Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Industry:</span>
                    <span class="text-sm font-medium">{{ $currentClient?->industry ?: 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Departments:</span>
                    <span class="text-sm font-medium">{{ $stats['departments_count'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Locations:</span>
                    <span class="text-sm font-medium">{{ $stats['locations_count'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Founded:</span>
                    <span class="text-sm font-medium">{{ $currentClient?->created_at?->format('Y') ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status:</span>
                    <span class="text-sm font-medium capitalize">{{ $currentClient?->status ?: '—' }}</span>
                </div>
            </div>
        </div>

        <!-- Employee Breakdown -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Breakdown</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active:</span>
                    <span class="text-sm font-medium text-green-600">{{ $stats['active_employees'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">On Leave:</span>
                    <span class="text-sm font-medium text-yellow-600">{{ $stats['on_leave_employees'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Probation:</span>
                    <span class="text-sm font-medium text-blue-600">{{ $stats['probation_employees'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Terminated:</span>
                    <span class="text-sm font-medium text-gray-600">{{ $stats['terminated_employees'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Turnover (12 mo):</span>
                    <span class="text-sm font-medium text-red-600">{{ $stats['turnover_rate'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Time & Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time & Attendance</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Absent Today:</span>
                    <span class="text-sm font-medium text-red-600">{{ $stats['absent_today'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Late Today:</span>
                    <span class="text-sm font-medium text-yellow-600">{{ $stats['late_today'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Hours This Month:</span>
                    <span class="text-sm font-medium">{{ number_format($stats['monthly_total_hours'], 1) }} hrs</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Overtime This Month:</span>
                    <span class="text-sm font-medium text-blue-600">{{ number_format($stats['monthly_overtime_hours'], 1) }} hrs</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        <!-- Employee Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Distribution</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="employeeChart"></canvas>
            </div>
        </div>

        <!-- Attendance Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Attendance Trend</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Payroll Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Payroll Trend</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="payrollChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alerts and Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Critical Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Critical Alerts</h3>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ count($alerts) ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ count($alerts) ? count($alerts) . ' open' : 'All clear' }}
                </span>
            </div>
            <div class="space-y-3">
                @forelse($alerts as $alert)
                    <div class="p-3 bg-{{ $alert['color'] }}-50 rounded-lg border border-{{ $alert['color'] }}-100">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start space-x-3">
                                <i data-feather="{{ $alert['icon'] }}" class="w-5 h-5 text-{{ $alert['color'] }}-600 mt-0.5"></i>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</p>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $alert['severity'] === 'critical' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ ucfirst($alert['severity']) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">{{ $alert['description'] }}</p>
                                </div>
                            </div>
                            @if(!empty($alert['link']))
                                <a href="{{ $alert['link'] }}" class="text-xs font-medium text-{{ $alert['color'] }}-700 hover:text-{{ $alert['color'] }}-800 whitespace-nowrap">
                                    {{ $alert['action_label'] ?? 'Open' }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500">
                        <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm">No critical alerts</p>
                        <p class="text-xs text-gray-400 mt-1">Attendance, payroll, and requests are currently in a healthy state.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
                <a href="{{ route('selfservice.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                    @php($activityHref = $activity['link'] ?? '#')
                    <a href="{{ $activityHref }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="{{ $activity['icon'] }}" class="w-4 h-4 text-{{ $activity['color'] }}-600"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $activity['title'] }}</p>
                                    <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ $activity['time'] }}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $activity['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-6 text-gray-500">
                        <i data-feather="activity" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm">No recent activities</p>
                        <p class="text-xs text-gray-400 mt-1">New hires, payroll updates, and HR requests will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @foreach(($quickActions ?? []) as $action)
                    <a href="{{ $action['href'] }}" class="w-full flex items-start justify-between gap-3 p-3 bg-{{ $action['color'] }}-50 rounded-lg hover:bg-{{ $action['color'] }}-100 transition-colors text-left border border-{{ $action['color'] }}-100">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                <i data-feather="{{ $action['icon'] }}" class="w-5 h-5 text-{{ $action['color'] }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $action['label'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $action['description'] }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-[11px] font-medium bg-white text-{{ $action['color'] }}-700 whitespace-nowrap">
                            {{ $action['badge'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Legal Compliance Dashboard -->
    <div class="bg-gradient-to-r from-indigo-900 to-purple-900 rounded-xl p-6 text-white mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold">Legal Compliance Status</h3>
            @if($compliance['status'] === 'Compliant')
            <span class="px-3 py-1 bg-green-500 rounded-full text-sm font-medium">Compliant</span>
            @elseif($compliance['status'] === 'Partially Compliant')
            <span class="px-3 py-1 bg-yellow-500 rounded-full text-sm font-medium">Partially Compliant</span>
            @else
            <span class="px-3 py-1 bg-red-500 rounded-full text-sm font-medium">Needs Attention</span>
            @endif
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">{{ $compliance['labour'] }}%</div>
                <p class="text-indigo-200 text-sm">LABOUR ACT COMPLIANCE</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">{{ $compliance['nssf'] }}%</div>
                <p class="text-indigo-200 text-sm">NSSF CONTRIBUTIONS</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">{{ $compliance['wcf'] }}%</div>
                <p class="text-indigo-200 text-sm">WCF COMPLIANCE</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">{{ $compliance['data'] }}%</div>
                <p class="text-indigo-200 text-sm">DATA PROTECTION</p>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-indigo-700">
            <div class="flex items-center justify-between">
                <p class="text-indigo-200 text-sm">Overall compliance: {{ $compliance['average'] }}% · Last audit: {{ $compliance['last_audit'] ?? 'N/A' }}</p>
                <a href="{{ route('compliance.index') }}" class="px-4 py-2 bg-white text-indigo-900 rounded-lg font-medium hover:bg-indigo-50 transition-colors">
                    View Full Report
                </a>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events & Deadlines</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-sm font-medium text-gray-900">Contract Renewals</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $events['contract_renewals'] }}</p>
                <p class="text-sm text-gray-600">Due in the next 30 days</p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="users" class="w-5 h-5 text-green-600"></i>
                    <span class="text-sm font-medium text-gray-900">Training Sessions</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $events['trainings'] }}</p>
                <p class="text-sm text-gray-600">Scheduled in the next 30 days</p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="file-text" class="w-5 h-5 text-purple-600"></i>
                    <span class="text-sm font-medium text-gray-900">Statutory IDs Missing</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $events['statutory_gaps'] }}</p>
                <p class="text-sm text-gray-600">Active employees lacking NSSF/TIN/NHIF</p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="award" class="w-5 h-5 text-amber-600"></i>
                    <span class="text-sm font-medium text-gray-900">Probation Reviews</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $events['probation_ending'] }}</p>
                <p class="text-sm text-gray-600">Probation ending in the next 30 days</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Notification functions
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    function removeNotification(id) {
        const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (notification) {
            notification.remove();
            updateNotificationBadge();
        }
    }

    function markAllAsRead() {
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach(notification => {
            notification.remove();
        });
        updateNotificationBadge();
        toggleNotifications();
    }

    function updateNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        const notifications = document.querySelectorAll('.notification-item');
        if (badge) {
            const count = notifications.length;
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather Icons first
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Initialize Charts
        initializeCharts();
    });
    
    function initializeCharts() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }
        
        // Destroy existing charts if they exist
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.destroy();
        });
        
        // Employee Distribution Chart
        const employeeCtx = document.getElementById('employeeChart');
        if (employeeCtx) {
            const dist = @json($charts['distribution']);
            const hasData = (dist.data || []).length > 0;
            new Chart(employeeCtx, {
                type: 'doughnut',
                data: {
                    labels: hasData ? dist.labels : ['No Data'],
                    datasets: [{
                        data: hasData ? dist.data : [1],
                        backgroundColor: hasData ? dist.colors : ['#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: hasData ? undefined : {
                            callbacks: {
                                label: () => 'No employees yet'
                            }
                        }
                    },
                    animation: {
                        duration: 0 // Disable animation to prevent continuous updates
                    }
                }
            });
        }

        // Attendance Trend Chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            const att = @json($charts['attendance']);
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: att.labels,
                    datasets: [{
                        label: 'Attendance Rate %',
                        data: att.rates,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    animation: {
                        duration: 0 // Disable animation to prevent continuous updates
                    }
                }
            });
        }

        // Payroll Trend Chart
        const payrollCtx = document.getElementById('payrollChart');
        if (payrollCtx) {
            const pay = @json($charts['payroll']);
            new Chart(payrollCtx, {
                type: 'bar',
                data: {
                    labels: pay.labels,
                    datasets: [{
                        label: 'Net Pay',
                        data: pay.totals,
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('en', { notation: 'compact' }).format(value);
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 0 // Disable animation to prevent continuous updates
                    }
                }
            });
        }
    }
</script>

@push('scripts')
<script>
// Fallback switchClient function - only use if main function not available
if (typeof window.switchClient !== 'function') {
    window.switchClient = function(clientId) {
        // Show client switch modal
        showClientSwitchModal(clientId);
    };
    
    // Include necessary modal functions
    function showClientSwitchModal(clientId) {
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        modalOverlay.id = 'clientSwitchModal';
        
        // Use live client data from window.allClients
        const clientName = getClientNameById ? getClientNameById(clientId) : 'Unknown Client';
        
        modalOverlay.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 transform transition-all">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-feather="briefcase" class="w-8 h-8 text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Switch Client</h3>
                    <p class="text-gray-600">Are you sure you want to switch to <strong>${clientName}</strong>?</p>
                    <p class="text-sm text-gray-500 mt-2">All data will be refreshed and updated.</p>
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="closeClientSwitchModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmClientSwitch('${clientId}', '${clientName}')" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Switch Client
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modalOverlay);
        
        // Add blur effect to background
        document.body.classList.add('backdrop-blur-sm');
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        setTimeout(() => {
            modalOverlay.querySelector('.transform').classList.add('scale-100');
        }, 10);
    }
    
    function closeClientSwitchModal() {
        const modal = document.getElementById('clientSwitchModal');
        if (modal) {
            modal.querySelector('.transform').classList.remove('scale-100');
            
            // Remove blur effect from background
            document.body.classList.remove('backdrop-blur-sm');
            
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 200);
        }
    }
    
    function confirmClientSwitch(clientId, clientName) {
        closeClientSwitchModal();
        
        // Store selected client
        localStorage.setItem('selectedClient', clientId);
        
        // Destroy charts before reload
        if (typeof Chart !== 'undefined') {
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.destroy();
            });
        }
        
        // Reload page to update data
        window.location.reload();
    }
}
</script>
@endpush
@endsection
