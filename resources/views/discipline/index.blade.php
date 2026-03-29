@extends('layouts.app')

@section('title', 'Employee Relations & Discipline - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Relations & Discipline</h1>
            <p class="text-gray-600 mt-2">Manage disciplinary cases with legal compliance and risk prevention</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Reports
            </button>
            <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-feather="alert-triangle" class="w-4 h-4 inline mr-2"></i>
                New Case File
            </button>
        </div>
    </div>

    <!-- Risk Assessment Banner -->
    <div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold mb-2">Legal Risk Assessment</h3>
                <p class="text-red-100">Current risk exposure: <span class="font-bold">MEDIUM</span></p>
                <p class="text-sm text-red-200 mt-1">3 high-risk cases require immediate HR Admin attention</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">73%</div>
                <p class="text-sm text-red-100">Compliance Score</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Cases</p>
                    <p class="text-2xl font-bold text-gray-900">7</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900">3</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Resolved This Month</p>
                    <p class="text-2xl font-bold text-gray-900">12</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Hearings Scheduled</p>
                    <p class="text-2xl font-bold text-gray-900">2</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Cases Alert -->
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
        <div class="flex items-center mb-4">
            <i data-feather="alert-circle" class="w-6 h-6 text-red-600 mr-3"></i>
            <h3 class="text-lg font-semibold text-red-900">Critical Cases Requiring Immediate Attention</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Case #001</span>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">HIGH RISK</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Gross Misconduct - Fraud</p>
                <p class="text-xs text-gray-500">Employee: John Doe | Dept: Finance</p>
                <p class="text-xs text-red-600 mt-2">HR Admin approval required for termination</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Case #003</span>
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">MEDIUM RISK</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Repeated Absenteeism</p>
                <p class="text-xs text-gray-500">Employee: Sarah Williams | Dept: Operations</p>
                <p class="text-xs text-orange-600 mt-2">Show cause letter issued</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Case #007</span>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">LOW RISK</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">Policy Violation</p>
                <p class="text-xs text-gray-500">Employee: Michael Chen | Dept: IT</p>
                <p class="text-xs text-yellow-600 mt-2">Warning letter issued</p>
            </div>
        </div>
    </div>

    <!-- Case Management Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-gray-900">All Disciplinary Cases</h3>
                <div class="flex space-x-3 mt-3 md:mt-0">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>All Risk Levels</option>
                        <option>Critical</option>
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>All Status</option>
                        <option>Investigation</option>
                        <option>Hearing</option>
                        <option>Decision</option>
                        <option>Appeal</option>
                        <option>Resolved</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allegation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Opened</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HR Admin Review</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        [
                            'id' => '#001',
                            'employee' => 'John Doe',
                            'allegation' => 'Gross Misconduct - Fraud',
                            'risk' => 'critical',
                            'status' => 'decision',
                            'date' => '2024-11-01',
                            'review' => 'pending'
                        ],
                        [
                            'id' => '#002',
                            'employee' => 'Jane Smith',
                            'allegation' => 'Insubordination',
                            'risk' => 'high',
                            'status' => 'investigation',
                            'date' => '2024-11-05',
                            'review' => 'pending'
                        ],
                        [
                            'id' => '#003',
                            'employee' => 'Sarah Williams',
                            'allegation' => 'Repeated Absenteeism',
                            'risk' => 'medium',
                            'status' => 'hearing',
                            'date' => '2024-11-10',
                            'review' => 'approved'
                        ],
                        [
                            'id' => '#004',
                            'employee' => 'Michael Johnson',
                            'allegation' => 'Policy Violation',
                            'risk' => 'low',
                            'status' => 'resolved',
                            'date' => '2024-11-15',
                            'review' => 'approved'
                        ],
                        [
                            'id' => '#005',
                            'employee' => 'Emily Chen',
                            'allegation' => 'Harassment',
                            'risk' => 'high',
                            'status' => 'appeal',
                            'date' => '2024-11-18',
                            'review' => 'pending'
                        ]
                    ] as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $case['id'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($case['employee'], 0, 1) }}</span>
                                </div>
                                <span class="ml-2 text-sm text-gray-900">{{ $case['employee'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $case['allegation'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($case['risk'] == 'critical') bg-red-100 text-red-800
                                @elseif($case['risk'] == 'high') bg-orange-100 text-orange-800
                                @elseif($case['risk'] == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($case['risk']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($case['status'] == 'investigation') bg-blue-100 text-blue-800
                                @elseif($case['status'] == 'hearing') bg-purple-100 text-purple-800
                                @elseif($case['status'] == 'decision') bg-orange-100 text-orange-800
                                @elseif($case['status'] == 'appeal') bg-red-100 text-red-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($case['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($case['date'])->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($case['review'] == 'pending') bg-red-100 text-red-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($case['review']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" title="View Case">
                                    <i data-feather="folder" class="w-4 h-4"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Evidence">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Hearing">
                                    <i data-feather="users" class="w-4 h-4"></i>
                                </button>
                                @if($case['review'] == 'pending' && isset($currentUser) && ((is_object($currentUser) && $currentUser->role === 'hr_admin') || (is_array($currentUser) && $currentUser['role'] === 'hr_admin')))
                                <button class="text-red-600 hover:text-red-900" title="Review">
                                    <i data-feather="check-square" class="w-4 h-4"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legal Compliance Tools -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="shield" class="w-8 h-8 text-purple-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Legal Risk Calculator</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Calculate potential legal risks and exposure before making decisions</p>
            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Calculate Risk
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="book-open" class="w-8 h-8 text-blue-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Labour Law Database</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Access Tanzania Labour Act, ELRA guidelines, and legal precedents</p>
            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Search Laws
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="file-plus" class="w-8 h-8 text-green-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">Document Templates</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Generate legally compliant letters and forms automatically</p>
            <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Generate Document
            </button>
        </div>
    </div>
</div>
@endsection
