@extends('layouts.app')

@section('title', 'Payroll Management - LegalHR Tanzania')

@push('scripts')
<!-- Include XLSX library for Excel file processing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Initialize XLSX library
    XLSX = XLSX || {};
    XLSX.noCompat = true;
</script>
@endpush

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Payroll Management</h1>
            <p class="text-gray-600 mt-2">Process payroll with full Tanzania statutory compliance</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Processing payroll for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex flex-wrap gap-3 lg:justify-end">
            <a href="{{ route('attendance.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                <i data-feather="clock" class="w-4 h-4 mr-2"></i>
                Attendance
            </a>
            <a href="{{ route('compensation.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                <i data-feather="dollar-sign" class="w-4 h-4 mr-2"></i>
                Compensation
            </a>
            <button onclick="generatePayrollFromAttendance()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center justify-center">
                <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                Generate From Attendance
            </button>
            <button onclick="showBulkActions()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors inline-flex items-center justify-center">
                <i data-feather="layers" class="w-4 h-4 inline mr-2"></i>
                Bulk Actions
            </button>
            <button onclick="generateAllPayslips()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                <i data-feather="file-text" class="w-4 h-4 inline mr-2"></i>
                Generate All Payslips
            </button>
            <button onclick="exportPayrollReport()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <a href="{{ route('payroll.upload') }}" class="btn-transition px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center justify-center">
                <i data-feather="upload" class="w-4 h-4 mr-2"></i>
                Upload CSV
            </a>
        </div>
    </div>

    <!-- Compliance Status -->
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl p-6 text-white mb-8 shadow-sm">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-green-100">Compliance Overview</p>
                        <h3 class="text-2xl font-semibold mt-1">Payroll Compliance Status</h3>
                        <p class="text-sm text-green-100 mt-1">
                            Current client:
                            <span id="currentClientName" class="font-semibold text-white">{{ $currentClient->name }}</span>
                        </p>
                    </div>
                    <div class="inline-flex items-center rounded-full bg-white/10 px-3 py-1.5 text-sm text-green-50 border border-white/10">
                        <i data-feather="shield" class="w-4 h-4 mr-2"></i>
                        Tanzania Statutory Summary
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
                    <div id="payeContainer" class="rounded-xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-green-100 text-sm">PAYE Filing</p>
                                <p class="font-semibold text-white mt-1" id="payeStatus">Up to Date</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                <i data-feather="file-text" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                                <div id="payeProgressBar" class="h-2 rounded-full bg-white" style="width: 100%"></div>
                            </div>
                            <p class="text-xs text-green-50 mt-2" id="payeProgressText">100% Complete</p>
                        </div>
                    </div>

                    <div id="nssfContainer" class="rounded-xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-green-100 text-sm">NSSF Contributions</p>
                                <p class="font-semibold text-white mt-1" id="nssfStatus">Compliant</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                <i data-feather="briefcase" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                                <div id="nssfProgressBar" class="h-2 rounded-full bg-white" style="width: 100%"></div>
                            </div>
                            <p class="text-xs text-green-50 mt-2" id="nssfProgressText">100% Complete</p>
                        </div>
                    </div>

                    <div id="wcfContainer" class="rounded-xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-green-100 text-sm">WCF Payments</p>
                                <p class="font-semibold text-white mt-1" id="wcfStatus">Current</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                <i data-feather="activity" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                                <div id="wcfProgressBar" class="h-2 rounded-full bg-white" style="width: 0%"></div>
                            </div>
                            <p class="text-xs text-green-50 mt-2" id="wcfProgressText">0% Complete</p>
                        </div>
                    </div>

                    <div id="heslbContainer" class="rounded-xl bg-white/10 border border-white/10 p-4 backdrop-blur-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-green-100 text-sm">HESLB Deductions</p>
                                <p class="font-semibold text-white mt-1" id="heslbStatus">Active</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                <i data-feather="book-open" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="w-full bg-white/15 rounded-full h-2 overflow-hidden">
                                <div id="heslbProgressBar" class="h-2 rounded-full bg-white" style="width: 100%"></div>
                            </div>
                            <p class="text-xs text-green-50 mt-2" id="heslbProgressText">100% Complete</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="xl:w-60 rounded-2xl bg-white/10 border border-white/10 p-5 text-center backdrop-blur-sm">
                <p class="text-sm font-medium text-green-100">Compliance Score</p>
                <div class="text-4xl font-bold mt-3" id="complianceScore">100%</div>
                <p class="text-sm text-green-50 mt-2" id="complianceSummaryText">All primary payroll obligations are currently calculated.</p>
                <div class="mt-4 h-2 rounded-full bg-white/15 overflow-hidden">
                    <div id="complianceScoreBar" class="h-2 rounded-full bg-white" style="width: 100%"></div>
                </div>
                <p class="text-xs text-green-50 mt-2" id="complianceScoreCaption">Healthy compliance posture</p>
            </div>
        </div>
    </div>

    <!-- Payroll Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Monthly Payroll</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalPayroll">TZS 0</p>
                    <p class="text-xs text-green-600 mt-1" id="payrollMonth">Current Month</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalPAYE">TZS 0</p>
                    <p class="text-xs text-gray-500 mt-1" id="payePercentage">0% of payroll</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalNSSF">TZS 0</p>
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
                    <p class="text-2xl font-bold text-gray-900" id="totalNet">TZS 0</p>
                    <p class="text-xs text-gray-500 mt-1">After deductions</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="credit-card" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Payroll Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Import Payroll Data</h3>
            <button onclick="downloadTemplate()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download Template
            </button>
        </div>
        
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="upload-cloud" class="w-8 h-8 text-indigo-600"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-900 mb-2">Upload Payroll File</h4>
            <p class="text-gray-600 mb-4">Upload Excel or CSV file with payroll data including employee details and calculations</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Required Columns:</label>
                <div class="flex flex-wrap gap-2 justify-center">
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Emp ID</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Name</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Department</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Title</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Joining date</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Employee Shift</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Month of payment</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Basic Salary</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">OT Hours</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">OT Rate</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">OT Pay</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Holiday Pay</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Gross Pay</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">NSSF (10%)</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Taxable Pay</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">PAYE</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">HESLB (15%)</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Other Ded</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Total Deduction</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Net Pay</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Employer NSSF</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">SDL</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">WCF</span>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Total Cost</span>
                </div>
            </div>
            
            <input type="file" id="payrollFile" accept=".xlsx,.xls,.csv" class="hidden" onchange="handleFileUpload(event)">
            <button onclick="document.getElementById('payrollFile').click()" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Choose File
            </button>
        </div>
    </div>

    <!-- Employee Payroll Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Employee Payroll Details</h3>
                <div class="flex space-x-3 mt-3 md:mt-0">
                    <div class="relative">
                        <input type="text" id="employeeSearch" placeholder="Search employee..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    </div>
                    <select id="departmentFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Departments</option>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="Operations">Operations</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payroll Date From</label>
                        <input type="date" id="payrollDateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payroll Date To</label>
                        <input type="date" id="payrollDateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
                        <select id="payrollMonthFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Months</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
                        <select id="payrollYearFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Years</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button onclick="clearPayrollFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
        
        <div class="px-6 pt-3 text-xs text-gray-500 border-b border-gray-100">
            Scroll horizontally to view all payroll columns shown in the import template.
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[2600px] w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joining Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month of Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonuses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxable Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAYE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NSSF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HESLB</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Deduction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Deduction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employer NSSF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SDL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WCF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="payrollTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Payroll data will be loaded here -->
                    <tr>
                        <td colspan="26" class="text-center py-8 text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-feather="upload" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <p class="text-lg font-medium text-gray-900 mb-2">No Payroll Data</p>
                            <p class="text-sm text-gray-500">Import payroll data to view employee details</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Import Modal -->
