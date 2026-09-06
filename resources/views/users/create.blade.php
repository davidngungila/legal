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
        <form id="userForm" data-no-transition class="space-y-8">
            <!-- User Type Selection -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">User Type</h2>
                <div class="flex space-x-6">
                    <div class="flex items-center">
                        <input type="radio" id="user_type_client" name="user_type" value="client" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" checked>
                        <label for="user_type_client" class="ml-2 text-sm font-medium text-gray-700">Client's User</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="user_type_orvion" name="user_type" value="orvion" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <label for="user_type_orvion" class="ml-2 text-sm font-medium text-gray-700">Orvion User</label>
                    </div>
                </div>
            </div>

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

            <!-- Job Information (Client User Only) -->
            <div id="jobInformationSection">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Job Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID *</label>
                        <input type="text" name="employee_id" id="employee_id" required readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="EMP-001">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                        <select name="client_id" id="client_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Client</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                        <select name="department" id="departmentSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                        <select name="position" id="positionSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Position</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" id="roleSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                        </select>
                    </div>

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

            <!-- Permissions (Orvion User Only) -->
            <div id="permissionsSection" class="hidden">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Permissions</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Permissions *</label>
                    <select name="permissions[]" id="permissionsSelect" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 h-48">
                        <!-- Will be populated dynamically -->
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple permissions</p>
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
const API_BASE = '/users/data';

// Load roles and clients from API
async function loadRolesAndClients() {
    try {
        const response = await fetch(`${API_BASE}/roles-permissions`);
        const data = await response.json();

        if (data.success) {
            // Load roles
            if (data.roles) {
                const roleSelect = document.getElementById('roleSelect');
                if (roleSelect) {
                    roleSelect.innerHTML = '<option value="">Select Role</option>';
                    data.roles.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.name;
                        option.textContent = role.display_name || role.name;
                        roleSelect.appendChild(option);
                    });
                }
            }

            // Load clients
            if (data.clients) {
                const clientSelect = document.getElementById('client_id');
                if (clientSelect) {
                    clientSelect.innerHTML = '<option value="">Select Client</option>';
                    data.clients.forEach(client => {
                        const option = document.createElement('option');
                        option.value = client.id;
                        option.textContent = client.name;
                        clientSelect.appendChild(option);
                    });
                }
            }

            // Load permissions
            if (data.permissions) {
                const permissionsSelect = document.getElementById('permissionsSelect');
                if (permissionsSelect) {
                    permissionsSelect.innerHTML = '';
                    data.permissions.forEach(permission => {
                        const option = document.createElement('option');
                        option.value = permission.name;
                        option.textContent = permission.display_name || permission.name;
                        permissionsSelect.appendChild(option);
                    });
                }
            }
        }
    } catch (error) {
        console.error('Error loading roles/clients:', error);
        showNotification('Failed to load roles/clients', 'error');
    }
}

