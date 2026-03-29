@extends('layouts.app')

@section('title', 'Create New User - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Create New User</h1>
            <p class="text-gray-600 mt-2">Add a new user to the system</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <!-- User Creation Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="userForm" class="space-y-8">
            <!-- Basic Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter first name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter last name">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="user@company.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+255 754 123 456">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter password">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Confirm password">
                    </div>
                </div>
            </div>

            <!-- Job Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Job Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID *</label>
                        <input type="text" name="employee_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="EMP-001">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" name="job_title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Software Developer">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                        <select name="department" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="Finance & Accounting">Finance & Accounting</option>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Operations">Operations</option>
                            <option value="Sales & Marketing">Sales & Marketing</option>
                            <option value="Administration">Administration</option>
                            <option value="Legal">Legal</option>
                            <option value="Compliance">Compliance</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="lead_hr_admin">Lead HR Admin</option>
                            <option value="hr_officer">HR Officer</option>
                            <option value="finance_payroll_officer">Finance/Payroll Officer</option>
                            <option value="line_manager">Line Manager</option>
                            <option value="employee">Employee</option>
                            <option value="external_auditor">External Auditor</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reports To</label>
                        <select name="reports_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Manager</option>
                            <!-- Will be populated dynamically -->
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type *</label>
                        <select name="employment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Type</option>
                            <option value="permanent">Permanent</option>
                            <option value="contract">Contract</option>
                            <option value="probation">Probation</option>
                            <option value="internship">Internship</option>
                            <option value="part_time">Part Time</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Status *</label>
                        <select name="is_active" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                        <input type="date" name="date_of_birth" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">National ID *</label>
                        <input type="text" name="national_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tanzanian National ID Number">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NSSF Number</label>
                        <input type="text" name="nssf_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="NSSF Number">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea name="address" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter complete address"></textarea>
                </div>
            </div>

            <!-- Tanzania Compliance -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tanzania Compliance Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Identification Number (TIN)</label>
                        <input type="text" name="tin_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="TIN Number">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Work Permit Number</label>
                        <input type="text" name="work_permit_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="For non-citizens only">
                    </div>
                </div>
                
                <div class="space-y-3 mt-6">
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="tanzanian_citizen" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1" checked>
                        <label for="tanzanian_citizen" class="text-sm text-gray-700">
                            Tanzanian Citizen
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="background_check_required" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="background_check_required" class="text-sm text-gray-700">
                            Background check required
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="medical_clearance_required" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="medical_clearance_required" class="text-sm text-gray-700">
                            Medical clearance required
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/users';

// Load managers for dropdown
async function loadManagers() {
    try {
        const response = await fetch('/api/users/managers');
        const data = await response.json();
        
        if (data.success) {
            const select = document.querySelector('select[name="reports_to"]');
            select.innerHTML = '<option value="">Select Manager</option>';
            
            data.managers.forEach(manager => {
                select.innerHTML += `<option value="${manager.id}">${manager.first_name} ${manager.last_name} - ${manager.job_title}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading managers:', error);
    }
}

// Form submission
document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const formDataObj = Object.fromEntries(formData);
    
    // Handle checkboxes
    formDataObj.tanzanian_citizen = formData.get('tanzanian_citizen') === 'on';
    formDataObj.background_check_required = formData.get('background_check_required') === 'on';
    formDataObj.medical_clearance_required = formData.get('medical_clearance_required') === 'on';
    
    // Show loading
    showNotification('Creating user...', 'info');
    
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
            showNotification('User created successfully!', 'success');
            
            // Redirect to users list after successful creation
            setTimeout(() => {
                window.location.href = '/users';
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to create user', 'error');
            
            // Show specific validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    showNotification(`${field}: ${data.errors[field].join(', ')}`, 'error');
                });
            }
        }
    } catch (error) {
        showNotification('Error creating user: ' + error.message, 'error');
    }
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadManagers();
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
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