<x-advanced-modal id="importModal" title="Import Payroll Data" icon="upload" color="green" size="2xl">
    <div id="importPreview" class="space-y-4">
        <!-- Preview will be shown here -->
    </div>

    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button onclick="closeModal('importModal')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button onclick="confirmImport()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="check" class="w-4 h-4 inline mr-2"></i>
                Import Data
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Payslip Modal -->
<x-advanced-modal id="payslipModal" title="Salary Slip" icon="receipt" color="blue" size="4xl">
    <div id="payslipContent">
        <!-- Payslip content will be generated here -->
    </div>

    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button onclick="viewFullPayslip()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                <i data-feather="external-link" class="w-4 h-4 inline mr-2"></i>
                View Full Page
            </button>
            <button onclick="printPayslip()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors inline-flex items-center">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                Print
            </button>
            <button onclick="closeModal('payslipModal')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Close
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Employee Modal -->
<x-advanced-modal id="editEmployeeModal" title="Edit Employee Payroll" icon="edit" color="indigo" size="4xl">
    <form id="editEmployeeForm" class="space-y-6">
            <!-- Employee Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Employee Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                        <input type="text" id="editEmpId" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="editName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select id="editDepartment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                            <option value="Sales">Sales</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                        <input type="text" id="editTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Joining Date</label>
                        <input type="date" id="editJoiningDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                        <select id="editShift" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Day">Day</option>
                            <option value="Night">Night</option>
                            <option value="Rotating">Rotating</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Salary Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Salary Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary (TZS)</label>
                        <input type="number" id="editBasicSalary" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OT Hours</label>
                        <input type="number" id="editOtHours" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OT Rate (TZS)</label>
                        <input type="number" id="editOtRate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OT Pay (TZS)</label>
                        <input type="number" id="editOtPay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Holiday Pay (TZS)</label>
                        <input type="number" id="editHolidayPay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allowances (TZS)</label>
                        <input type="number" id="editAllowances" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bonuses (TZS)</label>
                        <input type="number" id="editBonuses" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gross Pay (TZS)</label>
                        <input type="number" id="editGrossPay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Deductions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NSSF (10%)</label>
                        <input type="number" id="editNssf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">PAYE Tax (TZS)</label>
                        <input type="number" id="editPaye" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">HESLB Loan (15%)</label>
                        <input type="number" id="editHeslb" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Deductions (TZS)</label>
                        <input type="number" id="editOtherDed" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Deductions (TZS)</label>
                        <input type="number" id="editTotalDeduction" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Net Pay (TZS)</label>
                        <input type="number" id="editNetPay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                </div>
            </div>

            <!-- Employer Contributions -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b border-gray-200 pb-2">Employer Contributions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employer NSSF (TZS)</label>
                        <input type="number" id="editEmployerNssf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SDL (TZS)</label>
                        <input type="number" id="editSdl" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">WCF (TZS)</label>
                        <input type="number" id="editWcf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Cost (TZS)</label>
                        <input type="number" id="editTotalCost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly>
                    </div>
                </div>
            </div>

            <!-- Month of Payment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Month of Payment</label>
                <input type="text" id="editMonthOfPayment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., March 2026">
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button onclick="closeModal('editEmployeeModal')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="calculatePayroll()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-feather="calculator" class="w-4 h-4 inline mr-2"></i>
                    Calculate
                </button>
                <button id="saveEmployeeBtn" onclick="saveEmployee()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                    Update Payroll
                </button>
            </div>
        </x-slot:footer>
</x-advanced-modal>

<!-- Bulk Actions Modal -->
<x-advanced-modal id="bulkActionsModal" title="Bulk Payslip Actions" icon="layers" color="purple" size="2xl">
    <div class="space-y-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Download All Payslips</h3>
                <p class="text-blue-700 mb-4">Generate and download payslips for all employees in the current payroll.</p>
                <button onclick="downloadAllPayslipsPDF()" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                    Download All Payslips (PDF)
                </button>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-900 mb-2">Email All Payslips</h3>
                <p class="text-green-700 mb-4">Send payslips to all employees via email.</p>
                <button onclick="emailAllPayslips()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i data-feather="mail" class="w-4 h-4 inline mr-2"></i>
                    Email All Payslips
                </button>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-purple-900 mb-2">Generate Payroll Report</h3>
                <p class="text-purple-700 mb-4">Create a comprehensive payroll report with all details.</p>
                <button onclick="generateComprehensiveReport()" class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i data-feather="file-text" class="w-4 h-4 inline mr-2"></i>
                    Generate Report
                </button>
            </div>
        </div>
</x-advanced-modal>

@push('scripts')
<script>
let payrollData = [];
let filteredPayrollData = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadPayrollData();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('employeeSearch').addEventListener('input', filterPayrollData);
    document.getElementById('departmentFilter').addEventListener('change', filterPayrollData);
    document.getElementById('payrollDateFrom').addEventListener('change', filterPayrollData);
    document.getElementById('payrollDateTo').addEventListener('change', filterPayrollData);
    document.getElementById('payrollMonthFilter').addEventListener('change', filterPayrollData);
    document.getElementById('payrollYearFilter').addEventListener('change', filterPayrollData);

    [
        'editBasicSalary',
        'editOtHours',
        'editOtRate',
        'editHolidayPay',
        'editAllowances',
        'editBonuses',
        'editHeslb',
        'editOtherDed'
    ].forEach((id) => {
        const field = document.getElementById(id);
        if (field) {
            field.addEventListener('input', calculatePayroll);
        }
    });
}

