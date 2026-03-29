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
            <button onclick="window.location.href='/roles'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="shield" class="w-4 h-4 inline mr-2"></i>
                Manage Roles
            </button>
            <button onclick="window.location.href='/permissions'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="key" class="w-4 h-4 inline mr-2"></i>
                Manage Permissions
            </button>
            <button onclick="exportUsers()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Users
            </button>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Add New User
            </a>
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
            <h3 class="text-2xl font-bold text-gray-900" id="totalUsersCount">48</h3>
            <p class="text-gray-600 text-sm">Total Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="activeUsersCount">45</h3>
            <p class="text-gray-600 text-sm">Active Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="user-x" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">-2</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="inactiveUsersCount">3</h3>
            <p class="text-gray-600 text-sm">Inactive Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Admin</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="adminUsersCount">8</h3>
            <p class="text-gray-600 text-sm">Admin Users</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <div class="relative">
                    <input type="text" id="userSearch" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full md:w-64">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
                <select id="roleFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="hr_admin">HR Admin</option>
                    <option value="manager">Manager</option>
                    <option value="employee">Employee</option>
                </select>
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                Reset Filters
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Users List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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

<script>
    // API endpoints
    const API_BASE = '/api/users';

    // Sample user data - will be replaced with API call
    let users = [];
    let currentPage = 1;
    let filteredUsers = [];

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadUsers();
    });

    // Load users from API
    async function loadUsers() {
        try {
            const response = await fetch(`${API_BASE}?${new URLSearchParams(window.location.search)}`);
            const data = await response.json();
            
            if (data.success) {
                users = data.users;
                filteredUsers = [...users];
                renderUsers();
                updateStats();
            } else {
                showNotification('Failed to load users', 'error');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            showNotification('Error loading users', 'error');
        }
    }

    // Render users table
    function renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';
        
        const startIndex = (currentPage - 1) * 10;
        const endIndex = startIndex + 10;
        const pageUsers = filteredUsers.slice(startIndex, endIndex);
        
        pageUsers.forEach(user => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-id="${user.id}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-indigo-600 font-medium text-sm">${user.first_name.charAt(0)}${user.last_name.charAt(0)}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${user.first_name} ${user.last_name}</div>
                            <div class="text-sm text-gray-500">${user.email}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${user.email}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getRoleBadgeClass(user.role)}">
                        ${user.role_display || user.role}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.last_login_at)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900" title="Delete">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Form submissions
document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeCreateUserModal();
            showNotification('User created successfully', 'success');
            loadUsers(); // Reload users
        } else {
            showNotification(data.message || 'Failed to create user', 'error');
            if (data.errors) {
            }
        } catch (error) {
            console.error('Error loading user:', error);
            showNotification('Error loading user', 'error');
        }
    }

    async function deleteUser(userId) {
        document.getElementById('deleteUserId').value = userId;
        showDeleteUserModal();
    } catch (error) {
        console.error('Error updating user:', error);
        showNotification('Error updating user', 'error');
    }
});
</script>
