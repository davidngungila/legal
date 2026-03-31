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
        <button class="px-6 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
            <i data-feather="x" class="w-4 h-4 mr-2"></i>
            Cancel
        </button>
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
            <i data-feather="save" class="w-4 h-4 mr-2"></i>
            Save Organization Details
        </button>
    </div>
</div>

@push('scripts')
<script>
// Client-specific organization data
const organizationData = {
    '1': { // ABC Manufacturing Ltd
        name: 'ABC Manufacturing Ltd',
        registration: 'REG-2021-001234',
        tin: '101-234-567',
        nssf: 'NSSF-EMP-045678',
        wcf: 'WCF-2023-001234',
        industry: 'Manufacturing',
        address: 'Plot 123, Industrial Area, Dar es Salaam, Tanzania',
        phone: '+255 22 123 4567',
        email: 'hr@abcmanufacturing.co.tz'
    },
    '2': { // XYZ Construction Co
        name: 'XYZ Construction Co',
        registration: 'REG-2018-005678',
        tin: '102-345-678',
        nssf: 'NSSF-EMP-034567',
        wcf: 'WCF-2020-005678',
        industry: 'Construction',
        address: 'Plot 456, Kijitonyama, Dar es Salaam, Tanzania',
        phone: '+255 22 987 6543',
        email: 'hr@xyzconstruction.co.tz'
    },
    '3': { // Tanzania Mining Corp
        name: 'Tanzania Mining Corp',
        registration: 'REG-2015-009876',
        tin: '103-456-789',
        nssf: 'NSSF-EMP-067890',
        wcf: 'WCF-2019-009876',
        industry: 'Mining',
        address: 'Plot 789, Mining Area, Mbeya, Tanzania',
        phone: '+255 25 123 9876',
        email: 'hr@tanzaniamining.co.tz'
    },
    '4': { // East Africa Logistics
        name: 'East Africa Logistics',
        registration: 'REG-2019-012345',
        tin: '104-567-890',
        nssf: 'NSSF-EMP-078901',
        wcf: 'WCF-2021-012345',
        industry: 'Logistics',
        address: 'Plot 234, Logistics Hub, Dar es Salaam, Tanzania',
        phone: '+255 22 456 7890',
        email: 'hr@eastafricalogistics.co.tz'
    }
};

// Update organization data when client changes
function updateOrganizationData(clientId) {
    const data = organizationData[clientId];
    if (!data) return;
    
    console.log('Updating organization data for client:', clientId);
    
    // Update all form fields with smooth transitions
    const updates = {
        '[data-org-name]': data.name,
        '[data-org-registration]': data.registration,
        '[data-org-tin]': data.tin,
        '[data-org-nssf]': data.nssf,
        '[data-org-wcf]': data.wcf,
        '[data-org-industry]': data.industry,
        '[data-org-address]': data.address,
        '[data-org-phone]': data.phone,
        '[data-org-email]': data.email
    };
    
    Object.entries(updates).forEach(([selector, value]) => {
        const element = document.querySelector(selector);
        if (element) {
            // Add transition effect
            element.style.transition = 'all 0.3s ease';
            element.style.transform = 'scale(0.98)';
            element.style.opacity = '0.7';
            
            setTimeout(() => {
                element.value = value;
                element.style.transform = 'scale(1)';
                element.style.opacity = '1';
            }, 150);
        }
    });
    
    // Show notification
    if (typeof showNotification === 'function') {
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        showNotification(`Organization data updated for ${clientNames[clientId]}`, 'success');
    }
}

// Listen for client changes
document.addEventListener('DOMContentLoaded', function() {
    // Initial data load
    const currentClient = getCurrentClient();
    if (currentClient && currentClient.id) {
        updateOrganizationData(currentClient.id);
    }
    
    // Listen for client change events
    document.addEventListener('clientChanged', function(event) {
        console.log('Client changed in organization setup:', event.detail);
        updateOrganizationData(event.detail.clientId);
    });
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

// Fallback getCurrentClient function if not available globally
if (typeof getCurrentClient === 'undefined') {
    function getCurrentClient() {
        const clientId = sessionStorage.getItem('selectedClientId') || '1';
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        return {
            id: clientId,
            name: clientNames[clientId]
        };
    }
}
</script>
@endpush
@endsection
