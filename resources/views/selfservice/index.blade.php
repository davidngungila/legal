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
            <button class="text-green-600 hover:text-green-800 text-sm font-medium">Apply Now →</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="download" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Download Payslip</h3>
            <p class="text-sm text-gray-600 mb-4">Access your monthly salary statements</p>
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download →</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">View Contract</h3>
            <p class="text-sm text-gray-600 mb-4">Review your employment contract details</p>
            <button class="text-purple-600 hover:text-purple-800 text-sm font-medium">View →</button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="alert-circle" class="w-6 h-6 text-orange-600"></i>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">File Complaint</h3>
            <p class="text-sm text-gray-600 mb-4">Submit grievances or concerns</p>
            <button class="text-orange-600 hover:text-orange-800 text-sm font-medium">File Now →</button>
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
@endsection
