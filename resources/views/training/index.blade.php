@extends('layouts.app')

@section('title', 'Training & Development - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training & Development</h1>
            <p class="text-gray-600 mt-2">Manage employee training programs and development</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Training Program
            </button>
        </div>
    </div>

    <!-- Training Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="book-open" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+8%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">24</h3>
            <p class="text-gray-600 text-sm">Active Programs</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">156</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">89%</h3>
            <p class="text-gray-600 text-sm">Participation Rate</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">1,247</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">3,450</h3>
            <p class="text-gray-600 text-sm">Training Hours</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">92%</h3>
            <p class="text-gray-600 text-sm">Completion Rate</p>
        </div>
    </div>

    <!-- Active Training Programs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Active Training Programs</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Leadership Excellence', 'type' => 'Management', 'participants' => 24, 'duration' => '6 weeks', 'start' => '2024-03-01', 'progress' => 65],
                ['name' => 'Digital Marketing Mastery', 'type' => 'Marketing', 'participants' => 18, 'duration' => '4 weeks', 'start' => '2024-03-10', 'progress' => 80],
                ['name' => 'Advanced Excel Skills', 'type' => 'Technical', 'participants' => 45, 'duration' => '2 weeks', 'start' => '2024-03-15', 'progress' => 90],
                ['name' => 'Customer Service Excellence', 'type' => 'Soft Skills', 'participants' => 32, 'duration' => '3 weeks', 'start' => '2024-03-20', 'progress' => 45],
                ['name' => 'Project Management Professional', 'type' => 'Professional', 'participants' => 15, 'duration' => '8 weeks', 'start' => '2024-02-15', 'progress' => 85],
                ['name' => 'Financial Analysis Workshop', 'type' => 'Finance', 'participants' => 22, 'duration' => '3 weeks', 'start' => '2024-03-25', 'progress' => 25]
            ] as $program)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">{{ $program['type'] }}</span>
                    <span class="text-xs text-gray-500">{{ $program['progress'] }}% complete</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">{{ $program['name'] }}</h4>
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Participants:</span>
                        <span class="font-medium">{{ $program['participants'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-medium">{{ $program['duration'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Started:</span>
                        <span class="font-medium">{{ $program['start'] }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium">{{ $program['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $program['progress'] }}%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage →</button>
                    <button class="text-gray-600 hover:text-gray-800 text-sm">View Details</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Upcoming Training Schedule -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Training Schedule</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Add Schedule</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['program' => 'Time Management Workshop', 'type' => 'Soft Skills', 'start' => '2024-04-01', 'duration' => '2 days', 'instructor' => 'Dr. Sarah Johnson', 'capacity' => '20/30', 'status' => 'Open'],
                        ['program' => 'Advanced Excel Techniques', 'type' => 'Technical', 'start' => '2024-04-05', 'duration' => '1 week', 'instructor' => 'Michael Chen', 'capacity' => '15/25', 'status' => 'Open'],
                        ['program' => 'Leadership Skills for Managers', 'type' => 'Management', 'start' => '2024-04-10', 'duration' => '3 days', 'instructor' => 'Prof. David Wilson', 'capacity' => '18/20', 'status' => 'Open'],
                        ['program' => 'Communication Excellence', 'type' => 'Soft Skills', 'start' => '2024-04-15', 'duration' => '2 days', 'instructor' => 'Emily Davis', 'capacity' => '25/25', 'status' => 'Full'],
                        ['program' => 'Financial Planning & Analysis', 'type' => 'Finance', 'start' => '2024-04-20', 'duration' => '1 week', 'instructor' => 'John Smith', 'capacity' => '12/20', 'status' => 'Open']
                    ] as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $schedule['program'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule['type'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule['start'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule['duration'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule['instructor'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule['capacity'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($schedule['status'] === 'Open') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $schedule['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Enroll</button>
                            <button class="text-gray-600 hover:text-gray-900">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Training Budget & ROI -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Training Budget Utilization</h3>
            <div class="space-y-4">
                @foreach([
                    ['category' => 'External Training', 'budget' => 5000000, 'spent' => 3200000, 'percentage' => 64],
                    ['category' => 'Internal Training', 'budget' => 2000000, 'spent' => 1800000, 'percentage' => 90],
                    ['category' => 'Certifications', 'budget' => 3000000, 'spent' => 2100000, 'percentage' => 70],
                    ['category' => 'Conferences', 'budget' => 1500000, 'spent' => 750000, 'percentage' => 50],
                    ['category' => 'Materials', 'budget' => 500000, 'spent' => 450000, 'percentage' => 90]
                ] as $budget)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ $budget['category'] }}</h4>
                        <span class="text-sm text-gray-500">{{ $budget['percentage'] }}% used</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Budget:</span>
                            <span class="font-medium">TZS {{ number_format($budget['budget'], 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Spent:</span>
                            <span class="font-medium">TZS {{ number_format($budget['spent'], 0) }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $budget['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Training ROI Analysis</h3>
            <div class="space-y-4">
                @foreach([
                    ['program' => 'Leadership Training', 'investment' => 1200000, 'return' => 2400000, 'roi' => 100],
                    ['program' => 'Sales Training', 'investment' => 800000, 'return' => 1600000, 'roi' => 100],
                    ['program' => 'Technical Skills', 'investment' => 1500000, 'return' => 2250000, 'roi' => 50],
                    ['program' => 'Soft Skills', 'investment' => 600000, 'return' => 900000, 'roi' => 50],
                    ['program' => 'Compliance Training', 'investment' => 400000, 'return' => 600000, 'roi' => 50]
                ] as $roi)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ $roi['program'] }}</h4>
                        <span class="text-sm font-medium {{ $roi['roi'] >= 100 ? 'text-green-600' : 'text-blue-600' }}">{{ $roi['roi'] }}% ROI</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Investment:</span>
                            <span class="font-medium">TZS {{ number_format($roi['investment'], 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Return:</span>
                            <span class="font-medium text-green-600">TZS {{ number_format($roi['return'], 0) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Training Certificates -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Certifications</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'John Doe', 'cert' => 'PMP Certification', 'date' => '2024-03-15', 'expiry' => '2027-03-15', 'status' => 'Active'],
                ['name' => 'Sarah Smith', 'cert' => 'HR Professional', 'date' => '2024-03-10', 'expiry' => '2026-03-10', 'status' => 'Active'],
                ['name' => 'Michael Chen', 'cert' => 'Advanced Excel', 'date' => '2024-03-08', 'expiry' => '2025-03-08', 'status' => 'Active'],
                ['name' => 'Emily Davis', 'cert' => 'Digital Marketing', 'date' => '2024-03-05', 'expiry' => '2025-03-05', 'status' => 'Active'],
                ['name' => 'David Wilson', 'cert' => 'Financial Analysis', 'date' => '2024-03-01', 'expiry' => '2026-03-01', 'status' => 'Active'],
                ['name' => 'Lisa Brown', 'cert' => 'Project Management', 'date' => '2024-02-28', 'expiry' => '2027-02-28', 'status' => 'Active']
            ] as $cert)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $cert['status'] }}</span>
                    <span class="text-xs text-gray-500">Expires: {{ $cert['expiry'] }}</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-medium">{{ substr($cert['name'], 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-gray-900">{{ $cert['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $cert['cert'] }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Issued: {{ $cert['date'] }}</span>
                    <button class="text-indigo-600 hover:text-indigo-800 font-medium">View →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
