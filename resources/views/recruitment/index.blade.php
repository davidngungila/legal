@extends('layouts.app')

@section('title', 'Recruitment - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Recruitment Management</h1>
            <p class="text-gray-600 mt-2">Manage job applications and hiring process</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Post New Job
            </button>
        </div>
    </div>

    <!-- Recruitment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">24</h3>
            <p class="text-gray-600 text-sm">Active Positions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+8%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">156</h3>
            <p class="text-gray-600 text-sm">Total Applicants</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">18</h3>
            <p class="text-gray-600 text-sm">In Progress</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+15%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">8</h3>
            <p class="text-gray-600 text-sm">Hired This Month</p>
        </div>
    </div>

    <!-- Job Postings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Active Job Postings</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['title' => 'Senior Software Engineer', 'dept' => 'IT', 'type' => 'Full-time', 'applicants' => 45, 'status' => 'Active'],
                ['title' => 'HR Manager', 'dept' => 'HR', 'type' => 'Full-time', 'applicants' => 28, 'status' => 'Active'],
                ['title' => 'Marketing Specialist', 'dept' => 'Marketing', 'type' => 'Contract', 'applicants' => 32, 'status' => 'Active'],
                ['title' => 'Financial Analyst', 'dept' => 'Finance', 'type' => 'Full-time', 'applicants' => 19, 'status' => 'Closing Soon'],
                ['title' => 'Operations Supervisor', 'dept' => 'Operations', 'type' => 'Full-time', 'applicants' => 24, 'status' => 'Active'],
                ['title' => 'Sales Executive', 'dept' => 'Sales', 'type' => 'Commission', 'applicants' => 8, 'status' => 'Urgent']
            ] as $job)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $job['status'] }}</span>
                    <span class="text-xs text-gray-500">{{ $job['type'] }}</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">{{ $job['title'] }}</h4>
                <p class="text-sm text-gray-600 mb-3">{{ $job['dept'] }} Department</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ $job['applicants'] }} applicants</span>
                    <button class="text-indigo-600 hover:text-indigo-800 font-medium">View →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Applications -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'John Smith', 'position' => 'Senior Software Engineer', 'applied' => '2 days ago', 'status' => 'Screening', 'score' => 85],
                        ['name' => 'Sarah Johnson', 'position' => 'HR Manager', 'applied' => '3 days ago', 'status' => 'Interview', 'score' => 92],
                        ['name' => 'Michael Brown', 'position' => 'Marketing Specialist', 'applied' => '1 week ago', 'status' => 'Review', 'score' => 78],
                        ['name' => 'Emily Davis', 'position' => 'Financial Analyst', 'applied' => '2 weeks ago', 'status' => 'Shortlisted', 'score' => 88],
                        ['name' => 'David Wilson', 'position' => 'Operations Supervisor', 'applied' => '3 weeks ago', 'status' => 'Rejected', 'score' => 65]
                    ] as $application)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($application['name'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $application['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application['position'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application['applied'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($application['status'] === 'Screening') bg-blue-100 text-blue-800
                                @elseif($application['status'] === 'Interview') bg-yellow-100 text-yellow-800
                                @elseif($application['status'] === 'Review') bg-purple-100 text-purple-800
                                @elseif($application['status'] === 'Shortlisted') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $application['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium {{ $application['score'] >= 80 ? 'text-green-600' : ($application['score'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $application['score'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Review</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
