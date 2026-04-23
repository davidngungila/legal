@extends('layouts.app')

@section('title', 'Edit Client - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Client</h1>
            <p class="text-gray-600 mt-2">Update client organization information</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Client Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="clientForm" class="space-y-8">
            <input type="hidden" id="clientId" name="client_id">
            
            <!-- Basic Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                        <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter client company name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="client@company.com">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                        <input type="tel" id="phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+255 22 123 4567">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Industry *</label>
                        <select id="industry" name="industry" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Industry</option>
                            <option value="Manufacturing">Manufacturing</option>
                            <option value="Construction">Construction</option>
                            <option value="Mining">Mining</option>
                            <option value="Logistics">Logistics</option>
                            <option value="Tourism">Tourism</option>
                            <option value="Government">Government</option>
                            <option value="Textile">Textile</option>
                            <option value="Agriculture">Agriculture</option>
                            <option value="Technology">Technology</option>
                            <option value="Construction Materials">Construction Materials</option>
                            <option value="Financial Services">Financial Services</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Education">Education</option>
                            <option value="Retail">Retail</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" id="website" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://www.company.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Count *</label>
                        <input type="number" id="employee_count" name="employee_count" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Number of employees">
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Address Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Physical Address *</label>
                    <textarea id="address" name="address" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter complete physical address"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" id="city" name="city" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Dar es Salaam">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <input type="text" id="country" name="country" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tanzania">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="P.O. Box 1234">
                    </div>
                </div>
            </div>

            <!-- Contact Person Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Primary Contact Person</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name *</label>
                        <input type="text" id="contact_person" name="contact_person" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="John Doe">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" id="contact_title" name="contact_title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="HR Director">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email *</label>
                        <input type="email" id="contact_email" name="contact_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="john.doe@company.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone *</label>
                        <input type="tel" id="contact_phone" name="contact_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+255 754 123 456">
                    </div>
                </div>
            </div>

            <!-- Subscription & Status -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Subscription & Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subscription Plan *</label>
                        <select id="subscription_plan" name="subscription_plan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Plan</option>
                            <option value="basic">Basic - TZS 50,000/month</option>
                            <option value="premium">Premium - TZS 150,000/month</option>
                            <option value="enterprise">Enterprise - TZS 500,000/month</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Choose the appropriate subscription plan for the client</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Status *</label>
                        <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Set the status of the client account</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Additional Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add any additional notes about this client..."></textarea>
                    <p class="mt-2 text-sm text-gray-500">Include any special requirements, agreements, or important information</p>
                </div>
            </div>

            <!-- Client Statistics -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Created Date</p>
                        <p class="text-lg font-semibold text-gray-900" id="createdDate">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="text-lg font-semibold text-gray-900" id="updatedDate">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-lg font-semibold text-gray-900" id="totalUsers">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Users</p>
                        <p class="text-lg font-semibold text-gray-900" id="activeUsers">-</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('clients.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Update Client
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/clients';

// Get client ID from URL
const urlParams = new URLSearchParams(window.location.search);
const clientId = urlParams.get('id');

// Load client data
async function loadClientData() {
    if (!clientId) {
        showNotification('Client ID not provided', 'error');
        setTimeout(() => {
            window.location.href = '/clients';
        }, 1500);
        return;
    }

    try {
        showNotification('Loading client data...', 'info');
        
        const response = await fetch(`${API_BASE}/${clientId}`);
        const data = await response.json();
        
        if (data.success) {
            const client = data.client;
            
            // Fill form fields
            document.getElementById('clientId').value = client.id;
            document.getElementById('name').value = client.name || '';
            document.getElementById('email').value = client.email || '';
            document.getElementById('phone').value = client.phone || '';
            document.getElementById('industry').value = client.industry || '';
            document.getElementById('website').value = client.website || '';
            document.getElementById('employee_count').value = client.employee_count || '';
            document.getElementById('address').value = client.address || '';
            document.getElementById('city').value = client.city || '';
            document.getElementById('country').value = client.country || '';
            document.getElementById('postal_code').value = client.postal_code || '';
            document.getElementById('contact_person').value = client.contact_person || '';
            document.getElementById('contact_title').value = client.contact_title || '';
            document.getElementById('contact_email').value = client.contact_email || '';
            document.getElementById('contact_phone').value = client.contact_phone || '';
            document.getElementById('subscription_plan').value = client.subscription_plan || '';
            document.getElementById('status').value = client.status || '';
            document.getElementById('notes').value = client.notes || '';
            
            // Update statistics
            document.getElementById('createdDate').textContent = new Date(client.created_at).toLocaleDateString();
            document.getElementById('updatedDate').textContent = new Date(client.updated_at).toLocaleDateString();
            document.getElementById('totalUsers').textContent = Math.floor(Math.random() * 50) + 10; // Mock data
            document.getElementById('activeUsers').textContent = Math.floor(Math.random() * 40) + 5; // Mock data
            
        } else {
            showNotification('Failed to load client data', 'error');
            setTimeout(() => {
                window.location.href = '/clients';
            }, 1500);
        }
    } catch (error) {
        showNotification('Error loading client data: ' + error.message, 'error');
        setTimeout(() => {
            window.location.href = '/clients';
        }, 1500);
    }
}

// Form submission
document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const clientId = formData.get('client_id');
    
    if (!clientId) {
        showNotification('Client ID not found', 'error');
        return;
    }
    
    // Show loading
    showNotification('Updating client...', 'info');
    
    try {
        const response = await fetch(`${API_BASE}/${clientId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Client updated successfully!', 'success');
            
            // Redirect to clients list after successful update
            setTimeout(() => {
                window.location.href = '/clients';
            }, 1500);
        } else {
            showNotification(data.message || 'Failed to update client', 'error');
        }
    } catch (error) {
        showNotification('Error updating client: ' + error.message, 'error');
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

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadClientData();
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

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
