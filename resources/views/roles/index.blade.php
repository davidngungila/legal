@extends('layouts.app')

@section('title', 'Role Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Role Management</h1>
            <p class="text-gray-600 mt-2">Manage system roles and their permissions</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.location.href='/users'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="users" class="w-4 h-4 inline mr-2"></i>
                Manage Users
            </button>
            <button onclick="window.location.href='/permissions'" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="key" class="w-4 h-4 inline mr-2"></i>
                Manage Permissions
            </button>
            <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus-circle" class="w-4 h-4 inline mr-2"></i>
                Create New Role
            </a>
        </div>
    </div>

    <!-- Role Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalRolesCount">4</h3>
            <p class="text-gray-600 text-sm">Total Roles</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="activeRolesCount">3</h3>
            <p class="text-gray-600 text-sm">Active Roles</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Users</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalUsersInRoles">10</h3>
            <p class="text-gray-600 text-sm">Users with Roles</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="key" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Permissions</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900" id="totalPermissionsCount">25</h3>
            <p class="text-gray-600 text-sm">Total Permissions</p>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Roles List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="rolesTableBody">
                    <!-- Roles will be dynamically loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div id="createRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Create New Role</h2>
            <button onclick="closeCreateRoleModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="createRoleForm" class="space-y-6">
            <!-- Role Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., hr_admin">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                        <input type="text" name="display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., HR Admin">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe the role and its responsibilities"></textarea>
                </div>
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <!-- Permissions -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Permissions</h3>
                <div id="permissionsContainer" class="space-y-4">
                    <!-- Permissions will be loaded dynamically -->
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCreateRoleModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Edit Role</h2>
            <button onclick="closeEditRoleModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <form id="editRoleForm" class="space-y-6">
            <input type="hidden" name="role_id" id="editRoleId">
            <!-- Similar fields as create form but pre-filled -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                        <input type="text" name="name" id="editRoleName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                        <input type="text" name="display_name" id="editRoleDisplayName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="editRoleDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="editRoleActive" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Permissions</h3>
                <div id="editPermissionsContainer" class="space-y-4">
                    <!-- Permissions will be loaded dynamically -->
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Update Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/roles';
const PERMISSIONS_API = '/api/roles/permissions';

let roles = [];
let permissions = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadRoles();
    loadPermissions();
});

// Load roles from API
async function loadRoles() {
    try {
        const response = await fetch(API_BASE);
        const data = await response.json();
        
        if (data.success) {
            roles = data.roles;
            renderRoles();
            updateStats();
        } else {
            showNotification('Failed to load roles', 'error');
        }
    } catch (error) {
        console.error('Error loading roles:', error);
        showNotification('Error loading roles', 'error');
    }
}

