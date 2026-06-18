@extends('layouts.app')

@section('title', 'HR Intelligence - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">HR Intelligence</h1>
            <p class="text-gray-600 mt-2">AI-powered workforce insights and strategic recommendations</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <select class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option>Last 30 Days</option>
                <option>Last 3 Months</option>
                <option>Last 6 Months</option>
                <option>Last Year</option>
            </select>
        </div>
    </div>

    <!-- Key Insights Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900">78%</h3>
            <p class="text-gray-600 text-sm">Employee Engagement Score</p>
            <p class="text-xs text-green-600 mt-2 font-medium">+3.2% from last quarter</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                    <i data-feather="target" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900">92%</h3>
            <p class="text-gray-600 text-sm">Goal Achievement Rate</p>
            <p class="text-xs text-green-600 mt-2 font-medium">+5.1% from last quarter</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900">85%</h3>
            <p class="text-gray-600 text-sm">Retention Succession Coverage</p>
            <p class="text-xs text-yellow-600 mt-2 font-medium">Needs improvement in IT Dept</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                    <i data-feather="activity" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900">68%</h3>
            <p class="text-gray-600 text-sm">Skill Gap Index</p>
            <p class="text-xs text-red-600 mt-2 font-medium">-2.3% from last quarter</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Talent Insights -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Talent Insights & Recommendations</h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <i data-feather="trending-up" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">High Potential Employees Identified</h4>
                            <p class="text-sm text-gray-600 mb-3">AI has identified 8 employees with high leadership potential. Consider them for upcoming promotion opportunities.</p>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Sarah Williams</span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">John Doe</span>
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">+6 more</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Retention Risk Alert</h4>
                            <p class="text-sm text-gray-600 mb-3">3 employees show patterns indicating high turnover risk. Recommended actions: schedule stay interviews.</p>
                            <button class="text-sm text-indigo-600 font-medium hover:underline">View Details</button>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <i data-feather="book-open" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 mb-1">Skill Development Recommendations</h4>
                            <p class="text-sm text-gray-600 mb-3">Based on performance data, 15 employees would benefit from leadership training programs.</p>
                            <button class="text-sm text-indigo-600 font-medium hover:underline">View Recommended Courses</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement Drivers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Engagement Drivers</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Career Growth</span>
                        <span class="text-sm font-bold text-green-600">94%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 94%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Work-Life Balance</span>
                        <span class="text-sm font-bold text-yellow-600">72%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 72%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Compensation</span>
                        <span class="text-sm font-bold text-green-600">88%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 88%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Team Culture</span>
                        <span class="text-sm font-bold text-green-600">91%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 91%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Leadership</span>
                        <span class="text-sm font-bold text-red-600">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Skills & Competencies -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Skill Gap Analysis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Skill Gap Analysis</h3>
            <div class="space-y-4">
                @foreach([
                    ['skill' => 'Leadership', 'current' => 68, 'target' => 85, 'gap' => 17],
                    ['skill' => 'Digital Literacy', 'current' => 82, 'target' => 90, 'gap' => 8],
                    ['skill' => 'Communication', 'current' => 76, 'target' => 88, 'gap' => 12],
                    ['skill' => 'Project Management', 'current' => 71, 'target' => 85, 'gap' => 14],
                    ['skill' => 'Data Analysis', 'current' => 54, 'target' => 75, 'gap' => 21]
                ] as $skill)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="font-medium text-gray-900">{{ $skill['skill'] }}</span>
                        <span class="text-sm text-gray-600">{{ $skill['current'] }}% / {{ $skill['target'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full" style="width: {{ $skill['current'] }}%"></div>
                    </div>
                    <div class="text-xs text-right text-red-600">Gap: {{ $skill['gap'] }}%</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Training Recommendations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">AI Recommended Training</h3>
            <div class="space-y-3">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <h4 class="font-medium text-gray-900 mb-1">Advanced Leadership Program</h4>
                    <p class="text-xs text-gray-600 mb-2">Recommended for 15 high-potential employees</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">40 hours</span>
                        <button class="text-xs text-indigo-600 font-medium hover:underline">Enroll Employees</button>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                    <h4 class="font-medium text-gray-900 mb-1">Data Analytics Bootcamp</h4>
                    <p class="text-xs text-gray-600 mb-2">Recommended for IT and Finance teams</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">32 hours</span>
                        <button class="text-xs text-indigo-600 font-medium hover:underline">View Details</button>
                    </div>
                </div>

                <div class="p-4 bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border border-green-100">
                    <h4 class="font-medium text-gray-900 mb-1">Communication Skills Workshop</h4>
                    <p class="text-xs text-gray-600 mb-2">Recommended for all team leaders</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">16 hours</span>
                        <button class="text-xs text-indigo-600 font-medium hover:underline">Schedule Session</button>
                    </div>
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
