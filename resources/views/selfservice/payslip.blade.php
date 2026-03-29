@extends('layouts.app')

@section('title', 'Download Payslip - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Download Payslip</h1>
            <p class="text-gray-600 mt-2">Access your monthly salary statements</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Current Month Summary -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">November 2024 Payslip</h2>
                <p class="text-blue-200">Employee ID: EMP001 | Department: IT</p>
            </div>
            <div class="mt-4 md:mt-0">
                <p class="text-3xl font-bold">TZS 2,405,000</p>
                <p class="text-sm text-blue-200">Net Pay</p>
            </div>
        </div>
    </div>

    <!-- Payslip History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Payslip History</h2>
            <div class="flex items-center space-x-3">
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Months</option>
                    <option value="2024-11">November 2024</option>
                    <option value="2024-10">October 2024</option>
                    <option value="2024-09">September 2024</option>
                    <option value="2024-08">August 2024</option>
                    <option value="2024-07">July 2024</option>
                </select>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i data-feather="filter" class="w-4 h-4 inline mr-2"></i>
                    Filter
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            November 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(2500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(595000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format(2405000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="downloadPayslip('2024-11')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                            <button onclick="viewPayslip('2024-11')" class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            October 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(2500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(595000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format(2405000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="downloadPayslip('2024-10')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                            <button onclick="viewPayslip('2024-10')" class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            September 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(2500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(450000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(580000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format(2370000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="downloadPayslip('2024-09')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                            <button onclick="viewPayslip('2024-09')" class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            August 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(2500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(500000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(595000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format(2405000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="downloadPayslip('2024-08')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                            <button onclick="viewPayslip('2024-08')" class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            July 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(2400000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(400000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS {{ number_format(560000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                            TZS {{ number_format(2240000, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Paid
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="downloadPayslip('2024-07')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                            <button onclick="viewPayslip('2024-07')" class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Salary Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Earnings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Current Month Earnings</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Basic Salary</p>
                        <p class="text-sm text-gray-500">Monthly base salary</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 2,500,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Housing Allowance</p>
                        <p class="text-sm text-gray-500">Monthly housing benefit</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 300,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Transport Allowance</p>
                        <p class="text-sm text-gray-500">Monthly transport benefit</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 150,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Communication Allowance</p>
                        <p class="text-sm text-gray-500">Phone & internet benefit</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 50,000</p>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-900">Total Earnings</p>
                        <p class="text-2xl font-bold text-green-600">TZS 3,000,000</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deductions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Current Month Deductions</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">PAYE (Tax)</p>
                        <p class="text-sm text-gray-500">Pay As You Earn tax</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 350,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">NSSF</p>
                        <p class="text-sm text-gray-500">National Social Security Fund</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 120,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">WCF</p>
                        <p class="text-sm text-gray-500">Workers Compensation Fund</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 75,000</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Other Deductions</p>
                        <p class="text-sm text-gray-500">Loan repayments, etc.</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS 50,000</p>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-900">Total Deductions</p>
                        <p class="text-2xl font-bold text-red-600">TZS 595,000</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Options -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Download Options</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="downloadAllPayslips()" class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-6 h-6 text-blue-600 mb-2"></i>
                <p class="font-medium text-gray-900">Download All</p>
                <p class="text-sm text-gray-500">All payslips as ZIP</p>
            </button>
            
            <button onclick="downloadCurrentYear()" class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="calendar" class="w-6 h-6 text-green-600 mb-2"></i>
                <p class="font-medium text-gray-900">Current Year</p>
                <p class="text-sm text-gray-500">2024 payslips</p>
            </button>
            
            <button onclick="emailPayslips()" class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="mail" class="w-6 h-6 text-purple-600 mb-2"></i>
                <p class="font-medium text-gray-900">Email Payslips</p>
                <p class="text-sm text-gray-500">Send to email</p>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Download payslip
function downloadPayslip(month) {
    showNotification('Preparing payslip download...', 'info');
    
    setTimeout(() => {
        const payslipData = {
            '2024-11': 'Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nNovember 2024,2500000,500000,595000,2405000,Paid',
            '2024-10': 'Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nOctober 2024,2500000,500000,595000,2405000,Paid',
            '2024-09': 'Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nSeptember 2024,2500000,450000,580000,2370000,Paid',
            '2024-08': 'Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nAugust 2024,2500000,500000,595000,2405000,Paid',
            '2024-07': 'Month,Basic Salary,Allowances,Deductions,Net Pay,Status\nJuly 2024,2400000,400000,560000,2240000,Paid'
        };
        
        const link = document.createElement('a');
        link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(payslipData[month] || payslipData['2024-11']);
        link.download = `payslip_${month}.csv`;
        link.click();
        
        showNotification('Payslip downloaded successfully!', 'success');
    }, 1000);
}

// View payslip
function viewPayslip(month) {
    showNotification('Opening payslip viewer...', 'info');
    
    setTimeout(() => {
        showNotification('Payslip viewer opened', 'success');
        // In a real app, this would open a modal or new page
    }, 500);
}

// Download all payslips
function downloadAllPayslips() {
    showNotification('Preparing all payslips...', 'info');
    
    setTimeout(() => {
        showNotification('All payslips downloaded successfully!', 'success');
        // In a real app, this would create and download a ZIP file
    }, 2000);
}

// Download current year
function downloadCurrentYear() {
    showNotification('Preparing 2024 payslips...', 'info');
    
    setTimeout(() => {
        showNotification('2024 payslips downloaded successfully!', 'success');
        // In a real app, this would create and download a ZIP file
    }, 1500);
}

// Email payslips
function emailPayslips() {
    showNotification('Sending payslips to email...', 'info');
    
    setTimeout(() => {
        showNotification('Payslips sent to your email successfully!', 'success');
        // In a real app, this would send via API
    }, 1500);
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