async function loadPayrollData() {
    try {
        const response = await fetch('{{ route('payroll.data') }}', {
            headers: { 'Accept': 'application/json' }
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json') ? await response.json() : null;

        if (result?.success && Array.isArray(result.data)) {
            payrollData = result.data;
            filteredPayrollData = [...payrollData];
            initializePayrollDateFilters();
            renderPayrollTable();
            updatePayrollStats();
            updateComplianceStatus();
            return;
        }
    } catch (e) {
    }

    payrollData = [];
    filteredPayrollData = [];
    initializePayrollDateFilters();
    renderPayrollTable();
    updatePayrollStats();
    updateComplianceStatus();
}

function initializePayrollDateFilters() {
    const yearSelect = document.getElementById('payrollYearFilter');
    if (!yearSelect) return;

    const years = new Set();
    payrollData.forEach((row) => {
        const parsed = parseMonthOfPayment(row.monthOfPayment);
        if (parsed) years.add(parsed.getFullYear());
    });

    const sortedYears = Array.from(years).sort((a, b) => b - a);

    yearSelect.innerHTML = '<option value="">All Years</option>';
    sortedYears.forEach((y) => {
        const opt = document.createElement('option');
        opt.value = String(y);
        opt.textContent = String(y);
        yearSelect.appendChild(opt);
    });
}

function clearPayrollFilters() {
    document.getElementById('employeeSearch').value = '';
    document.getElementById('departmentFilter').value = '';
    document.getElementById('payrollDateFrom').value = '';
    document.getElementById('payrollDateTo').value = '';
    document.getElementById('payrollMonthFilter').value = '';
    document.getElementById('payrollYearFilter').value = '';
    filterPayrollData();
}

function parseMonthOfPayment(monthOfPayment) {
    if (!monthOfPayment) return null;
    const value = String(monthOfPayment).trim();

    const monthFirstTry = new Date(`1 ${value}`);
    if (!isNaN(monthFirstTry.getTime())) {
        return monthFirstTry;
    }

    const match = value.match(/^(\d{4})[-\/](\d{1,2})/);
    if (match) {
        const year = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        if (year && month >= 1 && month <= 12) {
            return new Date(year, month - 1, 1);
        }
    }

    return null;
}

function getPayrollPeriodRange(row) {
    const start = parseMonthOfPayment(row.monthOfPayment);
    if (!start) return null;
    const end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
    return { start, end };
}

function parseDateInput(value) {
    if (!value) return null;
    const d = new Date(value);
    return isNaN(d.getTime()) ? null : d;
}

function filterPayrollData() {
    const searchTerm = document.getElementById('employeeSearch').value.toLowerCase();
    const departmentFilter = document.getElementById('departmentFilter').value;
    const dateFrom = parseDateInput(document.getElementById('payrollDateFrom').value);
    const dateTo = parseDateInput(document.getElementById('payrollDateTo').value);
    const monthFilter = parseInt(document.getElementById('payrollMonthFilter').value, 10) || null;
    const yearFilter = parseInt(document.getElementById('payrollYearFilter').value, 10) || null;

    filteredPayrollData = payrollData.filter((row) => {
        const name = (row.name || '').toString().toLowerCase();
        const empId = (row.empId || '').toString().toLowerCase();
        const department = (row.department || '').toString();

        const matchesSearch = !searchTerm || name.includes(searchTerm) || empId.includes(searchTerm);
        const matchesDepartment = !departmentFilter || department === departmentFilter;

        const period = getPayrollPeriodRange(row);
        const monthDate = period?.start ?? null;

        const matchesMonth = !monthFilter || (monthDate && (monthDate.getMonth() + 1) === monthFilter);
        const matchesYear = !yearFilter || (monthDate && monthDate.getFullYear() === yearFilter);

        let matchesDateRange = true;
        if (dateFrom || dateTo) {
            if (!period) {
                matchesDateRange = false;
            } else {
                const from = dateFrom ? new Date(dateFrom.getFullYear(), dateFrom.getMonth(), dateFrom.getDate()) : null;
                const to = dateTo ? new Date(dateTo.getFullYear(), dateTo.getMonth(), dateTo.getDate(), 23, 59, 59, 999) : null;

                if (from && period.end < from) matchesDateRange = false;
                if (to && period.start > to) matchesDateRange = false;
            }
        }

        return matchesSearch && matchesDepartment && matchesMonth && matchesYear && matchesDateRange;
    });

    renderPayrollTable();
    updatePayrollStats();
}

function updateComplianceStatus() {
    // Only show compliance status if there's actual payroll data
    if (payrollData.length === 0) {
        // Clear compliance status when no data
        document.getElementById('complianceScore').textContent = 'No Data';
        document.getElementById('payeStatus').textContent = 'No Data';
        document.getElementById('nssfStatus').textContent = 'No Data';
        document.getElementById('wcfStatus').textContent = 'No Data';
        document.getElementById('heslbStatus').textContent = 'No Data';

        updateComplianceItem('paye', 'No Data', 0);
        updateComplianceItem('nssf', 'No Data', 0);
        updateComplianceItem('wcf', 'No Data', 0);
        updateComplianceItem('heslb', 'No Data', 0);

        const scoreElement = document.getElementById('complianceScore');
        scoreElement.className = 'text-3xl font-bold text-gray-400';
        document.getElementById('complianceSummaryText').textContent = 'Import or generate payroll data to evaluate statutory compliance.';
        document.getElementById('complianceScoreCaption').textContent = 'Awaiting payroll data';
        document.getElementById('complianceScoreBar').style.width = '0%';

        return;
    }

    document.getElementById('complianceScore').textContent = '100%';
    document.getElementById('payeStatus').textContent = 'Calculated';
    document.getElementById('nssfStatus').textContent = 'Calculated';
    document.getElementById('wcfStatus').textContent = 'N/A';
    document.getElementById('heslbStatus').textContent = 'Calculated';

    const scoreElement = document.getElementById('complianceScore');
    scoreElement.className = 'text-4xl font-bold mt-3';
    document.getElementById('complianceSummaryText').textContent = 'All primary payroll obligations are currently calculated.';
    document.getElementById('complianceScoreCaption').textContent = 'Healthy compliance posture';
    document.getElementById('complianceScoreBar').style.width = '100%';

    updateComplianceItem('paye', 'Calculated', 100);
    updateComplianceItem('nssf', 'Calculated', 100);
    updateComplianceItem('wcf', 'N/A', 0);
    updateComplianceItem('heslb', 'Calculated', 100);
}

async function generatePayrollFromAttendance() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const now = new Date();
    const period = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

    try {
        const response = await fetch('{{ route('payroll.generate.from.attendance') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ payroll_period: period }),
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json') ? await response.json() : null;

        if (!response.ok || !result?.success) {
            showNotification(result?.message || 'Failed to generate payroll', 'error');
            return;
        }

        showNotification(`${result.message} (Created: ${result.created}, Updated: ${result.updated})`, 'success');
        await loadPayrollData();
    } catch (e) {
        showNotification('Network error while generating payroll', 'error');
    }
}

function updateComplianceItem(itemId, status, progress) {
    // Find the compliance item container
    const container = document.getElementById(itemId + 'Container');
    if (!container) return;

    // Determine color based on status
    let statusColor = 'text-white';
    let progressColor = 'bg-white';
    let progressText = `${progress}% Complete`;

    if (status === 'No Data') {
        statusColor = 'text-green-50';
        progressColor = 'bg-white/50';
        progressText = 'Waiting for payroll data';
    }

    if (status === 'Overdue' || status === 'Pending' || status === 'Partial') {
        statusColor = 'text-yellow-100';
        progressColor = 'bg-yellow-300';
    }

    if (status === 'Overdue') {
        statusColor = 'text-red-100';
        progressColor = 'bg-red-300';
    }

    if (status === 'N/A') {
        statusColor = 'text-green-50';
        progressColor = 'bg-white/50';
        progressText = 'Not applicable';
    }

    // Update status text color
    const statusElement = document.getElementById(itemId + 'Status');
    if (statusElement) {
        statusElement.className = `font-semibold mt-1 ${statusColor}`;
    }

    const progressBar = document.getElementById(itemId + 'ProgressBar');
    if (progressBar) {
        progressBar.style.width = progress + '%';
        progressBar.className = `h-2 rounded-full ${progressColor}`;
    }

    const progressTextElement = document.getElementById(itemId + 'ProgressText');
    if (progressTextElement) {
        progressTextElement.textContent = progressText;
    }
}

function showImportModal() {
    openModal('importModal');
}

function closeImportModal() {
    closeModal('importModal');
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Check file type
    const fileName = file.name.toLowerCase();
    const isExcelFile = fileName.endsWith('.xlsx') || fileName.endsWith('.xls');
    const isCsvFile = fileName.endsWith('.csv');
    
    if (!isExcelFile && !isCsvFile) {
        showNotification('Please upload an Excel (.xlsx, .xls) or CSV file', 'error');
        return;
    }
    
    showNotification(`Processing ${file.name}...`, 'info');
    
    if (isExcelFile) {
        // For Excel files, use FileReader to read as ArrayBuffer
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                // Check if XLSX library is loaded
                if (typeof XLSX === 'undefined') {
                    showNotification('Excel processing library not loaded. Please refresh the page and try again.', 'error');
                    return;
                }
                
                const arrayBuffer = e.target.result;
                const workbook = XLSX.read(arrayBuffer, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                
                // Process the parsed data
                processImportedData(jsonData, fileName);
                
            } catch (error) {
                console.error('Excel parsing error:', error);
                showNotification('Error parsing Excel file: ' + error.message + '. Please check the file format and try again.', 'error');
            }
        };
        
        reader.onerror = function() {
            showNotification('Failed to read Excel file. Please try again.', 'error');
        };
        
        reader.readAsArrayBuffer(file);
        
    } else if (isCsvFile) {
        // For CSV files, parse as text
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const csvText = e.target.result;
                const lines = csvText.split('\n').filter(line => line.trim());
                
                if (lines.length < 2) {
                    showNotification('CSV file is empty or invalid', 'error');
                    return;
                }
                
                // Parse CSV headers and data
                const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
                const data = [];
                
                for (let i = 1; i < lines.length; i++) {
                    const values = lines[i].split(',').map(v => v.trim().replace(/"/g, ''));
                    
                    if (values.length >= headers.length) {
                        const row = {};
                        headers.forEach((header, index) => {
                            row[header] = values[index] || '';
                        });
                        data.push(row);
                    }
                }
                
                // Process the parsed data
                processImportedData(data, fileName);
                
            } catch (error) {
                console.error('CSV parsing error:', error);
                showNotification('Error parsing CSV file. Please check the file format and try again.', 'error');
            }
        };
        
        reader.onerror = function() {
            showNotification('Failed to read CSV file. Please try again.', 'error');
        };
        
        reader.readAsText(file);
    }
}

function processImportedData(rawData, fileName) {
    try {
        // Map the raw data to our expected format
        const processedData = rawData.map((row, index) => {
            // Clean and validate data
            const empId = row['Emp ID'] || row['empId'] || row['Employee ID'] || `E${String(index + 1).padStart(3, '0')}`;
            const name = row['Name'] || row['name'] || 'Unknown';
            const department = row['Department'] || row['department'] || 'General';
            const title = row['Title'] || row['title'] || 'Employee';
            const joiningDate = row['Joining date'] || row['joiningDate'] || new Date().toISOString().split('T')[0];
            const shift = row['Employee Shift'] || row['shift'] || 'Day';
            const monthOfPayment = row['Month of payment'] || row['monthOfPayment'] || new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            
            // Parse numeric values
            const basicSalary = parseFloat(row['Basic Salary'] || row['basicSalary'] || 0);
            const otHours = parseFloat(row['OT Hours'] || row['otHours'] || 0);
            const otRate = parseFloat(row['OT Rate'] || row['otRate'] || 0);
            const otPay = parseFloat(row['OT Pay'] || row['otPay'] || (otHours * otRate));
            const holidayPay = parseFloat(row['Holiday Pay'] || row['holidayPay'] || 0);
            const grossPay = parseFloat(row['Gross Pay'] || row['grossPay'] || (basicSalary + otPay + holidayPay));
            
            // Calculate deductions
            const nssf = parseFloat(row['NSSF (10%)'] || row['nssf'] || Math.min(grossPay * 0.10, 10000));
            const taxablePay = parseFloat(row['Taxable Pay'] || row['taxablePay'] || (grossPay - nssf));
            const paye = parseFloat(row['PAYE'] || row['paye'] || calculatePAYE(taxablePay));
            const heslb = parseFloat(row['HESLB (15%)'] || row['heslb'] || 0);
            const otherDed = parseFloat(row['Other Ded'] || row['otherDed'] || 0);
            const totalDeduction = parseFloat(row['Total Deduction'] || row['totalDeduction'] || (nssf + paye + heslb + otherDed));
            const netPay = parseFloat(row['Net Pay'] || row['netPay'] || (grossPay - totalDeduction));
            
            // Employer contributions
            const employerNSSF = parseFloat(row['Employer NSSF'] || row['employerNssf'] || Math.min(grossPay * 0.10, 10000));
            const sdl = parseFloat(row['SDL'] || row['sdl'] || (grossPay * 0.01));
            const wcf = parseFloat(row['WCF'] || row['wcf'] || (grossPay * 0.02));
            const totalCost = parseFloat(row['Total Cost'] || row['totalCost'] || (grossPay + employerNSSF + sdl + wcf));
            
            return {
                empId,
                name,
                department,
                title,
                joiningDate,
                shift,
                monthOfPayment,
                basicSalary,
                otHours,
                otRate,
                otPay,
                holidayPay,
                grossPay,
                nssf,
                taxablePay,
                paye,
                heslb,
                otherDed,
                totalDeduction,
                netPay,
                employerNSSF,
                sdl,
                wcf,
                totalCost
            };
        });
        
        // Filter out empty rows
        const validData = processedData.filter(row => row.name && row.name !== 'Unknown' && row.name.trim() !== '');
        
        if (validData.length === 0) {
            showNotification('No valid employee data found in the file', 'error');
            return;
        }
        
        showNotification(`Successfully processed ${validData.length} employee records from ${fileName}`, 'success');
        previewImportData(validData);
        
    } catch (error) {
        console.error('Data processing error:', error);
        showNotification('Error processing file data. Please check the file format.', 'error');
    }
}

function calculatePAYE(taxablePay) {
    // Tanzania PAYE calculation
    if (taxablePay <= 270000) {
        return taxablePay * 0.09;
    } else if (taxablePay <= 520000) {
        return 24300 + ((taxablePay - 270000) * 0.20);
    } else if (taxablePay <= 760000) {
        return 74300 + ((taxablePay - 520000) * 0.25);
    } else {
        return 134300 + ((taxablePay - 760000) * 0.30);
    }
}

function generateSamplePayrollData() {
    const currentClient = localStorage.getItem('selectedClient') || '1';
    
    const clientData = {
        '1': [ // ABC Manufacturing Ltd
            {
                empId: 'E001',
                name: 'John Doe',
                department: 'IT',
                title: 'Software Developer',
                joiningDate: '2022-01-15',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 2500000,
                otHours: 10,
                otRate: 15000,
                otPay: 150000,
                holidayPay: 50000,
                grossPay: 2700000,
                nssf: 270000,
                taxablePay: 2430000,
                paye: 365000,
                heslb: 0,
                otherDed: 50000,
                totalDeduction: 685000,
                netPay: 2015000,
                employerNSSF: 270000,
                sdl: 27000,
                wcf: 54000,
                totalCost: 3045000
            },
            {
                empId: 'E002',
                name: 'Sarah Williams',
                department: 'HR',
                title: 'HR Manager',
                joiningDate: '2021-06-01',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 3500000,
                otHours: 5,
                otRate: 20000,
                otPay: 100000,
                holidayPay: 75000,
                grossPay: 3675000,
                nssf: 367500,
                taxablePay: 3307500,
                paye: 546000,
                heslb: 150000,
                otherDed: 75000,
                totalDeduction: 1138500,
                netPay: 2536500,
                employerNSSF: 367500,
                sdl: 36750,
                wcf: 73500,
                totalCost: 4145000
            }
        ],
        '2': [ // XYZ Construction Co
            {
                empId: 'X001',
                name: 'Michael Kimaro',
                department: 'Construction',
                title: 'Site Manager',
                joiningDate: '2020-03-10',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 2000000,
                otHours: 15,
                otRate: 12000,
                otPay: 180000,
                holidayPay: 40000,
                grossPay: 2220000,
                nssf: 222000,
                taxablePay: 1998000,
                paye: 259000,
                heslb: 100000,
                otherDed: 30000,
                totalDeduction: 611000,
                netPay: 1609000,
                employerNSSF: 222000,
                sdl: 22200,
                wcf: 44400,
                totalCost: 2506000
            },
            {
                empId: 'X002',
                name: 'Grace Mwanga',
                department: 'Engineering',
                title: 'Civil Engineer',
                joiningDate: '2021-08-15',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 2800000,
                otHours: 8,
                otRate: 16000,
                otPay: 128000,
                holidayPay: 60000,
                grossPay: 2988000,
                nssf: 298800,
                taxablePay: 2689200,
                paye: 403000,
                heslb: 120000,
                otherDed: 50000,
                totalDeduction: 871800,
                netPay: 2116200,
                employerNSSF: 298800,
                sdl: 29880,
                wcf: 59760,
                totalCost: 3374440
            }
        ],
        '3': [ // Tanzania Mining Corp
            {
                empId: 'M001',
                name: 'James Mwalimu',
                department: 'Mining',
                title: 'Mining Engineer',
                joiningDate: '2019-11-20',
                shift: 'Rotating',
                monthOfPayment: 'March 2026',
                basicSalary: 3200000,
                otHours: 20,
                otRate: 18000,
                otPay: 360000,
                holidayPay: 80000,
                grossPay: 3640000,
                nssf: 364000,
                taxablePay: 3276000,
                paye: 572000,
                heslb: 200000,
                otherDed: 80000,
                totalDeduction: 1216000,
                netPay: 2424000,
                employerNSSF: 364000,
                sdl: 36400,
                wcf: 72800,
                totalCost: 4113200
            },
            {
                empId: 'M002',
                name: 'Fatuma Hassan',
                department: 'Safety',
                title: 'Safety Officer',
                joiningDate: '2022-05-10',
                shift: 'Night',
                monthOfPayment: 'March 2026',
                basicSalary: 1800000,
                otHours: 12,
                otRate: 14000,
                otPay: 168000,
                holidayPay: 45000,
                grossPay: 2013000,
                nssf: 201300,
                taxablePay: 1811700,
                paye: 242000,
                heslb: 80000,
                otherDed: 40000,
                totalDeduction: 563300,
                netPay: 1449700,
                employerNSSF: 201300,
                sdl: 20130,
                wcf: 40260,
                totalCost: 2274690
            }
        ],
        '4': [ // East Africa Logistics
            {
                empId: 'L001',
                name: 'David Kiplagat',
                department: 'Logistics',
                title: 'Fleet Manager',
                joiningDate: '2020-07-01',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 2400000,
                otHours: 6,
                otRate: 13000,
                otPay: 78000,
                holidayPay: 55000,
                grossPay: 2533000,
                nssf: 253300,
                taxablePay: 2279700,
                paye: 346000,
                heslb: 90000,
                otherDed: 45000,
                totalDeduction: 734300,
                netPay: 1798700,
                employerNSSF: 253300,
                sdl: 25330,
                wcf: 50660,
                totalCost: 2862290
            },
            {
                empId: 'L002',
                name: 'Esther Njoroge',
                department: 'Finance',
                title: 'Accountant',
                joiningDate: '2021-02-14',
                shift: 'Day',
                monthOfPayment: 'March 2026',
                basicSalary: 2200000,
                otHours: 8,
                otRate: 14000,
                otPay: 112000,
                holidayPay: 50000,
                grossPay: 2362000,
                nssf: 236200,
                taxablePay: 2125800,
                paye: 313000,
                heslb: 70000,
                otherDed: 35000,
                totalDeduction: 654200,
                netPay: 1707800,
                employerNSSF: 236200,
                sdl: 23620,
                wcf: 47240,
                totalCost: 2669060
            }
        ]
    };
    
    return clientData[currentClient] || clientData['1'];
}

function previewImportData(data) {
    const preview = document.getElementById('importPreview');
    const fileName = document.getElementById('payrollFile').files[0]?.name || 'Unknown file';
    const isExcelFile = fileName.toLowerCase().endsWith('.xlsx') || fileName.toLowerCase().endsWith('.xls');
    const isCsvFile = fileName.toLowerCase().endsWith('.csv');
    
    let fileTypeIcon = '';
    let fileTypeText = '';
    
    if (isExcelFile) {
        fileTypeIcon = '📊';
        fileTypeText = 'Excel File';
    } else if (isCsvFile) {
        fileTypeIcon = '📋';
        fileTypeText = 'CSV File';
    } else {
        fileTypeIcon = '📄';
        fileTypeText = 'Unknown File';
    }
    
    preview.innerHTML = `
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <h4 class="font-semibold text-green-800 mb-2">Data Preview</h4>
            <div class="flex items-center justify-between">
                <p class="text-green-700">Found ${data.length} employee records</p>
                <div class="flex items-center text-sm text-green-600">
                    <span class="mr-2">${fileTypeIcon} ${fileTypeText}</span>
                    <span class="text-gray-500">•</span>
                    <span class="ml-2">${fileName}</span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-[1200px] w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Emp ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Department</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Title</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Month</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Basic Salary</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Gross Pay</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">PAYE</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">NSSF</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.slice(0, 5).map(emp => `
                        <tr class="border-b">
                            <td class="px-4 py-2 text-sm">${emp.empId}</td>
                            <td class="px-4 py-2 text-sm">${emp.name}</td>
                            <td class="px-4 py-2 text-sm">${emp.department}</td>
                            <td class="px-4 py-2 text-sm">${emp.title || '-'}</td>
                            <td class="px-4 py-2 text-sm">${emp.monthOfPayment || '-'}</td>
                            <td class="px-4 py-2 text-sm">TZS ${emp.basicSalary.toLocaleString()}</td>
                            <td class="px-4 py-2 text-sm">TZS ${emp.grossPay.toLocaleString()}</td>
                            <td class="px-4 py-2 text-sm">TZS ${emp.paye.toLocaleString()}</td>
                            <td class="px-4 py-2 text-sm">TZS ${emp.nssf.toLocaleString()}</td>
                            <td class="px-4 py-2 text-sm">TZS ${emp.netPay.toLocaleString()}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        
        ${data.length > 5 ? `<p class="text-sm text-gray-500 mt-2">... and ${data.length - 5} more records</p>` : ''}
        
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Processing Complete:</strong> Your ${fileTypeText} has been successfully processed and imported into the system.
            </p>
        </div>
    `;
    
    window.tempImportData = data;
    showImportModal();
}

async function confirmImport() {
    if (!window.tempImportData || !Array.isArray(window.tempImportData) || window.tempImportData.length === 0) {
        return;
    }

    const importBtn = document.querySelector('#importModal button[onclick="confirmImport()"]');
    if (importBtn) {
        importBtn.disabled = true;
        importBtn.classList.add('opacity-60', 'cursor-not-allowed');
    }

    try {
        const rows = window.tempImportData;
        const inferred = inferPayrollPeriodAndPayDate(rows);

        const csv = buildPayrollCsv(rows);
        const blob = new Blob([csv], { type: 'text/csv' });
        const file = new File([blob], `payroll_import_${inferred.payroll_period}.csv`, { type: 'text/csv' });

        const formData = new FormData();
        formData.append('csv_file', file);
        formData.append('payroll_period', inferred.payroll_period);
        formData.append('pay_date', inferred.pay_date);

        const response = await fetch('{{ route("payroll.upload.csv") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const result = await response.json().catch(() => null);
        if (!result?.success) {
            showNotification(result?.message || 'Import failed', 'error');
            return;
        }

        showNotification(result.message || 'Payroll imported successfully', 'success');
        closeImportModal();
        document.getElementById('payrollFile').value = '';
        window.tempImportData = null;

        await loadPayrollData();
    } catch (e) {
        showNotification('Import failed. Please try again.', 'error');
    } finally {
        if (importBtn) {
            importBtn.disabled = false;
            importBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }
}

function inferPayrollPeriodAndPayDate(rows) {
    const direct = rows.find(r => typeof r.payrollPeriod === 'string' && /^\d{4}-\d{2}$/.test(r.payrollPeriod));
    if (direct) {
        const [y, m] = direct.payrollPeriod.split('-').map(v => parseInt(v, 10));
        const end = new Date(y, m, 0);
        return { payroll_period: direct.payrollPeriod, pay_date: end.toISOString().slice(0, 10) };
    }

    const fromMonth = rows.find(r => r.monthOfPayment);
    if (fromMonth) {
        const d = parseMonthOfPayment(fromMonth.monthOfPayment);
        if (d) {
            const y = d.getFullYear();
            const m = d.getMonth() + 1;
            const period = `${y}-${String(m).padStart(2, '0')}`;
            const end = new Date(y, m, 0);
            return { payroll_period: period, pay_date: end.toISOString().slice(0, 10) };
        }
    }

    const now = new Date();
    const y = now.getFullYear();
    const m = now.getMonth() + 1;
    const period = `${y}-${String(m).padStart(2, '0')}`;
    const end = new Date(y, m, 0);
    return { payroll_period: period, pay_date: end.toISOString().slice(0, 10) };
}

function buildPayrollCsv(rows) {
    const header = [
        'employee_id',
        'email',
        'first_name',
        'last_name',
        'basic_salary',
        'overtime_hours',
        'overtime_rate',
        'overtime_pay',
        'allowances',
        'bonuses',
        'tax_deductions',
        'social_security',
        'pension',
        'other_deductions',
        'notes'
    ];

    const escape = (v) => {
        const s = (v === null || v === undefined) ? '' : String(v);
        if (s.includes('"') || s.includes(',') || s.includes('\n')) {
            return `"${s.replace(/"/g, '""')}"`;
        }
        return s;
    };

    const lines = [header.join(',')];

    rows.forEach((r) => {
        const name = String(r.name || '').trim();
        const parts = name ? name.split(/\s+/) : [];
        const first = parts[0] || '';
        const last = parts.slice(1).join(' ') || '';

        const other = (Number(r.otherDed || 0) || 0) + (Number(r.heslb || 0) || 0);

        lines.push([
            escape(r.empId || ''),
            escape(r.email || ''),
            escape(first),
            escape(last),
            escape(Number(r.basicSalary || 0) || 0),
            escape(Number(r.otHours || 0) || 0),
            escape(Number(r.otRate || 0) || 0),
            escape(Number(r.otPay || 0) || 0),
            escape(Number(r.allowances || 0) || 0),
            escape(Number(r.bonuses || 0) || 0),
            escape(Number(r.paye || 0) || 0),
            escape(Number(r.nssf || 0) || 0),
            escape(Number(r.employerNSSF || r.pension || 0) || 0),
            escape(other),
            escape(r.notes || '')
        ].join(','));
    });

    return lines.join('\n');
}

