@extends('layouts.app')

@section('title', 'Predictive Analytics - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Predictive Analytics</h1>
            <p class="text-gray-600 mt-2">Future-focused insights for proactive HR planning</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <select class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option>Next 6 Months</option>
                <option>Next 12 Months</option>
                <option>Next 18 Months</option>
            </select>
        </div>
    </div>

    <!-- Predictive Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold">22%</h3>
            <p class="text-blue-100 text-sm">Projected Turnover Rate</p>
            <p class="text-xs text-blue-200 mt-2">Based on current trends</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-feather="target" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold">18</h3>
            <p class="text-purple-100 text-sm">At-Risk Employees</p>
            <p class="text-xs text-purple-200 mt-2">Recommended for intervention</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold">12</h3>
            <p class="text-green-100 text-sm">Critical Roles to Fill</p>
            <p class="text-xs text-green-200 mt-2">Projected vacancies</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold">TZS 45M</h3>
            <p class="text-orange-100 text-sm">Projected Hiring Costs</p>
            <p class="text-xs text-orange-200 mt-2">Including onboarding</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- At-Risk Employees -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">At-Risk Employees (Turnover Predictions)</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key Factors</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommended Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach([
                            ['name' => 'David Kimani', 'dept' => 'Sales', 'risk' => 'High', 'factors' => ['Low Engagement', 'Recent Offer'], 'action' => 'Stay Interview'],
                            ['name' => 'Amina Juma', 'dept' => 'IT', 'risk' => 'High', 'factors' => ['Long Overtime', 'Compensation Below Market'], 'action' => 'Compensation Review'],
                            ['name' => 'John Smith', 'dept' => 'Marketing', 'risk' => 'Medium', 'factors' => ['Last Promotion > 2 Years'], 'action' => 'Career Discussion'],
                            ['name' => 'Sarah Johnson', 'dept' => 'HR', 'risk' => 'Medium', 'factors' => ['Commute Time'], 'action' => 'Flexible Work Options'],
                            ['name' => 'Michael Brown', 'dept' => 'Finance', 'risk' => 'Low', 'factors' => ['High Performance'], 'action' => 'Recognition']
                        ] as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white text-sm font-bold">{{ substr($employee['name'], 0, 1) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $employee['dept'] }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($employee['risk'] == 'High')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">High Risk</span>
                                @elseif($employee['risk'] == 'Medium')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Medium Risk</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Low Risk</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($employee['factors'] as $factor)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full">{{ $factor }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button class="text-sm text-indigo-600 font-medium hover:underline">{{ $employee['action'] }}</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Turnover Predictions by Department -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Turnover Predictions by Department</h3>
            <div class="space-y-4">
                @foreach([
                    ['dept' => 'Sales', 'rate' => 28, 'color' => 'red'],
                    ['dept' => 'IT', 'rate' => 25, 'color' => 'orange'],
                    ['dept' => 'Operations', 'rate' => 22, 'color' => 'yellow'],
                    ['dept' => 'Marketing', 'rate' => 18, 'color' => 'green'],
                    ['dept' => 'HR', 'rate' => 15, 'color' => 'teal'],
                    ['dept' => 'Finance', 'rate' => 12, 'color' => 'blue']
                ] as $dept)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between mb-1">
                        <span class="font-medium text-gray-900">{{ $dept['dept'] }}</span>
                        <span class="text-sm text-gray-600 font-bold">{{ $dept['rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-{{ $dept['color'] }}-500 to-{{ $dept['color'] }}-600 h-2 rounded-full" style="width: {{ $dept['rate'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Forecast & Scenarios -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Headcount Forecast -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Headcount Forecast (Next 12 Months)</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border border-green-100">
                    <h4 class="font-medium text-gray-900 mb-2">Planned Hires</h4>
                    <div class="text-3xl font-bold text-green-600 mb-1">+24</div>
                    <p class="text-sm text-gray-600">New roles approved for FY 2024/25</p>
                </div>

                <div class="p-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-100">
                    <h4 class="font-medium text-gray-900 mb-2">Projected Departures</h4>
                    <div class="text-3xl font-bold text-red-600 mb-1">-18</div>
                    <p class="text-sm text-gray-600">Based on historical and predictive data</p>
                </div>

                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <h4 class="font-medium text-gray-900 mb-2">Net Change</h4>
                    <div class="text-3xl font-bold text-blue-600 mb-1">+6</div>
                    <p class="text-sm text-gray-600">Projected net headcount growth</p>
                </div>
            </div>
        </div>

        <!-- Risk Scenarios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Scenario Analysis</h3>
            <div class="space-y-3">
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-1 flex items-center">
                        <i data-feather="alert-triangle" class="w-4 h-4 text-yellow-600 mr-2"></i>
                        Moderate Risk
                    </h4>
                    <p class="text-xs text-gray-600">If no intervention: 22% turnover rate, TZS 45M hiring costs</p>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-1 flex items-center">
                        <i data-feather="trending-up" class="w-4 h-4 text-blue-600 mr-2"></i>
                        Target Intervention
                    </h4>
                    <p class="text-xs text-gray-600">With stay interviews and retention programs: 15% turnover, TZS 32M cost savings</p>
                </div>

                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-1 flex items-center">
                        <i data-feather="check-circle" class="w-4 h-4 text-green-600 mr-2"></i>
                        Optimistic Scenario
                    </h4>
                    <p class="text-xs text-gray-600">Full retention program implementation: 10% turnover, TZS 55M cost savings</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
