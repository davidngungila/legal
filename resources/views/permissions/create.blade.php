@extends('layouts.app')

@section('title', 'Create New Permission - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Create New Permission</h1>
            <p class="text-gray-600 mt-2">Add a new permission to the system</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('permissions.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Permissions
            </a>
        </div>
    </div>

    <!-- Permission Creation Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="permissionForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., users.create">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
                    <input type="text" name="display_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Create User">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Describe what this permission allows users to do..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permission Group *</label>
                <select name="group" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Group</option>
                    <option value="users">Users Management</option>
                    <option value="roles">Roles Management</option>
                    <option value="permissions">Permissions Management</option>
                    <option value="clients">Clients Management</option>
                    <option value="employees">Employees Management</option>
                    <option value="attendance">Attendance & Timesheet</option>
                    <option value="payroll">Payroll Management</option>
                    <option value="performance">Performance Management</option>
                    <option value="training">Training & Development</option>
                    <option value="recruitment">Recruitment & Hiring</option>
                    <option value="compliance">Compliance Management</option>
                    <option value="reports">Reports & Analytics</option>
                    <option value="settings">System Settings</option>
                    <option value="selfservice">Employee Self Service</option>
                </select>
            </div>
            
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permission Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                        <input type="text" name="module" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., users, roles, clients">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Action</option>
                            <option value="create">Create</option>
                            <option value="read">Read/View</option>
                            <option value="update">Update/Edit</option>
                            <option value="delete">Delete</option>
                            <option value="list">List</option>
                            <option value="export">Export</option>
                            <option value="import">Import</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="manage">Manage</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tanzania Compliance</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="is_critical" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="is_critical" class="text-sm text-gray-700">
                            Mark as critical permission (required for Tanzania Labour Law compliance)
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="requires_approval" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="requires_approval" class="text-sm text-gray-700">
                            Requires supervisor approval for sensitive operations
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="audit_trail" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1" checked>
                        <label for="audit_trail" class="text-sm text-gray-700">
                            Enable audit trail for this permission
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('permissions.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Create Permission
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/permissions';

// Form submission
document.getElementById('permissionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formDataObj = Object.fromEntries(formData);
    
    // Show loading
    showNotification('Creating permission...', 'info');
    
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
            showNotification('Permission created successfully!', 'success');
            
            // Redirect to permissions list after successful creation
            setTimeout(() => {
                window.location.href = '/permissions';
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to create permission', 'error');
            
            // Show specific validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showNotification(`${field}: ${data.errors[field].join(', ')}`, 'error');
                });
            }
        }
    } catch (error) {
        showNotification('Error creating permission: ' + error.message, 'error');
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
