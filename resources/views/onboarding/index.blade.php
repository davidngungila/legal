@extends('layouts.app')

@section('title', 'Employee Onboarding - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Onboarding</h1>
            <p class="text-gray-600 mt-2">Manage new employee onboarding process</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Start Onboarding
            </button>
        </div>
    </div>

    <!-- Onboarding Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">12</h3>
            <p class="text-gray-600 text-sm">Active Onboarding</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">95%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">8</h3>
            <p class="text-gray-600 text-sm">Completed This Month</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">2</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">3</h3>
            <p class="text-gray-600 text-sm">Pending Tasks</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">-2 days</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">5</h3>
            <p class="text-gray-600 text-sm">Avg. Onboarding Days</p>
        </div>
    </div>

    <!-- Active Onboarding Processes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Active Onboarding Processes</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Alice Johnson', 'position' => 'Software Developer', 'start' => '2024-03-15', 'progress' => 75, 'status' => 'In Progress'],
                ['name' => 'Bob Smith', 'position' => 'HR Assistant', 'start' => '2024-03-18', 'progress' => 60, 'status' => 'In Progress'],
                ['name' => 'Carol Williams', 'position' => 'Accountant', 'start' => '2024-03-20', 'progress' => 40, 'status' => 'In Progress'],
                ['name' => 'David Brown', 'position' => 'Marketing Coordinator', 'start' => '2024-03-22', 'progress' => 90, 'status' => 'Almost Complete'],
                ['name' => 'Emma Davis', 'position' => 'Operations Manager', 'start' => '2024-03-25', 'progress' => 20, 'status' => 'Just Started'],
                ['name' => 'Frank Miller', 'position' => 'Sales Executive', 'start' => '2024-03-28', 'progress' => 10, 'status' => 'Just Started']
            ] as $onboarding)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ $onboarding['status'] }}</span>
                    <span class="text-xs text-gray-500">{{ $onboarding['progress'] }}%</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr($onboarding['name'], 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-gray-900">{{ $onboarding['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $onboarding['position'] }}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium">{{ $onboarding['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $onboarding['progress'] }}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Started: {{ $onboarding['start'] }}</span>
                    <button class="text-indigo-600 hover:text-indigo-800 font-medium">Manage →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Onboarding Checklist Template -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Onboarding Checklist Template</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit Template</button>
        </div>
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Day 1: Orientation</h4>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3" checked>
                        <label class="text-sm text-gray-700">Welcome kit and company materials</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3" checked>
                        <label class="text-sm text-gray-700">IT setup and system access</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Office tour and introductions</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">HR policy review</label>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Week 1: Department Integration</h4>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Department-specific training</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Team meetings and introductions</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Assign mentor/buddy</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">First project assignment</label>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Month 1: Full Integration</h4>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Performance goals setting</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">30-day review meeting</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Benefits enrollment completion</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3">
                        <label class="text-sm text-gray-700">Training plan finalization</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Completed Onboarding -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recently Completed Onboarding</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'Grace Taylor', 'position' => 'Junior Developer', 'dept' => 'IT', 'start' => '2024-02-01', 'duration' => '4 days'],
                        ['name' => 'Henry Anderson', 'position' => 'Sales Rep', 'dept' => 'Sales', 'start' => '2024-02-05', 'duration' => '6 days'],
                        ['name' => 'Iris Martinez', 'position' => 'HR Coordinator', 'dept' => 'HR', 'start' => '2024-02-10', 'duration' => '5 days'],
                        ['name' => 'Jack Thompson', 'position' => 'Accountant', 'dept' => 'Finance', 'start' => '2024-02-15', 'duration' => '7 days'],
                        ['name' => 'Kate Wilson', 'position' => 'Marketing Assistant', 'dept' => 'Marketing', 'start' => '2024-02-20', 'duration' => '4 days']
                    ] as $completed)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($completed['name'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $completed['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $completed['position'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $completed['dept'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $completed['start'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $completed['duration'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
