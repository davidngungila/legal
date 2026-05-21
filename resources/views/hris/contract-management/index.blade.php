@extends('layouts.app')

@section('title', 'Contract Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Contract Management</h1>
            <p class="text-gray-600 mt-2">Manage employee contracts and employment agreements</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showStatisticsModal()" 
                    class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                Statistics
            </button>
            <button onclick="showRequiringAttentionModal()" 
                    class="px-4 py-2 border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors flex items-center">
                <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i>
                Requiring Attention
            </button>
            <button onclick="showCalendarModal()" 
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Calendar
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Contracts</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalContracts">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active</p>
                    <p class="text-2xl font-semibold text-gray-900" id="activeContracts">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                    <p class="text-2xl font-semibold text-gray-900" id="expiringSoon">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Terminated</p>
                    <p class="text-2xl font-semibold text-gray-900" id="terminatedContracts">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Renewal Rate</p>
                    <p class="text-2xl font-semibold text-gray-900" id="renewalRate">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search employees..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="workStationFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Work Stations</option>
                    @foreach($employees->pluck('work_station')->unique() as $workStation)
                        <option value="{{ $workStation }}">{{ $workStation }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="contractTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Contract Types</option>
                    <option value="permanent">Permanent</option>
                    <option value="temporary">Temporary</option>
                    <option value="probation">Probation</option>
                    <option value="internship">Internship</option>
                    <option value="consultant">Consultant</option>
                    <option value="contractor">Contractor</option>
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="terminated">Terminated</option>
                    <option value="renewed">Renewed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contract Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contract Period
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salary & Benefits
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="employeesTableBody">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors employee-row" 
                            data-name="{{ $employee->first_name . ' ' . $employee->surname }}"
                            data-workstation="{{ $employee->work_station }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-bold text-sm">
                                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->surname, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->surname }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->work_station }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">PERM20240001</div>
                                <div class="text-sm text-gray-500">Permanent</div>
                                <div class="text-xs text-gray-400">Senior Developer</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">2024-01-15 to 2025-01-15</div>
                                <div class="text-sm text-gray-500">365 days</div>
                                <div class="text-xs text-green-600">11 months remaining</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">$5,000/month</div>
                                <div class="text-sm text-gray-500">Health, Transport</div>
                                <div class="text-xs text-blue-600">21 days leave</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                                <div class="text-xs text-gray-500 mt-1">Probation: Completed</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEmployeeContracts({{ $employee->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="renewContract({{ $employee->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="terminateContract({{ $employee->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        <i data-feather="x-circle" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="generateReport({{ $employee->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">No approved employees to manage contracts for.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $employees->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $employees->firstItem() }}</span> to 
                            <span class="font-medium">{{ $employees->lastItem() }}</span> of 
                            <span class="font-medium">{{ $employees->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Statistics Modal -->
<div id="statisticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contract Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Contracts:</span>
                    <span class="text-sm font-medium" id="modalTotalContracts">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Contracts:</span>
                    <span class="text-sm font-medium" id="modalActiveContracts">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Expired Contracts:</span>
                    <span class="text-sm font-medium" id="modalExpiredContracts">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Terminated Contracts:</span>
                    <span class="text-sm font-medium" id="modalTerminatedContracts">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Expiring Soon:</span>
                    <span class="text-sm font-medium" id="modalExpiringSoon">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Average Duration:</span>
                    <span class="text-sm font-medium" id="modalAvgDuration">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Renewal Rate:</span>
                    <span class="text-sm font-medium" id="modalRenewalRate">-</span>
                </div>
            </div>
            <div class="border-t pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">By Contract Type</h4>
                <div class="space-y-2" id="contractTypeStats">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideStatisticsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Requiring Attention Modal -->
<div id="requiringAttentionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contracts Requiring Attention</h3>
            <div id="requiringAttentionList" class="space-y-2 max-h-64 overflow-y-auto">
                <!-- Will be populated dynamically -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideRequiringAttentionModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Modal -->
<div id="calendarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contract Calendar</h3>
            <div class="space-y-2" id="calendarEvents">
                <!-- Will be populated dynamically -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideCalendarModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Terminate Contract Modal -->
<div id="terminateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Terminate Contract</h3>
            <form id="terminateForm" class="space-y-4">
                <input type="hidden" name="employee_id" id="terminateEmployeeId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Termination Date <span class="text-red-500">*</span></label>
                    <input type="date" name="termination_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Termination Reason <span class="text-red-500">*</span></label>
                    <textarea name="termination_reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Termination Type <span class="text-red-500">*</span></label>
                    <select name="termination_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="resignation">Resignation</option>
                        <option value="dismissal">Dismissal</option>
                        <option value="retirement">Retirement</option>
                        <option value="contract_expiry">Contract Expiry</option>
                        <option value="mutual_agreement">Mutual Agreement</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Final Settlement Amount</label>
                    <input type="number" name="final_settlement_amount" min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="handover_completed" id="handover_completed"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="handover_completed" class="ml-2 block text-sm text-gray-900">
                            Handover Completed
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="clearance_completed" id="clearance_completed"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="clearance_completed" class="ml-2 block text-sm text-gray-900">
                            Clearance Completed
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="exit_interview_completed" id="exit_interview_completed"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="exit_interview_completed" class="ml-2 block text-sm text-gray-900">
                            Exit Interview Completed
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="hideTerminateModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="terminateBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <span id="terminateBtnText">Terminate</span>
                        <div id="terminateBtnLoader" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Contract Management System
class ContractManagementManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
        this.loadStatistics();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterEmployees());

        // Filter functionality
        const filters = ['workStationFilter', 'contractTypeFilter', 'statusFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterEmployees());
        });

        // Terminate form
        const terminateForm = document.getElementById('terminateForm');
        terminateForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.terminateContract();
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/contract-management/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;
                
                // Update main page statistics
                document.getElementById('totalContracts').textContent = stats.total_contracts;
                document.getElementById('activeContracts').textContent = stats.active_contracts;
                document.getElementById('expiringSoon').textContent = stats.expiring_soon;
                document.getElementById('terminatedContracts').textContent = stats.terminated_contracts;
                document.getElementById('renewalRate').textContent = stats.renewal_rate + '%';

                // Update modal statistics
                document.getElementById('modalTotalContracts').textContent = stats.total_contracts;
                document.getElementById('modalActiveContracts').textContent = stats.active_contracts;
                document.getElementById('modalExpiredContracts').textContent = stats.expired_contracts;
                document.getElementById('modalTerminatedContracts').textContent = stats.terminated_contracts;
                document.getElementById('modalExpiringSoon').textContent = stats.expiring_soon;
                document.getElementById('modalAvgDuration').textContent = stats.average_duration_months + ' months';
                document.getElementById('modalRenewalRate').textContent = stats.renewal_rate + '%';

                // Update contract types
                const typesContainer = document.getElementById('contractTypeStats');
                typesContainer.innerHTML = '';
                Object.entries(stats.by_type).forEach(([type, count]) => {
                    const typeLabel = this.getContractTypeLabel(type);
                    typesContainer.innerHTML += `
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">${typeLabel}:</span>
                            <span class="text-sm font-medium">${count}</span>
                        </div>
                    `;
                });
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    getContractTypeLabel(type) {
        const labels = {
            'permanent' => 'Permanent',
            'temporary' => 'Temporary',
            'probation' => 'Probation',
            'internship' => 'Internship',
            'consultant' => 'Consultant',
            'contractor' => 'Contractor'
        };
        return labels[type] || type;
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const workStationFilter = document.getElementById('workStationFilter').value;
        const contractTypeFilter = document.getElementById('contractTypeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const workStation = row.dataset.workstation;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesWorkStation = !workStationFilter || workStation === workStationFilter;
            const matchesContractType = !contractTypeFilter; // Placeholder
            const matchesStatus = !statusFilter; // Placeholder

            if (matchesSearch && matchesWorkStation && matchesContractType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async terminateContract() {
        const form = document.getElementById('terminateForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Convert checkboxes to boolean
        data.handover_completed = form.querySelector('#handover_completed').checked;
        data.clearance_completed = form.querySelector('#clearance_completed').checked;
        data.exit_interview_completed = form.querySelector('#exit_interview_completed').checked;

        this.setTerminateLoadingState(true);

        try {
            const response = await fetch(`/contract-management/${data.employee_id}/terminate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                hideTerminateModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Termination failed', 'error');
            }
        } catch (error) {
            console.error('Contract termination error:', error);
            this.showNotification('An error occurred during termination', 'error');
        } finally {
            this.setTerminateLoadingState(false);
        }
    }

    setTerminateLoadingState(loading) {
        const btnText = document.getElementById('terminateBtnText');
        const btnLoader = document.getElementById('terminateBtnLoader');
        const terminateBtn = document.getElementById('terminateBtn');

        if (loading) {
            btnText.textContent = 'Terminating...';
            btnLoader.classList.remove('hidden');
            terminateBtn.disabled = true;
        } else {
            btnText.textContent = 'Terminate';
            btnLoader.classList.add('hidden');
            terminateBtn.disabled = false;
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Modal functions
function showStatisticsModal() {
    document.getElementById('statisticsModal').classList.remove('hidden');
}

function hideStatisticsModal() {
    document.getElementById('statisticsModal').classList.add('hidden');
}

async function showRequiringAttentionModal() {
    try {
        const response = await fetch('/contract-management/requiring-attention');
        const result = await response.json();

        if (result.success) {
            const list = document.getElementById('requiringAttentionList');
            list.innerHTML = '';
            
            if (result.employees.length === 0) {
                list.innerHTML = '<p class="text-sm text-gray-500">No contracts require attention at this time.</p>';
            } else {
                result.employees.forEach(employee => {
                    const item = document.createElement('div');
                    item.className = 'p-2 bg-gray-50 rounded';
                    item.innerHTML = `
                        <div class="text-sm font-medium">${employee.first_name} ${employee.surname}</div>
                        <div class="text-xs text-gray-500">${employee.employee_number}</div>
                        <div class="text-xs text-orange-600">Requires attention</div>
                    `;
                    list.appendChild(item);
                });
            }
            
            document.getElementById('requiringAttentionModal').classList.remove('hidden');
        } else {
            window.contractManagementManager.showNotification('Failed to load requiring attention contracts', 'error');
        }
    } catch (error) {
        console.error('Failed to load requiring attention contracts:', error);
        window.contractManagementManager.showNotification('An error occurred', 'error');
    }
}

function hideRequiringAttentionModal() {
    document.getElementById('requiringAttentionModal').classList.add('hidden');
}

async function showCalendarModal() {
    try {
        const response = await fetch('/contract-management/calendar');
        const result = await response.json();

        if (result.success) {
            const container = document.getElementById('calendarEvents');
            container.innerHTML = '';
            
            result.events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'p-3 bg-gray-50 rounded';
                eventDiv.innerHTML = `
                    <div class="text-sm font-medium">${event.title}</div>
                    <div class="text-xs text-gray-500">${event.start}</div>
                    <div class="text-xs text-gray-400">${event.employee}</div>
                    <div class="text-xs text-blue-600">${event.type}</div>
                `;
                container.appendChild(eventDiv);
            });
            
            document.getElementById('calendarModal').classList.remove('hidden');
        } else {
            window.contractManagementManager.showNotification('Failed to load calendar events', 'error');
        }
    } catch (error) {
        console.error('Failed to load calendar events:', error);
        window.contractManagementManager.showNotification('An error occurred', 'error');
    }
}

function hideCalendarModal() {
    document.getElementById('calendarModal').classList.add('hidden');
}

function showTerminateModal(employeeId) {
    document.getElementById('terminateEmployeeId').value = employeeId;
    document.getElementById('terminateModal').classList.remove('hidden');
}

function hideTerminateModal() {
    document.getElementById('terminateModal').classList.add('hidden');
    document.getElementById('terminateForm').reset();
}

// Action functions
function showEmployeeContracts(employeeId) {
    window.location.href = `/contract-management/employee/${employeeId}`;
}

function renewContract(employeeId) {
    window.contractManagementManager.showNotification('Contract renewal feature coming soon', 'info');
}

function terminateContract(employeeId) {
    showTerminateModal(employeeId);
}

function generateReport(employeeId) {
    window.contractManagementManager.showNotification('Contract report generation feature coming soon', 'info');
}

// Initialize contract management manager
document.addEventListener('DOMContentLoaded', function() {
    window.contractManagementManager = new ContractManagementManager();
});
</script>
@endpush
