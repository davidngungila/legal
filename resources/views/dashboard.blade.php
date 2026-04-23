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
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['monthly_payroll'], 1) }}M</h3>
            <p class="text-gray-600 text-sm">Monthly Payroll</p>
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
                    <span class="text-sm font-medium">{{ $currentClient?->industry ?? 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Departments:</span>
                    <span class="text-sm font-medium">{{ $currentClient?->departments_count ?? 8 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Locations:</span>
                    <span class="text-sm font-medium">{{ $currentClient?->locations_count ?? 3 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Founded:</span>
                    <span class="text-sm font-medium">{{ $currentClient?->founded_year ?? '2015' }}</span>
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
                    <span class="text-sm text-gray-600">Turnover:</span>
                    <span class="text-sm font-medium text-red-600">3.2%</span>
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
                    <span class="text-sm text-gray-600">Weekly Hours:</span>
                    <span class="text-sm font-medium">{{ $stats['present_today'] * 40 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Overtime:</span>
                    <span class="text-sm font-medium text-blue-600">120</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
    </div>

    <!-- Alerts and Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Critical Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Critical Alerts</h3>
                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
            </div>
            <div class="space-y-3">
                @forelse($alerts as $alert)
                    <div class="flex items-start space-x-3 p-3 bg-{{ $alert['color'] }}-50 rounded-lg">
                        <i data-feather="{{ $alert['icon'] }}" class="w-5 h-5 text-{{ $alert['color'] }}-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</p>
                            <p class="text-xs text-gray-600">{{ $alert['description'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        <i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm">No critical alerts</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h3>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-feather="{{ $activity['icon'] }}" class="w-4 h-4 text-{{ $activity['color'] }}-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-600">{{ $activity['description'] }} - {{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        <i data-feather="activity" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-sm">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="user-plus" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Add New Employee</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="calendar" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Approve Leave Request</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="credit-card" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Process Payroll</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="file-plus" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Create Case File</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="trending-up" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Generate Reports</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Legal Compliance Dashboard -->
    <div class="bg-gradient-to-r from-indigo-900 to-purple-900 rounded-xl p-6 text-white mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold">Legal Compliance Status</h3>
            <span class="px-3 py-1 bg-green-500 rounded-full text-sm font-medium">Compliant</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">100%</div>
                <p class="text-indigo-200 text-sm">LABOUR ACT COMPLIANCE</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">100%</div>
                <p class="text-indigo-200 text-sm">NSSF CONTRIBUTIONS</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">100%</div>
                <p class="text-indigo-200 text-sm">WCF COMPLIANCE</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">98%</div>
                <p class="text-indigo-200 text-sm">DATA PROTECTION</p>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-indigo-700">
            <div class="flex items-center justify-between">
                <p class="text-indigo-200 text-sm">Last compliance audit: {{ now()->subDays(15)->format('d M Y') }}</p>
                <button class="px-4 py-2 bg-white text-indigo-900 rounded-lg font-medium hover:bg-indigo-50 transition-colors">
                    View Full Report
                </button>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Events & Deadlines</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                    <span class="text-sm font-medium text-gray-900">Contract Renewals</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">3</p>
                <p class="text-sm text-gray-600">Due this month</p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="users" class="w-5 h-5 text-green-600"></i>
                    <span class="text-sm font-medium text-gray-900">Training Sessions</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">2</p>
                <p class="text-sm text-gray-600">Scheduled this week</p>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center space-x-3 mb-2">
                    <i data-feather="file-text" class="w-5 h-5 text-purple-600"></i>
                    <span class="text-sm font-medium text-gray-900">Statutory Filings</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">5</p>
                <p class="text-sm text-gray-600">Due next month</p>
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
            new Chart(employeeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Permanent', 'Contract', 'Probation', 'Intern'],
                    datasets: [{
                        data: [180, 45, 18, 5],
                        backgroundColor: [
                            '#6366f1',
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
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
            new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                    datasets: [{
                        label: 'Attendance Rate %',
                        data: [95, 94, 96, 93, 95, 97, 96, 94, 95, 93, 94],
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
                            beginAtZero: false,
                            min: 85,
                            max: 100
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
