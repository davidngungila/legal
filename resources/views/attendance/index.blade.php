@extends('layouts.app')

@section('title', 'Attendance & Timesheet - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Attendance & Timesheet</h1>
            <p class="text-gray-600 mt-2">Track employee attendance and manage timesheets</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Tracking attendance for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Import Timesheet
            </button>
        </div>
    </div>

    <!-- Attendance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">94.2%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">234</h3>
            <p class="text-gray-600 text-sm">Present Today</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">+2</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">8</h3>
            <p class="text-gray-600 text-sm">Late Arrivals</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">12</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">6</h3>
            <p class="text-gray-600 text-sm">On Leave</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">-1</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">2</h3>
            <p class="text-gray-600 text-sm">Absent</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="check-in" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Quick Action</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Check In/Out</h3>
            <p class="text-sm opacity-90 mb-4">Record attendance for employees</p>
            <button class="bg-white text-green-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Manage Attendance
            </button>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="file-text" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Timesheet</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Timesheet Entry</h3>
            <p class="text-sm opacity-90 mb-4">Submit weekly timesheets</p>
            <button class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Enter Timesheet
            </button>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <i data-feather="bar-chart-2" class="w-8 h-8"></i>
                <span class="text-sm opacity-90">Reports</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Attendance Report</h3>
            <p class="text-sm opacity-90 mb-4">Generate monthly reports</p>
            <button class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Generate Report
            </button>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Today's Attendance</h3>
            <div class="flex space-x-3">
                <input type="date" class="form-input" value="2024-03-29">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i data-feather="search" class="w-4 h-4 inline mr-2"></i>
                    Search
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'John Doe', 'dept' => 'IT', 'checkin' => '08:30', 'checkout' => '17:45', 'status' => 'Present', 'hours' => '9.25'],
                        ['name' => 'Sarah Smith', 'dept' => 'HR', 'checkin' => '08:45', 'checkout' => '17:30', 'status' => 'Present', 'hours' => '8.75'],
                        ['name' => 'Mike Johnson', 'dept' => 'Finance', 'checkin' => '09:15', 'checkout' => '-', 'status' => 'Late', 'hours' => '-'],
                        ['name' => 'Emily Davis', 'dept' => 'Marketing', 'checkin' => '-', 'checkout' => '-', 'status' => 'Leave', 'hours' => '-'],
                        ['name' => 'David Wilson', 'dept' => 'Operations', 'checkin' => '08:00', 'checkout' => '17:00', 'status' => 'Present', 'hours' => '9.00'],
                        ['name' => 'Lisa Brown', 'dept' => 'Sales', 'checkin' => '-', 'checkout' => '-', 'status' => 'Absent', 'hours' => '-']
                    ] as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($attendance['name'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['dept'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['checkin'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['checkout'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($attendance['status'] === 'Present') bg-green-100 text-green-800
                                @elseif($attendance['status'] === 'Late') bg-yellow-100 text-yellow-800
                                @elseif($attendance['status'] === 'Leave') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $attendance['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance['hours'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Weekly Timesheet Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Weekly Timesheet Summary</h3>
            <div class="flex space-x-3">
                <select class="form-select">
                    <option>Week 12 (Mar 25-31, 2024)</option>
                    <option>Week 11 (Mar 18-24, 2024)</option>
                    <option>Week 10 (Mar 11-17, 2024)</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Department Summary</h4>
                <div class="space-y-3">
                    @foreach([
                        ['dept' => 'IT', 'employees' => 45, 'hours' => 405, 'avg' => 9.0],
                        ['dept' => 'HR', 'dept' => 'HR', 'employees' => 12, 'hours' => 108, 'avg' => 9.0],
                        ['dept' => 'Finance', 'employees' => 28, 'hours' => 252, 'avg' => 9.0],
                        ['dept' => 'Operations', 'employees' => 89, 'hours' => 801, 'avg' => 9.0],
                        ['dept' => 'Sales', 'employees' => 56, 'hours' => 504, 'avg' => 9.0]
                    ] as $dept)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="briefcase" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $dept['dept'] }}</p>
                                <p class="text-xs text-gray-500">{{ $dept['employees'] }} employees</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $dept['hours'] }} hrs</p>
                            <p class="text-xs text-gray-500">{{ $dept['avg'] }} avg</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Overtime Summary</h4>
                <div class="space-y-3">
                    @foreach([
                        ['name' => 'John Doe', 'dept' => 'IT', 'regular' => 45, 'overtime' => 5, 'total' => 50],
                        ['name' => 'Sarah Smith', 'dept' => 'HR', 'regular' => 40, 'overtime' => 2, 'total' => 42],
                        ['name' => 'Mike Johnson', 'dept' => 'Finance', 'regular' => 40, 'overtime' => 8, 'total' => 48],
                        ['name' => 'David Wilson', 'dept' => 'Operations', 'regular' => 45, 'overtime' => 10, 'total' => 55],
                        ['name' => 'Lisa Brown', 'dept' => 'Sales', 'regular' => 40, 'overtime' => 3, 'total' => 43]
                    ] as $overtime)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $overtime['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $overtime['dept'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ $overtime['total'] }} hrs</p>
                            <p class="text-xs text-orange-600">+{{ $overtime['overtime'] }} OT</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Calendar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Attendance Calendar</h3>
            <div class="flex space-x-3">
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <i data-feather="chevron-left" class="w-4 h-4"></i>
                </button>
                <span class="px-4 py-1 bg-indigo-100 text-indigo-700 rounded font-medium">March 2024</span>
                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-7 gap-2">
            <!-- Week days -->
            <div class="text-center text-xs font-medium text-gray-500 py-2">Sun</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Mon</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Tue</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Wed</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Thu</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Fri</div>
            <div class="text-center text-xs font-medium text-gray-500 py-2">Sat</div>
            
            <!-- Calendar days -->
            @for($day = 1; $day <= 31; $day++)
            <div class="aspect-square border border-gray-200 rounded-lg p-2 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="text-sm font-medium text-gray-900">{{ $day }}</div>
                <div class="mt-1">
                    @if($day <= 28)
                    <div class="w-full h-1 bg-green-500 rounded-full"></div>
                    @endif
                </div>
            </div>
            @endfor
        </div>
        <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Normal Day</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Weekend</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                <span class="text-gray-600">Holiday</span>
            </div>
        </div>
    </div>
</div>
@endsection
