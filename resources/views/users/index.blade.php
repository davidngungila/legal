@extends('layouts.app')

@section('title', 'User Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">User Management</h1>
            <p class="text-gray-600 mt-2">Manage system users and access permissions</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Users
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Add New User
            </button>
        </div>
    </div>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+3</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">48</h3>
            <p class="text-gray-600 text-sm">Total Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">45</h3>
            <p class="text-gray-600 text-sm">Active Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="user-check" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">Admin</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">8</h3>
            <p class="text-gray-600 text-sm">Admin Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">Recent</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">5</h3>
            <p class="text-gray-600 text-sm">New This Month</p>
        </div>
    </div>

    <!-- User List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">System Users</h3>
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Search users..." class="form-input pl-10 pr-4 py-2">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                </div>
                <select class="form-select">
                    <option>All Roles</option>
                    <option>Super Admin</option>
                    <option>HR Admin</option>
                    <option>HR Officer</option>
                    <option>Finance Officer</option>
                    <option>Line Manager</option>
                    <option>Employee</option>
                </select>
                <select class="form-select">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Suspended</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['name' => 'Admin User', 'email' => 'admin@legalhr.co.tz', 'role' => 'Super Admin', 'dept' => 'IT', 'lastLogin' => '2024-03-29 09:30', 'status' => 'Active'],
                        ['name' => 'Sarah Williams', 'email' => 'sarah.williams@legalhr.co.tz', 'role' => 'HR Admin', 'dept' => 'HR', 'lastLogin' => '2024-03-29 08:45', 'status' => 'Active'],
                        ['name' => 'John Smith', 'email' => 'john.smith@legalhr.co.tz', 'role' => 'HR Officer', 'dept' => 'HR', 'lastLogin' => '2024-03-29 09:15', 'status' => 'Active'],
                        ['name' => 'Michael Chen', 'email' => 'michael.chen@legalhr.co.tz', 'role' => 'Finance Officer', 'dept' => 'Finance', 'lastLogin' => '2024-03-28 16:30', 'status' => 'Active'],
                        ['name' => 'Emily Davis', 'email' => 'emily.davis@legalhr.co.tz', 'role' => 'Line Manager', 'dept' => 'Operations', 'lastLogin' => '2024-03-29 07:45', 'status' => 'Active'],
                        ['name' => 'David Wilson', 'email' => 'david.wilson@legalhr.co.tz', 'role' => 'Employee', 'dept' => 'Sales', 'lastLogin' => '2024-03-28 17:00', 'status' => 'Active'],
                        ['name' => 'Lisa Brown', 'email' => 'lisa.brown@legalhr.co.tz', 'role' => 'Employee', 'dept' => 'Marketing', 'lastLogin' => '2024-03-27 14:30', 'status' => 'Active'],
                        ['name' => 'James Taylor', 'email' => 'james.taylor@legalhr.co.tz', 'role' => 'HR Officer', 'dept' => 'HR', 'lastLogin' => '2024-03-26 11:20', 'status' => 'Suspended']
                    ] as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($user['name'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $user['name'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user['email'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($user['role'] === 'Super Admin') bg-purple-100 text-purple-800
                                @elseif($user['role'] === 'HR Admin') bg-blue-100 text-blue-800
                                @elseif($user['role'] === 'HR Officer') bg-green-100 text-green-800
                                @elseif($user['role'] === 'Finance Officer') bg-yellow-100 text-yellow-800
                                @elseif($user['role'] === 'Line Manager') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $user['role'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user['dept'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['lastLogin'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($user['status'] === 'Active') bg-green-100 text-green-800
                                @elseif($user['status'] === 'Suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $user['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-gray-600 hover:text-gray-900">Permissions</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Role Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Role Distribution</h3>
            <div class="space-y-4">
                @foreach([
                    ['role' => 'Super Admin', 'count' => 1, 'color' => 'purple'],
                    ['role' => 'HR Admin', 'count' => 2, 'color' => 'blue'],
                    ['role' => 'HR Officer', 'count' => 8, 'color' => 'green'],
                    ['role' => 'Finance Officer', 'count' => 3, 'color' => 'yellow'],
                    ['role' => 'Line Manager', 'count' => 12, 'color' => 'orange'],
                    ['role' => 'Employee', 'count' => 22, 'color' => 'gray']
                ] as $role)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-{{ $role['color'] }}-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-900">{{ $role['role'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-900 mr-2">{{ $role['count'] }}</span>
                        <span class="text-sm text-gray-500">({{ round(($role['count'] / 48) * 100) }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">User Activity</h3>
            <div class="space-y-4">
                @foreach([
                    ['metric' => 'Daily Active Users', 'value' => 42, 'percentage' => 87],
                    ['metric' => 'Weekly Active Users', 'value' => 45, 'percentage' => 94],
                    ['metric' => 'Monthly Active Users', 'value' => 47, 'percentage' => 98],
                    ['metric' => 'New Users (This Month)', 'value' => 5, 'percentage' => 10]
                ] as $activity)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $activity['metric'] }}</p>
                        <p class="text-sm text-gray-500">{{ $activity['value'] }} users</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-900">{{ $activity['percentage'] }}%</p>
                        <p class="text-sm text-gray-500">of total</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Permissions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">System Permissions Matrix</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit Permissions</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Super Admin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">HR Admin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">HR Officer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Finance Officer</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Line Manager</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        ['module' => 'Dashboard', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => true, 'line_manager' => true, 'employee' => true],
                        ['module' => 'Employee Management', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => false, 'line_manager' => true, 'employee' => false],
                        ['module' => 'Payroll', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => false, 'finance_officer' => true, 'line_manager' => false, 'employee' => false],
                        ['module' => 'Performance', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => false, 'line_manager' => true, 'employee' => false],
                        ['module' => 'Discipline', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => false, 'line_manager' => true, 'employee' => false],
                        ['module' => 'Compliance', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => false, 'line_manager' => false, 'employee' => false],
                        ['module' => 'Analytics', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => true, 'line_manager' => false, 'employee' => false],
                        ['module' => 'Self Service', 'super_admin' => true, 'hr_admin' => true, 'hr_officer' => true, 'finance_officer' => true, 'line_manager' => true, 'employee' => true]
                    ] as $permission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $permission['module'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['super_admin'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['super_admin'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['hr_admin'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['hr_admin'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['hr_officer'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['hr_officer'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['finance_officer'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['finance_officer'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['line_manager'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['line_manager'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <i data-feather="{{ $permission['employee'] ? 'check-circle' : 'x-circle' }}" class="w-5 h-5 {{ $permission['employee'] ? 'text-green-600' : 'text-red-600' }}"></i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
