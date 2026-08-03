@extends('layouts.app')

@section('title', 'HRIS Dashboard - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">HRIS Dashboard</h1>
            <p class="text-gray-600 mt-2">Human Resource Information System Overview</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showSystemStats()" 
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                System Statistics
            </button>
        </div>
    </div>

    <!-- System Overview Cards -->
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
                    <i data-feather="briefcase" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Contracts</p>
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
                    <p class="text-sm font-medium text-gray-500">Pending Approvals</p>
                    <p class="text-2xl font-semibold text-gray-900" id="pendingApprovals">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Documents</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalDocuments">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- User Registration -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('user-registration')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <i data-feather="user-plus" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">User Registration</h3>
                    <p class="text-sm text-gray-500">Manage user accounts</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Active Users</span>
                <span class="text-sm font-medium text-indigo-600" id="userRegCount">-</span>
            </div>
        </div>

        <!-- Client Registration -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('client-registration')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="building" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Client Registration</h3>
                    <p class="text-sm text-gray-500">Manage employers</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Active Clients</span>
                <span class="text-sm font-medium text-green-600" id="clientRegCount">-</span>
            </div>
        </div>

        <!-- Job Vacancy -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('job-vacancy')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Job Vacancies</h3>
                    <p class="text-sm text-gray-500">Manage job openings</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Open Positions</span>
                <span class="text-sm font-medium text-purple-600" id="jobVacancyCount">-</span>
            </div>
        </div>

        <!-- HR Interview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('hr-interview')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="message-square" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">HR Interviews</h3>
                    <p class="text-sm text-gray-500">Competency assessments</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pending</span>
                <span class="text-sm font-medium text-blue-600" id="hrInterviewCount">-</span>
            </div>
        </div>

        <!-- Technical Interview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('technical-interview')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="code" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Technical Interviews</h3>
                    <p class="text-sm text-gray-500">Technical assessments</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pending</span>
                <span class="text-sm font-medium text-orange-600" id="techInterviewCount">-</span>
            </div>
        </div>

        <!-- Employee Registration -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('employee-registration')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-teal-100 rounded-lg p-3">
                    <i data-feather="users" class="w-6 h-6 text-teal-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Employee Registration</h3>
                    <p class="text-sm text-gray-500">Register employees</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Registered</span>
                <span class="text-sm font-medium text-teal-600" id="employeeRegCount">-</span>
            </div>
        </div>

        <!-- Employee Documents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('employee-document')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i data-feather="file" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Employee Documents</h3>
                    <p class="text-sm text-gray-500">Manage documents</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Files</span>
                <span class="text-sm font-medium text-red-600" id="documentCount">-</span>
            </div>
        </div>

        <!-- Social Records -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('social-records')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-pink-100 rounded-lg p-3">
                    <i data-feather="shield" class="w-6 h-6 text-pink-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Social Records</h3>
                    <p class="text-sm text-gray-500">Social security info</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Complete</span>
                <span class="text-sm font-medium text-pink-600" id="socialRecordsCount">-</span>
            </div>
        </div>

        <!-- Induction Training -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('induction-training')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="award" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Induction Training</h3>
                    <p class="text-sm text-gray-500">Training programs</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Trained</span>
                <span class="text-sm font-medium text-yellow-600" id="trainingCount">-</span>
            </div>
        </div>

        <!-- Personnel ID -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('personnel-id')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <i data-feather="credit-card" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Personnel ID</h3>
                    <p class="text-sm text-gray-500">ID cards & access</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Issued</span>
                <span class="text-sm font-medium text-indigo-600" id="personnelIdCount">-</span>
            </div>
        </div>

        <!-- Contract Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('contract-management')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="file-text" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Contract Management</h3>
                    <p class="text-sm text-gray-500">Contract oversight</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Active</span>
                <span class="text-sm font-medium text-green-600" id="contractMgmtCount">-</span>
            </div>
        </div>

        <!-- Employment Contracts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('employment-contracts')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="file-text" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Employment Contracts</h3>
                    <p class="text-sm text-gray-500">Employment agreements</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Active</span>
                <span class="text-sm font-medium text-purple-600" id="employmentContractCount">-</span>
            </div>
        </div>

        <!-- Workflow Management -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="navigateToModule('workflow')">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="git-branch" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Workflow Management</h3>
                    <p class="text-sm text-gray-500">Approval workflows</p>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pending</span>
                <span class="text-sm font-medium text-orange-600" id="workflowCount">-</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <button onclick="navigateToModule('user-registration/create')" 
                    class="flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 mr-2"></i>
                Register User
            </button>
            <button onclick="navigateToModule('job-vacancy/create')" 
                    class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i>
                Post Job
            </button>
            <button onclick="navigateToModule('employee-registration/create')" 
                    class="flex items-center justify-center px-4 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                <i data-feather="users" class="w-4 h-4 mr-2"></i>
                Register Employee
            </button>
            <button onclick="navigateToModule('workflow')" 
                    class="flex items-center justify-center px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                <i data-feather="git-branch" class="w-4 h-4 mr-2"></i>
                View Workflows
            </button>
        </div>
    </div>
</div>

<!-- System Statistics Modal -->
<x-advanced-modal id="systemStatsModal" title="System Statistics"
    icon="bar-chart-2" color="indigo" size="md">
    <div class="space-y-4" id="systemStatsContent">
        <!-- Will be populated dynamically -->
    </div>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button onclick="hideSystemStatsModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Close
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
// HRIS Dashboard Management
class HrisDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.initializeFeather();
        this.loadSystemStats();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    async loadSystemStats() {
        try {
            const response = await fetch('/api/hris/stats');
            const data = await response.json();
            
            if (data.success) {
                const stats = data.stats;
                // Update dashboard counts
                Object.keys(stats).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        element.textContent = stats[key];
                    }
                });
            }

        } catch (error) {
            console.error('Failed to load system stats:', error);
        }
    }

    navigateToModule(path) {
        window.location.href = `/${path}`;
    }

    showSystemStats() {
        const content = document.getElementById('systemStatsContent');
        
        content.innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Employees:</span>
                    <span class="text-sm font-medium">127</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Contracts:</span>
                    <span class="text-sm font-medium">89</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Approvals:</span>
                    <span class="text-sm font-medium">15</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Documents:</span>
                    <span class="text-sm font-medium">342</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">System Uptime:</span>
                    <span class="text-sm font-medium text-green-600">99.9%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Last Backup:</span>
                    <span class="text-sm font-medium">2 hours ago</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Storage Used:</span>
                    <span class="text-sm font-medium">2.3 GB / 10 GB</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Users:</span>
                    <span class="text-sm font-medium">12</span>
                </div>
            </div>
        `;
        
        openModal('systemStatsModal');
    }

    hideSystemStatsModal() {
        closeModal('systemStatsModal');
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    window.hrisDashboard = new HrisDashboard();
});

// Global function for modal
function hideSystemStatsModal() {
    window.hrisDashboard.hideSystemStatsModal();
}
</script>
@endpush
