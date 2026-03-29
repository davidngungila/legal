@extends('layouts.app')

@section('title', 'Permission Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Permission Management</h1>
            <p class="text-gray-600 mt-2">Manage system permissions and access controls</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.location.href='/users'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="users" class="w-4 h-4 inline mr-2"></i>
                Manage Users
            </button>
            <button onclick="window.location.href='/roles'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="shield" class="w-4 h-4 inline mr-2"></i>
                Manage Roles
            </button>
            <a href="{{ route('permissions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus-circle" class="w-4 h-4 inline mr-2"></i>
                Create New Permission
            </a>
        </div>
    </div>

    <!-- Permission Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="key" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalPermissionsCount">25</h3>
            <p class="text-gray-600 text-sm">Total Permissions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Groups</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalGroupsCount">8</h3>
            <p class="text-gray-600 text-sm">Permission Groups</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Roles</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalRolesCount">4</h3>
            <p class="text-gray-600 text-sm">Roles Using Permissions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="activePermissionsCount">20</h3>
            <p class="text-gray-600 text-sm">Active Permissions</p>
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Permissions List</h2>
                <div class="flex items-center space-x-4">
                    <input type="text" id="permissionSearch" placeholder="Search permissions..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64">
                    <select id="groupFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Groups</option>
                        <option value="dashboard">Dashboard</option>
                        <option value="users">Users</option>
                        <option value="employees">Employees</option>
                        <option value="recruitment">Recruitment</option>
                        <option value="attendance">Attendance</option>
                        <option value="payroll">Payroll</option>
                        <option value="performance">Performance</option>
                        <option value="compliance">Compliance</option>
                        <option value="training">Training</option>
                        <option value="settings">Settings</option>
                        <option value="clients">Clients</option>
                        <option value="self-service">Self Service</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permission
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Group
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Roles
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="permissionsTableBody">
                    <!-- Permissions will be dynamically loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Permission Modal -->
<div id="createPermissionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Create New Permission</h2>
            <button onclick="closeCreatePermissionModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="createPermissionForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., users.view">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                <input type="text" name="display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., View Users">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                <select name="group" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Group</option>
                    <option value="dashboard">Dashboard</option>
                    <option value="users">Users</option>
                    <option value="employees">Employees</option>
                    <option value="recruitment">Recruitment</option>
                    <option value="attendance">Attendance</option>
                    <option value="payroll">Payroll</option>
                    <option value="performance">Performance</option>
                    <option value="compliance">Compliance</option>
                    <option value="training">Training</option>
                    <option value="settings">Settings</option>
                    <option value="clients">Clients</option>
                    <option value="self-service">Self Service</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe what this permission allows"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCreatePermissionModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Permission Modal -->
<div id="editPermissionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Edit Permission</h2>
            <button onclick="closeEditPermissionModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="editPermissionForm" class="space-y-6">
            <input type="hidden" name="permission_id" id="editPermissionId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" name="name" id="editPermissionName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                <input type="text" name="display_name" id="editPermissionDisplayName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                <select name="group" id="editPermissionGroup" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Group</option>
                    <option value="dashboard">Dashboard</option>
                    <option value="users">Users</option>
                    <option value="employees">Employees</option>
                    <option value="recruitment">Recruitment</option>
                    <option value="attendance">Attendance</option>
                    <option value="payroll">Payroll</option>
                    <option value="performance">Performance</option>
                    <option value="compliance">Compliance</option>
                    <option value="training">Training</option>
                    <option value="settings">Settings</option>
                    <option value="clients">Clients</option>
                    <option value="self-service">Self Service</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="editPermissionDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditPermissionModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Update Permission
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/permissions';

let permissions = [];
let filteredPermissions = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadPermissions();
});

