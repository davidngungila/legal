@extends('layouts.app')

@section('title', 'Workflow Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Workflow Management</h1>
            <p class="text-gray-600 mt-2">Manage approval workflows and track pending items</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showAnalyticsModal()" 
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="trending-up" class="w-4 h-4 mr-2"></i>
                Analytics
            </button>
            <button onclick="showCalendarModal()" 
                    class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Calendar
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Approved Today</p>
                    <p class="text-2xl font-semibold text-gray-900" id="approvedToday">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rejected Today</p>
                    <p class="text-2xl font-semibold text-gray-900" id="rejectedToday">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Overdue</p>
                    <p class="text-2xl font-semibold text-gray-900" id="overdueApprovals">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('pending')" 
                        class="tab-btn py-4 px-6 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600"
                        data-tab="pending">
                    Pending Approvals
                </button>
                <button onclick="showTab('history')" 
                        class="tab-btn py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        data-tab="history">
                    Approval History
                </button>
                <button onclick="showTab('analytics')" 
                        class="tab-btn py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        data-tab="analytics">
                    Analytics
                </button>
            </nav>
        </div>

        <!-- Pending Approvals Tab -->
        <div id="pending-tab" class="tab-content p-6">
            <div class="space-y-4" id="pendingApprovalsList">
                <!-- Will be populated dynamically -->
            </div>
        </div>

        <!-- History Tab -->
        <div id="history-tab" class="tab-content p-6 hidden">
            <div class="space-y-4" id="historyList">
                <!-- Will be populated dynamically -->
            </div>
        </div>

        <!-- Analytics Tab -->
        <div id="analytics-tab" class="tab-content p-6 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Trends</h3>
                    <div id="approvalTrendsChart" class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">Chart will be rendered here</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Average Approval Times</h3>
                    <div id="approvalTimesChart" class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">Chart will be rendered here</p>
                    </div>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Bottlenecks</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Step</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time (hours)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue Size</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="bottlenecksTable">
                            <!-- Will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve/Reject Modal -->
<div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="actionModalTitle">Action Required</h3>
            <form id="actionForm" class="space-y-4">
                <input type="hidden" name="workflow_id" id="workflowId">
                <input type="hidden" name="action" id="actionType">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea name="comments" rows="3" id="actionComments"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Add your comments..."></textarea>
                </div>
                <div id="rejectReasonSection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" id="rejectReason"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please provide a reason for rejection..."></textarea>
                </div>
                <div id="resubmissionSection" class="hidden space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_resubmission" id="allowResubmission"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="allowResubmission" class="ml-2 block text-sm text-gray-900">
                            Allow resubmission
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Resubmission Instructions</label>
                        <textarea name="resubmission_instructions" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Instructions for resubmission..."></textarea>
                    </div>
                </div>
                <div id="forwardSection" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Forward To <span class="text-red-500">*</span></label>
                    <input type="text" name="forward_to" id="forwardTo"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Enter approver name...">
                    <label class="block text-sm font-medium text-gray-700 mb-1 mt-2">Reason for Forwarding <span class="text-red-500">*</span></label>
                    <textarea name="forward_reason" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Reason for forwarding..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="hideActionModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="actionBtn"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <span id="actionBtnText">Submit</span>
                        <div id="actionBtnLoader" class="hidden ml-2">
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

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white max-h-96 overflow-y-auto">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Details</h3>
            <div id="detailsContent">
                <!-- Will be populated dynamically -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideDetailsModal()" 
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Calendar</h3>
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

<!-- Analytics Modal -->
<div id="analyticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white max-h-96 overflow-y-auto">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Analytics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Workflows:</span>
                    <span class="text-sm font-medium" id="modalTotalWorkflows">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Approval Rate:</span>
                    <span class="text-sm font-medium" id="modalApprovalRate">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Avg Approval Time:</span>
                    <span class="text-sm font-medium" id="modalAvgApprovalTime">-</span>
                </div>
                <div class="border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">By Workflow Type</h4>
                    <div class="space-y-2" id="workflowTypeStats">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideAnalyticsModal()" 
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
// Workflow Management System
class WorkflowManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
        this.loadStatistics();
        this.loadPendingApprovals();
        this.loadHistory();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Action form
        const actionForm = document.getElementById('actionForm');
        actionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitAction();
        });

        // Tab switching
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.showTab(button.dataset.tab);
            });
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/workflow/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;
                
                // Update main page statistics
                document.getElementById('pendingApprovals').textContent = stats.pending_approvals;
                document.getElementById('approvedToday').textContent = stats.approved_today;
                document.getElementById('rejectedToday').textContent = stats.rejected_today;
                document.getElementById('overdueApprovals').textContent = stats.overdue_approvals;

                // Update modal statistics
                document.getElementById('modalTotalWorkflows').textContent = stats.total_workflows;
                document.getElementById('modalApprovalRate').textContent = stats.approval_rate + '%';
                document.getElementById('modalAvgApprovalTime').textContent = stats.avg_approval_time_hours + ' hours';

                // Update workflow types
                const typesContainer = document.getElementById('workflowTypeStats');
                typesContainer.innerHTML = '';
                Object.entries(stats.by_type).forEach(([type, count]) => {
                    const typeLabel = this.getWorkflowTypeLabel(type);
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

    async loadPendingApprovals() {
        try {
            const response = await fetch('/workflow/pending-approvals');
            const result = await response.json();

            if (result.success) {
                this.renderPendingApprovals(result.approvals);
            }
        } catch (error) {
            console.error('Failed to load pending approvals:', error);
        }
    }

    async loadHistory() {
        try {
            const response = await fetch('/workflow/history');
            const result = await response.json();

            if (result.success) {
                this.renderHistory(result.history);
            }
        } catch (error) {
            console.error('Failed to load history:', error);
        }
    }

    renderPendingApprovals(approvals) {
        const container = document.getElementById('pendingApprovalsList');
        
        if (approvals.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i data-feather="check-circle" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No pending approvals</p>
                    <p class="text-sm text-gray-500">All workflows are up to date.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = approvals.map(approval => `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <h4 class="text-sm font-medium text-gray-900">${approval.title}</h4>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getPriorityColor(approval.priority)}">
                                ${approval.priority}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${approval.description}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span>Submitted: ${new Date(approval.submitted_at).toLocaleDateString()}</span>
                            <span>Step: ${approval.current_step}/${approval.total_steps}</span>
                            <span>Current: ${approval.workflow_step}</span>
                        </div>
                    </div>
                    <div class="flex space-x-2 ml-4">
                        <button onclick="showDetails(${approval.id})" 
                                class="text-indigo-600 hover:text-indigo-900">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, 'approve')" 
                                class="text-green-600 hover:text-green-900">
                            <i data-feather="check" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, 'reject')" 
                                class="text-red-600 hover:text-red-900">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, 'forward')" 
                                class="text-blue-600 hover:text-blue-900">
                            <i data-feather="share" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    renderHistory(history) {
        const container = document.getElementById('historyList');
        
        if (history.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i data-feather="clock" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No approval history</p>
                    <p class="text-sm text-gray-500">No approvals have been processed yet.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = history.map(item => `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <h4 class="text-sm font-medium text-gray-900">${item.title}</h4>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getActionColor(item.action)}">
                                ${item.action}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">${item.comments || 'No comments'}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                            <span>By: ${item.performed_by}</span>
                            <span>${new Date(item.performed_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    getPriorityColor(priority) {
        const colors = {
            'high': 'bg-red-100 text-red-800',
            'medium': 'bg-yellow-100 text-yellow-800',
            'low': 'bg-green-100 text-green-800'
        };
        return colors[priority] || 'bg-gray-100 text-gray-800';
    }

    getActionColor(action) {
        const colors = {
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'forwarded': 'bg-blue-100 text-blue-800'
        };
        return colors[action] || 'bg-gray-100 text-gray-800';
    }

    getWorkflowTypeLabel(type) {
        const labels = {
            'user_registration' => 'User Registration',
            'client_registration' => 'Client Registration',
            'job_vacancy' => 'Job Vacancy',
            'hr_interview' => 'HR Interview',
            'technical_interview' => 'Technical Interview',
            'employee_registration' => 'Employee Registration',
            'employee_documents' => 'Employee Documents',
            'social_records' => 'Social Records',
            'induction_training' => 'Induction Training',
            'personnel_id' => 'Personnel ID',
            'contract_management' => 'Contract Management',
            'employment_contracts' => 'Employment Contracts'
        };
        return labels[type] || type;
    }

    showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Remove active state from all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-indigo-500', 'text-indigo-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab
        document.getElementById(`${tabName}-tab`).classList.remove('hidden');

        // Add active state to selected button
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-indigo-500', 'text-indigo-600');

        // Load analytics data if analytics tab is shown
        if (tabName === 'analytics') {
            this.loadAnalytics();
        }
    }

    async loadAnalytics() {
        try {
            const response = await fetch('/workflow/analytics');
            const result = await response.json();

            if (result.success) {
                const analytics = result.analytics;
                
                // Update bottlenecks table
                const bottlenecksTable = document.getElementById('bottlenecksTable');
                bottlenecksTable.innerHTML = analytics.bottlenecks.map(bottleneck => `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${bottleneck.step}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${bottleneck.avg_time}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${bottleneck.queue_size}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${bottleneck.avg_time > 30 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                                ${bottleneck.avg_time > 30 ? 'Critical' : 'Normal'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (error) {
            console.error('Failed to load analytics:', error);
        }
    }

    async submitAction() {
        const form = document.getElementById('actionForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        this.setActionLoadingState(true);

        try {
            let url, method;
            
            switch (data.action) {
                case 'approve':
                    url = '/workflow/approve';
                    method = 'POST';
                    break;
                case 'reject':
                    url = '/workflow/reject';
                    method = 'POST';
                    break;
                case 'forward':
                    url = '/workflow/forward';
                    method = 'POST';
                    break;
                default:
                    throw new Error('Invalid action');
            }

            const response = await fetch(url, {
                method: method,
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
                hideActionModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Action failed', 'error');
            }
        } catch (error) {
            console.error('Action submission error:', error);
            this.showNotification('An error occurred during the action', 'error');
        } finally {
            this.setActionLoadingState(false);
        }
    }

    setActionLoadingState(loading) {
        const btnText = document.getElementById('actionBtnText');
        const btnLoader = document.getElementById('actionBtnLoader');
        const actionBtn = document.getElementById('actionBtn');

        if (loading) {
            btnText.textContent = 'Processing...';
            btnLoader.classList.remove('hidden');
            actionBtn.disabled = true;
        } else {
            btnText.textContent = 'Submit';
            btnLoader.classList.add('hidden');
            actionBtn.disabled = false;
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
function showActionModal(workflowId, action) {
    document.getElementById('workflowId').value = workflowId;
    document.getElementById('actionType').value = action;
    
    const modal = document.getElementById('actionModal');
    const title = document.getElementById('actionModalTitle');
    const rejectSection = document.getElementById('rejectReasonSection');
    const resubmissionSection = document.getElementById('resubmissionSection');
    const forwardSection = document.getElementById('forwardSection');
    const actionBtn = document.getElementById('actionBtnText');
    
    // Hide all optional sections
    rejectSection.classList.add('hidden');
    resubmissionSection.classList.add('hidden');
    forwardSection.classList.add('hidden');
    
    switch (action) {
        case 'approve':
            title.textContent = 'Approve Workflow Item';
            actionBtn.textContent = 'Approve';
            modal.querySelector('#actionBtn').className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center';
            break;
        case 'reject':
            title.textContent = 'Reject Workflow Item';
            actionBtn.textContent = 'Reject';
            modal.querySelector('#actionBtn').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center';
            rejectSection.classList.remove('hidden');
            resubmissionSection.classList.remove('hidden');
            break;
        case 'forward':
            title.textContent = 'Forward Workflow Item';
            actionBtn.textContent = 'Forward';
            modal.querySelector('#actionBtn').className = 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center';
            forwardSection.classList.remove('hidden');
            break;
    }
    
    modal.classList.remove('hidden');
}

function hideActionModal() {
    document.getElementById('actionModal').classList.add('hidden');
    document.getElementById('actionForm').reset();
}

async function showDetails(workflowId) {
    try {
        const response = await fetch(`/workflow/details/${workflowId}`);
        const result = await response.json();

        if (result.success) {
            const details = result.details;
            const content = document.getElementById('detailsContent');
            
            content.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">${details.title}</h4>
                        <p class="text-sm text-gray-600">${details.description}</p>
                    </div>
                    <div class="border-t pt-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Workflow Progress</h5>
                        <div class="space-y-2">
                            ${details.workflow_steps.map(step => `
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="w-2 h-2 rounded-full mr-2 ${step.status === 'completed' ? 'bg-green-400' : step.status === 'pending' ? 'bg-yellow-400' : 'bg-gray-300'}"></span>
                                        <span class="text-sm">${step.name}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">${step.assignee}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Comments</h5>
                        <div class="space-y-2">
                            ${details.comments.map(comment => `
                                <div class="text-sm">
                                    <span class="font-medium">${comment.author}:</span> ${comment.comment}
                                    <div class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleDateString()}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detailsModal').classList.remove('hidden');
        } else {
            window.workflowManager.showNotification('Failed to load workflow details', 'error');
        }
    } catch (error) {
        console.error('Failed to load workflow details:', error);
        window.workflowManager.showNotification('An error occurred', 'error');
    }
}

function hideDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function showAnalyticsModal() {
    document.getElementById('analyticsModal').classList.remove('hidden');
}

function hideAnalyticsModal() {
    document.getElementById('analyticsModal').classList.add('hidden');
}

function showCalendarModal() {
    document.getElementById('calendarModal').classList.remove('hidden');
    loadCalendarEvents();
}

function hideCalendarModal() {
    document.getElementById('calendarModal').classList.add('hidden');
}

async function loadCalendarEvents() {
    try {
        const response = await fetch('/workflow/calendar');
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
                    <div class="text-xs text-blue-600">${event.type}</div>
                `;
                container.appendChild(eventDiv);
            });
        } else {
            window.workflowManager.showNotification('Failed to load calendar events', 'error');
        }
    } catch (error) {
        console.error('Failed to load calendar events:', error);
        window.workflowManager.showNotification('An error occurred', 'error');
    }
}

// Initialize workflow manager
document.addEventListener('DOMContentLoaded', function() {
    window.workflowManager = new WorkflowManager();
});
</script>
@endpush
