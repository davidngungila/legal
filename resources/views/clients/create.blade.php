@extends('layouts.app')

@section('title', 'Add New Client - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Add New Client</h1>
            <p class="text-gray-600 mt-2">Register a new client organization in the system</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Client Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="clientForm" class="space-y-8">
            <input type="hidden" id="clientId" name="client_id">
            
            <!-- Basic Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client Name *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter client company name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="client@company.com">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                        <input type="tel" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+255 22 123 4567">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Industry *</label>
                        <select name="industry" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input type="url" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://www.company.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Count *</label>
                        <input type="number" name="employee_count" required min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Number of employees">
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Address Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Physical Address *</label>
                    <textarea name="address" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter complete physical address"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                        <input type="text" name="city" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Dar es Salaam">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <input type="text" name="country" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tanzania">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="P.O. Box 1234">
                    </div>
                </div>
            </div>

            <!-- Contact Person Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Primary Contact Person</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name *</label>
                        <input type="text" name="contact_person" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="John Doe">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                        <input type="text" name="contact_title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="HR Director">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email *</label>
                        <input type="email" name="contact_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="john.doe@company.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone *</label>
                        <input type="tel" name="contact_phone" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="+255 754 123 456">
                    </div>
                </div>
            </div>

            <!-- Subscription & Status -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Subscription & Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subscription Plan *</label>
                        <select name="subscription_plan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Plan</option>
                            <option value="basic">Basic - TZS 50,000/month</option>
                            <option value="premium">Premium - TZS 150,000/month</option>
                            <option value="enterprise">Enterprise - TZS 500,000/month</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Choose the appropriate subscription plan for the client</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Status *</label>
                        <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Set the initial status of the client account</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Additional Information</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add any additional notes about this client..."></textarea>
                    <p class="mt-2 text-sm text-gray-500">Include any special requirements, agreements, or important information</p>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Terms and Conditions</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="terms_accepted" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="terms_accepted" class="text-sm text-gray-700">
                            I confirm that I have the authority to register this client organization and that all provided information is accurate and complete.
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="compliance_accepted" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="compliance_accepted" class="text-sm text-gray-700">
                            I acknowledge that the client organization agrees to comply with Tanzania Labour Laws and LegalHR terms of service.
                        </label>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" name="data_protection_accepted" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <label for="data_protection_accepted" class="text-sm text-gray-700">
                            I understand and agree to the data protection and privacy policies regarding client information.
                        </label>
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
                    Create Client
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/clients';

// Form submission
document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const clientId = formData.get('client_id');
    const isEdit = clientId && clientId !== '';
    
    const url = isEdit ? `${API_BASE}/${clientId}` : API_BASE;
    const method = isEdit ? 'PUT' : 'POST';
    
    // Show loading
    showNotification('Creating client...', 'info');
    
    // Debug: Log form data
    const formDataObj = Object.fromEntries(formData);
    console.log('Form data being sent:', formDataObj);
    console.log('Form data JSON:', JSON.stringify(formDataObj, null, 2));
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formDataObj)
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(`Client ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
            
            // Redirect to clients list after successful creation
            setTimeout(() => {
                window.location.href = '/clients';
            }, 1500);
        } else {
            console.log('Validation errors:', data.errors);
            showNotification(data.message || 'Failed to create client', 'error');
            
            // Show specific validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    console.log(`${field}: ${data.errors[field].join(', ')}`);
                    showNotification(`${field}: ${data.errors[field].join(', ')}`, 'error');
                });
            }
        }
    } catch (error) {
        console.error('Error creating client:', error);
        showNotification('Error creating client: ' + error.message, 'error');
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