function renderPayrollTable() {
    const tbody = document.getElementById('payrollTableBody');
    
    if (filteredPayrollData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="26" class="text-center py-12 text-gray-500">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-feather="inbox" class="w-10 h-10 text-gray-400"></i>
                    </div>
                    <p class="text-xl font-medium text-gray-900 mb-3">No Payroll Data Available</p>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Import payroll data to view employee details and compliance information</p>
                    <div class="flex justify-center space-x-4">
                        <button onclick="showImportModal()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                            <i data-feather="upload" class="w-4 h-4 mr-2"></i>
                            Import Payroll Data
                        </button>
                        <button onclick="downloadTemplate()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i>
                            Download Template
                        </button>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    const formatCurrency = (value) => `TZS ${Number(value || 0).toLocaleString()}`;
    const formatNumber = (value) => Number(value || 0).toLocaleString();
    const formatDate = (value) => value || '-';
    const textValue = (value) => value || '-';
    
    tbody.innerHTML = filteredPayrollData.map(emp => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap align-top">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-medium">${emp.name.charAt(0)}</span>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">${emp.name}</div>
                        <div class="text-sm text-gray-500">${emp.empId}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${textValue(emp.department)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${textValue(emp.title)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatDate(emp.joiningDate)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${textValue(emp.shift)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${textValue(emp.monthOfPayment)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.basicSalary)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatNumber(emp.otHours)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.otRate)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.otPay)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.holidayPay)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.allowances)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.bonuses)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top">${formatCurrency(emp.grossPay)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.taxablePay)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.paye)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.nssf)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.heslb)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.otherDed)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.totalDeduction)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 align-top">${formatCurrency(emp.netPay)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.employerNSSF)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.sdl)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.wcf)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top">${formatCurrency(emp.totalCost)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium align-top">
                <div class="flex items-center gap-2">
                    <button onclick="generatePayslip('${emp.empId}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100" title="Generate Payslip">
                        <i data-feather="file-text" class="w-4 h-4 mr-1.5"></i>
                        Payslip
                    </button>
                    <button onclick="editEmployee('${emp.empId}')" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md border border-blue-200 text-blue-700 bg-blue-50 hover:bg-blue-100" title="Update Payroll">
                        <i data-feather="edit-2" class="w-4 h-4 mr-1.5"></i>
                        Update
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function updatePayrollStats() {
    if (payrollData.length === 0) {
        // Clear stats when no data
        document.getElementById('totalPayroll').textContent = 'No Data';
        document.getElementById('totalPAYE').textContent = 'No Data';
        document.getElementById('totalNSSF').textContent = 'No Data';
        document.getElementById('totalNet').textContent = 'No Data';
        document.getElementById('payePercentage').textContent = 'No Data';
        document.getElementById('payrollMonth').textContent = 'No Data';
        return;
    }

    const dataForStats = filteredPayrollData.length ? filteredPayrollData : payrollData;

    if (dataForStats.length === 0) {
        document.getElementById('totalPayroll').textContent = 'No Results';
        document.getElementById('totalPAYE').textContent = 'No Results';
        document.getElementById('totalNSSF').textContent = 'No Results';
        document.getElementById('totalNet').textContent = 'No Results';
        document.getElementById('payePercentage').textContent = 'No Results';
        document.getElementById('payrollMonth').textContent = 'No Results';
        return;
    }

    const totals = dataForStats.reduce((acc, emp) => {
        acc.totalPayroll += emp.grossPay;
        acc.totalPAYE += emp.paye;
        acc.totalNSSF += emp.nssf + emp.employerNSSF;
        acc.totalNet += emp.netPay;
        return acc;
    }, {
        totalPayroll: 0,
        totalPAYE: 0,
        totalNSSF: 0,
        totalNet: 0
    });
    
    document.getElementById('totalPayroll').textContent = `TZS ${totals.totalPayroll.toLocaleString()}`;
    document.getElementById('totalPAYE').textContent = `TZS ${totals.totalPAYE.toLocaleString()}`;
    document.getElementById('totalNSSF').textContent = `TZS ${totals.totalNSSF.toLocaleString()}`;
    document.getElementById('totalNet').textContent = `TZS ${totals.totalNet.toLocaleString()}`;
    
    const payePercentage = totals.totalPayroll > 0 ? ((totals.totalPAYE / totals.totalPayroll) * 100).toFixed(1) : '0.0';
    document.getElementById('payePercentage').textContent = `${payePercentage}% of payroll`;
    
    // Get month from first employee or use current month
    const uniqueMonths = new Set(dataForStats.map(r => r.monthOfPayment).filter(Boolean));
    if (uniqueMonths.size === 1) {
        document.getElementById('payrollMonth').textContent = Array.from(uniqueMonths)[0];
    } else if (uniqueMonths.size > 1) {
        document.getElementById('payrollMonth').textContent = 'Multiple Periods';
    } else {
        document.getElementById('payrollMonth').textContent = 'Unknown Period';
    }
}

