@extends('layouts.app')

@section('title', 'File Complaint - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">File Complaint</h1>
            <p class="text-gray-600 mt-2">Submit grievances or concerns</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 mb-8">
        <div class="flex items-start space-x-3">
            <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600 mt-1"></i>
            <div>
                <h2 class="text-lg font-semibold text-orange-900 mb-2">Important Notice</h2>
                <p class="text-orange-800 text-sm">
                    All complaints are handled confidentially and in accordance with Tanzania Labour Act. 
                    Your submission will be legally tracked and processed according to company policies and legal requirements. 
                    False or malicious complaints may result in disciplinary action.
                </p>
            </div>
        </div>
    </div>

    <!-- Complaint Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Pending</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">2</p>
            <p class="text-sm text-gray-500">Under Review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Resolved</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">5</p>
            <p class="text-sm text-gray-500">Successfully Resolved</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="activity" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">In Progress</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">1</p>
            <p class="text-sm text-gray-500">Being Investigated</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="bar-chart" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">8</p>
            <p class="text-sm text-gray-500">All Complaints</p>
        </div>
    </div>

    <!-- Complaint Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Complaint Details</h2>
        
        <form id="complaintForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Complaint Type *</label>
                    <select name="complaint_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Complaint Type</option>
                        <option value="workplace">Workplace Issue</option>
                        <option value="harassment">Harassment</option>
                        <option value="discrimination">Discrimination</option>
                        <option value="safety">Safety Concern</option>
                        <option value="policy">Policy Violation</option>
                        <option value="salary">Salary Issue</option>
                        <option value="working">Working Conditions</option>
                        <option value="management">Management Issue</option>
                        <option value="other">Other</option>
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
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                <input type="text" name="subject" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Brief description of the issue...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Description *</label>
                <textarea name="description" rows="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please provide detailed description of your complaint, including specific dates, times, locations, and any witnesses..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desired Resolution *</label>
                <textarea name="resolution" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="What would you like to see as the outcome of this complaint?"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Incident</label>
                    <input type="date" name="incident_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time of Incident</label>
                    <input type="time" name="incident_time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location of Incident</label>
                <input type="text" name="incident_location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Where did the incident occur?">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Witnesses (if any)</label>
                <textarea name="witnesses" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Names and contact information of any witnesses..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Supporting Documents</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <i data-feather="upload" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">PDF, JPG, PNG up to 10MB</p>
                    <input type="file" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Contact Method</label>
                    <select name="contact_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                        <option value="inperson">In Person</option>
                        <option value="anonymous">Anonymous</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Availability for Follow-up</label>
                    <input type="text" name="availability" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Best times to contact you...">
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="acknowledgement" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="acknowledgement" class="text-sm text-gray-700">
                    I confirm that the information provided is accurate and true to the best of my knowledge. I understand that false statements may result in disciplinary action.
                </label>
            </div>
            
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="confidentiality" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="confidentiality" class="text-sm text-gray-700">
                    I request that this complaint be handled confidentially where possible, in accordance with company policy and Tanzania Labour Law.
                </label>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('selfservice') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Complaints -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Complaints</h2>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All History</button>
        </div>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Resolved</span>
                    <span class="text-xs text-gray-500">2 weeks ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Salary Discrepancy</p>
                <p class="text-sm text-gray-600 mb-2">Incorrect overtime calculation for October 2024</p>
                <p class="text-xs text-gray-500">Reference: COMP-2024-015 | Resolved: Salary corrected and back-paid</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">In Progress</span>
                    <span class="text-xs text-gray-500">1 week ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Working Conditions</p>
                <p class="text-sm text-gray-600 mb-2">Air conditioning issues in office area</p>
                <p class="text-xs text-gray-500">Reference: COMP-2024-016 | Status: Maintenance team contacted</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Pending</span>
                    <span class="text-xs text-gray-500">3 days ago</span>
                </div>
                <p class="font-medium text-gray-900 mb-1">Policy Clarification</p>
                <p class="text-sm text-gray-600 mb-2">Questions about remote work policy</p>
                <p class="text-xs text-gray-500">Reference: COMP-2024-017 | Status: Awaiting HR response</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form submission
document.getElementById('complaintForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Show loading
    showNotification('Submitting complaint...', 'info');
    
    // Simulate API call
    setTimeout(() => {
        const reference = 'COMP-2024-' + Math.floor(Math.random() * 1000);
        showNotification(`Complaint submitted successfully! Reference: ${reference}`, 'success');
        
        // Reset form
        this.reset();
        
        // Update statistics (simulation)
        updateComplaintStats();
    }, 1500);
});

// Update complaint statistics (simulation)
function updateComplaintStats() {
    // This would normally fetch from API
    const pendingCount = document.querySelector('.text-blue-600').parentElement.parentElement.querySelector('.text-2xl');
    if (pendingCount) {
        const currentValue = parseInt(pendingCount.textContent);
        pendingCount.textContent = currentValue + 1;
    }
    
    const totalCount = document.querySelector('.text-purple-600').parentElement.parentElement.querySelector('.text-2xl');
    if (totalCount) {
        const currentValue = parseInt(totalCount.textContent);
        totalCount.textContent = currentValue + 1;
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
