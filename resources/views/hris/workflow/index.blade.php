@extends('layouts.app')

@section('title', 'Workflow Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Workflow Management</h1>
            <p class="text-gray-600 mt-2">Approve, forward and track employee-related workflow items</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showCalendarModal()"
                    class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Calendar
            </button>
            <button onclick="showAnalyticsModal()"
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="trending-up" class="w-4 h-4 mr-2"></i>
                Analytics
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
            <nav class="flex -mb-px overflow-x-auto">
                <button onclick="showTab('pending')"
                        class="tab-btn py-4 px-6 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600 whitespace-nowrap"
                        data-tab="pending">
                    Pending Approvals
                </button>
                <button onclick="showTab('history')"
                        class="tab-btn py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                        data-tab="history">
                    Approval History
                </button>
                <button onclick="showTab('analytics')"
                        class="tab-btn py-4 px-6 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                        data-tab="analytics">
                    Analytics
                </button>
            </nav>
        </div>

        <!-- Pending Approvals Tab -->
        <div id="pending-tab" class="tab-content p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
                <p class="text-sm text-gray-500">Items awaiting your approval</p>
                <div class="relative w-full md:w-72">
                    <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="pendingSearch" oninput="window.workflowManager.filterPending(this.value)"
                           placeholder="Search pending approvals..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Trends (Last 6 Months)</h3>
                    <div class="h-72 relative">
                        <canvas id="approvalTrendsChart"></canvas>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Average Approval Time (Hours)</h3>
                    <div class="h-72 relative">
                        <canvas id="approvalTimesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Bottlenecks</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workflow Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
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

<!-- Approve/Reject/Forward Modal -->
<x-advanced-modal id="actionModal" title="Action Required" title-id="actionModalTitle"
                  description="Review and take action on this workflow request" icon="check-circle" color="indigo" size="lg">
    <form id="actionForm" class="space-y-4">
        <input type="hidden" name="workflow_id" id="workflowId">
        <input type="hidden" name="workflow_type" id="workflowType">
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
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideActionModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="actionForm" id="actionBtn"
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
    </x-slot:footer>
</x-advanced-modal>

<!-- Details Modal -->
<x-advanced-modal id="detailsModal" title="Workflow Details"
                  description="Full details, timeline and comments" icon="file-text" color="blue" size="2xl">
    <div id="detailsContent">
        <!-- Will be populated dynamically -->
    </div>
    <div class="mt-6 border-t pt-4">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Add Comment</h4>
        <div class="flex space-x-2">
            <input type="text" id="commentInput" placeholder="Type your comment..."
                   class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <button onclick="window.workflowManager.addComment()"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="message-square" class="w-4 h-4 mr-1"></i>
                Add
            </button>
        </div>
    </div>
    <x-slot:footer>
        <button onclick="hideDetailsModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Close
        </button>
    </x-slot:footer>
</x-advanced-modal>

<!-- Calendar Modal -->
<x-advanced-modal id="calendarModal" title="Workflow Calendar & Deadlines"
                  description="Upcoming deadlines and scheduled workflows" icon="calendar" color="purple" size="xl">
    <div class="space-y-2" id="calendarEvents">
        <!-- Will be populated dynamically -->
    </div>
    <x-slot:footer>
        <button onclick="hideCalendarModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Close
        </button>
    </x-slot:footer>
</x-advanced-modal>

<!-- Analytics Modal -->
<x-advanced-modal id="analyticsModal" title="Workflow Analytics"
                  description="Performance metrics and workflow distribution" icon="trending-up" color="green" size="xl">
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Workflows</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" id="modalTotalWorkflows">-</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Approval Rate</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" id="modalApprovalRate">-</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avg Approval Time</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" id="modalAvgApprovalTime">-</p>
            </div>
        </div>
        <div class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">By Workflow Type</h4>
            <div class="space-y-2" id="workflowTypeStats">
                <!-- Will be populated dynamically -->
            </div>
        </div>
    </div>
    <x-slot:footer>
        <button onclick="hideAnalyticsModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Close
        </button>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
// Workflow Management System
class WorkflowManager {
    constructor() {
        this.pendingData = [];
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
        const actionForm = document.getElementById('actionForm');
        actionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitAction();
        });

        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                showTab(button.dataset.tab);
            });
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/workflow/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;

                document.getElementById('pendingApprovals').textContent = stats.pending_approvals;
                document.getElementById('approvedToday').textContent = stats.approved_today;
                document.getElementById('rejectedToday').textContent = stats.rejected_today;
                document.getElementById('overdueApprovals').textContent = stats.overdue_approvals;

                document.getElementById('modalTotalWorkflows').textContent = stats.total_workflows;
                document.getElementById('modalApprovalRate').textContent = (stats.approval_rate || 0) + '%';
                document.getElementById('modalAvgApprovalTime').textContent = (stats.avg_approval_time_hours || 0) + ' hours';

                const typesContainer = document.getElementById('workflowTypeStats');
                typesContainer.innerHTML = '';
                Object.entries(stats.by_type || {}).forEach(([type, count]) => {
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
                this.pendingData = result.approvals || [];
                this.renderPendingApprovals(this.pendingData);
            }
        } catch (error) {
            console.error('Failed to load pending approvals:', error);
        }
    }

    filterPending(query) {
        const term = query.toLowerCase().trim();
        const filtered = term
            ? this.pendingData.filter(a =>
                (a.title || '').toLowerCase().includes(term) ||
                (a.type_label || '').toLowerCase().includes(term) ||
                (a.submitted_by || '').toLowerCase().includes(term))
            : this.pendingData;
        this.renderPendingApprovals(filtered);
    }

    async loadHistory() {
        try {
            const response = await fetch('/workflow/history');
            const result = await response.json();

            if (result.success) {
                this.renderHistory(result.history || []);
            }
        } catch (error) {
            console.error('Failed to load history:', error);
        }
    }

    renderPendingApprovals(approvals) {
        const container = document.getElementById('pendingApprovalsList');

        if (approvals.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i data-feather="check-circle" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No pending approvals</p>
                    <p class="text-sm text-gray-500">All workflows are up to date.</p>
                </div>
            `;
            this.initializeFeather();
            return;
        }

        container.innerHTML = approvals.map(approval => `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2 flex-wrap gap-y-1">
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 whitespace-nowrap">
                                ${approval.type_label || this.getWorkflowTypeLabel(approval.type)}
                            </span>
                            <h4 class="text-sm font-medium text-gray-900">${approval.title}</h4>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getPriorityColor(approval.priority)}">
                                ${approval.priority}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${approval.description}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 flex-wrap gap-y-1">
                            <span class="flex items-center"><i data-feather="user" class="w-3 h-3 mr-1"></i>${approval.submitted_by}</span>
                            <span>Submitted: ${new Date(approval.submitted_at).toLocaleDateString()}</span>
                            <span>Step: ${approval.current_step}/${approval.total_steps}</span>
                            <span class="flex items-center"><i data-feather="git-branch" class="w-3 h-3 mr-1"></i>${approval.workflow_step}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full ${this.getProgressColor(approval)} rounded-full" style="width: ${(approval.current_step / approval.total_steps) * 100}%"></div>
                        </div>
                    </div>
                    <div class="flex space-x-2 ml-4">
                        <button onclick="showDetails(${approval.id}, '${approval.type}')" title="View Details"
                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                            <i data-feather="eye" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, '${approval.type}', 'approve')" title="Approve"
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg">
                            <i data-feather="check" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, '${approval.type}', 'reject')" title="Reject"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                        <button onclick="showActionModal(${approval.id}, '${approval.type}', 'forward')" title="Forward"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                            <i data-feather="share" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        this.initializeFeather();
    }

    renderHistory(history) {
        const container = document.getElementById('historyList');

        if (history.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i data-feather="clock" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No approval history</p>
                    <p class="text-sm text-gray-500">No approvals have been processed yet.</p>
                </div>
            `;
            this.initializeFeather();
            return;
        }

        container.innerHTML = history.map(item => `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1 flex-wrap gap-y-1">
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800 whitespace-nowrap">
                                ${item.type_label || this.getWorkflowTypeLabel(item.type)}
                            </span>
                            <h4 class="text-sm font-medium text-gray-900">${item.title}</h4>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${this.getActionColor(item.action)}">
                                ${item.action}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">${item.comments || 'No comments'}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                            <span class="flex items-center"><i data-feather="user-check" class="w-3 h-3 mr-1"></i>${item.performed_by}</span>
                            <span>${new Date(item.performed_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                    <button onclick="showDetails(${item.id}, '${item.type}')" title="View Details"
                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg ml-4">
                        <i data-feather="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `).join('');

        this.initializeFeather();
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

    getProgressColor(approval) {
        if (approval.current_step >= approval.total_steps) return 'bg-green-500';
        if (approval.priority === 'high') return 'bg-red-500';
        return 'bg-indigo-500';
    }

    getWorkflowTypeLabel(type) {
        const labels = {
            'user_registration': 'User Registration',
            'client_registration': 'Client Registration',
            'employee_registration': 'Employee Registration',
            'job_vacancy': 'Job Vacancy',
            'hr_interview': 'HR Interview',
            'technical_interview': 'Technical Interview',
            'employee_documents': 'Employee Documents',
            'social_records': 'Social Records',
            'induction_training': 'Induction Training',
            'personnel_id': 'Personnel ID',
            'contract_management': 'Contract Management',
            'employment_contracts': 'Employment Contracts'
        };
        return labels[type] || type;
    }

    async submitAction() {
        const form = document.getElementById('actionForm');
        const data = {
            workflow_id: form.workflowId.value,
            workflow_type: form.workflowType.value,
            action: form.actionType.value,
            comments: form.comments ? form.comments.value : '',
        };

        let url = '';
        let payload = { ...data };

        switch (data.action) {
            case 'approve':
                url = '/workflow/approve';
                delete payload.action;
                break;
            case 'reject': {
                const reason = document.getElementById('rejectReason').value.trim();
                if (!reason) {
                    this.showNotification('Please provide a rejection reason', 'error');
                    return;
                }
                url = '/workflow/reject';
                payload.reason = reason;
                payload.allow_resubmission = document.getElementById('allowResubmission').checked;
                payload.resubmission_instructions = form.resubmission_instructions ? form.resubmission_instructions.value : '';
                delete payload.action;
                break;
            }
            case 'forward': {
                const forwardTo = document.getElementById('forwardTo').value.trim();
                const forwardReason = form.forward_reason.value.trim();
                if (!forwardTo || !forwardReason) {
                    this.showNotification('Please provide a forwarding recipient and reason', 'error');
                    return;
                }
                url = '/workflow/forward';
                payload.forward_to = forwardTo;
                payload.reason = forwardReason;
                delete payload.action;
                delete payload.comments;
                break;
            }
            default:
                this.showNotification('Invalid action', 'error');
                return;
        }

        this.setActionLoadingState(true);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
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

    async addComment() {
        const input = document.getElementById('commentInput');
        const comment = input.value.trim();
        if (!comment) {
            this.showNotification('Please type a comment', 'error');
            return;
        }
        if (!this.currentDetails) {
            this.showNotification('No workflow item selected', 'error');
            return;
        }

        try {
            const response = await fetch('/workflow/add-comment', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    workflow_id: this.currentDetails.id,
                    workflow_type: this.currentDetails.type,
                    comment: comment
                })
            });

            const result = await response.json();

            if (result.success) {
                input.value = '';
                this.showNotification('Comment added', 'success');
                await showDetails(this.currentDetails.id, this.currentDetails.type, true);
            } else {
                this.showNotification(result.message || 'Failed to add comment', 'error');
            }
        } catch (error) {
            console.error('Comment submission error:', error);
            this.showNotification('An error occurred adding the comment', 'error');
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
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
}

// Tab functions
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-500', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById(`${tabName}-tab`).classList.remove('hidden');

    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-indigo-500', 'text-indigo-600');

    if (tabName === 'analytics') {
        loadAnalytics();
    }
}

// Action modal functions
function showActionModal(workflowId, workflowType, action) {
    document.getElementById('workflowId').value = workflowId;
    document.getElementById('workflowType').value = workflowType;
    document.getElementById('actionType').value = action;

    const modal = document.getElementById('actionModal');
    const title = document.getElementById('actionModalTitle');
    const rejectSection = document.getElementById('rejectReasonSection');
    const resubmissionSection = document.getElementById('resubmissionSection');
    const forwardSection = document.getElementById('forwardSection');
    const actionBtn = document.getElementById('actionBtnText');

    rejectSection.classList.add('hidden');
    resubmissionSection.classList.add('hidden');
    forwardSection.classList.add('hidden');

    switch (action) {
        case 'approve':
            title.textContent = 'Approve Workflow Item';
            actionBtn.textContent = 'Approve';
            document.getElementById('actionBtn').className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center';
            break;
        case 'reject':
            title.textContent = 'Reject Workflow Item';
            actionBtn.textContent = 'Reject';
            document.getElementById('actionBtn').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center';
            rejectSection.classList.remove('hidden');
            resubmissionSection.classList.remove('hidden');
            break;
        case 'forward':
            title.textContent = 'Forward Workflow Item';
            actionBtn.textContent = 'Forward';
            document.getElementById('actionBtn').className = 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center';
            forwardSection.classList.remove('hidden');
            break;
    }

    openModal('actionModal');
}

function hideActionModal() {
    closeModal('actionModal');
    document.getElementById('actionForm').reset();
}

// Details modal functions
async function showDetails(workflowId, workflowType, keepModalOpen = false) {
    if (typeof window.workflowManager === 'undefined' || !window.workflowManager) return;

    try {
        const response = await fetch(`/workflow/details/${workflowId}?type=${encodeURIComponent(workflowType)}`);
        const result = await response.json();

        if (result.success) {
            const details = result.details;
            window.workflowManager.currentDetails = details;
            const content = document.getElementById('detailsContent');

            content.innerHTML = `
                <div class="space-y-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-2 mb-1 flex-wrap gap-y-1">
                                <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">${details.type_label}</span>
                                <h4 class="text-base font-semibold text-gray-900">${details.title}</h4>
                            </div>
                            <p class="text-sm text-gray-600">${details.description}</p>
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusBadgeClass(details.status)} whitespace-nowrap">${details.status.replace(/_/g, ' ')}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">Submitted by:</span> <span class="font-medium">${details.submitted_by}</span></div>
                        <div><span class="text-gray-500">Submitted:</span> <span class="font-medium">${new Date(details.submitted_at).toLocaleString()}</span></div>
                        <div><span class="text-gray-500">Priority:</span> <span class="font-medium capitalize">${details.priority}</span></div>
                        <div><span class="text-gray-500">Progress:</span> <span class="font-medium">Step ${details.current_step} of ${details.total_steps}</span></div>
                    </div>

                    <div class="border-t pt-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-3">Workflow Progress</h5>
                        <div class="space-y-3">
                            ${(details.workflow_steps || []).map(step => `
                                <div class="flex items-start space-x-3">
                                    <div class="mt-1 flex flex-col items-center">
                                        <span class="w-2.5 h-2.5 rounded-full ${step.status === 'completed' ? 'bg-green-500' : step.status === 'rejected' ? 'bg-red-500' : step.status === 'pending' ? 'bg-yellow-400' : 'bg-gray-300'}"></span>
                                        ${step.step < (details.total_steps) ? '<span class="w-0.5 h-4 bg-gray-200"></span>' : ''}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium ${step.status === 'completed' ? 'text-gray-900' : step.status === 'rejected' ? 'text-red-600' : 'text-gray-600'}">${step.name}</span>
                                            <span class="text-xs text-gray-500">${step.assignee}</span>
                                        </div>
                                        ${step.comments ? `<p class="text-xs text-gray-500 mt-0.5">${step.comments}</p>` : ''}
                                        ${step.completed_at ? `<p class="text-xs text-gray-400">${new Date(step.completed_at).toLocaleString()}</p>` : ''}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    ${(details.attachments || []).length > 0 ? `
                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-900 mb-2">Attachments</h5>
                            <div class="space-y-2">
                                ${details.attachments.map(att => `
                                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                                        <span class="flex items-center text-gray-700"><i data-feather="paperclip" class="w-3.5 h-3.5 mr-2 text-gray-400"></i>${att.name}</span>
                                        <span class="text-xs text-gray-500">${att.uploaded_by}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}

                    <div class="border-t pt-4">
                        <h5 class="text-sm font-medium text-gray-900 mb-2">Comments</h5>
                        <div class="space-y-3" id="commentsList">
                            ${(details.comments || []).map(comment => `
                                <div class="text-sm bg-gray-50 rounded-lg px-3 py-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-900">${comment.author}</span>
                                        <span class="text-xs text-gray-400">${new Date(comment.created_at).toLocaleString()}</span>
                                    </div>
                                    <p class="text-gray-600 mt-0.5">${comment.comment}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;

            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            if (!keepModalOpen) {
                openModal('detailsModal');
            }
        } else {
            window.workflowManager.showNotification('Failed to load workflow details', 'error');
        }
    } catch (error) {
        console.error('Failed to load workflow details:', error);
        window.workflowManager.showNotification('An error occurred', 'error');
    }
}

function statusBadgeClass(status) {
    if (status === 'approved' || status === 'hr_approved' || status === 'manager_approved' || status === 'supervisor_approved') return 'bg-green-100 text-green-800';
    if (status === 'rejected') return 'bg-red-100 text-red-800';
    return 'bg-yellow-100 text-yellow-800';
}

function hideDetailsModal() {
    closeModal('detailsModal');
}

// Analytics modal functions
function showAnalyticsModal() {
    openModal('analyticsModal');
}

function hideAnalyticsModal() {
    closeModal('analyticsModal');
}

// Calendar modal functions
function showCalendarModal() {
    openModal('calendarModal');
    loadCalendarEvents();
}

function hideCalendarModal() {
    closeModal('calendarModal');
}

async function loadCalendarEvents() {
    try {
        const response = await fetch('/workflow/calendar');
        const result = await response.json();

        const container = document.getElementById('calendarEvents');

        if (result.success && result.events.length > 0) {
            container.innerHTML = '';
            result.events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'p-3 bg-gray-50 rounded-lg flex items-center justify-between';
                eventDiv.innerHTML = `
                    <div>
                        <div class="text-sm font-medium text-gray-900">${event.title}</div>
                        <div class="text-xs text-gray-500 mt-0.5">${event.start}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${calendarTypeColor(event.type)}">${event.type.replace(/_/g, ' ')}</span>
                        ${event.workflow_id && event.workflow_type ? `
                            <button onclick="showDetails(${event.workflow_id}, '${event.workflow_type}'); hideCalendarModal()" class="text-indigo-600 hover:text-indigo-900">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                        ` : ''}
                    </div>
                `;
                container.appendChild(eventDiv);
            });
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i data-feather="calendar" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-sm text-gray-500">No upcoming workflow deadlines.</p>
                </div>
            `;
        }

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    } catch (error) {
        console.error('Failed to load calendar events:', error);
        window.workflowManager.showNotification('An error occurred', 'error');
    }
}

function calendarTypeColor(type) {
    const colors = {
        'deadline': 'bg-red-100 text-red-800',
        'review': 'bg-blue-100 text-blue-800',
        'renewal': 'bg-green-100 text-green-800'
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
}

// Analytics functions
async function loadAnalytics() {
    try {
        const response = await fetch('/workflow/analytics');
        const result = await response.json();

        if (!result.success) {
            return;
        }

        const analytics = result.analytics;

        // Bottlenecks table
        const bottlenecksTable = document.getElementById('bottlenecksTable');
        bottlenecksTable.innerHTML = analytics.bottlenecks.map(bottleneck => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${window.workflowManager.getWorkflowTypeLabel(bottleneck.type)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${bottleneck.step}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${bottleneck.avg_time} hrs</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${bottleneck.queue_size}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${bottleneck.avg_time > 30 || bottleneck.queue_size > 5 ? 'bg-red-100 text-red-800' : bottleneck.queue_size > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                        ${bottleneck.avg_time > 30 || bottleneck.queue_size > 5 ? 'Critical' : bottleneck.queue_size > 0 ? 'Normal' : 'Clear'}
                    </span>
                </td>
            </tr>
        `).join('');

        // Approval trends chart
        const trends = analytics.approval_trends || [];
        const trendsCtx = document.getElementById('approvalTrendsChart');
        if (trendsCtx) {
            const existing = Chart.getChart(trendsCtx);
            if (existing) existing.destroy();

            new Chart(trendsCtx, {
                type: 'bar',
                data: {
                    labels: trends.map(t => t.month),
                    datasets: [
                        { label: 'Approved', data: trends.map(t => t.approved), backgroundColor: '#10b981' },
                        { label: 'Rejected', data: trends.map(t => t.rejected), backgroundColor: '#ef4444' },
                        { label: 'Pending', data: trends.map(t => t.pending), backgroundColor: '#f59e0b' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        }

        // Approval times chart
        const times = analytics.approval_times || {};
        const timeEntries = Object.entries(times);
        const timesCtx = document.getElementById('approvalTimesChart');
        if (timesCtx) {
            const existing = Chart.getChart(timesCtx);
            if (existing) existing.destroy();

            new Chart(timesCtx, {
                type: 'bar',
                data: {
                    labels: timeEntries.map(([key]) => window.workflowManager.getWorkflowTypeLabel(key)),
                    datasets: [{
                        label: 'Avg Hours',
                        data: timeEntries.map(([, value]) => value),
                        backgroundColor: '#6366f1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        }
    } catch (error) {
        console.error('Failed to load analytics:', error);
    }
}

// Initialize workflow manager
document.addEventListener('DOMContentLoaded', function() {
    window.workflowManager = new WorkflowManager();
});
</script>
@endpush
