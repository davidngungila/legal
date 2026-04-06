@extends('layouts.app')

@section('title', 'Payslip - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Payslip</h1>
            <p class="text-gray-600 mt-2">Detailed salary breakdown for <span id="currentClientName">ABC Manufacturing Ltd</span></p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.history.back()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Payroll
            </button>
            <button onclick="downloadPDF()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download PDF
            </button>
            <button onclick="printPayslip()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Payslip Content -->
    <div id="payslipContainer" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <!-- Payslip will be loaded here -->
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="file-text" class="w-8 h-8 text-gray-400"></i>
            </div>
            <p class="text-lg font-medium text-gray-900 mb-2">Loading Payslip...</p>
            <p class="text-sm text-gray-500">Please wait while we fetch the payslip data</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-6">
        <div class="flex space-x-3 mb-4 md:mb-0">
            <button onclick="downloadPDF()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download PDF
            </button>
            <button onclick="emailPayslip()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-feather="mail" class="w-4 h-4 inline mr-2"></i>
                Email Payslip
            </button>
        </div>
        <div class="flex space-x-3">
            <button onclick="previousEmployee()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="chevron-left" class="w-4 h-4 inline mr-2"></i>
                Previous Employee
            </button>
            <button onclick="nextEmployee()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Next Employee
                <i data-feather="chevron-right" class="w-4 h-4 inline ml-2"></i>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentEmployee = null;
let allEmployees = [];
let currentEmployeeIndex = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadPayslipData();
    loadSavedClientData();
});

function loadSavedClientData() {
    const savedClient = localStorage.getItem('selectedClient');
    if (savedClient) {
        updateClientDisplay(savedClient);
    }
}

function updateClientDisplay(clientId) {
    const clientNames = {
        '1': 'ABC Manufacturing Ltd',
        '2': 'XYZ Construction Co',
        '3': 'Tanzania Mining Corp',
        '4': 'East Africa Logistics'
    };
    
    const clientName = clientNames[clientId] || 'Unknown Client';
    document.getElementById('currentClientName').textContent = clientName;
}

function loadPayslipData() {
    // Get employee ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const empId = urlParams.get('empId');
    
    if (!empId) {
        showNotification('Employee ID not provided', 'error');
        setTimeout(() => {
            window.location.href = '/payroll';
        }, 2000);
        return;
    }
    
    // Load payroll data
    const currentClient = localStorage.getItem('selectedClient') || '1';
    const clientPayrollKey = `payrollData_client_${currentClient}`;
    const savedData = localStorage.getItem(clientPayrollKey);
    
    if (savedData) {
        const payrollData = JSON.parse(savedData);
        allEmployees = payrollData;
        currentEmployee = payrollData.find(emp => emp.empId === empId);
        currentEmployeeIndex = payrollData.findIndex(emp => emp.empId === empId);
        
        if (currentEmployee) {
            renderPayslip();
        } else {
            showNotification('Employee not found', 'error');
            setTimeout(() => {
                window.location.href = '/payroll';
            }, 2000);
        }
    } else {
        showNotification('No payroll data available', 'error');
        setTimeout(() => {
            window.location.href = '/payroll';
        }, 2000);
    }
}

