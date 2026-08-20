@extends('layouts.app')

@section('title', 'User Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">User Management</h1>
            <p class="text-gray-600 mt-2">Manage system users and access permissions</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Showing users for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
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
                <span class="text-sm text-gray-500 font-medium" id="totalUsersChange">-</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalUsersCount">-</h3>
            <p class="text-gray-600 text-sm">Total Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-gray-500 font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="activeUsersCount">-</h3>
            <p class="text-gray-600 text-sm">Active Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="user-x" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-gray-500 font-medium">-</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="inactiveUsersCount">-</h3>
            <p class="text-gray-600 text-sm">Inactive Users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-gray-500 font-medium">Admin</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="adminUsersCount">-</h3>
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
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Users List</h2>
            <div class="flex flex-wrap gap-2">
                <button onclick="bulkOperation('activate')" class="px-3 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="bulkActivateBtn">
                    Activate Selected
                </button>
                <button onclick="bulkOperation('deactivate')" class="px-3 py-2 text-sm bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="bulkDeactivateBtn">
                    Deactivate Selected
                </button>
                <button onclick="bulkOperation('delete')" class="px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="bulkDeleteBtn">
                    Delete Selected
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllUsers" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="w-64 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="w-64 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="w-48 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="w-40 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="w-52 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                    <tr id="loadingRow">
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-3"></div>
                                <span>Loading users...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Modal -->
    <x-advanced-modal id="editUserModal" title="Edit User" icon="edit" color="indigo" size="2xl">
        <form id="editUserForm" class="space-y-6">
                <input type="hidden" id="editUserId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="editFirstName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="editLastName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editEmail" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="editPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="editRole" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="editIsActive" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                        <input type="password" id="editPassword" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Leave blank to keep current password">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions (optional)</label>
                    <div id="editPermissions" class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                    </div>
                </div>
        </form>

    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="editUserForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Save Changes
            </button>
        </div>
    </x-slot:footer>
    </x-advanced-modal>

    <!-- View User Modal -->
    <x-advanced-modal id="viewUserModal" title="View User Details" icon="file-text" color="blue" size="2xl">
        <div class="flex items-center mb-6">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                        <span id="viewUserInitials" class="text-indigo-600 font-bold text-2xl">--</span>
                    </div>
                    <div>
                        <h4 id="viewUserName" class="text-xl font-semibold text-gray-900">--</h4>
                        <p id="viewUserEmail" class="text-gray-500">--</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Role</p>
                        <p id="viewUserRole" class="font-medium text-gray-900">--</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <span id="viewUserStatus" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">--</span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Phone</p>
                        <p id="viewUserPhone" class="font-medium text-gray-900">--</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Company</p>
                        <p id="viewUserCompany" class="font-medium text-gray-900">--</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Last Login</p>
                        <p id="viewUserLastLogin" class="font-medium text-gray-900">--</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Created At</p>
                        <p id="viewUserCreatedAt" class="font-medium text-gray-900">--</p>
                    </div>
                </div>
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-2">Permissions</p>
                    <div id="viewUserPermissions" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                        <!-- Permissions will be populated here -->
                    </div>
                </div>
    <x-slot:footer>
        <div class="flex justify-end">
            <button onclick="closeViewUserModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                Close
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
</div>
@endsection

<script>
    // API endpoints
    const API_BASE = '/users/data';
    const EXPORT_URL = '/users/export';
    const ROLES_PERMISSIONS_URL = '/users/data/roles-permissions';

    // Sample user data - will be replaced with API call
    let users = [];
    let currentPage = 1;
    let filteredUsers = [];

    // Global functions (must be defined outside DOMContentLoaded)
// Client switching function is now defined in layouts/app.blade.php

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

    let availableRoles = [];
    let availablePermissions = [];

    // Load users from API
    async function requestJson(url, options = {}) {
        const method = (options.method || 'GET').toUpperCase();
        const headers = Object.assign({}, options.headers || {}, { 'Accept': 'application/json' });

        if (method !== 'GET') {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (token) headers['X-CSRF-TOKEN'] = token;
            if (!headers['Content-Type'] && !(options.body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
            }
        }

        const response = await fetch(url, Object.assign({}, options, {
            method,
            headers,
            credentials: 'same-origin'
        }));

        if (response.status === 401) {
            window.location.href = '/login';
            return null;
        }

        if (response.status === 419) {
            showNotification('Session expired. Please refresh the page and try again.', 'error');
            return null;
        }

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            if (response.url && response.url.includes('/login')) {
                window.location.href = response.url;
                return null;
            }
            throw new Error('Non-JSON response');
        }

        return await response.json();
    }

    async function loadUsers(retryCount = 0) {
        const maxRetries = 3;
        
        try {
            console.log(`Fetching users from: ${API_BASE}?${new URLSearchParams(window.location.search)}`);
            const data = await requestJson(`${API_BASE}?${new URLSearchParams(window.location.search)}`);
            if (!data) return;
            
            console.log('Users API response:', data);
            
            if (data.success) {
                users = data.users || [];
                filteredUsers = [...users];
                
                console.log('Users loaded:', users);
                console.log('Filtered users:', filteredUsers);
                console.log('Users count:', users.length);
                
                renderUsers();
                updateStats(data.stats);
            } else {
                console.error('API returned success=false:', data);
                showNotification('Failed to load users', 'error');
                renderError('Failed to load users');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            
            if (retryCount < maxRetries) {
                console.log(`Retrying loadUsers (${retryCount + 1}/${maxRetries})...`);
                setTimeout(() => loadUsers(retryCount + 1), 1000 * (retryCount + 1));
            } else {
                showNotification('Error loading users. Please refresh the page.', 'error');
                renderError('Failed to load users after multiple attempts');
            }
        }
    }

    // Render error state
    function renderError(message) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i data-feather="alert-circle" class="w-12 h-12 text-red-500 mb-3"></i>
                        <span class="text-red-600 font-medium">${message}</span>
                        <button onclick="loadUsers()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Retry
                        </button>
                    </div>
                </td>
            </tr>
        `;
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Render users table
    function renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        if (!tbody) {
            console.error('Table body element not found');
            return;
        }
        
        tbody.innerHTML = '';
        
        console.log('Rendering users, count:', filteredUsers.length);
        
        if (filteredUsers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-12 text-gray-500">No users found</td></tr>';
            return;
        }
        
        filteredUsers.forEach((user) => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            // Safely get user details
            const firstName = (user.first_name || '').toString();
            const lastName = (user.last_name || '').toString();
            const fullName = `${firstName} ${lastName}`.trim() || 'Unknown User';
            const firstInitial = (firstName.charAt(0) || '?').toUpperCase();
            const lastInitial = (lastName.charAt(0) || '?').toUpperCase();
            const email = user.email || 'No Email';
            
            // Get single company
        const companyName = user.company_name || 'Orvion';
            
            // Get role
            const userRoles = user.roles || [];
            const roleName = userRoles.length > 0 
                ? (userRoles[0].display_name || userRoles[0].name) 
                : (user.role_display || user.role || 'No Role');
            const roleValue = userRoles.length > 0 
                ? userRoles[0].name 
                : (user.role || 'no_role');
            
            row.innerHTML = `
                <td class="w-12 px-4 py-4 whitespace-nowrap">
                    <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-id="${user.id}">
                </td>
                <td class="w-64 px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        ${user.profile_photo_url 
                            ? `<img src="${user.profile_photo_url}" alt="Profile" class="w-10 h-10 rounded-full object-cover mr-3">` 
                            : `<div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-indigo-600 font-medium text-sm">${firstInitial}${lastInitial}</span>
                            </div>`}
                        <div class="text-sm font-medium text-gray-900 truncate">${fullName}</div>
                    </div>
                </td>
                <td class="w-64 px-6 py-4 text-sm text-gray-900">
                    <div class="truncate">${email}</div>
                </td>
                <td class="w-40 px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getRoleBadgeClass(roleValue)}">
                        ${roleName}
                    </span>
                </td>
                <td class="w-48 px-6 py-4 whitespace-nowrap text-sm text-gray-900 truncate">
                    <div class="flex items-center">
                        <i data-feather="building" class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0"></i>
                        <span class="truncate">${companyName}</span>
                    </div>
                </td>
                <td class="w-32 px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="w-40 px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${formatDate(user.last_login_at)}
                </td>
                <td class="w-52 px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-1">
                        <button onclick="viewUser(${user.id})" class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="editUser(${user.id})" class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button onclick="toggleUserStatus(${user.id})" class="p-2 ${user.is_active ? 'text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50' : 'text-green-600 hover:text-green-900 hover:bg-green-50'} rounded-lg transition-colors" title="${user.is_active ? 'Deactivate' : 'Activate'}">
                            <i data-feather="${user.is_active ? 'user-x' : 'user-check'}" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        console.log('Rendered', filteredUsers.length, 'users to table');
    }

    // Helper functions
    function getRoleBadgeClass(role) {
        const roleClasses = {
            'super_admin': 'bg-purple-100 text-purple-800',
            'lead_hr_admin': 'bg-blue-100 text-blue-800',
            'hr_officer': 'bg-green-100 text-green-800',
            'finance_payroll_officer': 'bg-yellow-100 text-yellow-800',
            'line_manager': 'bg-orange-100 text-orange-800',
            'employee': 'bg-gray-100 text-gray-800',
            'external_auditor': 'bg-red-100 text-red-800'
        };
        return roleClasses[role] || 'bg-gray-100 text-gray-800';
    }

    function formatDate(dateString) {
        if (!dateString) return 'Never';
        
        try {
            const date = new Date(dateString);
            // Check if date is valid
            if (isNaN(date.getTime())) {
                return 'Invalid Date';
            }
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return 'Never';
        }
    }

    // Filter functions
    function filterUsers() {
        const searchTerm = document.getElementById('userSearch').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        
        filteredUsers = users.filter(user => {
            // Safe search checks
            const firstName = (user.first_name || '').toString().toLowerCase();
            const lastName = (user.last_name || '').toString().toLowerCase();
            const email = (user.email || '').toString().toLowerCase();
            
            const matchesSearch = !searchTerm || 
                firstName.includes(searchTerm) ||
                lastName.includes(searchTerm) ||
                email.includes(searchTerm);
            
            // Safe role check
            const userRoles = user.roles || [];
            const userRole = userRoles.length > 0 
                ? userRoles[0].name 
                : (user.role || '');
            const matchesRole = !roleFilter || userRole === roleFilter;
            
            // Safe status check
            const matchesStatus = !statusFilter || (user.is_active ? '1' : '0') === statusFilter;
            
            return matchesSearch && matchesRole && matchesStatus;
        });
        
        renderUsers();
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAllUsers');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    function updateStats(stats = null) {
        const totalUsers = stats?.total ?? users.length;
        const activeUsers = stats?.active ?? users.filter(user => user.is_active).length;
        const inactiveUsers = stats?.inactive ?? (totalUsers - activeUsers);
        
        // Safe admin count using roles array
        const adminUsers = stats?.admin ?? users.filter(user => {
            const userRoles = user.roles || [];
            if (userRoles.length > 0) {
                return userRoles.some(r => (r.name || '').includes('admin'));
            }
            return (user.role || '').includes('admin');
        }).length;

        const totalEl = document.getElementById('totalUsersCount');
        const activeEl = document.getElementById('activeUsersCount');
        const inactiveEl = document.getElementById('inactiveUsersCount');
        const adminEl = document.getElementById('adminUsersCount');
        
        if (totalEl) totalEl.textContent = totalUsers;
        if (activeEl) activeEl.textContent = activeUsers;
        if (inactiveEl) inactiveEl.textContent = inactiveUsers;
        if (adminEl) adminEl.textContent = adminUsers;
    }

    function resetFilters() {
        document.getElementById('userSearch').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('selectAllUsers').checked = false;
        
        filteredUsers = [...users];
        renderUsers();
    }

    function exportUsers() {
        const params = new URLSearchParams();
        const search = document.getElementById('userSearch').value.trim();
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;

        if (search) params.set('search', search);
        if (role) params.set('role', role);
        if (status !== '') params.set('status', status);

        window.location.href = `${EXPORT_URL}?${params.toString()}`;
    }

    function openEditUserModal() {
        openModal('editUserModal');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function closeEditUserModal() {
        closeModal('editUserModal');
        document.getElementById('editUserForm').reset();
        document.getElementById('editPermissions').innerHTML = '';
        document.getElementById('editPassword').value = '';
    }

    function openViewUserModal() {
        openModal('viewUserModal');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function closeViewUserModal() {
        closeModal('viewUserModal');
    }

    async function viewUser(userId) {
        const data = await requestJson(`${API_BASE}/${userId}`);
        if (!data || !data.success) {
            showNotification(data?.message || 'Failed to load user', 'error');
            return;
        }

        const user = data.user;
        
        // Safely get user details with fallbacks
        const firstName = (user.first_name || '').toString();
        const lastName = (user.last_name || '').toString();
        const fullName = `${firstName} ${lastName}`.trim() || 'Unknown User';
        const firstInitial = (firstName.charAt(0) || '?').toUpperCase();
        const lastInitial = (lastName.charAt(0) || '?').toUpperCase();
        
        // Get single company
        const companyName = user.company_name || 'Orvion';
        
        // Get role with fallback
        const userRoles = user.roles || [];
        const roleName = userRoles.length > 0 
            ? (userRoles[0].display_name || userRoles[0].name) 
            : (user.role_display || user.role || 'N/A');

        // Update avatar
        const avatarContainer = document.querySelector('#viewUserModal .w-20.h-20');
        if (avatarContainer) {
            if (user.profile_photo_url) {
                avatarContainer.innerHTML = `<img src="${user.profile_photo_url}" alt="Profile" class="w-20 h-20 rounded-full object-cover">`;
            } else {
                avatarContainer.innerHTML = `<span id="viewUserInitials" class="text-indigo-600 font-bold text-2xl">${firstInitial}${lastInitial}</span>`;
            }
        }
        
        // Update user details with null checks
        const userNameEl = document.getElementById('viewUserName');
        if (userNameEl) userNameEl.textContent = fullName;
        
        const userEmailEl = document.getElementById('viewUserEmail');
        if (userEmailEl) userEmailEl.textContent = user.email || 'N/A';
        
        const userRoleEl = document.getElementById('viewUserRole');
        if (userRoleEl) userRoleEl.textContent = roleName;
        
        const userPhoneEl = document.getElementById('viewUserPhone');
        if (userPhoneEl) userPhoneEl.textContent = user.phone || 'N/A';
        
        const userCompanyEl = document.getElementById('viewUserCompany');
        if (userCompanyEl) userCompanyEl.textContent = companyName;
        
        const userLastLoginEl = document.getElementById('viewUserLastLogin');
        if (userLastLoginEl) userLastLoginEl.textContent = formatDate(user.last_login_at);
        
        const userCreatedAtEl = document.getElementById('viewUserCreatedAt');
        if (userCreatedAtEl) userCreatedAtEl.textContent = formatDate(user.created_at);

        // Update status
        const statusElement = document.getElementById('viewUserStatus');
        if (statusElement) {
            statusElement.textContent = user.is_active ? 'Active' : 'Inactive';
            statusElement.className = `inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        }

        // Update permissions
        const permissionsContainer = document.getElementById('viewUserPermissions');
        if (permissionsContainer) {
            permissionsContainer.innerHTML = '';
            const permissions = user.permissions || [];
            if (permissions.length > 0) {
                permissions.forEach(perm => {
                    const badge = document.createElement('span');
                    badge.className = 'px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full';
                    badge.textContent = perm.display_name || perm.name;
                    permissionsContainer.appendChild(badge);
                });
            } else {
                permissionsContainer.innerHTML = '<span class="text-gray-500 text-sm">No permissions assigned</span>';
            }
        }

        openViewUserModal();
    }

    async function loadRolesAndPermissions(retryCount = 0) {
        const maxRetries = 3;
        
        try {
            const data = await requestJson(ROLES_PERMISSIONS_URL);
            if (!data || !data.success) {
                if (retryCount < maxRetries) {
                    setTimeout(() => loadRolesAndPermissions(retryCount + 1), 1000 * (retryCount + 1));
                }
                return;
            }

            availableRoles = data.roles || [];
            availablePermissions = data.permissions || [];

            const roleFilter = document.getElementById('roleFilter');
            const editRole = document.getElementById('editRole');

            if (!roleFilter || !editRole) return;

            const preserveRole = roleFilter.value;
            roleFilter.innerHTML = '<option value="">All Roles</option>';
            editRole.innerHTML = '<option value="">Select Role</option>';

            availableRoles.forEach((role) => {
                const opt1 = document.createElement('option');
                opt1.value = role.name;
                opt1.textContent = role.display_name || role.name;
                roleFilter.appendChild(opt1);

                const opt2 = document.createElement('option');
                opt2.value = role.name;
                opt2.textContent = role.display_name || role.name;
                editRole.appendChild(opt2);
            });

            roleFilter.value = preserveRole;
        } catch (error) {
            console.error('Error loading roles and permissions:', error);
            if (retryCount < maxRetries) {
                setTimeout(() => loadRolesAndPermissions(retryCount + 1), 1000 * (retryCount + 1));
            }
        }
    }

    async function editUser(userId) {
        const data = await requestJson(`${API_BASE}/${userId}`);
        if (!data || !data.success) {
            showNotification(data?.message || 'Failed to load user', 'error');
            return;
        }

        const user = data.user;
        const roleName = (user.roles && user.roles.length > 0) ? user.roles[0].name : (user.role || '');
        const permissionNames = new Set((user.permissions || []).map(p => p.name));

        document.getElementById('editUserId').value = user.id;
        document.getElementById('editFirstName').value = user.first_name || '';
        document.getElementById('editLastName').value = user.last_name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editPhone').value = user.phone || '';
        document.getElementById('editRole').value = roleName;
        document.getElementById('editIsActive').value = user.is_active ? '1' : '0';

        const permsContainer = document.getElementById('editPermissions');
        permsContainer.innerHTML = '';
        availablePermissions.forEach((perm) => {
            const id = `perm_${perm.id}`;
            const wrapper = document.createElement('label');
            wrapper.className = 'flex items-center space-x-2';
            wrapper.innerHTML = `
                <input type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" value="${perm.name}" ${permissionNames.has(perm.name) ? 'checked' : ''}>
                <span class="text-sm text-gray-700">${perm.display_name || perm.name}</span>
            `;
            permsContainer.appendChild(wrapper);
        });

        openEditUserModal();
    }

    async function toggleUserStatus(userId) {
        const user = users.find(u => u.id === userId);
        if (!user) return;

        const nextStatus = !user.is_active;
        const confirmed = confirm(`${nextStatus ? 'Activate' : 'Deactivate'} this user?`);
        if (!confirmed) return;

        const payload = {
            first_name: user.first_name,
            last_name: user.last_name,
            email: user.email,
            phone: user.phone,
            role: user.role,
            is_active: nextStatus ? 1 : 0
        };

        const data = await requestJson(`${API_BASE}/${userId}`, { method: 'PUT', body: JSON.stringify(payload) });
        if (!data) return;

        if (data.success) {
            showNotification(data.message || 'User updated', 'success');
            await loadUsers();
        } else {
            showNotification(data.message || 'Operation failed', 'error');
        }
    }

    async function deleteUser(userId) {
        if (!confirm('Are you sure you want to delete this user?')) {
            return;
        }

        const data = await requestJson(`${API_BASE}/${userId}`, { method: 'DELETE' });
        if (!data) return;

        if (data.success) {
            showNotification(data.message || 'User deleted successfully', 'success');
            await loadUsers();
        } else {
            showNotification(data.message || 'Failed to delete user', 'error');
        }
    }

    async function bulkOperation(operation) {
        const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => parseInt(cb.dataset.id, 10)).filter(Boolean);
        if (selected.length === 0) {
            showNotification('No users selected', 'warning');
            return;
        }

        if (operation === 'delete' && !confirm('Delete selected users?')) {
            return;
        }

        // Disable buttons during operation
        const buttons = ['bulkActivateBtn', 'bulkDeactivateBtn', 'bulkDeleteBtn'];
        buttons.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) btn.disabled = true;
        });

        try {
            const data = await requestJson(`${API_BASE}/bulk`, {
                method: 'POST',
                body: JSON.stringify({ operation, user_ids: selected })
            });
            if (!data) return;

            if (data.success) {
                showNotification(data.message || 'Operation completed', 'success');
                document.getElementById('selectAllUsers').checked = false;
                await loadUsers();
            } else {
                showNotification(data.message || 'Operation failed', 'error');
            }
        } finally {
            // Re-enable buttons
            buttons.forEach(id => {
                const btn = document.getElementById(id);
                if (btn) btn.disabled = false;
            });
        }
    }



    document.addEventListener('DOMContentLoaded', async function() {
        console.log('=== USERS PAGE INITIALIZED ===');
        console.log('API_BASE:', API_BASE);
        console.log('ROLES_PERMISSIONS_URL:', ROLES_PERMISSIONS_URL);
        
        try {
            await loadRolesAndPermissions();
            console.log('Roles and permissions loaded');
        } catch (error) {
            console.error('Failed to load roles and permissions:', error);
        }
        
        try {
            await loadUsers();
            console.log('Users loaded');
        } catch (error) {
            console.error('Failed to load users:', error);
        }

        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Add event listeners only if elements exist
        const userSearch = document.getElementById('userSearch');
        if (userSearch) userSearch.addEventListener('input', filterUsers);
        
        const roleFilter = document.getElementById('roleFilter');
        if (roleFilter) roleFilter.addEventListener('change', filterUsers);
        
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) statusFilter.addEventListener('change', filterUsers);
        
        const selectAllUsers = document.getElementById('selectAllUsers');
        if (selectAllUsers) selectAllUsers.addEventListener('change', toggleSelectAll);
        
        // Edit user form listener
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const userId = document.getElementById('editUserId').value;
                const permissions = Array.from(document.querySelectorAll('#editPermissions input[type="checkbox"]:checked')).map(cb => cb.value);

                const payload = {
                    first_name: document.getElementById('editFirstName').value.trim(),
                    last_name: document.getElementById('editLastName').value.trim(),
                    email: document.getElementById('editEmail').value.trim(),
                    phone: document.getElementById('editPhone').value.trim(),
                    role: document.getElementById('editRole').value,
                    is_active: parseInt(document.getElementById('editIsActive').value, 10),
                    permissions
                };

                const password = document.getElementById('editPassword').value;
                if (password) {
                    payload.password = password;
                }

                const data = await requestJson(`${API_BASE}/${userId}`, { method: 'PUT', body: JSON.stringify(payload) });
                if (!data) return;

                if (data.success) {
                    showNotification(data.message || 'User updated successfully', 'success');
                    closeEditUserModal();
                    await loadUsers();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach((field) => {
                            const msg = Array.isArray(data.errors[field]) ? data.errors[field].join(', ') : data.errors[field];
                            showNotification(`${field}: ${msg}`, 'error');
                        });
                    } else {
                        showNotification(data.message || 'Failed to update user', 'error');
                    }
                }
            });
        }
    });
</script>
