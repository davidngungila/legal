@extends('layouts.app')

@section('title', 'Compensation & Benefits - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Compensation & Benefits</h1>
            <p class="text-gray-600 mt-2">Manage salary structures and employee benefits</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Salary Structure
            </button>
        </div>
    </div>

    <!-- Compensation Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+8%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS 45.2M</h3>
            <p class="text-gray-600 text-sm">Monthly Payroll</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+5%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS 182K</h3>
            <p class="text-gray-600 text-sm">Avg. Salary</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+12%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS 8.7M</h3>
            <p class="text-gray-600 text-sm">Benefits Cost</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">15</h3>
            <p class="text-gray-600 text-sm">Bonus Plans</p>
        </div>
    </div>

    <!-- Salary Structures -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Salary Structures</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Add Structure</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Executive Level', 'grade' => 'E1-E5', 'min' => 5000000, 'max' => 15000000, 'employees' => 8],
                ['name' => 'Management Level', 'grade' => 'M1-M4', 'min' => 3000000, 'max' => 8000000, 'employees' => 24],
                ['name' => 'Professional Level', 'grade' => 'P1-P5', 'min' => 1500000, 'max' => 4000000, 'employees' => 89],
                ['name' => 'Technical Level', 'grade' => 'T1-T4', 'min' => 800000, 'max' => 2500000, 'employees' => 67],
                ['name' => 'Support Level', 'grade' => 'S1-S3', 'min' => 400000, 'max' => 1200000, 'employees' => 45],
                ['name' => 'Intern Level', 'grade' => 'I1-I2', 'min' => 200000, 'max' => 500000, 'employees' => 15]
            ] as $structure)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">{{ $structure['grade'] }}</span>
                    <span class="text-xs text-gray-500">{{ $structure['employees'] }} employees</span>
                </div>
                <h4 class="font-semibold text-gray-900 mb-2">{{ $structure['name'] }}</h4>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Minimum:</span>
                        <span class="font-medium">TZS {{ number_format($structure['min'], 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Maximum:</span>
                        <span class="font-medium">TZS {{ number_format($structure['max'], 0) }}</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Details →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Benefits Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Benefits Programs</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Benefits</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="shield" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Health Insurance</h4>
                                <p class="text-sm text-gray-500">Comprehensive medical coverage</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Covers: Medical, Dental, Vision</p>
                        <p>Employer Contribution: 80%</p>
                        <p>Monthly Cost: TZS 2.5M</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="home" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Retirement Plan</h4>
                                <p class="text-sm text-gray-500">NSSF + Private pension</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>NSSF: 10% (5% employee, 5% employer)</p>
                        <p>Private Pension: Optional</p>
                        <p>Monthly Cost: TZS 4.5M</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="calendar" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Leave Benefits</h4>
                                <p class="text-sm text-gray-500">Paid time off</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Annual Leave: 28 days</p>
                        <p>Sick Leave: 90 days</p>
                        <p>Maternity Leave: 84 days</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="car" class="w-5 h-5 text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Transportation</h4>
                                <p class="text-sm text-gray-500">Company vehicle allowance</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Vehicle Allowance: TZS 500K/month</p>
                        <p>Fuel Card: TZS 300K/month</p>
                        <p>Eligibility: Management level and above</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="book-open" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Training & Development</h4>
                                <p class="text-sm text-gray-500">Professional development</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Training Budget: TZS 2M/year</p>
                        <p>Certification Support: 100%</p>
                        <p>Conference Allowance: TZS 500K/year</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="phone" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Communication</h4>
                                <p class="text-sm text-gray-500">Phone & internet</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Phone Allowance: TZS 150K/month</p>
                        <p>Internet Allowance: TZS 100K/month</p>
                        <p>Eligibility: All employees</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bonus & Incentive Plans -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Bonus & Incentive Plans</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Create Plan</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligibility</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'Performance Bonus', 'type' => 'Performance-based', 'eligibility' => 'All employees', 'frequency' => 'Quarterly', 'budget' => 'TZS 5M', 'status' => 'Active'],
                        ['name' => 'Sales Commission', 'type' => 'Commission', 'eligibility' => 'Sales team', 'frequency' => 'Monthly', 'budget' => 'TZS 3M', 'status' => 'Active'],
                        ['name' => 'Year-end Bonus', 'type' => 'Profit-sharing', 'eligibility' => 'Management level', 'frequency' => 'Annually', 'budget' => 'TZS 15M', 'status' => 'Active'],
                        ['name' => 'Referral Bonus', 'type' => 'Referral', 'eligibility' => 'All employees', 'frequency' => 'Per referral', 'budget' => 'TZS 500K', 'status' => 'Active'],
                        ['name' => 'Innovation Award', 'type' => 'Special recognition', 'eligibility' => 'All employees', 'frequency' => 'Bi-annually', 'budget' => 'TZS 2M', 'status' => 'Active']
                    ] as $bonus)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $bonus['name'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['type'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['eligibility'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['frequency'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $bonus['budget'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $bonus['status'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Compensation Analytics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Compensation Analytics</h3>
            <select class="form-select">
                <option>Last Quarter</option>
                <option>Last 6 Months</option>
                <option>Last Year</option>
            </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Salary Distribution by Department</h4>
                <div class="space-y-3">
                    @foreach([
                        ['dept' => 'IT', 'avg' => 2500000, 'count' => 45],
                        ['dept' => 'Finance', 'avg' => 2200000, 'count' => 28],
                        ['dept' => 'Operations', 'avg' => 1800000, 'count' => 89],
                        ['dept' => 'Sales', 'avg' => 2100000, 'count' => 56],
                        ['dept' => 'Marketing', 'avg' => 1900000, 'count' => 18]
                    ] as $dept)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i data-feather="briefcase" class="w-4 h-4 text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $dept['dept'] }}</p>
                                <p class="text-xs text-gray-500">{{ $dept['count'] }} employees</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">TZS {{ number_format($dept['avg'], 0) }}</p>
                            <p class="text-xs text-gray-500">average</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-4">Benefits Utilization</h4>
                <div class="space-y-3">
                    @foreach([
                        ['benefit' => 'Health Insurance', 'usage' => 92, 'cost' => 2500000],
                        ['benefit' => 'Retirement Plan', 'usage' => 78, 'cost' => 4500000],
                        ['benefit' => 'Training Budget', 'usage' => 65, 'cost' => 1300000],
                        ['benefit' => 'Transport Allowance', 'usage' => 88, 'cost' => 1200000],
                        ['benefit' => 'Phone Allowance', 'usage' => 95, 'cost' => 350000]
                    ] as $benefit)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $benefit['benefit'] }}</p>
                            <p class="text-xs text-gray-500">{{ $benefit['usage'] }}% utilization</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">TZS {{ number_format($benefit['cost'], 0) }}</p>
                            <p class="text-xs text-gray-500">monthly</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
