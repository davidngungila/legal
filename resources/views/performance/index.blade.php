@extends('layouts.app')

@section('title', 'Performance Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Performance Management</h1>
            <p class="text-gray-600 mt-2">Track employee performance and manage reviews</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Review
            </button>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">87%</h3>
            <p class="text-gray-600 text-sm">Average Performance</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">12</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">156</h3>
            <p class="text-gray-600 text-sm">Completed Reviews</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">8</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">24</h3>
            <p class="text-gray-600 text-sm">Pending Reviews</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">18</h3>
            <p class="text-gray-600 text-sm">Top Performers</p>
        </div>
    </div>

    <!-- Performance Reviews Schedule -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Performance Reviews Schedule</h3>
            <div class="flex space-x-3">
                <select class="form-select">
                    <option>Q1 2024</option>
                    <option>Q2 2024</option>
                    <option>Q3 2024</option>
                    <option>Q4 2024</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="calendar" class="w-4 h-4 inline mr-2"></i>
                    Schedule Review
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['employee' => 'John Doe', 'position' => 'Senior Developer', 'review' => 'Q1 Performance Review', 'date' => '2024-03-15', 'status' => 'Completed'],
                ['employee' => 'Sarah Smith', 'position' => 'HR Manager', 'review' => 'Q1 Performance Review', 'date' => '2024-03-18', 'status' => 'Completed'],
                ['employee' => 'Mike Johnson', 'position' => 'Finance Analyst', 'review' => 'Q1 Performance Review', 'date' => '2024-03-20', 'status' => 'In Progress'],
                ['employee' => 'Emily Davis', 'position' => 'Marketing Coordinator', 'review' => 'Q1 Performance Review', 'date' => '2024-03-22', 'status' => 'Scheduled'],
                ['employee' => 'David Wilson', 'position' => 'Operations Manager', 'review' => 'Q1 Performance Review', 'date' => '2024-03-25', 'status' => 'Scheduled'],
                ['employee' => 'Lisa Brown', 'position' => 'Sales Executive', 'review' => 'Q1 Performance Review', 'date' => '2024-03-28', 'status' => 'Pending']
            ] as $review)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($review['status'] === 'Completed') bg-green-100 text-green-800
                        @elseif($review['status'] === 'In Progress') bg-yellow-100 text-yellow-800
                        @elseif($review['status'] === 'Scheduled') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $review['status'] }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $review['date'] }}</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-medium">{{ substr($review['employee'], 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold text-gray-900">{{ $review['employee'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $review['position'] }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-700 mb-3">{{ $review['review'] }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        @if($review['status'] === 'Completed')
                        <span class="text-sm font-medium text-green-600">Score: 92%</span>
                        @endif
                    </div>
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Performers -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Top Performers This Quarter</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Sarah Williams', 'position' => 'HR Director', 'score' => 96, 'dept' => 'HR'],
                ['name' => 'John Doe', 'position' => 'Senior Developer', 'score' => 94, 'dept' => 'IT'],
                ['name' => 'Michael Chen', 'position' => 'Finance Manager', 'score' => 92, 'dept' => 'Finance'],
                ['name' => 'Emily Johnson', 'position' => 'Marketing Manager', 'score' => 91, 'dept' => 'Marketing'],
                ['name' => 'David Kimani', 'position' => 'Sales Director', 'score' => 89, 'dept' => 'Sales'],
                ['name' => 'Alice Brown', 'position' => 'Operations Manager', 'score' => 88, 'dept' => 'Operations']
            ] as $performer)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">{{ substr($performer['name'], 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">{{ $performer['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $performer['position'] }}</p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $performer['score'] }}%</div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Department:</span>
                        <span class="font-medium">{{ $performer['dept'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Performance:</span>
                        <span class="font-medium text-green-600">Outstanding</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Achievements:</span>
                        <span class="font-medium">5 goals met</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Profile →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Performance Goals -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Department Performance Goals</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Set Goals</button>
        </div>
        <div class="space-y-4">
            @foreach([
                ['dept' => 'IT', 'goal' => 'System uptime 99.9%', 'current' => 99.7, 'target' => 99.9, 'status' => 'On Track'],
                ['dept' => 'Sales', 'goal' => 'Revenue TZS 50M', 'current' => 42, 'target' => 50, 'status' => 'On Track'],
                ['dept' => 'HR', 'goal' => 'Employee retention 95%', 'current' => 93, 'target' => 95, 'status' => 'At Risk'],
                ['dept' => 'Finance', 'goal' => 'Cost reduction 10%', 'current' => 8, 'target' => 10, 'status' => 'On Track'],
                ['dept' => 'Operations', 'goal' => 'Efficiency 15%', 'current' => 12, 'target' => 15, 'status' => 'On Track']
            ] as $goal)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <i data-feather="target" class="w-4 h-4 text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $goal['dept'] }} Department</h4>
                            <p class="text-sm text-gray-600">{{ $goal['goal'] }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($goal['status'] === 'On Track') bg-green-100 text-green-800
                        @elseif($goal['status'] === 'At Risk') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $goal['status'] }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Current Progress:</span>
                        <span class="font-medium">{{ $goal['current'] }}{{ is_numeric($goal['current']) ? '%' : 'M' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Target:</span>
                        <span class="font-medium">{{ $goal['target'] }}{{ is_numeric($goal['target']) ? '%' : 'M' }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($goal['current'] / $goal['target']) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Performance Analytics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Performance Analytics</h3>
            <select class="form-select">
                <option>Last Quarter</option>
                <option>Last 6 Months</option>
                <option>Last Year</option>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Performance Distribution</h4>
                <div class="space-y-3">
                    @foreach([
                        ['rating' => 'Outstanding (90-100%)', 'count' => 18, 'color' => 'green'],
                        ['rating' => 'Excellent (80-89%)', 'count' => 67, 'color' => 'blue'],
                        ['rating' => 'Good (70-79%)', 'count' => 89, 'color' => 'yellow'],
                        ['rating' => 'Needs Improvement (60-69%)', 'count' => 45, 'color' => 'orange'],
                        ['rating' => 'Poor (Below 60%)', 'count' => 12, 'color' => 'red']
                    ] as $rating)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-{{ $rating['color'] }}-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">{{ $rating['rating'] }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $rating['count'] }}</span>
                            <span class="text-sm text-gray-500">({{ round(($rating['count'] / 231) * 100) }}%)</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Improvement Areas</h4>
                <div class="space-y-3">
                    @foreach([
                        ['area' => 'Time Management', 'employees' => 23, 'focus' => 'High'],
                        ['area' => 'Communication Skills', 'employees' => 18, 'focus' => 'Medium'],
                        ['area' => 'Technical Skills', 'employees' => 15, 'focus' => 'High'],
                        ['area' => 'Leadership', 'employees' => 12, 'focus' => 'Medium'],
                        ['area' => 'Project Management', 'employees' => 8, 'focus' => 'Low']
                    ] as $area)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $area['area'] }}</p>
                            <p class="text-xs text-gray-500">{{ $area['employees'] }} employees</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($area['focus'] === 'High') bg-red-100 text-red-800
                            @elseif($area['focus'] === 'Medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ $area['focus'] }} Priority
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
