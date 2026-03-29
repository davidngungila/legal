@extends('layouts.app')

@section('title', 'Compliance & Legal - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Compliance & Legal</h1>
            <p class="text-gray-600 mt-2">Ensure full compliance with Tanzania Labour Laws and regulations</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download Reports
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="shield" class="w-4 h-4 inline mr-2"></i>
                Run Audit
            </button>
        </div>
    </div>

    <!-- Compliance Score Overview -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 text-white mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-semibold mb-2">Overall Compliance Score</h3>
                <div class="text-5xl font-bold mb-2">94%</div>
                <p class="text-indigo-200">Excellent standing</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2">Risk Assessment</h3>
                <div class="text-5xl font-bold mb-2">LOW</div>
                <p class="text-indigo-200">Minimal legal exposure</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-2">Last Audit</h3>
                <div class="text-2xl font-bold mb-2">{{ now()->subDays(15)->format('d M Y') }}</div>
                <p class="text-indigo-200">Next audit in 15 days</p>
            </div>
        </div>
    </div>

    <!-- Compliance Areas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="book-open" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-2xl font-bold text-green-600">100%</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Labour Act Compliance</h3>
            <p class="text-sm text-gray-600 mb-3">Tanzania Employment and Labour Relations Act</p>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Contracts</span>
                    <span class="text-green-600">Compliant</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Working Hours</span>
                    <span class="text-green-600">Compliant</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Leave Policy</span>
                    <span class="text-green-600">Compliant</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-2xl font-bold text-blue-600">100%</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Statutory Filings</h3>
            <p class="text-sm text-gray-600 mb-3">TRA, NSSF, WCF, HESLB compliance</p>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">PAYE Returns</span>
                    <span class="text-green-600">Current</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">NSSF Schedule</span>
                    <span class="text-green-600">Current</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">WCF Declaration</span>
                    <span class="text-green-600">Current</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-2xl font-bold text-yellow-600">85%</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Data Protection</h3>
            <p class="text-sm text-gray-600 mb-3">Personal Data Protection Act compliance</p>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Consent Records</span>
                    <span class="text-green-600">Complete</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Data Encryption</span>
                    <span class="text-green-600">Active</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Access Logs</span>
                    <span class="text-yellow-600">Review Needed</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-2xl font-bold text-green-600">95%</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">OSHA Compliance</h3>
            <p class="text-sm text-gray-600 mb-3">Occupational Safety and Health</p>
            <div class="space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Safety Training</span>
                    <span class="text-green-600">Complete</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Risk Assessment</span>
                    <span class="text-green-600">Current</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Incident Reports</span>
                    <span class="text-green-600">Filed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Compliance Deadlines -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Upcoming Compliance Deadlines</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">PAYE Monthly Return</span>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">7 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Tanzania Revenue Authority</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(7)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Estimated Amount:</span>
                        <span class="font-medium">TZS 8.1M</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">NSSF Monthly Schedule</span>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">15 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">National Social Security Fund</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(15)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Employee Count:</span>
                        <span class="font-medium">248</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">Annual Labour Audit</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">30 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Ministry of Labour</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(30)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium text-green-600">In Progress</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">WCF Quarterly Declaration</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">45 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Workers Compensation Fund</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(45)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Quarter:</span>
                        <span class="font-medium">Q4 2024</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">Work Permit Renewals</span>
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">60 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Immigration Department</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(60)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Expiring:</span>
                        <span class="font-medium">3 Permits</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-900">Data Protection Audit</span>
                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">90 Days</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Personal Data Protection Act</p>
                <p class="text-xs text-gray-500">Due: {{ now()->addDays(90)->format('d M Y') }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Audit:</span>
                        <span class="font-medium">{{ now()->subYear()->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legal Documents & Templates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Labour Law Database -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Labour Law Database</h3>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="book" class="w-5 h-5 text-gray-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Employment and Labour Relations Act</p>
                            <p class="text-xs text-gray-500">Cap 366 R.E 2019</p>
                        </div>
                    </div>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="book" class="w-5 h-5 text-gray-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Labour Institutions Act</p>
                            <p class="text-xs text-gray-500">Cap 365 R.E 2019</p>
                        </div>
                    </div>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="book" class="w-5 h-5 text-gray-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Non-Citizens (Employment Regulation) Act</p>
                            <p class="text-xs text-gray-500">Cap 381 R.E 2019</p>
                        </div>
                    </div>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="book" class="w-5 h-5 text-gray-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Personal Data Protection Act</p>
                            <p class="text-xs text-gray-500">Cap 426 R.E 2022</p>
                        </div>
                    </div>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Document Templates -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Legal Document Templates</h3>
                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Browse All</button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file-text" class="w-5 h-5 text-blue-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Employment Contract Template</p>
                            <p class="text-xs text-gray-500">Labour Act Compliant</p>
                        </div>
                    </div>
                    <button class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700">
                        Use
                    </button>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file-text" class="w-5 h-5 text-red-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Show Cause Letter Template</p>
                            <p class="text-xs text-gray-500">Disciplinary Action</p>
                        </div>
                    </div>
                    <button class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">
                        Use
                    </button>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file-text" class="w-5 h-5 text-green-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Termination Letter Template</p>
                            <p class="text-xs text-gray-500">Legal Compliant</p>
                        </div>
                    </div>
                    <button class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                        Use
                    </button>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file-text" class="w-5 h-5 text-purple-600"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Warning Letter Template</p>
                            <p class="text-xs text-gray-500">Progressive Discipline</p>
                        </div>
                    </div>
                    <button class="px-3 py-1 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700">
                        Use
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Alerts -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-center mb-4">
            <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600 mr-3"></i>
            <h3 class="text-lg font-semibold text-yellow-900">Compliance Alerts</h3>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Work Permit Expiry Alert</p>
                    <p class="text-xs text-gray-600">3 employee work permits expiring in the next 60 days</p>
                </div>
                <button class="px-3 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700">
                    Review Now
                </button>
            </div>
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Contract Renewal Required</p>
                    <p class="text-xs text-gray-600">5 fixed-term contracts expiring this month</p>
                </div>
                <button class="px-3 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700">
                    Review Now
                </button>
            </div>
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200">
                <div>
                    <p class="text-sm font-medium text-gray-900">Mandatory Training Due</p>
                    <p class="text-xs text-gray-600">OSHA compliance training for 12 employees</p>
                </div>
                <button class="px-3 py-1 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700">
                    Schedule Training
                </button>
            </div>
        </div>
    </div>

    <!-- Audit History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Audit History</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Full History</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Audit Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conducted By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ now()->subDays(15)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Internal Compliance Audit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Internal Audit Team
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-green-600">94%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">View Report</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ now()->subMonths(3)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Ministry of Labour Inspection
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Ministry Labour Officer
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-green-600">96%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Passed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">View Report</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ now()->subMonths(6)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Data Protection Audit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            External Consultant
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-yellow-600">85%</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Improvements Needed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">View Report</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