// Load permissions from API
async function loadPermissions() {
    try {
        const response = await fetch(PERMISSIONS_API);
        const data = await response.json();
        
        if (data.success) {
            permissions = data.permissions;
            renderPermissionCheckboxes();
            renderEditPermissionCheckboxes();
        } else {
            showNotification('Failed to load permissions', 'error');
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        showNotification('Error loading permissions', 'error');
    }
}

// Render roles table
function renderRoles() {
    const tbody = document.getElementById('rolesTableBody');
    tbody.innerHTML = '';
    
    roles.forEach(role => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i data-feather="shield" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${role.display_name}</div>
                        <div class="text-sm text-gray-500">${role.name}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${role.description || 'No description'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${role.permissions ? role.permissions.length : 0} permissions
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${role.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${role.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${role.users ? role.users.length : 0} users
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button onclick="editRole(${role.id})" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <i data-feather="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button onclick="deleteRole(${role.id})" class="text-red-600 hover:text-red-900" title="Delete">
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

// Render permission checkboxes
function renderPermissionCheckboxes() {
    const container = document.getElementById('permissionsContainer');
    container.innerHTML = '';
    
    // Group permissions by group
    const groupedPermissions = {};
    permissions.forEach(permission => {
        if (!groupedPermissions[permission.group]) {
            groupedPermissions[permission.group] = [];
        }
        groupedPermissions[permission.group].push(permission);
    });
    
    Object.keys(groupedPermissions).forEach(group => {
        const groupDiv = document.createElement('div');
        groupDiv.className = 'border border-gray-200 rounded-lg p-4';
        groupDiv.innerHTML = `
            <h4 class="font-medium text-gray-900 mb-3">${group}</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                ${groupedPermissions[group].map(permission => `
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="${permission.id}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${permission.display_name}</span>
                    </label>
                `).join('')}
            </div>
        `;
        container.appendChild(groupDiv);
    });
}

// Render edit permission checkboxes
function renderEditPermissionCheckboxes() {
    const container = document.getElementById('editPermissionsContainer');
    container.innerHTML = '';
    
    // Group permissions by group
    const groupedPermissions = {};
    permissions.forEach(permission => {
        if (!groupedPermissions[permission.group]) {
            groupedPermissions[permission.group] = [];
        }
        groupedPermissions[permission.group].push(permission);
    });
    
    Object.keys(groupedPermissions).forEach(group => {
        const groupDiv = document.createElement('div');
        groupDiv.className = 'border border-gray-200 rounded-lg p-4';
        groupDiv.innerHTML = `
            <h4 class="font-medium text-gray-900 mb-3">${group}</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                ${groupedPermissions[group].map(permission => `
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="${permission.id}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">${permission.display_name}</span>
                    </label>
                `).join('')}
            </div>
        `;
        container.appendChild(groupDiv);
    });
}

// Update statistics
function updateStats() {
    const totalRoles = roles.length;
    const activeRoles = roles.filter(r => r.is_active).length;
    const totalUsers = roles.reduce((sum, role) => sum + (role.users ? role.users.length : 0), 0);
    const totalPermissions = permissions.length;
    
    document.getElementById('totalRolesCount').textContent = totalRoles;
    document.getElementById('activeRolesCount').textContent = activeRoles;
    document.getElementById('totalUsersInRoles').textContent = totalUsers;
    document.getElementById('totalPermissionsCount').textContent = totalPermissions;
}

// Modal functions
function showCreateRoleModal() {
    document.getElementById('createRoleModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeCreateRoleModal() {
    document.getElementById('createRoleModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('createRoleForm').reset();
}

function showEditRoleModal() {
    document.getElementById('editRoleModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditRoleModal() {
    document.getElementById('editRoleModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// CRUD operations
async function editRole(roleId) {
    try {
        const response = await fetch(`${API_BASE}/${roleId}`);
        const data = await response.json();
        
        if (data.success) {
            const role = data.role;
            document.getElementById('editRoleId').value = role.id;
            document.getElementById('editRoleName').value = role.name;
            document.getElementById('editRoleDisplayName').value = role.display_name;
            document.getElementById('editRoleDescription').value = role.description || '';
            document.getElementById('editRoleActive').checked = role.is_active;
            
            // Check permissions
            const checkboxes = document.querySelectorAll('#editPermissionsContainer input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = role.permissions && role.permissions.some(p => p.id == checkbox.value);
            });
            
            showEditRoleModal();
        } else {
            showNotification('Failed to load role', 'error');
        }
    } catch (error) {
        console.error('Error loading role:', error);
        showNotification('Error loading role', 'error');
    }
}

async function deleteRole(roleId) {
    if (!confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Role deleted successfully', 'success');
            loadRoles();
        } else {
            showNotification(data.message || 'Failed to delete role', 'error');
        }
    } catch (error) {
        console.error('Error deleting role:', error);
        showNotification('Error deleting role', 'error');
    }
}

// Form submissions
document.getElementById('createRoleForm').addEventListener('submit', async function(e) {
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
            closeCreateRoleModal();
            showNotification('Role created successfully', 'success');
            loadRoles();
        } else {
            showNotification(data.message || 'Failed to create role', 'error');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    } catch (error) {
        console.error('Error creating role:', error);
        showNotification('Error creating role', 'error');
    }
});

document.getElementById('editRoleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const roleId = formData.get('role_id');
    
    try {
        const response = await fetch(`${API_BASE}/${roleId}`, {
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
            closeEditRoleModal();
            showNotification('Role updated successfully', 'success');
            loadRoles();
        } else {
            showNotification(data.message || 'Failed to update role', 'error');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    } catch (error) {
        console.error('Error updating role:', error);
        showNotification('Error updating role', 'error');
    }
});

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
