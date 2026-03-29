@extends('layouts.app')

@section('title', 'Apply for Leave - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Apply for Leave</h1>
            <p class="text-gray-600 mt-2">Request annual, sick, or emergency leave</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Leave Balance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="sun" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">18</p>
            <p class="text-sm text-gray-500">Annual Leave Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="heart" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">75</p>
            <p class="text-sm text-gray-500">Sick Leave Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="home" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">84</p>
            <p class="text-sm text-gray-500">Maternity/Paternity Days</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">Available</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">7</p>
            <p class="text-sm text-gray-500">Compassionate Days</p>
        </div>
    </div>

    <!-- Leave Application Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Leave Application Details</h2>
        
        <form id="leaveForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type *</label>
                    <select name="leave_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Leave Type</option>
                        <option value="annual">Annual Leave</option>
                        <option value="sick">Sick Leave</option>
                        <option value="emergency">Emergency Leave</option>
                        <option value="maternity">Maternity Leave</option>
                        <option value="paternity">Paternity Leave</option>
                        <option value="compassionate">Compassionate Leave</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input type="date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                    <input type="date" name="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Leave *</label>
                <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please provide detailed reason for your leave request..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Information</label>
                <textarea name="additional_info" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional information that may be relevant to your leave request..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact (if applicable)</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="emergency_name" placeholder="Contact Name" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="tel" name="emergency_phone" placeholder="Phone Number" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="text" name="emergency_relation" placeholder="Relationship" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="acknowledgement" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="acknowledgement" class="text-sm text-gray-700">
                    I acknowledge that I have read and understood the company's leave policy and Tanzania Labour Act requirements.
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('selfservice') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Leave Request
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Leave Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Leave Requests</h2>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All History</button>
        </div>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Approved</span>
                    <span class="text-xs text-gray-500">5 days ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Annual Leave</p>
                <p class="text-sm text-gray-600 mb-2">15 Nov 2024 - 19 Nov 2024 (5 days)</p>
                <p class="text-xs text-gray-500">Approved by: Sarah Williams (HR Manager)</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pending</span>
                    <span class="text-xs text-gray-500">2 days ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Sick Leave</p>
                <p class="text-sm text-gray-600 mb-2">28 Nov 2024 - 29 Nov 2024 (2 days)</p>
                <p class="text-xs text-gray-500">Awaiting approval from: Department Manager</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejected</span>
                    <span class="text-xs text-gray-500">2 weeks ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Annual Leave</p>
                <p class="text-sm text-gray-600 mb-2">10 Dec 2024 - 20 Dec 2024 (10 days)</p>
                <p class="text-xs text-gray-500">Rejected: Insufficient leave balance</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form submission
document.getElementById('leaveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    showNotification('Submitting leave request...', 'info');
    
    // Simulate API call
    setTimeout(() => {
        showNotification('Leave request submitted successfully! Reference: LEAVE-2024-' + Math.floor(Math.random() * 1000), 'success');
        
        // Reset form
        this.reset();
        
        // Update leave balance (simulation)
        updateLeaveBalance();
    }, 1500);
});

// Update leave balance (simulation)
function updateLeaveBalance() {
    // This would normally fetch from API
    const leaveBalances = document.querySelectorAll('.text-2xl');
    if (leaveBalances.length > 0) {
        const currentValue = parseInt(leaveBalances[0].textContent);
        const newValue = Math.max(0, currentValue - 1);
        leaveBalances[0].textContent = newValue;
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

// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endpush

@endsection
