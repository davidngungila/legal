@extends('layouts.app')

@section('title', 'Organization Setup - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Organization Setup</h1>
            <p class="text-gray-600 mt-2">Configure your company profile and organizational structure</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Data
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Company Profile -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Company Profile</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                <input type="text" value="ABC Manufacturing Ltd" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Registration Number</label>
                <input type="text" value="REG-2021-001234" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">TIN Number</label>
                <input type="text" value="101-234-567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">NSSF Employer Number</label>
                <input type="text" value="NSSF-EMP-045678" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">WCF Policy Number</label>
                <input type="text" value="WCF-2023-001234" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sector Classification</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option>Manufacturing</option>
                    <option>Services</option>
                    <option>Construction</option>
                    <option>Agriculture</option>
                    <option>Mining</option>
                    <option>Tourism</option>
                    <option>Technology</option>
                    <option>Healthcare</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Address</label>
                <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">Plot 123, Industrial Area, Dar es Salaam, Tanzania</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" value="+255 22 123 4567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" value="hr@abcmanufacturing.co.tz" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <!-- Organizational Structure -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Organizational Structure</h3>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Department
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['name' => 'Executive Management', 'head' => 'CEO', 'employees' => 5, 'level' => 1],
                ['name' => 'Human Resources', 'head' => 'HR Director', 'employees' => 12, 'level' => 2],
                ['name' => 'Finance & Accounting', 'head' => 'CFO', 'employees' => 28, 'level' => 2],
                ['name' => 'Information Technology', 'head' => 'IT Manager', 'employees' => 45, 'level' => 2],
                ['name' => 'Operations', 'head' => 'Operations Director', 'employees' => 89, 'level' => 2],
                ['name' => 'Sales & Marketing', 'head' => 'Sales Director', 'employees' => 74, 'level' => 2]
            ] as $dept)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">{{ $dept['name'] }}</h4>
                    <div class="flex space-x-1">
                        <button class="text-blue-600 hover:text-blue-800">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Department Head:</span>
                        <span class="font-medium">{{ $dept['head'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Employees:</span>
                        <span class="font-medium">{{ $dept['employees'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Organization Level:</span>
                        <span class="font-medium">Level {{ $dept['level'] }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Details →</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Employment Categories -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Employment Categories (Per Tanzania Labour Act)</h3>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Category
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Probation Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notice Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Entitlement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Permanent</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Indefinite Contract</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">6 months</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">28 days (or as per contract)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">28 days annually</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">180</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Contract</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Fixed Term</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3 months</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">As per contract terms</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Proportional to contract</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">45</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Probation</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Probation Contract</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">N/A (Currently on probation)</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">7 days</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1 day per month</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">5</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">Casual</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Daily Wage</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">N/A</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1 day</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">After 6 months continuous service</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Union Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Union Information</h3>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Union
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">Tanzania Union of Industrial Workers</h4>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Union Recognition:</span>
                        <span class="font-medium">Recognized</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Collective Agreement:</span>
                        <span class="font-medium">Active until Dec 2025</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Member Employees:</span>
                        <span class="font-medium">156</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Union Representatives:</span>
                        <span class="font-medium">8</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Agreement →</button>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">No Union Representation</h4>
                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Non-Union</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Non-Union Employees:</span>
                        <span class="font-medium">92</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Management Staff:</span>
                        <span class="font-medium">28</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Support Staff:</span>
                        <span class="font-medium">64</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Last Union Activity:</span>
                        <span class="font-medium">N/A</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Non-Union Staff →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Permit Management (for Non-Citizen Employees) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Work Permit Management</h3>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Work Permit
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permit Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permit Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">JK</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">James Kim</div>
                                    <div class="text-sm text-gray-500">Senior Engineer</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Class A</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">WP-2023-001234</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">15 Jan 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">14 Jan 2025</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Expiring Soon</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">Renew</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">MS</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Maria Silva</div>
                                    <div class="text-sm text-gray-500">Finance Manager</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Class B</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">WP-2022-005678</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">01 Mar 2022</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">28 Feb 2025</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">RJ</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Robert Johnson</div>
                                    <div class="text-sm text-gray-500">Technical Director</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">Class C</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">WP-2021-009012</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">20 Jun 2021</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">19 Jun 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Expired</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-red-600 hover:text-red-900">Urgent Renewal</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center mb-4">
            <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600 mr-3"></i>
            <h3 class="text-lg font-semibold text-yellow-900">Organizational Risk Assessment</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Contract Compliance Risk</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Low</span>
                </div>
                <p class="text-xs text-gray-600">98% of contracts are compliant with Labour Act requirements</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Work Permit Risk</span>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Medium</span>
                </div>
                <p class="text-xs text-gray-600">1 work permit expired, 1 expiring within 60 days</p>
            </div>
            <div class="bg-white rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">Union Relations Risk</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Low</span>
                </div>
                <p class="text-xs text-gray-600">Active collective agreement in good standing</p>
            </div>
        </div>
    </div>
</div>
@endsection
