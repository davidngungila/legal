@extends('layouts.app')

@section('title', 'Social Records Registration - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Social Records Registration</h1>
            <p class="text-gray-600 mt-2">Manage employee social security and welfare records</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showStatisticsModal()" 
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                Statistics
            </button>
            <button onclick="showMissingRecordsModal()" 
                    class="px-4 py-2 border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors flex items-center">
                <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i>
                Missing Records
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Employees</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalEmployees">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="shield" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">With NSSF</p>
                    <p class="text-2xl font-semibold text-gray-900" id="nssfCount">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="heart" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">With NHIF</p>
                    <p class="text-2xl font-semibold text-gray-900" id="nhifCount">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="credit-card" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">With Bank Records</p>
                    <p class="text-2xl font-semibold text-gray-900" id="bankCount">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <select id="recordsFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees</option>
                    <option value="complete">Complete Records</option>
                    <option value="incomplete">Incomplete Records</option>
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
                            Social Security
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bank Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Emergency Contacts
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
                        @php
                            $hasSocialRecord = $employee->socialRecord !== null;
                            $nssfRegistered = $hasSocialRecord && $employee->socialRecord->nssf_number;
                            $nhifRegistered = $hasSocialRecord && $employee->socialRecord->nhif_number;
                            $tinRegistered = $hasSocialRecord && $employee->socialRecord->tin_number;
                            $wcfRegistered = $hasSocialRecord && $employee->socialRecord->wcf_number;
                            $bankRegistered = $hasSocialRecord && $employee->socialRecord->bank_account_number;
                            $emergencyComplete = $hasSocialRecord && $employee->socialRecord->emergency_contact_name;
                            $nextOfKinComplete = $hasSocialRecord && $employee->socialRecord->next_of_kin_name;
                            $isComplete = $nssfRegistered && $nhifRegistered && $tinRegistered && $bankRegistered;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors employee-row" 
                            data-name="{{ $employee->first_name . ' ' . $employee->surname }}"
                            data-workstation="{{ $employee->work_station }}"
                            data-has-social="{{ $hasSocialRecord ? '1' : '0' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <span class="text-green-600 font-bold text-sm">
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
                                <div class="text-xs space-y-1">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 {{ $nssfRegistered ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-1"></span>
                                        <span>NSSF: {{ $nssfRegistered ? 'Registered' : 'Not Found' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 {{ $nhifRegistered ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-1"></span>
                                        <span>NHIF: {{ $nhifRegistered ? 'Registered' : 'Not Found' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 {{ $tinRegistered ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-1"></span>
                                        <span>TIN: {{ $tinRegistered ? 'Registered' : 'Not Found' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 {{ $wcfRegistered ? 'bg-green-400' : 'bg-red-400' }} rounded-full mr-1"></span>
                                        <span>WCF: {{ $wcfRegistered ? 'Registered' : 'Not Found' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $bankRegistered ? 'text-gray-900' : 'text-red-600 font-semibold' }}">
                                    Bank: {{ $hasSocialRecord && $employee->socialRecord->bank_name ? $employee->socialRecord->bank_name : 'No Data' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Account: {{ $bankRegistered ? 'Verified' : 'Missing' }}
                                </div>
                                @if($hasSocialRecord && $employee->socialRecord->bank_verification_path)
                                    <div class="text-xs text-green-600">Documents: Complete</div>
                                @else
                                    <div class="text-xs text-red-500">Documents: Incomplete</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $emergencyComplete ? 'text-gray-900' : 'text-red-600 font-semibold' }}">
                                    Emergency: {{ $emergencyComplete ? 'On file' : 'Missing' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Next of Kin: {{ $nextOfKinComplete ? 'On file' : 'Missing' }}
                                </div>
                                <div class="text-xs {{ $emergencyComplete && $nextOfKinComplete ? 'text-blue-600' : 'text-red-500' }}">
                                    {{ $emergencyComplete && $nextOfKinComplete ? 'Complete' : 'Action Required' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isComplete ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isComplete ? 'Complete' : 'Incomplete' }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">Status: {{ $employee->status ?? 'Active' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEmployeeRecords({{ $employee->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="editSocialRecords({{ $employee->id }})" 
                                            class="text-blue-600 hover:text-blue-900">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="generateReport({{ $employee->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        <i data-feather="file-text" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="shield" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">No employees to manage social records for.</p>
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">Social Records Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Employees:</span>
                    <span class="text-sm font-medium" id="modalTotalEmployees">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">NSSF Registered:</span>
                    <span class="text-sm font-medium" id="modalNssfCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">NHIF Registered:</span>
                    <span class="text-sm font-medium" id="modalNhifCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">TIN Registered:</span>
                    <span class="text-sm font-medium" id="modalTinCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">WCF Registered:</span>
                    <span class="text-sm font-medium" id="modalWcfCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Bank Records:</span>
                    <span class="text-sm font-medium" id="modalBankCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Records:</span>
                    <span class="text-sm font-medium" id="modalActiveCount">-</span>
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

<!-- Missing Records Modal -->
<div id="missingRecordsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Employees Missing Records</h3>
            <div id="missingRecordsList" class="space-y-2 max-h-64 overflow-y-auto">
                <!-- Will be populated dynamically -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideMissingRecordsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Social Records Management System
class SocialRecordsManager {
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
        const filters = ['workStationFilter', 'recordsFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterEmployees());
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/social-records/statistics');
            const result = await response.json();
            
            console.log('Statistics response:', result);

            if (result.success) {
                const stats = result.statistics;
                
                // Update main page statistics
                document.getElementById('totalEmployees').textContent = stats.total_employees || 0;
                document.getElementById('nssfCount').textContent = stats.employees_with_nssf || 0;
                document.getElementById('nhifCount').textContent = stats.employees_with_nhif || 0;
                document.getElementById('bankCount').textContent = stats.employees_with_bank || 0;

                // Update modal statistics
                document.getElementById('modalTotalEmployees').textContent = stats.total_employees || 0;
                document.getElementById('modalNssfCount').textContent = stats.employees_with_nssf || 0;
                document.getElementById('modalNhifCount').textContent = stats.employees_with_nhif || 0;
                document.getElementById('modalTinCount').textContent = stats.employees_with_tin || 0;
                document.getElementById('modalWcfCount').textContent = stats.employees_with_wcf || 0;
                document.getElementById('modalBankCount').textContent = stats.employees_with_bank || 0;
                document.getElementById('modalActiveCount').textContent = stats.active_records || 0;
            } else {
                console.error('Statistics failed:', result.message);
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const workStationFilter = document.getElementById('workStationFilter').value;
        const recordsFilter = document.getElementById('recordsFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const workStation = row.dataset.workstation;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesWorkStation = !workStationFilter || workStation === workStationFilter;
            
            let matchesRecords = true;
            if (recordsFilter === 'complete') {
                // This would need to be implemented based on actual data
                matchesRecords = true; // Placeholder
            } else if (recordsFilter === 'incomplete') {
                matchesRecords = false; // Placeholder
            }

            if (matchesSearch && matchesWorkStation && matchesRecords) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
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

async function showMissingRecordsModal() {
    try {
        const response = await fetch('/social-records/missing-records');
        const result = await response.json();

        if (result.success) {
            const list = document.getElementById('missingRecordsList');
            list.innerHTML = '';
            
            if (result.employees.length === 0) {
                list.innerHTML = '<p class="text-sm text-gray-500">All employees have complete social records.</p>';
            } else {
                result.employees.forEach(employee => {
                    const item = document.createElement('div');
                    item.className = 'p-2 bg-gray-50 rounded';
                    item.innerHTML = `
                        <div class="text-sm font-medium">${employee.first_name} ${employee.surname}</div>
                        <div class="text-xs text-gray-500">${employee.employee_number}</div>
                        <div class="text-xs text-orange-600">Records incomplete</div>
                    `;
                    list.appendChild(item);
                });
            }
            
            document.getElementById('missingRecordsModal').classList.remove('hidden');
        } else {
            window.socialRecordsManager.showNotification('Failed to load missing records', 'error');
        }
    } catch (error) {
        console.error('Failed to load missing records:', error);
        window.socialRecordsManager.showNotification('An error occurred', 'error');
    }
}

function hideMissingRecordsModal() {
    document.getElementById('missingRecordsModal').classList.add('hidden');
}

// Action functions
function showEmployeeRecords(employeeId) {
    window.location.href = `/social-records/employee/${employeeId}`;
}

function editSocialRecords(employeeId) {
    window.location.href = `/social-records/employee/${employeeId}/edit`;
}

function generateReport(employeeId) {
    window.socialRecordsManager.showNotification('Report generation feature coming soon', 'info');
}

// Initialize social records manager
document.addEventListener('DOMContentLoaded', function() {
    window.socialRecordsManager = new SocialRecordsManager();
});
</script>
@endpush