// Load departments by client
async function loadDepartmentsByClient(clientId) {
    try {
        const response = await fetch(`${API_BASE}/departments/${clientId}`);
        const data = await response.json();
        
        const departmentSelect = document.getElementById('departmentSelect');
        const positionSelect = document.getElementById('positionSelect');
        
        if (departmentSelect) {
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            positionSelect.innerHTML = '<option value="">Select Position</option>';
            
            if (data.success && data.departments) {
                data.departments.forEach(department => {
                    const option = document.createElement('option');
                    option.value = department.name;
                    option.dataset.departmentId = department.id;
                    option.textContent = department.name;
                    departmentSelect.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error loading departments:', error);
        showNotification('Failed to load departments', 'error');
    }
}

// Load positions by department
async function loadPositionsByDepartment(departmentId) {
    try {
        const response = await fetch(`${API_BASE}/positions/${departmentId}`);
        const data = await response.json();
        
        const positionSelect = document.getElementById('positionSelect');
        
        if (positionSelect) {
            positionSelect.innerHTML = '<option value="">Select Position</option>';
            
            if (data.success && data.positions) {
                data.positions.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.title;
                    option.textContent = position.title;
                    positionSelect.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error loading positions:', error);
        showNotification('Failed to load positions', 'error');
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

    // Handle permissions array
    const permissionsSelect = document.getElementById('permissionsSelect');
    if (permissionsSelect) {
        const selectedPermissions = Array.from(permissionsSelect.selectedOptions).map(option => option.value);
        formDataObj.permissions = selectedPermissions;
    }

    // Handle user_type
    const userType = formData.get('user_type') || 'client';
    formDataObj.user_type = userType;

    // Remove fields that are not required for the selected user type
    if (userType === 'orvion') {
        delete formDataObj.employee_id;
        delete formDataObj.client_id;
        delete formDataObj.department;
        delete formDataObj.position;
        delete formDataObj.role;
        delete formDataObj.reports_to;
        delete formDataObj.employment_type;
        delete formDataObj.is_active;
    }

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

// Load next employee ID
    async function loadNextEmployeeId() {
        try {
            const response = await fetch(`${API_BASE}/next-employee-id`);
            const data = await response.json();
            
            if (data.success && data.employee_id) {
                document.getElementById('employee_id').value = data.employee_id;
            }
        } catch (error) {
            console.error('Error loading next employee ID:', error);
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Load roles, clients, and next employee ID first
        loadRolesAndClients();
        loadNextEmployeeId();

        // User type radio button change listener
        const userTypeRadios = document.querySelectorAll('input[name="user_type"]');
        userTypeRadios.forEach(radio => {
            radio.addEventListener('change', function(e) {
                toggleUserTypeFields(e.target.value);
            });
        });

        // Client change listener
        document.getElementById('client_id').addEventListener('change', function(e) {
            const clientId = e.target.value;
            if (clientId) {
                loadDepartmentsByClient(clientId);
            } else {
                document.getElementById('departmentSelect').innerHTML = '<option value="">Select Department</option>';
                document.getElementById('positionSelect').innerHTML = '<option value="">Select Position</option>';
            }
        });

        // Department change listener
        document.getElementById('departmentSelect').addEventListener('change', function(e) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const departmentId = selectedOption.dataset.departmentId;
            if (departmentId) {
                loadPositionsByDepartment(departmentId);
            } else {
                document.getElementById('positionSelect').innerHTML = '<option value="">Select Position</option>';
            }
        });

        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

// Toggle fields based on user type
function toggleUserTypeFields(userType) {
    const jobInfoSection = document.getElementById('jobInformationSection');
    const permissionsSection = document.getElementById('permissionsSection');

    if (userType === 'orvion') {
        // Hide job information, show permissions
        jobInfoSection.classList.add('hidden');
        permissionsSection.classList.remove('hidden');

        // Remove required attributes from job info fields
        const jobInfoFields = jobInfoSection.querySelectorAll('[required]');
        jobInfoFields.forEach(field => field.removeAttribute('required'));
    } else {
        // Show job information, hide permissions
        jobInfoSection.classList.remove('hidden');
        permissionsSection.classList.add('hidden');

        // Add required attributes back to job info fields
        const employeeId = document.getElementById('employee_id');
        const clientId = document.getElementById('client_id');
        const departmentSelect = document.getElementById('departmentSelect');
        const positionSelect = document.getElementById('positionSelect');
        const roleSelect = document.getElementById('roleSelect');
        const employmentType = document.querySelector('select[name="employment_type"]');
        const isActive = document.querySelector('select[name="is_active"]');

        if (employeeId) employeeId.setAttribute('required', 'required');
        if (clientId) clientId.setAttribute('required', 'required');
        if (departmentSelect) departmentSelect.setAttribute('required', 'required');
        if (positionSelect) positionSelect.setAttribute('required', 'required');
        if (roleSelect) roleSelect.setAttribute('required', 'required');
        if (employmentType) employmentType.setAttribute('required', 'required');
        if (isActive) isActive.setAttribute('required', 'required');
    }
}

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