function renderPayslip() {
    if (!currentEmployee) return;
    
    const payslipContent = `
        <!-- Header -->
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-6">
            <div class="mb-4">
                <img src="{{ asset('Orvion.png') }}" alt="LegalHR Logo" class="w-24 h-24 mx-auto">
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">SALARY SLIP</h1>
            <p class="text-xl text-gray-600">For the Month of ${currentEmployee.monthOfPayment}</p>
            <p class="text-sm text-gray-500 mt-2">LegalHR Tanzania - Payroll Management System</p>
        </div>
        
        <!-- Employee Details -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b-2 border-gray-800 pb-2">EMPLOYEE DETAILS</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Employee ID:</span>
                    <span class="font-semibold">${currentEmployee.empId}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Full Name:</span>
                    <span class="font-semibold">${currentEmployee.name}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Job Title:</span>
                    <span class="font-semibold">${currentEmployee.title}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Department:</span>
                    <span class="font-semibold">${currentEmployee.department}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Shift:</span>
                    <span class="font-semibold">${currentEmployee.shift}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Joining Date:</span>
                    <span class="font-semibold">${currentEmployee.joiningDate}</span>
                </div>
            </div>
        </div>
        
        <!-- Earnings Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b-2 border-gray-800 pb-2">EARNINGS</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Basic Salary:</span>
                    <span class="font-semibold text-lg">TZS ${currentEmployee.basicSalary.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Overtime Pay (${currentEmployee.otHours} hrs × TZS ${currentEmployee.otRate.toLocaleString()}):</span>
                    <span class="font-semibold">TZS ${currentEmployee.otPay.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Holiday Pay:</span>
                    <span class="font-semibold">TZS ${currentEmployee.holidayPay.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-4 font-bold text-xl border-t-2 border-gray-800 bg-green-50 px-4 rounded">
                    <span>GROSS PAY:</span>
                    <span class="text-green-600">TZS ${currentEmployee.grossPay.toLocaleString()}</span>
                </div>
            </div>
        </div>
        
        <!-- Deductions Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b-2 border-gray-800 pb-2">DEDUCTIONS</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">PAYE Tax:</span>
                    <span class="font-semibold">TZS ${currentEmployee.paye.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">NSSF (10%):</span>
                    <span class="font-semibold">TZS ${currentEmployee.nssf.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">HESLB Loan (15%):</span>
                    <span class="font-semibold">TZS ${currentEmployee.heslb.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Other Deductions:</span>
                    <span class="font-semibold">TZS ${currentEmployee.otherDed.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-4 font-bold text-xl border-t-2 border-gray-800 bg-red-50 px-4 rounded">
                    <span>TOTAL DEDUCTIONS:</span>
                    <span class="text-red-600">TZS ${currentEmployee.totalDeduction.toLocaleString()}</span>
                </div>
            </div>
        </div>
        
        <!-- Net Pay Section -->
        <div class="mb-8">
            <div class="flex justify-between py-6 font-bold text-2xl border-t-4 border-gray-800 border-b-4 border-gray-800 bg-blue-50 px-6 rounded-lg">
                <span>NET PAY:</span>
                <span class="text-blue-600">TZS ${currentEmployee.netPay.toLocaleString()}</span>
            </div>
            <p class="text-center text-sm text-gray-600 mt-2">(Amount in words: ${numberToWords(currentEmployee.netPay)} Tanzanian Shillings Only)</p>
        </div>
        
        <!-- Employer Contributions -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b-2 border-gray-800 pb-2">EMPLOYER CONTRIBUTIONS</h3>
            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Employer NSSF:</span>
                    <span class="font-semibold">TZS ${currentEmployee.employerNSSF.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Skills Development Levy (SDL):</span>
                    <span class="font-semibold">TZS ${currentEmployee.sdl.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-gray-700 font-medium">Workers Compensation Fund (WCF):</span>
                    <span class="font-semibold">TZS ${currentEmployee.wcf.toLocaleString()}</span>
                </div>
                <div class="flex justify-between py-4 font-bold text-xl border-t-2 border-gray-800 bg-purple-50 px-4 rounded">
                    <span>TOTAL COST TO COMPANY:</span>
                    <span class="text-purple-600">TZS ${currentEmployee.totalCost.toLocaleString()}</span>
                </div>
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="mt-12 pt-6 border-t-2 border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
                <div>
                    <p class="font-bold text-gray-900 mb-2">Prepared by:</p>
                    <p class="text-gray-700">HR Department</p>
                    <p class="text-gray-600">LegalHR System</p>
                    <p class="text-gray-600">${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
                <div class="text-center">
                    <p class="font-bold text-gray-900 mb-2">Authorized by:</p>
                    <div class="border-b-2 border-gray-400 w-48 mx-auto mb-2 pb-1"></div>
                    <p class="text-gray-700">Finance Manager</p>
                    <p class="text-gray-600">Signature & Date</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 mb-2">Employee Acknowledgment:</p>
                    <div class="border-b-2 border-gray-400 w-48 ml-auto mb-2 pb-1"></div>
                    <p class="text-gray-700">${currentEmployee.name}</p>
                    <p class="text-gray-600">Signature & Date</p>
                    <p class="text-xs text-gray-500 mt-2">I confirm that I have received my salary slip and the amount stated above</p>
                </div>
            </div>
        </div>
        
        <!-- Legal Notice -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-xs text-gray-600 text-center">
                <strong>Important Notice:</strong> This is a computer-generated payslip and does not require a physical signature. 
                This document is for official purposes and should be kept for your records. 
                For any discrepancies, please contact the HR department within 7 days of receipt.
            </p>
        </div>
    `;
    
    document.getElementById('payslipContainer').innerHTML = payslipContent;
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function numberToWords(num) {
    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    
    if (num === 0) return 'Zero';
    
    let words = '';
    
    if (num >= 1000000) {
        words += numberToWords(Math.floor(num / 1000000)) + ' Million ';
        num %= 1000000;
    }
    
    if (num >= 1000) {
        words += numberToWords(Math.floor(num / 1000)) + ' Thousand ';
        num %= 1000;
    }
    
    if (num >= 100) {
        words += ones[Math.floor(num / 100)] + ' Hundred ';
        num %= 100;
    }
    
    if (num >= 20) {
        words += tens[Math.floor(num / 10)] + ' ';
        num %= 10;
    }
    
    if (num >= 10) {
        words += teens[num - 10] + ' ';
        num = 0;
    }
    
    if (num > 0) {
        words += ones[num] + ' ';
    }
    
    return words.trim();
}

function downloadPDF() {
    if (!currentEmployee) {
        showNotification('No payslip data available', 'error');
        return;
    }
    
    showNotification('Generating PDF...', 'info');
    
    // In a real implementation, you would use a PDF library like jsPDF or html2canvas
    // For now, we'll create a printable version
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    const payslipHTML = document.getElementById('payslipContainer').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payslip - ${currentEmployee.name} - ${currentEmployee.monthOfPayment}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    line-height: 1.6;
                    color: #333;
                }
                .payslip-container { 
                    max-width: 800px; 
                    margin: 0 auto; 
                    background: white;
                    padding: 40px;
                }
                .text-center { text-align: center; }
                .text-xl { font-size: 1.25rem; }
                .text-2xl { font-size: 1.5rem; }
                .text-3xl { font-size: 1.875rem; }
                .font-bold { font-weight: bold; }
                .font-semibold { font-weight: 600; }
                .font-medium { font-weight: 500; }
                .text-gray-900 { color: #111827; }
                .text-gray-700 { color: #374151; }
                .text-gray-600 { color: #4b5563; }
                .text-gray-500 { color: #6b7280; }
                .text-green-600 { color: #059669; }
                .text-blue-600 { color: #2563eb; }
                .text-red-600 { color: #dc2626; }
                .text-purple-600 { color: #9333ea; }
                .border-b { border-bottom: 1px solid; }
                .border-t { border-top: 1px solid; }
                .border-2 { border-width: 2px; }
                .border-4 { border-width: 4px; }
                .border-gray-800 { border-color: #1f2937; }
                .border-gray-400 { border-color: #9ca3af; }
                .border-gray-200 { border-color: #e5e7eb; }
                .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
                .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
                .pb-1 { padding-bottom: 0.25rem; }
                .pb-2 { padding-bottom: 0.5rem; }
                .pb-6 { padding-bottom: 1.5rem; }
                .pt-6 { padding-top: 1.5rem; }
                .mt-8 { margin-top: 2rem; }
                .mt-12 { margin-top: 3rem; }
                .mb-2 { margin-bottom: 0.5rem; }
                .mb-4 { margin-bottom: 1rem; }
                .mb-8 { margin-bottom: 2rem; }
                .ml-auto { margin-left: auto; }
                .mx-auto { margin-left: auto; margin-right: auto; }
                .grid { display: grid; }
                .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
                .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
                .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
                .md\\:grid-cols-3 { @media (min-width: 768px) { grid-template-columns: repeat(3, 1fr); } }
                .gap-2 { gap: 0.5rem; }
                .gap-4 { gap: 1rem; }
                .gap-8 { gap: 2rem; }
                .flex { display: flex; }
                .justify-between { justify-content: space-between; }
                .text-right { text-align: right; }
                .space-y-3 > * + * { margin-top: 0.75rem; }
                .bg-green-50 { background-color: #f0fdf4; }
                .bg-red-50 { background-color: #fef2f2; }
                .bg-blue-50 { background-color: #eff6ff; }
                .bg-purple-50 { background-color: #faf5ff; }
                .bg-gray-50 { background-color: #f9fafb; }
                .rounded { border-radius: 0.375rem; }
                .rounded-lg { border-radius: 0.5rem; }
                .w-48 { width: 12rem; }
                .w-24 { width: 6rem; }
                .h-24 { height: 6rem; }
                .text-xs { font-size: 0.75rem; }
                .text-sm { font-size: 0.875rem; }
                .text-lg { font-size: 1.125rem; }
                .p-4 { padding: 1rem; }
                .border { border: 1px solid; }
                @media print {
                    body { margin: 0; }
                    .payslip-container { 
                        box-shadow: none; 
                        border: none;
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="payslip-container">
                ${payslipHTML}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
        showNotification('PDF download ready', 'success');
    }, 1000);
}

function printPayslip() {
    window.print();
}

function emailPayslip() {
    if (!currentEmployee) {
        showNotification('No payslip data available', 'error');
        return;
    }
    
    showNotification(`Email functionality coming soon for ${currentEmployee.name}`, 'info');
}

function previousEmployee() {
    if (currentEmployeeIndex > 0) {
        currentEmployeeIndex--;
        const prevEmployee = allEmployees[currentEmployeeIndex];
        const url = new URL(window.location);
        url.searchParams.set('empId', prevEmployee.empId);
        window.location.href = url.toString();
    } else {
        showNotification('This is the first employee', 'info');
    }
}

function nextEmployee() {
    if (currentEmployeeIndex < allEmployees.length - 1) {
        currentEmployeeIndex++;
        const nextEmployee = allEmployees[currentEmployeeIndex];
        const url = new URL(window.location);
        url.searchParams.set('empId', nextEmployee.empId);
        window.location.href = url.toString();
    } else {
        showNotification('This is the last employee', 'info');
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        previousEmployee();
    } else if (e.key === 'ArrowRight') {
        nextEmployee();
    } else if (e.key === 'p' && e.ctrlKey) {
        e.preventDefault();
        printPayslip();
    } else if (e.key === 's' && e.ctrlKey) {
        e.preventDefault();
        downloadPDF();
    }
});

// Fallback functions
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        console.log('Notification:', message, type);
    }
}

if (typeof feather !== 'undefined') {
    feather.replace();
}
</script>
@endpush

@endsection
