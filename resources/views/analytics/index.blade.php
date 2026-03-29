@extends('layouts.app')

@section('title', 'Workforce Analytics - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Workforce Analytics</h1>
            <p class="text-gray-600 mt-2">Data-driven insights for strategic HR decision making</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <select class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option>Last 30 Days</option>
                <option>Last 3 Months</option>
                <option>Last 6 Months</option>
                <option>Last Year</option>
                <option>Custom Range</option>
            </select>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-down" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">-8.5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">12.3%</h3>
            <p class="text-gray-600 text-sm">Turnover Rate (Annual)</p>
            <p class="text-xs text-gray-500 mt-2">Industry average: 15.2%</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar-x" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">+2.3%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">4.8%</h3>
            <p class="text-gray-600 text-sm">Absenteeism Rate</p>
            <p class="text-xs text-gray-500 mt-2">Target: < 4%</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS 182K</h3>
            <p class="text-gray-600 text-sm">Cost per Employee</p>
            <p class="text-xs text-gray-500 mt-2">Monthly average</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">Medium</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">23%</h3>
            <p class="text-gray-600 text-sm">Legal Risk Exposure</p>
            <p class="text-xs text-gray-500 mt-2">3 high-risk cases active</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Employee Turnover Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Turnover Trend</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="turnoverChart"></canvas>
            </div>
        </div>

        <!-- Department Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Headcount by Department</h3>
            <div class="relative" style="height: 300px;">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Predictive Analytics -->
    <div class="bg-gradient-to-r from-purple-900 to-indigo-900 rounded-xl p-6 text-white mb-8">
        <h3 class="text-xl font-semibold mb-4">Predictive Analytics & Risk Assessment</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">78%</div>
                <p class="text-purple-200 text-sm mb-1">Employee Retention Probability</p>
                <p class="text-xs text-purple-300">Next 6 months</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">15%</div>
                <p class="text-purple-200 text-sm mb-1">Dispute Probability</p>
                <p class="text-xs text-purple-300">Based on current patterns</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold mb-2">92%</div>
                <p class="text-purple-200 text-sm mb-1">Compliance Score Forecast</p>
                <p class="text-xs text-purple-300">Next quarter</p>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-purple-700">
            <h4 class="font-semibold mb-3">AI-Powered Insights</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-purple-800 bg-opacity-50 rounded-lg p-3">
                    <p class="text-sm font-medium mb-1">🔍 High-Risk Employees Identified</p>
                    <p class="text-xs text-purple-200">3 employees show patterns indicating potential turnover risk</p>
                </div>
                <div class="bg-purple-800 bg-opacity-50 rounded-lg p-3">
                    <p class="text-sm font-medium mb-1">⚠️ Compliance Alert</p>
                    <p class="text-xs text-purple-200">5 contracts expiring in next 30 days require renewal</p>
                </div>
                <div class="bg-purple-800 bg-opacity-50 rounded-lg p-3">
                    <p class="text-sm font-medium mb-1">📈 Performance Trend</p>
                    <p class="text-xs text-purple-200">IT department showing 15% productivity increase</p>
                </div>
                <div class="bg-purple-800 bg-opacity-50 rounded-lg p-3">
                    <p class="text-sm font-medium mb-1">💰 Cost Optimization</p>
                    <p class="text-xs text-purple-200">Overtime costs could be reduced by 22% with better scheduling</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Performers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performers</h3>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'Sarah Williams', 'dept' => 'HR', 'score' => 96, 'trend' => 'up'],
                    ['name' => 'John Doe', 'dept' => 'IT', 'score' => 94, 'trend' => 'up'],
                    ['name' => 'Michael Chen', 'dept' => 'Finance', 'score' => 92, 'trend' => 'stable'],
                    ['name' => 'Emily Johnson', 'dept' => 'Operations', 'score' => 91, 'trend' => 'up'],
                    ['name' => 'David Kimani', 'dept' => 'Sales', 'score' => 89, 'trend' => 'down']
                ] as $employee)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-medium">{{ substr($employee['name'], 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $employee['dept'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ $employee['score'] }}%</p>
                            <div class="flex items-center">
                                @if($employee['trend'] == 'up')
                                    <i data-feather="trending-up" class="w-3 h-3 text-green-500"></i>
                                @elseif($employee['trend'] == 'down')
                                    <i data-feather="trending-down" class="w-3 h-3 text-red-500"></i>
                                @else
                                    <i data-feather="minus" class="w-3 h-3 text-gray-400"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Attendance Analytics -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Attendance Analytics</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Average Attendance Rate</p>
                            <p class="text-xs text-gray-500">Monthly average</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-green-600">94.2%</p>
                        <p class="text-xs text-green-500">+1.2% vs last month</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i data-feather="clock" class="w-5 h-5 text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Late Arrivals</p>
                            <p class="text-xs text-gray-500">This month</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-yellow-600">127</p>
                        <p class="text-xs text-yellow-500">-8% vs last month</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Leave Utilization</p>
                            <p class="text-xs text-gray-500">Annual leave taken</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-blue-600">68%</p>
                        <p class="text-xs text-blue-500">1,892 days used</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-feather="activity" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Overtime Hours</p>
                            <p class="text-xs text-gray-500">This month</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-purple-600">847</p>
                        <p class="text-xs text-purple-500">TZS 2.5M cost</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Department Performance Metrics</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Headcount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Performance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turnover Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost per Employee</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['dept' => 'IT', 'count' => 45, 'attendance' => 96.5, 'performance' => 91, 'turnover' => 8.2, 'training' => 124, 'cost' => 195000],
                        ['dept' => 'HR', 'count' => 12, 'attendance' => 97.8, 'performance' => 94, 'turnover' => 6.5, 'training' => 89, 'cost' => 165000],
                        ['dept' => 'Finance', 'count' => 28, 'attendance' => 95.2, 'performance' => 88, 'turnover' => 9.1, 'training' => 76, 'cost' => 178000],
                        ['dept' => 'Operations', 'count' => 89, 'attendance' => 93.8, 'performance' => 85, 'turnover' => 14.3, 'training' => 92, 'cost' => 142000],
                        ['dept' => 'Sales', 'count' => 56, 'attendance' => 91.5, 'performance' => 87, 'turnover' => 18.7, 'training' => 68, 'cost' => 189000],
                        ['dept' => 'Marketing', 'count' => 18, 'attendance' => 94.1, 'performance' => 90, 'turnover' => 11.2, 'training' => 95, 'cost' => 172000]
                    ] as $dept)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-feather="briefcase" class="w-4 h-4 text-indigo-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $dept['dept'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $dept['count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium {{ $dept['attendance'] >= 95 ? 'text-green-600' : ($dept['attendance'] >= 90 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $dept['attendance'] }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium {{ $dept['performance'] >= 90 ? 'text-green-600' : ($dept['performance'] >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $dept['performance'] }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium {{ $dept['turnover'] <= 10 ? 'text-green-600' : ($dept['turnover'] <= 15 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $dept['turnover'] }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $dept['training'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($dept['cost'], 0) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legal Risk Analysis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Legal Risk Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Contract Compliance</h4>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Low Risk</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Valid Contracts</span>
                        <span class="font-medium">246 / 248</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Expiring Soon</span>
                        <span class="font-medium text-yellow-600">5</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Missing Signatures</span>
                        <span class="font-medium">0</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Disciplinary Cases</h4>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Medium Risk</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Active Cases</span>
                        <span class="font-medium">7</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">High Risk Cases</span>
                        <span class="font-medium text-red-600">3</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pending HR Admin Review</span>
                        <span class="font-medium text-orange-600">3</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900">Statutory Compliance</h4>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Compliant</span>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">PAYE Filings</span>
                        <span class="font-medium text-green-600">Current</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">NSSF Contributions</span>
                        <span class="font-medium text-green-600">Current</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">WCF Declarations</span>
                        <span class="font-medium text-green-600">Current</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather Icons first
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Initialize Analytics Charts
        initializeAnalyticsCharts();
    });
    
    function initializeAnalyticsCharts() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }
        
        // Destroy existing charts if they exist
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.destroy();
        });
        
        // Employee Turnover Trend Chart
        const turnoverCtx = document.getElementById('turnoverChart');
        if (turnoverCtx) {
            new Chart(turnoverCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Turnover Rate %',
                        data: [14.2, 13.8, 15.1, 13.5, 12.9, 11.8, 12.3, 13.1, 12.7, 11.9, 12.3, 11.5],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Industry Average',
                        data: [15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2, 15.2],
                        borderColor: '#9ca3af',
                        borderDash: [5, 5],
                        tension: 0,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 10,
                            max: 20
                        }
                    },
                    animation: {
                        duration: 0 // Disable animation to prevent continuous updates
                    }
                }
            });
        }

        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart');
        if (departmentCtx) {
            new Chart(departmentCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Operations', 'Sales', 'IT', 'Finance', 'Marketing', 'HR'],
                    datasets: [{
                        data: [89, 56, 45, 28, 18, 12],
                        backgroundColor: [
                            '#6366f1',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#ec4899'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
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
// Fallback switchClient function if main app.js is not loaded
if (typeof switchClient === 'undefined') {
    function switchClient(clientId) {
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        
        const clientName = clientNames[clientId] || 'Unknown Client';
        
        // Add blur effect to background
        document.body.classList.add('backdrop-blur-sm');
        
        // Create modal with transparent background
        const modalOverlay = document.createElement('div');
        modalOverlay.id = 'clientSwitchModal';
        modalOverlay.className = 'fixed inset-0 flex items-center justify-center z-50';
        modalOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.1)'; // Very light transparent overlay
        modalOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md mx-4 transform scale-0 transition-transform duration-200 shadow-xl">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                        <i data-feather="users" class="w-6 h-6 text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Switch Client</h2>
                        <p class="text-sm text-gray-600">Are you sure you want to switch to ${clientName}?</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">All data will be refreshed and updated.</p>
                <div class="flex space-x-3">
                    <button onclick="closeClientSwitchModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmClientSwitch('${clientId}', '${clientName}')" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Switch Client
                    </button>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(modalOverlay);
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Add animation
        setTimeout(() => {
            modalOverlay.querySelector('.transform').classList.add('scale-100');
        }, 10);
    }
}

// Fallback closeClientSwitchModal function
if (typeof closeClientSwitchModal === 'undefined') {
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
}

// Fallback confirmClientSwitch function
if (typeof confirmClientSwitch === 'undefined') {
    function confirmClientSwitch(clientId, clientName) {
        closeClientSwitchModal();
        
        // Store selected client
        localStorage.setItem('selectedClient', clientId);
        localStorage.setItem('selectedClientName', clientName);
        
        // Show success notification
        showNotification(`Successfully switched to ${clientName}`, 'success');
        
        // Reload page to update all data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}

// Fallback toggleNotifications function
if (typeof toggleNotifications === 'undefined') {
    function toggleNotifications() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            notificationDropdown.classList.toggle('hidden');
        }
    }
}

// Fallback showNotification function
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        
        // Set color based on type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-white',
            info: 'bg-blue-500 text-white'
        };
        
        notification.className += ' ' + colors[type] || colors.info;
        notification.innerHTML = `
            <div class="flex items-center">
                <i data-feather="${type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5 mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}
</script>
@endpush

@endsection
