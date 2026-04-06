@extends('layouts.app')

@section('title', 'Employee Self Service - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Self Service</h1>
            <p class="text-gray-600 mt-2">Manage your HR information and requests efficiently</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="help-circle" class="w-4 h-4 inline mr-2"></i>
                Help & Support
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Update Profile
            </button>
        </div>
    </div>

    <!-- Employee Profile Card -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-indigo-600">JD</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">John Doe</h2>
                    <p class="text-indigo-200">Senior Developer</p>
                    <p class="text-indigo-200">EMP001 • IT Department</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-3xl font-bold">3</p>
                    <p class="text-sm text-indigo-200">Years Service</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">18</p>
                    <p class="text-sm text-indigo-200">Leave Days</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">A+</p>
                    <p class="text-sm text-indigo-200">Performance</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">TZS 2.4M</p>
                    <p class="text-sm text-indigo-200">Net Salary</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="calendar" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Apply for Leave</h3>
            <p class="text-sm text-gray-600 mb-4">Request annual, sick, or emergency leave</p>
            <a href="{{ route('selfservice.leave') }}" class="text-green-600 hover:text-green-800 text-sm font-medium">Apply Now →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="download" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Download Payslip</h3>
            <p class="text-sm text-gray-600 mb-4">Access your monthly salary statements</p>
            <a href="{{ route('selfservice.payslip') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">View Contract</h3>
            <p class="text-sm text-gray-600 mb-4">Review your employment contract details</p>
            <a href="{{ route('selfservice.contract') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">View →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="alert-circle" class="w-6 h-6 text-orange-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">File Complaint</h3>
            <p class="text-sm text-gray-600 mb-4">Submit grievances or concerns</p>
            <a href="{{ route('selfservice.complaint') }}" class="text-orange-600 hover:text-orange-800 text-sm font-medium">File Now →</a>
        </div>
    </div>

    <!-- Leave Balance & Requests -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Leave Balance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Leave Balance</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-feather="sun" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Annual Leave</p>
                            <p class="text-sm text-gray-500">28 days per year</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">18</p>
                        <p class="text-sm text-gray-500">days remaining</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-feather="heart" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Sick Leave</p>
                            <p class="text-sm text-gray-500">90 days per year</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">75</p>
                        <p class="text-sm text-gray-500">days remaining</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-feather="home" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Maternity/Paternity</p>
                            <p class="text-sm text-gray-500">As per Labour Act</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">84</p>
                        <p class="text-sm text-gray-500">days available</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Compassionate Leave</p>
                            <p class="text-sm text-gray-500">7 days per instance</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">7</p>
                        <p class="text-sm text-gray-500">days remaining</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Leave Requests</h3>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
            </div>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Approved</span>
                        <span class="text-xs text-gray-500">5 days ago</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">Annual Leave</p>
                    <p class="text-sm text-gray-600 mb-2">15 Nov 2024 - 19 Nov 2024 (5 days)</p>
                    <p class="text-xs text-gray-500">Approved by: Sarah Williams (HR Manager)</p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pending</span>
                        <span class="text-xs text-gray-500">2 days ago</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">Sick Leave</p>
                    <p class="text-sm text-gray-600 mb-2">28 Nov 2024 - 29 Nov 2024 (2 days)</p>
                    <p class="text-xs text-gray-500">Awaiting approval from: Department Manager</p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejected</span>
                        <span class="text-xs text-gray-500">2 weeks ago</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">Annual Leave</p>
                    <p class="text-sm text-gray-600 mb-2">10 Dec 2024 - 20 Dec 2024 (10 days)</p>
                    <p class="text-xs text-gray-500">Rejected: Insufficient leave balance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payslips -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Payslips</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['month' => 'November 2024', 'basic' => 2500000, 'allowances' => 500000, 'deductions' => 595000, 'net' => 2405000, 'status' => 'Paid'],
                        ['month' => 'October 2024', 'basic' => 2500000, 'allowances' => 500000, 'deductions' => 595000, 'net' => 2405000, 'status' => 'Paid'],
                        ['month' => 'September 2024', 'basic' => 2500000, 'allowances' => 450000, 'deductions' => 580000, 'net' => 2370000, 'status' => 'Paid'],
                        ['month' => 'August 2024', 'basic' => 2500000, 'allowances' => 500000, 'deductions' => 595000, 'net' => 2405000, 'status' => 'Paid'],
                        ['month' => 'July 2024', 'basic' => 2400000, 'allowances' => 400000, 'deductions' => 560000, 'net' => 2240000, 'status' => 'Paid']
                    ] as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $payslip['month'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($payslip['basic'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($payslip['allowances'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($payslip['deductions'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format($payslip['net'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $payslip['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Download</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance & Training -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Performance Reviews -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Performance Reviews</h3>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Details</button>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Q3 2024 Performance Review</p>
                        <p class="text-sm text-gray-600">Reviewed by: Sarah Williams</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">A+</p>
                        <p class="text-sm text-gray-500">Outstanding</p>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 pl-4">
                    <p class="font-medium text-gray-900 mb-2">Key Achievements:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Led successful migration to new cloud infrastructure</li>
                        <li>• Mentored 3 junior developers</li>
                        <li>• Improved system performance by 40%</li>
                    </ul>
                </div>

                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="font-medium text-gray-900 mb-2">Development Goals:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Complete AWS certification by Q1 2025</li>
                        <li>• Lead project management training</li>
                        <li>• Develop technical documentation skills</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Training Records -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Training Records</h3>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Browse Courses</button>
            </div>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                        <span class="text-xs text-gray-500">15 Oct 2024</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">OSHA Safety Training</p>
                    <p class="text-sm text-gray-600 mb-2">Occupational Safety and Health Compliance</p>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Certificate: #OSHA2024-001</p>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm">View Certificate</button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">In Progress</span>
                        <span class="text-xs text-gray-500">Due: 30 Nov 2024</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">Data Protection Compliance</p>
                    <p class="text-sm text-gray-600 mb-2">Personal Data Protection Act Training</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">65% Complete</p>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Upcoming</span>
                        <span class="text-xs text-gray-500">Starts: 5 Dec 2024</span>
                    </div>
                    <p class="font-medium text-gray-900 mb-1">Leadership Development</p>
                    <p class="text-sm text-gray-600 mb-2">Management and Leadership Skills Program</p>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Duration: 3 months</p>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents & Policies -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Documents & Policies</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                    <i data-feather="file-text" class="w-5 h-5 text-blue-600"></i>
                </div>
                <p class="font-medium text-gray-900 mb-1">Employment Contract</p>
                <p class="text-sm text-gray-600 mb-2">Permanent Employment Agreement</p>
                <p class="text-xs text-indigo-600">View Document →</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                    <i data-feather="book" class="w-5 h-5 text-green-600"></i>
                </div>
                <p class="font-medium text-gray-900 mb-1">Employee Handbook</p>
                <p class="text-sm text-gray-600 mb-2">Company Policies & Procedures</p>
                <p class="text-xs text-indigo-600">View Document →</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                    <i data-feather="shield" class="w-5 h-5 text-purple-600"></i>
                </div>
                <p class="font-medium text-gray-900 mb-1">Code of Conduct</p>
                <p class="text-sm text-gray-600 mb-2">Ethical Guidelines & Standards</p>
                <p class="text-xs text-indigo-600">View Document →</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-3">
                    <i data-feather="alert-circle" class="w-5 h-5 text-orange-600"></i>
                </div>
                <p class="font-medium text-gray-900 mb-1">Safety Policy</p>
                <p class="text-sm text-gray-600 mb-2">Workplace Safety Guidelines</p>
                <p class="text-xs text-indigo-600">View Document →</p>
            </div>
        </div>
    </div>
</div>

<!-- Leave Application Modal -->
<div id="leaveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Apply for Leave</h2>
            <button onclick="closeLeaveModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="leaveForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select name="leave_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Leave Type</option>
                    <option value="annual">Annual Leave</option>
                    <option value="sick">Sick Leave</option>
                    <option value="emergency">Emergency Leave</option>
                    <option value="maternity">Maternity Leave</option>
                    <option value="paternity">Paternity Leave</option>
                    <option value="compassionate">Compassionate Leave</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please provide reason for leave request..."></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeLeaveModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">File Complaint</h2>
            <button onclick="closeComplaintModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="complaintForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Complaint Type</label>
                <select name="complaint_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Complaint Type</option>
                    <option value="workplace">Workplace Issue</option>
                    <option value="harassment">Harassment</option>
                    <option value="discrimination">Discrimination</option>
                    <option value="safety">Safety Concern</option>
                    <option value="policy">Policy Violation</option>
                    <option value="salary">Salary Issue</option>
                    <option value="working">Working Conditions</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Brief description of the issue...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please provide detailed description of your complaint..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desired Resolution</label>
                <textarea name="resolution" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="What would you like to see as the outcome?"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeComplaintModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Contract Modal -->
<div id="contractModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Employment Contract</h2>
            <button onclick="closeContractModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Employee ID:</span>
                            <span class="font-medium">EMP001</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Full Name:</span>
                            <span class="font-medium">John Doe</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Position:</span>
                            <span class="font-medium">Senior Developer</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Department:</span>
                            <span class="font-medium">IT Department</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Contract Type:</span>
                            <span class="font-medium">Permanent</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Start Date:</span>
                            <span class="font-medium">01 January 2022</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">End Date:</span>
                            <span class="font-medium">Open-ended</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Probation Period:</span>
                            <span class="font-medium">3 months</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Compensation</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Basic Salary:</span>
                            <span class="font-medium">TZS 2,000,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Housing Allowance:</span>
                            <span class="font-medium">TZS 500,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transport Allowance:</span>
                            <span class="font-medium">TZS 200,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Package:</span>
                            <span class="font-medium text-green-600">TZS 2,700,000</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Working Hours</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Working Days:</span>
                            <span class="font-medium">Monday - Friday</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Working Hours:</span>
                            <span class="font-medium">8:00 AM - 5:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lunch Break:</span>
                            <span class="font-medium">1:00 PM - 2:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Overtime Rate:</span>
                            <span class="font-medium">1.5x normal rate</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Entitlement</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Annual Leave:</span>
                        <span class="font-medium">28 days per year</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sick Leave:</span>
                        <span class="font-medium">90 days per year</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Maternity Leave:</span>
                        <span class="font-medium">84 days (as per Labour Act)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Public Holidays:</span>
                        <span class="font-medium">As per Tanzania Calendar</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Terms & Conditions</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-700 mb-3">
                        This employment contract is governed by the Tanzania Employment and Labour Relations Act, 2019. 
                        Both parties agree to comply with all statutory requirements including working hours, leave entitlements, 
                        and termination procedures as outlined in the Labour Act.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-start space-x-2">
                            <input type="checkbox" checked class="mt-1" disabled>
                            <span class="text-sm text-gray-700">I have read and understood the terms and conditions</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <input type="checkbox" checked class="mt-1" disabled>
                            <span class="text-sm text-gray-700">I agree to comply with company policies and procedures</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <input type="checkbox" checked class="mt-1" disabled>
                            <span class="text-sm text-gray-700">I understand my rights and responsibilities under Tanzania Labour Law</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="downloadContract()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                    Download PDF
                </button>
                <button onclick="printContract()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal functions
function showLeaveModal() {
    document.getElementById('leaveModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeLeaveModal() {
    document.getElementById('leaveModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('leaveForm').reset();
}

function showComplaintModal() {
    document.getElementById('complaintModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('complaintForm').reset();
}

function showContractModal() {
    document.getElementById('contractModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeContractModal() {
    document.getElementById('contractModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Client switching function (required by sidebar)
function switchClient(clientId) {
    // Show notification
    showNotification('Switching to client...', 'info');
    
    // Simulate client switch
    setTimeout(() => {
        // Update the select value
        const select = document.querySelector('select[onchange="switchClient(this.value)"]');
        if (select) {
            select.value = clientId;
        }
        
        // Show success message
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        
        showNotification(`Switched to ${clientNames[clientId]}`, 'success');
        
        // In a real application, this would reload the page or make an API call
        // window.location.reload();
    }, 500);
}

// Notification functions (required by header)
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function removeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.remove();
        updateNotificationBadge();
    }
}

function markAllAsRead() {
    const unreadNotifications = document.querySelectorAll('.notification-item:not(.read)');
    unreadNotifications.forEach(notification => {
        notification.classList.add('read');
    });
    updateNotificationBadge();
}

function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    const unreadCount = document.querySelectorAll('.notification-item:not(.read)').length;
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// Action functions
function downloadPayslip() {
    // Show notification
    showNotification('Preparing payslip download...', 'info');
    
    // Simulate download
    setTimeout(() => {
        const link = document.createElement('a');
        link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nNovember 2024,2500000,500000,595000,2405000,Paid');
        link.download = 'payslip_november_2024.csv';
        link.click();
        showNotification('Payslip downloaded successfully!', 'success');
    }, 1000);
}

function downloadContract() {
    // Show notification
    showNotification('Preparing contract download...', 'info');
    
    // Simulate download
    setTimeout(() => {
        const link = document.createElement('a');
        link.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent('EMPLOYMENT CONTRACT\n\nEmployee: John Doe\nID: EMP001\nPosition: Senior Developer\nDepartment: IT Department\nContract Type: Permanent\nStart Date: 01 January 2022\n\nThis is a sample contract document.');
        link.download = 'employment_contract.pdf';
        link.click();
        showNotification('Contract downloaded successfully!', 'success');
    }, 1000);
}

function printContract() {
    window.print();
    showNotification('Print dialog opened', 'info');
}

// Form submissions
document.getElementById('leaveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    showNotification('Submitting leave request...', 'info');
    
    // Simulate API call
    setTimeout(() => {
        closeLeaveModal();
        showNotification('Leave request submitted successfully!', 'success');
        
        // Update UI (in real app, this would refresh from API)
        updateLeaveBalance();
    }, 1500);
});

document.getElementById('complaintForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    showNotification('Submitting complaint...', 'info');
    
    // Simulate API call
    setTimeout(() => {
        closeComplaintModal();
        showNotification('Complaint submitted successfully! Reference: COMP-2024-' + Math.floor(Math.random() * 1000), 'success');
    }, 1500);
});

// Update leave balance (simulation)
function updateLeaveBalance() {
    // This would normally fetch from API
    const leaveBalances = document.querySelectorAll('.text-right .text-2xl');
    if (leaveBalances.length > 0) {
        const currentValue = parseInt(leaveBalances[0].textContent);
        const newValue = Math.max(0, currentValue - 1);
        leaveBalances[0].textContent = newValue;
    }
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
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
    
    document.body.appendChild(notification);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endpush

@endsection

