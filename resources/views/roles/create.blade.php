@extends('layouts.app')

@section('title', 'Create New Role - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Create New Role</h1>
            <p class="text-gray-600 mt-2">Define a new role with specific permissions</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Roles
            </a>
        </div>
    </div>

    <!-- Role Creation Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="roleForm" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., hr_manager">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                    <input type="text" name="display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., HR Manager">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe the role's responsibilities and purpose..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role Level *</label>
                    <select name="level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Level</option>
                        <option value="1">Level 1 - Super Admin</option>
                        <option value="2">Level 2 - Senior Management</option>
                        <option value="3">Level 3 - Middle Management</option>
                        <option value="4">Level 4 - Supervisors</option>
                        <option value="5">Level 5 - Regular Employees</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                    <select name="department" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Department</option>
                        <option value="hr">Human Resources</option>
                        <option value="finance">Finance & Accounting</option>
                        <option value="it">Information Technology</option>
                        <option value="operations">Operations</option>
                        <option value="admin">Administration</option>
                        <option value="management">Management</option>
                        <option value="all">All Departments</option>
                    </select>
                </div>
            </div>
            
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Permissions</h3>
                <p class="text-sm text-gray-600 mb-4">Select the permissions this role should have</p>
                
                <div class="space-y-6">
                    <!-- User Management Permissions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">User Management</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="users.create" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Create Users</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="users.read" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">View Users</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="users.update" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Update Users</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="users.delete" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Delete Users</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Client Management Permissions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Client Management</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="clients.create" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Create Clients</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="clients.read" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">View Clients</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="clients.update" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Update Clients</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="clients.delete" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Delete Clients</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Employee Management Permissions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Employee Management</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="employees.create" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Create Employees</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="employees.read" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">View Employees</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="employees.update" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Update Employees</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="employees.delete" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Delete Employees</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Payroll Permissions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Payroll Management</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="payroll.create" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Create Payroll</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="payroll.read" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">View Payroll</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="payroll.update" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Update Payroll</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="payroll.approve" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Approve Payroll</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Reports Permissions -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Reports & Analytics</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="reports.view" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">View Reports</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="reports.export" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Export Reports</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="reports.analytics" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Analytics</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="reports.audit" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm">Audit Reports</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tanzania Compliance Settings</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="is_management_role" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="is_management_role" class="text-sm text-gray-700">
                            Management role (can approve leave, timesheets, etc.)
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="can_approve_expenses" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="can_approve_expenses" class="text-sm text-gray-700">
                            Can approve expenses and reimbursements
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="access_sensitive_data" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="access_sensitive_data" class="text-sm text-gray-700">
                            Access to sensitive employee data (salary, medical info)
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="audit_required" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1" checked>
                        <label for="audit_required" class="text-sm text-gray-700">
                            Require audit trail for all actions
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('roles.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Create Role
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/roles';

// Form submission
document.getElementById('roleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formDataObj = Object.fromEntries(formData);
    
    // Handle permissions array
    const permissions = formData.getAll('permissions[]');
    formDataObj.permissions = permissions;
    
    // Show loading
    showNotification('Creating role...', 'info');
    
    try {
        const response = await fetch(API_BASE, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formDataObj)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Role created successfully!', 'success');
            
            // Redirect to roles list after successful creation
            setTimeout(() => {
                window.location.href = '/roles';
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to create role', 'error');
            
            // Show specific validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showNotification(`${field}: ${data.errors[field].join(', ')}`, 'error');
                });
            }
        }
    } catch (error) {
        showNotification('Error creating role: ' + error.message, 'error');
    }
});

// Notification function
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
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}

// Client switching function (required by sidebar)
function switchClient(clientId) {
    showNotification('Switching to client...', 'info');
    
    setTimeout(() => {
        const select = document.querySelector('select[onchange="switchClient(this.value)"]');
        if (select) {
            select.value = clientId;
        }
        
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        
        showNotification(`Switched to ${clientNames[clientId]}`, 'success');
    }, 500);
}

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
</script>
@endpush

@endsection
