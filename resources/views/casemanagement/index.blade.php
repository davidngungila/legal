@extends('layouts.app')

@section('title', 'Case Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Case Management</h1>
            <p class="text-gray-600 mt-2">Manage HR cases and legal documentation</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Case
            </button>
        </div>
    </div>

    <!-- Case Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="folder" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">+3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">24</h3>
            <p class="text-gray-600 text-sm">Active Cases</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">5</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">12</h3>
            <p class="text-gray-600 text-sm">Pending Review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+8</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">45</h3>
            <p class="text-gray-600 text-sm">Resolved This Month</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">2</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">3</h3>
            <p class="text-gray-600 text-sm">High Priority</p>
        </div>
    </div>

    <!-- Active Cases -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Active Cases</h3>
            <div class="flex space-x-3">
                <select class="form-select">
                    <option>All Cases</option>
                    <option>Disciplinary</option>
                    <option>Grievance</option>
                    <option>Complaint</option>
                    <option>Legal</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="filter" class="w-4 h-4 inline mr-2"></i>
                    Filter
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Opened</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['id' => 'CASE-001', 'employee' => 'John Doe', 'type' => 'Disciplinary', 'subject' => 'Unauthorized absence', 'date' => '2024-03-15', 'priority' => 'High', 'status' => 'Under Investigation'],
                        ['id' => 'CASE-002', 'employee' => 'Sarah Smith', 'type' => 'Grievance', 'subject' => 'Working hours dispute', 'date' => '2024-03-18', 'priority' => 'Medium', 'status' => 'Review'],
                        ['id' => 'CASE-003', 'employee' => 'Mike Johnson', 'type' => 'Complaint', 'subject' => 'Harassment allegation', 'date' => '2024-03-20', 'priority' => 'High', 'status' => 'Investigation'],
                        ['id' => 'CASE-004', 'employee' => 'Emily Davis', 'type' => 'Legal', 'subject' => 'Contract termination', 'date' => '2024-03-22', 'priority' => 'Medium', 'status' => 'Documentation'],
                        ['id' => 'CASE-005', 'employee' => 'David Wilson', 'type' => 'Disciplinary', 'subject' => 'Policy violation', 'date' => '2024-03-25', 'priority' => 'Low', 'status' => 'Pending'],
                        ['id' => 'CASE-006', 'employee' => 'Lisa Brown', 'type' => 'Grievance', 'subject' => 'Salary discrepancy', 'date' => '2024-03-28', 'priority' => 'Medium', 'status' => 'Resolution']
                    ] as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case['id'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($case['employee'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $case['employee'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($case['type'] === 'Disciplinary') bg-red-100 text-red-800
                                @elseif($case['type'] === 'Grievance') bg-yellow-100 text-yellow-800
                                @elseif($case['type'] === 'Complaint') bg-orange-100 text-orange-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ $case['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $case['subject'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $case['date'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($case['priority'] === 'High') bg-red-100 text-red-800
                                @elseif($case['priority'] === 'Medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $case['priority'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($case['status'] === 'Under Investigation') bg-red-100 text-red-800
                                @elseif($case['status'] === 'Investigation') bg-orange-100 text-orange-800
                                @elseif($case['status'] === 'Review') bg-yellow-100 text-yellow-800
                                @elseif($case['status'] === 'Documentation') bg-blue-100 text-blue-800
                                @elseif($case['status'] === 'Pending') bg-gray-100 text-gray-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $case['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                            <button class="text-gray-600 hover:text-gray-900">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Case Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach([
            ['type' => 'Disciplinary', 'count' => 8, 'color' => 'red', 'icon' => 'alert-triangle'],
            ['type' => 'Grievance', 'count' => 6, 'color' => 'yellow', 'icon' => 'message-square'],
            ['type' => 'Complaint', 'count' => 4, 'color' => 'orange', 'icon' => 'flag'],
            ['type' => 'Legal', 'count' => 6, 'color' => 'purple', 'icon' => 'gavel']
        ] as $category)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-{{ $category['color'] }}-100 rounded-lg flex items-center justify-center">
                    <i data-feather="{{ $category['icon'] }}" class="w-6 h-6 text-{{ $category['color'] }}-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $category['count'] }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">{{ $category['type'] }} Cases</h3>
            <p class="text-sm text-gray-600 mb-4">Active {{ $category['type'] }} cases under review</p>
            <button class="text-{{ $category['color'] }}-600 hover:text-{{ $category['color'] }}-800 text-sm font-medium">View All →</button>
        </div>
        @endforeach
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Case Activities</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="space-y-4">
            @foreach([
                ['case' => 'CASE-001', 'action' => 'New evidence submitted', 'employee' => 'John Doe', 'time' => '2 hours ago', 'user' => 'HR Admin'],
                ['case' => 'CASE-002', 'action' => 'Meeting scheduled', 'employee' => 'Sarah Smith', 'time' => '4 hours ago', 'user' => 'HR Manager'],
                ['case' => 'CASE-003', 'action' => 'Investigation completed', 'employee' => 'Mike Johnson', 'time' => '6 hours ago', 'user' => 'Legal Advisor'],
                ['case' => 'CASE-004', 'action' => 'Document uploaded', 'employee' => 'Emily Davis', 'time' => '1 day ago', 'user' => 'HR Officer'],
                ['case' => 'CASE-005', 'action' => 'Status updated', 'employee' => 'David Wilson', 'time' => '2 days ago', 'user' => 'HR Admin']
            ] as $activity)
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                    <i data-feather="activity" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['case'] }} • {{ $activity['employee'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['user'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Legal Documents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Legal Document Templates</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Templates</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Disciplinary Notice', 'type' => 'Disciplinary', 'uses' => 45, 'status' => 'Active'],
                ['name' => 'Grievance Form', 'type' => 'Grievance', 'uses' => 32, 'status' => 'Active'],
                ['name' => 'Warning Letter', 'type' => 'Disciplinary', 'uses' => 28, 'status' => 'Active'],
                ['name' => 'Termination Notice', 'type' => 'Legal', 'uses' => 15, 'status' => 'Active'],
                ['name' => 'Complaint Form', 'type' => 'Complaint', 'uses' => 22, 'status' => 'Active'],
                ['name' => 'Settlement Agreement', 'type' => 'Legal', 'uses' => 8, 'status' => 'Active']
            ] as $template)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $template['status'] }}</span>
                    <span class="text-xs text-gray-500">{{ $template['uses'] }} uses</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i data-feather="file-text" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $template['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $template['type'] }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Use Template</button>
                    <button class="text-gray-600 hover:text-gray-800 text-sm">Edit</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
