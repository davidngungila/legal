@extends('layouts.app')

@section('title', 'View Contract - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employment Contract</h1>
            <p class="text-gray-600 mt-2">Review your employment contract details</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Contract Overview -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Permanent Employment Agreement</h2>
                <p class="text-purple-200">Contract ID: EMP-2022-001 | Effective: 01 January 2022</p>
            </div>
            <div class="mt-4 md:mt-0">
                <p class="text-lg font-semibold">Status: Active</p>
                <p class="text-sm text-purple-200">Open-ended contract</p>
            </div>
        </div>
    </div>

    <!-- Contract Details -->
    <div class="space-y-6 mb-8">
        <!-- Employee Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Employee Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Employee ID:</span>
                        <span class="font-medium">EMP001</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Full Name:</span>
                        <span class="font-medium">John Doe</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Position:</span>
                        <span class="font-medium">Senior Developer</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Department:</span>
                        <span class="font-medium">IT Department</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">john.doe@company.com</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium">+255 712 345 678</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Address:</span>
                        <span class="font-medium">Dar es Salaam, Tanzania</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nationality:</span>
                        <span class="font-medium">Tanzanian</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contract Terms -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Contract Terms</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Contract Type:</span>
                        <span class="font-medium">Permanent</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Start Date:</span>
                        <span class="font-medium">01 January 2022</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">End Date:</span>
                        <span class="font-medium">Open-ended</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Probation Period:</span>
                        <span class="font-medium">3 months</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Notice Period:</span>
                        <span class="font-medium">30 days</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Trial Period:</span>
                        <span class="font-medium">3 months completed</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Contract Status:</span>
                        <span class="font-medium text-green-600">Active</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Renewal Date:</span>
                        <span class="font-medium">N/A (Permanent)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compensation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Compensation & Benefits</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Structure</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Basic Salary:</span>
                            <span class="font-medium">TZS 2,000,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Housing Allowance:</span>
                            <span class="font-medium">TZS 500,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transport Allowance:</span>
                            <span class="font-medium">TZS 200,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Communication Allowance:</span>
                            <span class="font-medium">TZS 50,000</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-900">Total Package:</span>
                                <span class="font-bold text-green-600">TZS 2,750,000</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Benefits</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            <span class="text-gray-700">NSSF Contributions</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            <span class="text-gray-700">Medical Insurance</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            <span class="text-gray-700">Annual Performance Bonus</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            <span class="text-gray-700">Training & Development</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i data-feather="check-circle" class="w-4 h-4 text-green-500"></i>
                            <span class="text-gray-700">Paid Annual Leave</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Working Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Working Hours & Conditions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Working Days:</span>
                        <span class="font-medium">Monday - Friday</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Working Hours:</span>
                        <span class="font-medium">8:00 AM - 5:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Lunch Break:</span>
                        <span class="font-medium">1:00 PM - 2:00 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Weekly Hours:</span>
                        <span class="font-medium">40 hours</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Overtime Rate:</span>
                        <span class="font-medium">1.5x normal rate</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Weekend Rate:</span>
                        <span class="font-medium">2x normal rate</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Holiday Rate:</span>
                        <span class="font-medium">2.5x normal rate</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Work Location:</span>
                        <span class="font-medium">Dar es Salaam Office</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Entitlement -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Leave Entitlement</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Annual Leave:</span>
                        <span class="font-medium">28 days per year</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sick Leave:</span>
                        <span class="font-medium">90 days per year</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Maternity Leave:</span>
                        <span class="font-medium">84 days (as per Labour Act)</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paternity Leave:</span>
                        <span class="font-medium">7 days</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Compassionate Leave:</span>
                        <span class="font-medium">7 days per instance</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Public Holidays:</span>
                        <span class="font-medium">As per Tanzania Calendar</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Study Leave:</span>
                        <span class="font-medium">As per company policy</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Leave Accrual:</span>
                        <span class="font-medium">Monthly accrual</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Terms & Conditions</h2>
            <div class="bg-gray-50 p-6 rounded-lg">
                <p class="text-sm text-gray-700 mb-4">
                    This employment contract is governed by the Tanzania Employment and Labour Relations Act, 2019. 
                    Both parties agree to comply with all statutory requirements including working hours, leave entitlements, 
                    and termination procedures as outlined in the Labour Act.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Employee Obligations</h3>
                        <ul class="text-sm text-gray-700 space-y-2">
                            <li>• Perform duties with due care and diligence</li>
                            <li>• Follow company policies and procedures</li>
                            <li>• Maintain confidentiality of company information</li>
                            <li>• Report any workplace safety concerns</li>
                            <li>• Participate in required training programs</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Employer Obligations</h3>
                        <ul class="text-sm text-gray-700 space-y-2">
                            <li>• Provide safe working environment</li>
                            <li>• Pay salary and benefits on time</li>
                            <li>• Comply with Tanzania Labour Laws</li>
                            <li>• Provide necessary training and equipment</li>
                            <li>• Maintain proper employment records</li>
                        </ul>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" checked class="mt-1" disabled>
                        <span class="text-sm text-gray-700">I have read and understood the terms and conditions</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" checked class="mt-1" disabled>
                        <span class="text-sm text-gray-700">I agree to comply with company policies and procedures</span>
                    </div>
                    <div class="flex items-start space-x-2">
                        <input type="checkbox" checked class="mt-1" disabled>
                        <span class="text-sm text-gray-700">I understand my rights and responsibilities under Tanzania Labour Law</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Contract Actions</h3>
                <p class="text-sm text-gray-600">Download, print, or request changes to your contract</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button onclick="downloadContract()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                    Download PDF
                </button>
                <button onclick="printContract()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                    Print
                </button>
                <button onclick="emailContract()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i data-feather="mail" class="w-4 h-4 inline mr-2"></i>
                    Email Copy
                </button>
                <button onclick="requestChanges()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>
                    Request Changes
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Download contract
function downloadContract() {
    showNotification('Preparing contract download...', 'info');
    
    setTimeout(() => {
        const contractData = `EMPLOYMENT CONTRACT

Employee Information:
- Name: John Doe
- ID: EMP001
- Position: Senior Developer
- Department: IT Department
- Contract Type: Permanent
- Start Date: 01 January 2022

Compensation:
- Basic Salary: TZS 2,000,000
- Housing Allowance: TZS 500,000
- Transport Allowance: TZS 200,000
- Communication Allowance: TZS 50,000
- Total Package: TZS 2,750,000

Terms & Conditions:
This employment contract is governed by the Tanzania Employment and Labour Relations Act, 2019.
Both parties agree to comply with all statutory requirements including working hours, 
leave entitlements, and termination procedures as outlined in the Labour Act.`;
        
        const link = document.createElement('a');
        link.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(contractData);
        link.download = 'employment_contract.pdf';
        link.click();
        
        showNotification('Contract downloaded successfully!', 'success');
    }, 1000);
}

// Print contract
function printContract() {
    window.print();
    showNotification('Print dialog opened', 'info');
}

// Email contract
function emailContract() {
    showNotification('Sending contract to email...', 'info');
    
    setTimeout(() => {
        showNotification('Contract sent to your email successfully!', 'success');
        // In a real app, this would send via API
    }, 1500);
}

// Request changes
function requestChanges() {
    showNotification('Opening change request form...', 'info');
    
    setTimeout(() => {
        showNotification('Change request submitted! Reference: CHANGE-2024-' + Math.floor(Math.random() * 1000), 'success');
        // In a real app, this would open a form or modal
    }, 1000);
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
