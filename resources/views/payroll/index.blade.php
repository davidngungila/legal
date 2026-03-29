@extends('layouts.app')

@section('title', 'Payroll Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Payroll Management</h1>
            <p class="text-gray-600 mt-2">Process payroll with full Tanzania statutory compliance</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Reports
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-feather="credit-card" class="w-4 h-4 inline mr-2"></i>
                Process Payroll
            </button>
        </div>
    </div>

    <!-- Compliance Status -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold mb-2">Payroll Compliance Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-green-100 text-sm">PAYE Filing</p>
                        <p class="font-bold">Up to Date</p>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm">NSSF Contributions</p>
                        <p class="font-bold">Compliant</p>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm">WCF Payments</p>
                        <p class="font-bold">Current</p>
                    </div>
                    <div>
                        <p class="text-green-100 text-sm">HESLB Deductions</p>
                        <p class="font-bold">Active</p>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">100%</div>
                <p class="text-sm text-green-100">Compliance Score</p>
            </div>
        </div>
    </div>

    <!-- Payroll Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Monthly Payroll</p>
                    <p class="text-2xl font-bold text-gray-900">TZS 45.2M</p>
                    <p class="text-xs text-green-600 mt-1">November 2024</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">PAYE Tax</p>
                    <p class="text-2xl font-bold text-gray-900">TZS 8.1M</p>
                    <p class="text-xs text-gray-500 mt-1">18% of payroll</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">NSSF Total</p>
                    <p class="text-2xl font-bold text-gray-900">TZS 1.8M</p>
                    <p class="text-xs text-gray-500 mt-1">Employee + Employer</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="shield" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Net Salaries</p>
                    <p class="text-2xl font-bold text-gray-900">TZS 35.3M</p>
                    <p class="text-xs text-gray-500 mt-1">After deductions</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="credit-card" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Processing -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Current Payroll -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">November 2024 Payroll</h3>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Processed</span>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Processing Status</p>
                        <p class="text-sm text-gray-600">Completed on 25 Nov 2024</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">100%</p>
                        <p class="text-sm text-gray-600">Complete</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Employees Paid</p>
                        <p class="text-xl font-bold text-gray-900">248 / 248</p>
                    </div>
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Total Amount</p>
                        <p class="text-xl font-bold text-gray-900">TZS 35.3M</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Recent Transactions</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Bank Transfer - CRDB</span>
                            <span class="font-medium">TZS 25.2M</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Bank Transfer - NBC</span>
                            <span class="font-medium">TZS 10.1M</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Mobile Money - Tigo Pesa</span>
                            <span class="font-medium">TZS 0.8M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Payroll Actions</h3>
            <div class="space-y-3">
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="plus-circle" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Run New Payroll</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="download" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Generate Payslips</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="file-text" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">PAYE Reports</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="shield" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">NSSF Schedule</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="activity" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">WCF Declaration</span>
                </button>
                <button class="w-full flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left">
                    <i data-feather="dollar-sign" class="w-5 h-5 text-gray-600"></i>
                    <span class="text-sm font-medium text-gray-900">Final Dues Calculator</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Employee Payroll Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Employee Payroll Details</h3>
                <div class="flex space-x-3 mt-3 md:mt-0">
                    <div class="relative">
                        <input type="text" placeholder="Search employee..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    </div>
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option>All Departments</option>
                        <option>IT</option>
                        <option>HR</option>
                        <option>Finance</option>
                        <option>Operations</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAYE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NSSF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([
                        [
                            'name' => 'John Doe',
                            'id' => 'EMP001',
                            'basic' => 2500000,
                            'allowances' => 500000,
                            'gross' => 3000000,
                            'paye' => 405000,
                            'nssf' => 90000,
                            'other' => 100000,
                            'net' => 2405000
                        ],
                        [
                            'name' => 'Sarah Williams',
                            'id' => 'EMP002',
                            'basic' => 3500000,
                            'allowances' => 800000,
                            'gross' => 4300000,
                            'paye' => 635000,
                            'nssf' => 129000,
                            'other' => 150000,
                            'net' => 3386000
                        ],
                        [
                            'name' => 'Michael Johnson',
                            'id' => 'EMP003',
                            'basic' => 1800000,
                            'allowances' => 300000,
                            'gross' => 2100000,
                            'paye' => 255000,
                            'nssf' => 63000,
                            'other' => 50000,
                            'net' => 1732000
                        ],
                        [
                            'name' => 'Emily Chen',
                            'id' => 'EMP004',
                            'basic' => 4200000,
                            'allowances' => 1000000,
                            'gross' => 5200000,
                            'paye' => 835000,
                            'nssf' => 156000,
                            'other' => 200000,
                            'net' => 4009000
                        ],
                        [
                            'name' => 'David Kimani',
                            'id' => 'EMP005',
                            'basic' => 1200000,
                            'allowances' => 200000,
                            'gross' => 1400000,
                            'paye' => 135000,
                            'nssf' => 42000,
                            'other' => 30000,
                            'net' => 1193000
                        ]
                    ] as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ substr($employee['name'], 0, 1) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $employee['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($employee['basic'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($employee['allowances'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            TZS {{ number_format($employee['gross'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($employee['paye'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($employee['nssf'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format($employee['other'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format($employee['net'], 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" title="Payslip">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="History">
                                    <i data-feather="clock" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Statutory Reports -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="file-text" class="w-8 h-8 text-blue-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">TRA PAYE Return</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Generate monthly PAYE returns for Tanzania Revenue Authority</p>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Last Filed:</span>
                    <span class="font-medium">25 Oct 2024</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-medium">TZS 8.1M</span>
                </div>
            </div>
            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Generate PAYE Return
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="shield" class="w-8 h-8 text-purple-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">NSSF Schedule</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Prepare NSSF contribution schedules for submission</p>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Last Filed:</span>
                    <span class="font-medium">25 Oct 2024</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-medium">TZS 1.8M</span>
                </div>
            </div>
            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                Generate NSSF Schedule
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <i data-feather="activity" class="w-8 h-8 text-green-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">WCF Declaration</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">Workers Compensation Fund monthly declarations</p>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Last Filed:</span>
                    <span class="font-medium">25 Oct 2024</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Premium:</span>
                    <span class="font-medium">TZS 450K</span>
                </div>
            </div>
            <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Generate WCF Declaration
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fallback switchClient function if main app.js is not loaded
if (typeof switchClient === 'undefined') {
    function switchClient(clientId) {
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        
        const clientName = clientNames[clientId] || 'Unknown Client';
        
        // Add blur effect to background
        document.body.classList.add('backdrop-blur-sm');
        
        // Create modal with transparent background
        const modalOverlay = document.createElement('div');
        modalOverlay.id = 'clientSwitchModal';
        modalOverlay.className = 'fixed inset-0 flex items-center justify-center z-50';
        modalOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.1)'; // Very light transparent overlay
        modalOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-md mx-4 transform scale-0 transition-transform duration-200 shadow-xl">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                        <i data-feather="users" class="w-6 h-6 text-indigo-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Switch Client</h2>
                        <p class="text-sm text-gray-600">Are you sure you want to switch to ${clientName}?</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">All data will be refreshed and updated.</p>
                <div class="flex space-x-3">
                    <button onclick="closeClientSwitchModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmClientSwitch('${clientId}', '${clientName}')" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Switch Client
                    </button>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(modalOverlay);
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Add animation
        setTimeout(() => {
            modalOverlay.querySelector('.transform').classList.add('scale-100');
        }, 10);
    }
}

// Fallback closeClientSwitchModal function
if (typeof closeClientSwitchModal === 'undefined') {
    function closeClientSwitchModal() {
        const modal = document.getElementById('clientSwitchModal');
        if (modal) {
            modal.querySelector('.transform').classList.remove('scale-100');
            
            // Remove blur effect from background
            document.body.classList.remove('backdrop-blur-sm');
            
            setTimeout(() => {
                document.body.removeChild(modal);
            }, 200);
        }
    }
}

// Fallback confirmClientSwitch function
if (typeof confirmClientSwitch === 'undefined') {
    function confirmClientSwitch(clientId, clientName) {
        closeClientSwitchModal();
        
        // Store selected client
        localStorage.setItem('selectedClient', clientId);
        localStorage.setItem('selectedClientName', clientName);
        
        // Show success notification
        showNotification(`Successfully switched to ${clientName}`, 'success');
        
        // Reload page to update all data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}

// Fallback toggleNotifications function
if (typeof toggleNotifications === 'undefined') {
    function toggleNotifications() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationDropdown) {
            notificationDropdown.classList.toggle('hidden');
        }
    }
}

// Fallback showNotification function
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        
        // Set color based on type
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
        
        // Add to body
        document.body.appendChild(notification);
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}
</script>
@endpush

@endsection