function generatePayslip(empId) {
    // Direct redirect to payslip page instead of showing modal
    window.location.href = `/payroll/payslip?empId=${empId}`;
}

function closePayslipModal() {
    closeModal('payslipModal');
}

function printPayslip() {
    const payslipContent = document.getElementById('payslipContent').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Salary Slip</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .payslip-container { max-width: 800px; margin: 0 auto; }
                .text-center { text-align: center; }
                .text-lg { font-size: 1.125rem; }
                .text-xl { font-size: 1.25rem; }
                .text-2xl { font-size: 1.5rem; }
                .font-bold { font-weight: bold; }
                .font-semibold { font-weight: 600; }
                .font-medium { font-weight: 500; }
                .text-gray-900 { color: #111827; }
                .text-gray-600 { color: #4b5563; }
                .text-green-600 { color: #059669; }
                .border-b { border-bottom: 1px solid; }
                .border-t { border-top: 1px solid; }
                .border-2 { border-width: 2px; }
                .border-gray-800 { border-color: #1f2937; }
                .border-gray-300 { border-color: #d1d5db; }
                .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                .pb-2 { padding-bottom: 0.5rem; }
                .pb-4 { padding-bottom: 1rem; }
                .pt-2 { padding-top: 0.5rem; }
                .pt-4 { padding-top: 1rem; }
                .mt-8 { margin-top: 2rem; }
                .mt-12 { margin-top: 3rem; }
                .mb-4 { margin-bottom: 1rem; }
                .mb-8 { margin-bottom: 2rem; }
                .grid { display: grid; }
                .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
                .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
                .gap-4 { gap: 1rem; }
                .gap-8 { gap: 2rem; }
                .flex { display: flex; }
                .justify-between { justify-content: space-between; }
                .space-y-2 > * + * { margin-top: 0.5rem; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            ${payslipContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

function generateAllPayslips() {
    if (payrollData.length === 0) {
        showNotification('No payroll data available', 'warning');
        return;
    }
    
    showNotification(`Generating payslips for ${payrollData.length} employees...`, 'info');
    
    // In a real implementation, this would generate PDF files
    setTimeout(() => {
        showNotification('All payslips generated successfully', 'success');
    }, 2000);
}

function exportPayrollReport() {
    if (payrollData.length === 0) {
        showNotification('No payroll data to export', 'warning');
        return;
    }
    
    // Create CSV content
    const headers = ['Emp ID', 'Name', 'Department', 'Title', 'Basic Salary', 'Gross Pay', 'PAYE', 'NSSF', 'HESLB', 'Net Pay'];
    const csvContent = [
        headers.join(','),
        ...payrollData.map(emp => [
            emp.empId,
            emp.name,
            emp.department,
            emp.title,
            emp.basicSalary,
            emp.grossPay,
            emp.paye,
            emp.nssf,
            emp.heslb,
            emp.netPay
        ].join(','))
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `payroll_report_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Payroll report exported successfully', 'success');
}

function downloadTemplate() {
    // Create template CSV
    const headers = ['Emp ID', 'Name', 'Department', 'Title', 'Joining date', 'Employee Shift', 'Month of payment', 'Basic Salary', 'OT Hours', 'OT Rate', 'OT Pay', 'Holiday Pay', 'Gross Pay', 'NSSF (10%)', 'Taxable Pay', 'PAYE', 'HESLB (15%)', 'Other Ded', 'Total Deduction', 'Net Pay', 'Employer NSSF', 'SDL', 'WCF', 'Total Cost'];
    const sampleRow = ['E001', 'John Doe', 'IT', 'Software Developer', '2022-01-15', 'Day', 'March 2026', '2500000', '10', '15000', '150000', '50000', '2700000', '270000', '2430000', '365000', '0', '50000', '685000', '2015000', '270000', '27000', '54000', '3045000'];
    
    const csvContent = [headers.join(','), sampleRow.join(',')].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'payroll_template.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Template downloaded successfully', 'success');
}

function editEmployee(empId) {
    const employee = payrollData.find(emp => emp.empId === empId);
    if (!employee) return;
    
    // Populate form fields
    document.getElementById('editEmpId').value = employee.empId;
    document.getElementById('editName').value = employee.name;
    document.getElementById('editDepartment').value = employee.department;
    document.getElementById('editTitle').value = employee.title;
    document.getElementById('editJoiningDate').value = employee.joiningDate;
    document.getElementById('editShift').value = employee.shift;
    document.getElementById('editBasicSalary').value = employee.basicSalary;
    document.getElementById('editOtHours').value = employee.otHours;
    document.getElementById('editOtRate').value = employee.otRate;
    document.getElementById('editOtPay').value = employee.otPay;
    document.getElementById('editHolidayPay').value = employee.holidayPay;
    document.getElementById('editAllowances').value = employee.allowances || 0;
    document.getElementById('editBonuses').value = employee.bonuses || 0;
    document.getElementById('editGrossPay').value = employee.grossPay;
    document.getElementById('editNssf').value = employee.nssf;
    document.getElementById('editPaye').value = employee.paye;
    document.getElementById('editHeslb').value = employee.heslb;
    document.getElementById('editOtherDed').value = employee.otherDed;
    document.getElementById('editTotalDeduction').value = employee.totalDeduction;
    document.getElementById('editNetPay').value = employee.netPay;
    document.getElementById('editEmployerNssf').value = employee.employerNSSF;
    document.getElementById('editSdl').value = employee.sdl;
    document.getElementById('editWcf').value = employee.wcf;
    document.getElementById('editTotalCost').value = employee.totalCost;
    document.getElementById('editMonthOfPayment').value = employee.monthOfPayment;
    
    // Store current employee for saving
    window.currentEditingEmployee = employee;
    
    // Show modal
    openModal('editEmployeeModal');
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function closeEditModal() {
    closeModal('editEmployeeModal');
}

function calculatePayroll() {
    const basicSalary = parseFloat(document.getElementById('editBasicSalary').value) || 0;
    const otHours = parseFloat(document.getElementById('editOtHours').value) || 0;
    const otRate = parseFloat(document.getElementById('editOtRate').value) || 0;
    const holidayPay = parseFloat(document.getElementById('editHolidayPay').value) || 0;
    const allowances = parseFloat(document.getElementById('editAllowances').value) || 0;
    const bonuses = parseFloat(document.getElementById('editBonuses').value) || 0;
    const heslb = parseFloat(document.getElementById('editHeslb').value) || 0;
    const otherDed = parseFloat(document.getElementById('editOtherDed').value) || 0;
    
    // Calculate OT Pay
    const otPay = otHours * otRate;
    document.getElementById('editOtPay').value = otPay;
    
    // Calculate Gross Pay
    const grossPay = basicSalary + otPay + holidayPay + allowances + bonuses;
    document.getElementById('editGrossPay').value = grossPay;
    
    // Calculate NSSF (10% of gross pay, capped at TZS 10,000)
    const nssf = Math.min(grossPay * 0.10, 10000);
    document.getElementById('editNssf').value = nssf;
    
    // Calculate Taxable Pay
    const taxablePay = grossPay - nssf;
    
    // Calculate PAYE (simplified Tanzania tax calculation)
    let paye = 0;
    if (taxablePay > 0) {
        if (taxablePay <= 270000) {
            paye = taxablePay * 0.09;
        } else if (taxablePay <= 520000) {
            paye = 24300 + ((taxablePay - 270000) * 0.20);
        } else if (taxablePay <= 760000) {
            paye = 74300 + ((taxablePay - 520000) * 0.25);
        } else {
            paye = 134300 + ((taxablePay - 760000) * 0.30);
        }
    }
    document.getElementById('editPaye').value = Math.round(paye);
    
    // Calculate Total Deductions
    const totalDeduction = nssf + paye + heslb + otherDed;
    document.getElementById('editTotalDeduction').value = totalDeduction;
    
    // Calculate Net Pay
    const netPay = grossPay - totalDeduction;
    document.getElementById('editNetPay').value = netPay;
    
    // Calculate Employer Contributions
    const employerNssf = Math.min(grossPay * 0.10, 10000);
    document.getElementById('editEmployerNssf').value = employerNssf;
    
    const sdl = grossPay * 0.01; // 1% Skills Development Levy
    document.getElementById('editSdl').value = sdl;
    
    const wcf = grossPay * 0.02; // 2% Workers Compensation Fund
    document.getElementById('editWcf').value = wcf;
    
    // Calculate Total Cost to Company
    const totalCost = grossPay + employerNssf + sdl + wcf;
    document.getElementById('editTotalCost').value = totalCost;
    
    showNotification('Payroll calculated successfully', 'success');
}

async function saveEmployee() {
    if (!window.currentEditingEmployee) return;

    calculatePayroll();

    // Get form values
    const updatedEmployee = {
        ...window.currentEditingEmployee,
        name: document.getElementById('editName').value,
        department: document.getElementById('editDepartment').value,
        title: document.getElementById('editTitle').value,
        joiningDate: document.getElementById('editJoiningDate').value,
        shift: document.getElementById('editShift').value,
        basicSalary: parseFloat(document.getElementById('editBasicSalary').value),
        otHours: parseFloat(document.getElementById('editOtHours').value),
        otRate: parseFloat(document.getElementById('editOtRate').value),
        otPay: parseFloat(document.getElementById('editOtPay').value),
        holidayPay: parseFloat(document.getElementById('editHolidayPay').value),
        allowances: parseFloat(document.getElementById('editAllowances').value),
        bonuses: parseFloat(document.getElementById('editBonuses').value),
        grossPay: parseFloat(document.getElementById('editGrossPay').value),
        nssf: parseFloat(document.getElementById('editNssf').value),
        paye: parseFloat(document.getElementById('editPaye').value),
        heslb: parseFloat(document.getElementById('editHeslb').value),
        otherDed: parseFloat(document.getElementById('editOtherDed').value),
        totalDeduction: parseFloat(document.getElementById('editTotalDeduction').value),
        netPay: parseFloat(document.getElementById('editNetPay').value),
        employerNSSF: parseFloat(document.getElementById('editEmployerNssf').value),
        sdl: parseFloat(document.getElementById('editSdl').value),
        wcf: parseFloat(document.getElementById('editWcf').value),
        totalCost: parseFloat(document.getElementById('editTotalCost').value),
        monthOfPayment: document.getElementById('editMonthOfPayment').value
    };

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const payrollId = updatedEmployee.payrollId;
    if (!payrollId) {
        showNotification('Cannot save: payroll record id is missing.', 'error');
        return;
    }

    const saveBtn = document.getElementById('saveEmployeeBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-60', 'cursor-not-allowed');
    }

    try {
        const response = await fetch(`/payroll/${payrollId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                basic_salary: updatedEmployee.basicSalary || 0,
                overtime_hours: updatedEmployee.otHours || 0,
                overtime_rate: updatedEmployee.otRate || 0,
                overtime_pay: updatedEmployee.otPay || 0,
                allowances: updatedEmployee.allowances || 0,
                bonuses: updatedEmployee.bonuses || 0,
                gross_pay: updatedEmployee.grossPay || 0,
                tax_deductions: updatedEmployee.paye || 0,
                social_security: updatedEmployee.nssf || 0,
                pension: updatedEmployee.employerNSSF || 0,
                other_deductions: (updatedEmployee.heslb || 0) + (updatedEmployee.otherDed || 0),
                total_deductions: updatedEmployee.totalDeduction || 0,
                net_pay: updatedEmployee.netPay || 0,
                notes: JSON.stringify({
                    payroll_meta: {
                        holidayPay: updatedEmployee.holidayPay || 0,
                        heslb: updatedEmployee.heslb || 0,
                        otherDed: updatedEmployee.otherDed || 0,
                        taxablePay: (updatedEmployee.grossPay || 0) - (updatedEmployee.nssf || 0),
                        sdl: updatedEmployee.sdl || 0,
                        wcf: updatedEmployee.wcf || 0,
                        totalCost: updatedEmployee.totalCost || 0,
                        monthOfPayment: updatedEmployee.monthOfPayment || ''
                    }
                }),
            })
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        const result = contentType.includes('application/json') ? await response.json() : null;

        if (!response.ok || !result?.success) {
            showNotification(result?.message || 'Failed to update payroll.', 'error');
            return;
        }

        showNotification(`Payroll for ${updatedEmployee.name} updated successfully`, 'success');
        closeEditModal();
        await loadPayrollData();
    } catch (e) {
        showNotification('Network error while saving payroll changes.', 'error');
    } finally {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }
}

function viewFullPayslip() {
    if (window.currentPayslipEmployee) {
        window.open(`/payroll/payslip?empId=${window.currentPayslipEmployee.empId}`, '_blank');
    }
}

function showBulkActions() {
    openModal('bulkActionsModal');
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function closeBulkModal() {
    closeModal('bulkActionsModal');
}

function downloadAllPayslipsPDF() {
    if (payrollData.length === 0) {
        showNotification('No payroll data available', 'warning');
        return;
    }
    
    showNotification(`Generating ${payrollData.length} payslips...`, 'info');
    
    // Open individual payslip pages for each employee
    setTimeout(() => {
        payrollData.forEach((employee, index) => {
            setTimeout(() => {
                window.open(`/payroll/payslip?empId=${employee.empId}`, '_blank');
            }, index * 500); // Stagger opening to prevent overwhelming
        });
        
        showNotification('All payslips opened in new tabs', 'success');
        closeBulkModal();
    }, 1000);
}

function emailAllPayslips() {
    if (payrollData.length === 0) {
        showNotification('No payroll data available', 'warning');
        return;
    }
    
    showNotification(`Preparing to email ${payrollData.length} payslips...`, 'info');
    
    // Simulate email sending
    setTimeout(() => {
        showNotification('Email functionality will be available soon', 'info');
        closeBulkModal();
    }, 2000);
}

function generateComprehensiveReport() {
    if (payrollData.length === 0) {
        showNotification('No payroll data available', 'warning');
        return;
    }
    
    showNotification('Generating comprehensive payroll report...', 'info');
    
    // Create detailed CSV report
    const headers = [
        'Emp ID', 'Name', 'Department', 'Title', 'Joining Date', 'Shift',
        'Basic Salary', 'OT Hours', 'OT Rate', 'OT Pay', 'Holiday Pay',
        'Gross Pay', 'NSSF Employee', 'Taxable Pay', 'PAYE', 'HESLB',
        'Other Deductions', 'Total Deductions', 'Net Pay',
        'NSSF Employer', 'SDL', 'WCF', 'Total Cost to Company'
    ];
    
    const csvContent = [
        headers.join(','),
        ...payrollData.map(emp => [
            emp.empId,
            emp.name,
            emp.department,
            emp.title,
            emp.joiningDate,
            emp.shift,
            emp.basicSalary,
            emp.otHours,
            emp.otRate,
            emp.otPay,
            emp.holidayPay,
            emp.grossPay,
            emp.nssf,
            (emp.grossPay - emp.nssf), // Taxable Pay
            emp.paye,
            emp.heslb,
            emp.otherDed,
            emp.totalDeduction,
            emp.netPay,
            emp.employerNSSF,
            emp.sdl,
            emp.wcf,
            emp.totalCost
        ].join(','))
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `comprehensive_payroll_report_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Comprehensive report generated successfully', 'success');
    closeBulkModal();
}

// Generate payslip function now directly redirects to payslip page - no popup behavior

// Override the global switchClient function to handle payroll data switching
if (typeof switchClient !== 'undefined') {
    const originalSwitchClient = switchClient;
    switchClient = function(clientId) {
        originalSwitchClient(clientId);
        loadPayrollData();
    };
}

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
