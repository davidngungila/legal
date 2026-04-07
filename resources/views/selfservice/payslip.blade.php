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
            <a href="{{ route('selfservice.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    <!-- Current Month Summary -->
    @if($payslips->count() > 0)
    @php($latestPayslip = $payslips->first())
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">{{ \Carbon\Carbon::parse($latestPayslip->pay_date)->format('F Y') }} Payslip</h2>
                @if($employee)
                <p class="text-blue-200">Employee ID: {{ $employee->employee_id }} | Department: {{ $employee->department }}</p>
                @endif
            </div>
            <div class="mt-4 md:mt-0">
                <p class="text-3xl font-bold">TZS {{ number_format($latestPayslip->net_pay, 0) }}</p>
                <p class="text-sm text-blue-200">Net Pay</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-center">
            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h3 class="text-yellow-800 font-semibold">No Payslip Records Found</h3>
                <p class="text-yellow-600 text-sm">Your payslip records are not available yet. Please contact HR if you need assistance.</p>
            </div>
        </div>
    </div>
    @endif

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
                    @if($payslips->count() > 0)
                        @foreach($payslips as $payslip)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($payslip->pay_date)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                TZS {{ number_format($payslip->basic_salary, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                TZS {{ number_format($payslip->allowances + $payslip->bonuses, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                TZS {{ number_format($payslip->total_deductions, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                TZS {{ number_format($payslip->net_pay, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $payslip->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="downloadPayslip('{{ $payslip->payroll_period }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Download</button>
                                <button onclick="viewPayslip('{{ $payslip->payroll_period }}')" class="text-gray-600 hover:text-gray-900">View</button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i data-feather="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p>No payslip records found.</p>
                                <p class="text-sm mt-2">Your payslips will appear here once processed by HR.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payslip Request Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Request Payslip</h2>
        
        <form method="POST" action="{{ route('selfservice.payslip.request') }}" class="space-y-4">
            @csrf
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="payroll_period" class="block text-sm font-medium text-gray-700 mb-2">Payroll Period *</label>
                    <select id="payroll_period" name="payroll_period" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Period</option>
                        @for($i = 0; $i < 12; $i++)
                            @php($period = \Carbon\Carbon::now()->subMonths($i)->format('Y-m'))
                            <option value="{{ $period }}">{{ \Carbon\Carbon::parse($period)->format('F Y') }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                        Request Payslip
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Salary Breakdown -->
    @if($payslips->count() > 0)
    @php($latestPayslip = $payslips->first())
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
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->basic_salary, 0) }}</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Allowances</p>
                        <p class="text-sm text-gray-500">Various allowances</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->allowances, 0) }}</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Bonuses</p>
                        <p class="text-sm text-gray-500">Performance bonuses</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->bonuses, 0) }}</p>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-900">Total Earnings</p>
                        <p class="text-2xl font-bold text-green-600">TZS {{ number_format($latestPayslip->gross_pay, 0) }}</p>
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
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->tax_deductions, 0) }}</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Social Security</p>
                        <p class="text-sm text-gray-500">Social security contributions</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->social_security, 0) }}</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Pension</p>
                        <p class="text-sm text-gray-500">Pension contributions</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->pension, 0) }}</p>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Other Deductions</p>
                        <p class="text-sm text-gray-500">Loan repayments, etc.</p>
                    </div>
                    <p class="text-xl font-bold text-gray-900">TZS {{ number_format($latestPayslip->other_deductions, 0) }}</p>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-gray-900">Total Deductions</p>
                        <p class="text-2xl font-bold text-red-600">TZS {{ number_format($latestPayslip->total_deductions, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