// Load permissions from API
async function loadPermissions() {
    try {
        const response = await fetch(API_BASE);
        const data = await response.json();
        
        if (data.success) {
            permissions = data.permissions;
            filteredPermissions = [...permissions];
            renderPermissions();
            updateStats();
        } else {
            showNotification('Failed to load permissions', 'error');
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        showNotification('Error loading permissions', 'error');
    }
}

// Render permissions table
function renderPermissions() {
    const tbody = document.getElementById('permissionsTableBody');
    tbody.innerHTML = '';
    
    filteredPermissions.forEach(permission => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i data-feather="key" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${permission.display_name}</div>
                        <div class="text-sm text-gray-500">${permission.name}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${permission.group}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${permission.description || 'No description'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                    ${permission.roles ? permission.roles.length : 0} roles
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Active
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button onclick="editPermission(${permission.id})" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <i data-feather="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="deletePermission(${permission.id})" class="text-red-600 hover:text-red-900" title="Delete">
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

// Update statistics
function updateStats() {
    const totalPermissions = filteredPermissions.length;
    const groups = [...new Set(filteredPermissions.map(p => p.group))];
    const roles = new Set();
    
    filteredPermissions.forEach(permission => {
        if (permission.roles) {
            permission.roles.forEach(role => roles.add(role.id));
        }
    });
    
    document.getElementById('totalPermissionsCount').textContent = totalPermissions;
    document.getElementById('totalGroupsCount').textContent = groups.length;
    document.getElementById('totalRolesCount').textContent = roles.size;
    document.getElementById('activePermissionsCount').textContent = totalPermissions;
}

// Modal functions
function showCreatePermissionModal() {
    document.getElementById('createPermissionModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeCreatePermissionModal() {
    document.getElementById('createPermissionModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('createPermissionForm').reset();
}

function showEditPermissionModal() {
    document.getElementById('editPermissionModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditPermissionModal() {
    document.getElementById('editPermissionModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// CRUD operations
async function editPermission(permissionId) {
    try {
        const response = await fetch(`${API_BASE}/${permissionId}`);
        const data = await response.json();
        
        if (data.success) {
            const permission = data.permission;
            document.getElementById('editPermissionId').value = permission.id;
            document.getElementById('editPermissionName').value = permission.name;
            document.getElementById('editPermissionDisplayName').value = permission.display_name;
            document.getElementById('editPermissionGroup').value = permission.group;
            document.getElementById('editPermissionDescription').value = permission.description || '';
            
            showEditPermissionModal();
        } else {
            showNotification('Failed to load permission', 'error');
        }
    } catch (error) {
        console.error('Error loading permission:', error);
        showNotification('Error loading permission', 'error');
    }
}

async function deletePermission(permissionId) {
    if (!confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/${permissionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Permission deleted successfully', 'success');
            loadPermissions();
        } else {
            showNotification(data.message || 'Failed to delete permission', 'error');
        }
    } catch (error) {
        console.error('Error deleting permission:', error);
        showNotification('Error deleting permission', 'error');
    }
}

// Form submissions
document.getElementById('createPermissionForm').addEventListener('submit', async function(e) {
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
            closeCreatePermissionModal();
            showNotification('Permission created successfully', 'success');
            loadPermissions();
        } else {
            showNotification(data.message || 'Failed to create permission', 'error');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    } catch (error) {
        console.error('Error creating permission:', error);
        showNotification('Error creating permission', 'error');
    }
});

document.getElementById('editPermissionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const permissionId = formData.get('permission_id');
    
    try {
        const response = await fetch(`${API_BASE}/${permissionId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeEditPermissionModal();
            showNotification('Permission updated successfully', 'success');
            loadPermissions();
        } else {
            showNotification(data.message || 'Failed to update permission', 'error');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    } catch (error) {
        console.error('Error updating permission:', error);
        showNotification('Error updating permission', 'error');
    }
});

// Filter and search functions
document.getElementById('permissionSearch').addEventListener('input', filterPermissions);
document.getElementById('groupFilter').addEventListener('change', filterPermissions);

function filterPermissions() {
    const searchTerm = document.getElementById('permissionSearch').value.toLowerCase();
    const groupFilter = document.getElementById('groupFilter').value;
    
    filteredPermissions = permissions.filter(permission => {
        const matchesSearch = !searchTerm || 
            permission.name.toLowerCase().includes(searchTerm) ||
            permission.display_name.toLowerCase().includes(searchTerm) ||
            (permission.description && permission.description.toLowerCase().includes(searchTerm));
        
        const matchesGroup = !groupFilter || permission.group === groupFilter;
        
        return matchesSearch && matchesGroup;
    });
    
    renderPermissions();
    updateStats();
}

// Fallback notification function
if (typeof showNotification === 'undefined') {
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
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}
</script>
@endpush

@endsection
