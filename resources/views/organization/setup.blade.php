@extends('layouts.app')

@section('title', 'Organization Setup - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Organization Setup</h1>
            <p class="text-gray-600 mt-2">Configure your organization details and company information</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                Reset
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Company Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-feather="building" class="w-5 h-5 mr-2 text-indigo-600"></i>
                Company Information
            </h2>
            <p class="text-sm text-gray-600 mt-1">Basic company details and registration information</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" data-org-name value="ABC Manufacturing Ltd" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registration Number</label>
                    <input type="text" data-org-registration value="REG-2021-001234" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">TIN Number</label>
                    <input type="text" data-org-tin value="101-234-567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NSSF Employer Number</label>
                    <input type="text" data-org-nssf value="NSSF-EMP-045678" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">WCF Policy Number</label>
                    <input type="text" data-org-wcf value="WCF-2023-001234" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sector Classification</label>
                    <select data-org-industry class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Manufacturing</option>
                        <option>Services</option>
                        <option>Construction</option>
                        <option>Retail</option>
                        <option>Technology</option>
                        <option>Healthcare</option>
                        <option>Education</option>
                        <option>Government</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Address</label>
                    <textarea rows="3" data-org-address class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">Plot 123, Industrial Area, Dar es Salaam, Tanzania</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" data-org-phone value="+255 22 123 4567" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" data-org-email value="hr@abcmanufacturing.co.tz" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Legal & Compliance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-feather="shield" class="w-5 h-5 mr-2 text-green-600"></i>
                Legal & Compliance
            </h2>
            <p class="text-sm text-gray-600 mt-1">Legal documents and compliance information</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Business License Number</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter license number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                    <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">VAT Registration</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="VAT registration number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trade License</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Trade license number">
                </div>
            </div>
        </div>
    </div>

    <!-- Banking Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-feather="credit-card" class="w-5 h-5 mr-2 text-blue-600"></i>
                Banking Information
            </h2>
            <p class="text-sm text-gray-600 mt-1">Bank account details for payroll and transactions</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter bank name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter account number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter branch name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Swift Code</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter swift code">
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-4">
        <button class="px-6 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center" onclick="resetForm()">
            <i data-feather="x" class="w-4 h-4 mr-2"></i>
            Cancel
        </button>
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center" onclick="saveOrganization()">
            <i data-feather="save" class="w-4 h-4 mr-2"></i>
            Save Organization Details
        </button>
    </div>
</div>

@push('scripts')
<script>
// Enhanced save function with better feedback
function saveOrganization() {
    const form = document.getElementById('organizationForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Show loading state
    const saveBtn = event.target;
    const originalContent = saveBtn.innerHTML;
    saveBtn.innerHTML = '<div class="flex items-center justify-center"><i data-feather="loader" class="w-5 h-5 mr-2 animate-spin"></i> Saving...</div>';
    saveBtn.disabled = true;
    
    // Add saving animation
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    fetch('{{ route("organization.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Organization information updated successfully!', 'success');
            // Update client display if name changed
            const clientDisplay = document.querySelector('[data-client-display]');
            if (clientDisplay && data.name) {
                clientDisplay.textContent = data.name;
            }
            
            // Add success animation to form
            form.classList.add('animate-pulse');
            setTimeout(() => form.classList.remove('animate-pulse'), 1000);
        } else {
            showNotification(result.message || 'Failed to update organization information', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while saving', 'error');
    })
    .finally(() => {
        // Restore button
        saveBtn.innerHTML = originalContent;
        saveBtn.disabled = false;
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
}

// Enhanced reset function with confirmation
function resetForm() {
    if (confirm('Are you sure you want to reset all unsaved changes? This action cannot be undone.')) {
        const form = document.getElementById('organizationForm');
        
        // Add reset animation
        form.style.transform = 'scale(0.98)';
        form.style.opacity = '0.7';
        
        setTimeout(() => {
            form.reset();
            form.style.transform = 'scale(1)';
            form.style.opacity = '1';
            showNotification('Form has been reset to original values', 'info');
        }, 200);
    }
}

// Export data function
function exportData() {
    showNotification('Preparing data export...', 'info');
    
    // Simulate export process
    setTimeout(() => {
        showNotification('Organization data exported successfully!', 'success');
    }, 2000);
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;
    
    const styles = {
        success: 'bg-gradient-to-r from-green-500 to-emerald-600 text-white border-l-4 border-green-400',
        error: 'bg-gradient-to-r from-red-500 to-pink-600 text-white border-l-4 border-red-400',
        warning: 'bg-gradient-to-r from-yellow-500 to-orange-600 text-white border-l-4 border-yellow-400',
        info: 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-l-4 border-blue-400'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    notification.className += ' ' + styles[type] || styles.info;
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <i data-feather="${icons[type] || 'info'}" class="w-6 h-6"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold">${message}</p>
                <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Animate in with slide effect
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Auto remove with slide effect
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        notification.classList.add('opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 500);
    }, 4000);
}

// Add input validation feedback
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('border-red-500', 'bg-red-50');
                this.classList.remove('border-green-500', 'bg-green-50');
            } else {
                this.classList.add('border-green-500', 'bg-green-50');
                this.classList.remove('border-red-500', 'bg-red-50');
            }
        });
    });
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush

@endsection
