@extends('layouts.app')

@section('title', 'Personnel ID Applications - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Personnel ID Applications</h1>
            <p class="text-gray-600 mt-2">Manage employee ID cards and access credentials</p>
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
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="credit-card" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Applications</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalApplications">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900" id="pendingApplications">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Issued</p>
                    <p class="text-2xl font-semibold text-gray-900" id="issuedCards">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Lost/Damaged</p>
                    <p class="text-2xl font-semibold text-gray-900" id="problemCards">-</p>
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
                <select id="idTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All ID Types</option>
                    <option value="employee_card">Employee Card</option>
                    <option value="access_card">Access Card</option>
                    <option value="visitor_card">Visitor Card</option>
                    <option value="contractor_card">Contractor Card</option>
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="issued">Issued</option>
                    <option value="expired">Expired</option>
                    <option value="lost">Lost</option>
                    <option value="damaged">Damaged</option>
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
                            ID Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Validity Period
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Access Details
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
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">
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
                                @if($latestApplication = $employee->personnelIdApplications->sortByDesc('created_at')->first())
                                    <div class="text-sm text-gray-900">{{ $latestApplication->id_number }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::headline($latestApplication->id_type) }}</div>
                                    <div class="text-xs text-gray-400">Applied: {{ $latestApplication->created_at->format('Y-m-d') }}</div>
                                @else
                                    <div class="text-sm text-gray-400 italic">No application</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestApplication)
                                    <div class="text-sm text-gray-900">Valid: {{ $latestApplication->valid_from->format('Y-m-d') }} to {{ $latestApplication->valid_until->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-500">{{ $latestApplication->valid_from->diffInDays($latestApplication->valid_until) }} days</div>
                                    @if($latestApplication->valid_until->isFuture())
                                        <div class="text-xs text-green-600">Active</div>
                                    @else
                                        <div class="text-xs text-red-600">Expired</div>
                                    @endif
                                @else
                                    <div class="text-sm text-gray-400">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestApplication)
                                    <div class="text-sm text-gray-900">{{ Str::limit($latestApplication->access_areas, 20) }}</div>
                                    <div class="text-sm text-gray-500">Access: {{ $latestApplication->after_hours_access ? '24/7' : 'Standard' }}</div>
                                    <div class="text-xs text-blue-600">Emergency: {{ $latestApplication->emergency_access ? 'Yes' : 'No' }}</div>
                                @else
                                    <div class="text-sm text-gray-400">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestApplication)
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-blue-100 text-blue-800',
                                            'issued' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'expired' => 'bg-gray-100 text-gray-800',
                                            'lost' => 'bg-red-100 text-red-800',
                                            'damaged' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$latestApplication->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($latestApplication->status) }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">Updated: {{ $latestApplication->updated_at->format('Y-m-d') }}</div>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Applied
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEmployeeId({{ $employee->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="generateCard({{ $employee->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="reportLost({{ $employee->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        <i data-feather="alert-circle" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="uploadPhoto({{ $employee->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i data-feather="camera" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="credit-card" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">No approved employees to manage ID cards for.</p>
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">ID Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Applications:</span>
                    <span class="text-sm font-medium" id="modalTotalApplications">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Applications:</span>
                    <span class="text-sm font-medium" id="modalPendingApplications">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Approved Applications:</span>
                    <span class="text-sm font-medium" id="modalApprovedApplications">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Issued Cards:</span>
                    <span class="text-sm font-medium" id="modalIssuedCards">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Expired Cards:</span>
                    <span class="text-sm font-medium" id="modalExpiredCards">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Lost Cards:</span>
                    <span class="text-sm font-medium" id="modalLostCards">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Damaged Cards:</span>
                    <span class="text-sm font-medium" id="modalDamagedCards">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Expiring Soon:</span>
                    <span class="text-sm font-medium" id="modalExpiringSoon">-</span>
                </div>
            </div>
            <div class="border-t pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">By ID Type</h4>
                <div class="space-y-2" id="idTypeStats">
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">Applications Requiring Attention</h3>
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

<!-- Report Lost Modal -->
<div id="reportLostModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Lost ID Card</h3>
            <form id="reportLostForm" class="space-y-4">
                <input type="hidden" name="employee_id" id="lostEmployeeId">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lost Date <span class="text-red-500">*</span></label>
                    <input type="date" name="lost_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lost Location <span class="text-red-500">*</span></label>
                    <input type="text" name="lost_location" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Circumstances <span class="text-red-500">*</span></label>
                    <textarea name="circumstances" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="police_report_filed" id="police_report_filed"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="police_report_filed" class="ml-2 block text-sm text-gray-900">
                        Police report filed
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Police Report Number</label>
                    <input type="text" name="police_report_number"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="hideReportLostModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="reportLostBtn"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <span id="reportLostBtnText">Report Lost</span>
                        <div id="reportLostBtnLoader" class="hidden ml-2">
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
// Personnel ID Management System
class PersonnelIdManager {
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
        const filters = ['workStationFilter', 'idTypeFilter', 'statusFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterEmployees());
        });

        // Report lost form
        const reportLostForm = document.getElementById('reportLostForm');
        reportLostForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.reportLost();
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/personnel-id/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;
                
                // Update main page statistics
                document.getElementById('totalApplications').textContent = stats.total_applications;
                document.getElementById('pendingApplications').textContent = stats.pending_applications;
                document.getElementById('issuedCards').textContent = stats.issued_cards;
                document.getElementById('problemCards').textContent = stats.lost_cards + stats.damaged_cards;

                // Update modal statistics
                document.getElementById('modalTotalApplications').textContent = stats.total_applications;
                document.getElementById('modalPendingApplications').textContent = stats.pending_applications;
                document.getElementById('modalApprovedApplications').textContent = stats.approved_applications;
                document.getElementById('modalIssuedCards').textContent = stats.issued_cards;
                document.getElementById('modalExpiredCards').textContent = stats.expired_cards;
                document.getElementById('modalLostCards').textContent = stats.lost_cards;
                document.getElementById('modalDamagedCards').textContent = stats.damaged_cards;
                document.getElementById('modalExpiringSoon').textContent = stats.expiring_soon;

                // Update ID types
                const typesContainer = document.getElementById('idTypeStats');
                typesContainer.innerHTML = '';
                Object.entries(stats.by_type).forEach(([type, count]) => {
                    const typeLabel = this.getIdTypeLabel(type);
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

    getIdTypeLabel(type) {
        const labels = {
            'employee_card' => 'Employee Card',
            'access_card' => 'Access Card',
            'visitor_card' => 'Visitor Card',
            'contractor_card' => 'Contractor Card'
        };
        return labels[type] || type;
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const workStationFilter = document.getElementById('workStationFilter').value;
        const idTypeFilter = document.getElementById('idTypeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const workStation = row.dataset.workstation;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesWorkStation = !workStationFilter || workStation === workStationFilter;
            const matchesIdType = !idTypeFilter; // Placeholder
            const matchesStatus = !statusFilter; // Placeholder

            if (matchesSearch && matchesWorkStation && matchesIdType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async reportLost() {
        const form = document.getElementById('reportLostForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        this.setReportLostLoadingState(true);

        try {
            const response = await fetch(`/personnel-id/${data.employee_id}/report-lost`, {
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
                hideReportLostModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Report failed', 'error');
            }
        } catch (error) {
            console.error('Report lost error:', error);
            this.showNotification('An error occurred during reporting', 'error');
        } finally {
            this.setReportLostLoadingState(false);
        }
    }

    setReportLostLoadingState(loading) {
        const btnText = document.getElementById('reportLostBtnText');
        const btnLoader = document.getElementById('reportLostBtnLoader');
        const reportLostBtn = document.getElementById('reportLostBtn');

        if (loading) {
            btnText.textContent = 'Reporting...';
            btnLoader.classList.remove('hidden');
            reportLostBtn.disabled = true;
        } else {
            btnText.textContent = 'Report Lost';
            btnLoader.classList.add('hidden');
            reportLostBtn.disabled = false;
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
        const response = await fetch('/personnel-id/requiring-attention');
        const result = await response.json();

        if (result.success) {
            const list = document.getElementById('requiringAttentionList');
            list.innerHTML = '';
            
            if (result.employees.length === 0) {
                list.innerHTML = '<p class="text-sm text-gray-500">No applications require attention at this time.</p>';
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
            window.personnelIdManager.showNotification('Failed to load requiring attention applications', 'error');
        }
    } catch (error) {
        console.error('Failed to load requiring attention applications:', error);
        window.personnelIdManager.showNotification('An error occurred', 'error');
    }
}

function hideRequiringAttentionModal() {
    document.getElementById('requiringAttentionModal').classList.add('hidden');
}

function showReportLostModal(employeeId) {
    document.getElementById('lostEmployeeId').value = employeeId;
    document.getElementById('reportLostModal').classList.remove('hidden');
}

function hideReportLostModal() {
    document.getElementById('reportLostModal').classList.add('hidden');
    document.getElementById('reportLostForm').reset();
}

// Action functions
function showEmployeeId(employeeId) {
    window.location.href = `/personnel-id/employee/${employeeId}`;
}

function generateCard(employeeId) {
    window.personnelIdManager.showNotification('ID card generation feature coming soon', 'info');
}

function reportLost(employeeId) {
    showReportLostModal(employeeId);
}

function uploadPhoto(employeeId) {
    window.personnelIdManager.showNotification('Photo upload feature coming soon', 'info');
}

// Initialize personnel ID manager
document.addEventListener('DOMContentLoaded', function() {
    window.personnelIdManager = new PersonnelIdManager();
});
</script>
@endpush
