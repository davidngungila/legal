@extends('layouts.app')

@section('title', 'Case Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Case Management</h1>
            <p class="text-gray-600 mt-2">Manage HR cases, investigations, grievances, and legal documentation for {{ $currentClient->name }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('casemanagement.export') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </a>
            <button type="button" onclick="openCreateCaseModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Case
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="folder" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">{{ $cases->count() }} listed</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</h3>
            <p class="text-gray-600 text-sm">Active Cases</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">Needs follow-up</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pending_review'] }}</h3>
            <p class="text-gray-600 text-sm">Pending Review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">{{ now()->format('M Y') }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['resolved_this_month'] }}</h3>
            <p class="text-gray-600 text-sm">Resolved This Month</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">Immediate focus</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['high_priority'] }}</h3>
            <p class="text-gray-600 text-sm">High Priority</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Active Cases</h3>
            <form method="GET" action="{{ route('casemanagement.index') }}" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3 w-full lg:max-w-5xl">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search case, employee, subject..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    @foreach(['disciplinary' => 'Disciplinary', 'grievance' => 'Grievance', 'complaint' => 'Complaint', 'legal' => 'Legal'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Statuses</option>
                    @foreach(['pending' => 'Pending', 'review' => 'Review', 'under_investigation' => 'Under Investigation', 'documentation' => 'Documentation', 'resolution' => 'Resolution', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="priority" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Priorities</option>
                    @foreach(['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm inline-flex items-center">
                        <i data-feather="filter" class="w-4 h-4 inline mr-2"></i>
                        Filter
                    </button>
                    <a href="{{ route('casemanagement.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm inline-flex items-center">Clear</a>
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Opened</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cases as $case)
                        @php
                            $caseTypeColor = match($case->case_type) {
                                'disciplinary' => 'red',
                                'grievance' => 'yellow',
                                'complaint' => 'orange',
                                default => 'purple',
                            };
                            $priorityColor = match($case->priority) {
                                'high' => 'red',
                                'medium' => 'yellow',
                                default => 'green',
                            };
                            $statusColor = match($case->status) {
                                'under_investigation' => 'red',
                                'review' => 'yellow',
                                'documentation' => 'blue',
                                'resolution' => 'purple',
                                'resolved' => 'green',
                                'closed' => 'gray',
                                default => 'gray',
                            };
                            $employeeName = trim(($case->employee?->first_name ?? '') . ' ' . ($case->employee?->last_name ?? '')) ?: 'Unassigned Employee';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $case->case_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">{{ strtoupper(substr($employeeName, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $employeeName }}</div>
                                        <div class="text-xs text-gray-500">{{ $case->employee?->employee_id ?? 'No employee code' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $caseTypeColor }}-100 text-{{ $caseTypeColor }}-800">{{ ucfirst($case->case_type) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 min-w-[220px]">{{ $case->subject }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($case->opened_date)->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $priorityColor }}-100 text-{{ $priorityColor }}-800">{{ ucfirst($case->priority) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">{{ ucwords(str_replace('_', ' ', $case->status)) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openViewCaseModal({{ $case->id }})" class="inline-flex items-center px-3 py-1.5 text-xs rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                        <i data-feather="eye" class="w-4 h-4 mr-1.5"></i>
                                        View
                                    </button>
                                    <button type="button" onclick="openEditCaseModal({{ $case->id }})" class="inline-flex items-center px-3 py-1.5 text-xs rounded-md border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100">
                                        <i data-feather="edit-2" class="w-4 h-4 mr-1.5"></i>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-feather="folder" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-900 mb-1">No case records found</p>
                                <p class="text-sm text-gray-500 mb-4">Create a new case or adjust your filters to see more results.</p>
                                <button type="button" onclick="openCreateCaseModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                                    <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                    Create First Case
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @foreach($categories as $category)
        <a href="{{ route('casemanagement.index', ['type' => $category['slug']]) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-{{ $category['color'] }}-100 rounded-lg flex items-center justify-center">
                    <i data-feather="{{ $category['icon'] }}" class="w-6 h-6 text-{{ $category['color'] }}-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $category['count'] }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">{{ $category['type'] }} Cases</h3>
            <p class="text-sm text-gray-600 mb-4">Active {{ strtolower($category['type']) }} cases under review</p>
            <span class="text-{{ $category['color'] }}-600 hover:text-{{ $category['color'] }}-800 text-sm font-medium">View All -&gt;</span>
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Case Activities</h3>
            <span class="text-sm text-gray-500">{{ $recentActivities->count() }} recent updates</span>
        </div>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-{{ $activity->action_color }}-100 rounded-lg flex items-center justify-center mr-4">
                        <i data-feather="{{ $activity->action_icon }}" class="w-5 h-5 text-{{ $activity->action_color }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $activity->legalCase?->case_number ?? 'Case' }}
                                    @if($activity->legalCase?->employee)
                                        • {{ trim($activity->legalCase->employee->first_name . ' ' . $activity->legalCase->employee->last_name) }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $activity->created_at?->diffForHumans() }}</p>
                                <p class="text-xs text-gray-500">{{ trim(($activity->user?->first_name ?? '') . ' ' . ($activity->user?->last_name ?? '')) ?: 'System' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="activity" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                    <p class="text-sm">No case activities yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Legal Document Templates</h3>
            <button type="button" onclick="openTemplateModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Create Template
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $index => $template)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $template['status'] }}</span>
                    <span class="text-xs text-gray-500">{{ $template['uses'] }} uses</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i data-feather="file-text" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $template['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ ucfirst($template['type']) }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" onclick="useTemplate('{{ $template['type'] }}', @js($template['subject']))" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Use Template</button>
                    <button type="button" onclick="openTemplateModal({{ $index }})" class="text-gray-600 hover:text-gray-800 text-sm font-medium underline">Edit</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="caseModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="caseModalTitle" class="text-xl font-semibold text-gray-900">Create Case</h3>
                <p class="text-sm text-gray-500">Capture case details and assign follow-up responsibility.</p>
            </div>
            <button type="button" onclick="closeCaseModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="caseForm" method="POST" action="{{ route('casemanagement.store') }}" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="_method" id="caseFormMethod" value="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                    <select name="employee_id" id="caseEmployee" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ trim($employee->first_name . ' ' . $employee->last_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Case Type</label>
                    <select name="case_type" id="caseType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="disciplinary">Disciplinary</option>
                        <option value="grievance">Grievance</option>
                        <option value="complaint">Complaint</option>
                        <option value="legal">Legal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                    <select name="assigned_to" id="caseAssignedTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Auto assign to current user</option>
                        @foreach($assignees as $assignee)
                            <option value="{{ $assignee->id }}">{{ trim(($assignee->first_name ?? '') . ' ' . ($assignee->last_name ?? '')) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" name="subject" id="caseSubject" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="caseDescription" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opened Date</label>
                    <input type="date" name="opened_date" id="caseOpenedDate" value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" id="caseDueDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="casePriority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="caseStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="pending">Pending</option>
                        <option value="review">Review</option>
                        <option value="under_investigation">Under Investigation</option>
                        <option value="documentation">Documentation</option>
                        <option value="resolution">Resolution</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution / Follow-up Notes</label>
                    <textarea name="resolution_notes" id="caseResolutionNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeCaseModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                    <span id="caseSubmitLabel">Save Case</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="viewCaseModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Case Details</h3>
                <p class="text-sm text-gray-500">Review the selected case summary and status.</p>
            </div>
            <button type="button" onclick="closeViewCaseModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Case Number</p>
                    <p id="viewCaseNumber" class="text-sm font-semibold text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Employee</p>
                    <p id="viewCaseEmployee" class="text-sm font-semibold text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Type</p>
                    <p id="viewCaseType" class="text-sm text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                    <p id="viewCaseStatus" class="text-sm text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Priority</p>
                    <p id="viewCasePriority" class="text-sm text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Assigned To</p>
                    <p id="viewCaseAssigned" class="text-sm text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Opened Date</p>
                    <p id="viewCaseOpenedDate" class="text-sm text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Due Date</p>
                    <p id="viewCaseDueDate" class="text-sm text-gray-900 mt-1"></p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Subject</p>
                <p id="viewCaseSubject" class="text-sm font-semibold text-gray-900 mt-1"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Description</p>
                <p id="viewCaseDescription" class="text-sm text-gray-700 mt-1 whitespace-pre-line"></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Resolution Notes</p>
                <p id="viewCaseResolutionNotes" class="text-sm text-gray-700 mt-1 whitespace-pre-line"></p>
            </div>
            <div class="flex justify-end">
                <button type="button" id="viewEditCaseBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                    <i data-feather="edit-2" class="w-4 h-4 mr-2"></i>
                    Edit Case
                </button>
            </div>
        </div>
    </div>
</div>

<div id="templateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="templateModalTitle" class="text-xl font-semibold text-gray-900">Create Template</h3>
                <p class="text-sm text-gray-500">Create or edit a legal document template.</p>
            </div>
            <button type="button" onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="templateForm" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="templateId">
            <input type="hidden" id="templateMethod" name="_method" value="POST">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                <input type="text" id="templateName" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Case Type</label>
                <select id="templateType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="disciplinary">Disciplinary</option>
                    <option value="grievance">Grievance</option>
                    <option value="complaint">Complaint</option>
                    <option value="legal">Legal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Default Subject</label>
                <input type="text" id="templateSubject" name="subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="templateStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeTemplateModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Save Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const caseBaseUrl = '{{ url('/casemanagement') }}';
const caseStoreUrl = '{{ route('casemanagement.store') }}';
const casesMap = new Map((@json($casesJson)).map(item => [item.id, item]));

function resetCaseForm() {
    const form = document.getElementById('caseForm');
    form.action = caseStoreUrl;
    document.getElementById('caseFormMethod').value = 'POST';
    document.getElementById('caseModalTitle').textContent = 'Create Case';
    document.getElementById('caseSubmitLabel').textContent = 'Save Case';
    form.reset();
    document.getElementById('caseOpenedDate').value = '{{ now()->format('Y-m-d') }}';
    document.getElementById('casePriority').value = 'medium';
    document.getElementById('caseStatus').value = 'pending';
}

function openCreateCaseModal() {
    resetCaseForm();
    document.getElementById('caseModal').classList.remove('hidden');
    if (typeof feather !== 'undefined') feather.replace();
}

function closeCaseModal() {
    document.getElementById('caseModal').classList.add('hidden');
}

function openEditCaseModal(caseId) {
    const item = casesMap.get(caseId);
    if (!item) return;

    const form = document.getElementById('caseForm');
    form.action = `${caseBaseUrl}/${caseId}`;
    document.getElementById('caseFormMethod').value = 'PUT';
    document.getElementById('caseModalTitle').textContent = 'Edit Case';
    document.getElementById('caseSubmitLabel').textContent = 'Update Case';

    document.getElementById('caseEmployee').value = item.employee_id || '';
    document.getElementById('caseType').value = item.case_type || 'disciplinary';
    document.getElementById('caseAssignedTo').value = item.assigned_to || '';
    document.getElementById('caseSubject').value = item.subject || '';
    document.getElementById('caseDescription').value = item.description || '';
    document.getElementById('caseOpenedDate').value = item.opened_date || '';
    document.getElementById('caseDueDate').value = item.due_date || '';
    document.getElementById('casePriority').value = item.priority || 'medium';
    document.getElementById('caseStatus').value = item.status || 'pending';
    document.getElementById('caseResolutionNotes').value = item.resolution_notes || '';

    document.getElementById('caseModal').classList.remove('hidden');
    if (typeof feather !== 'undefined') feather.replace();
}

function openViewCaseModal(caseId) {
    const item = casesMap.get(caseId);
    if (!item) return;

    document.getElementById('viewCaseNumber').textContent = item.case_number || '-';
    document.getElementById('viewCaseEmployee').textContent = item.employee_name || 'Unassigned Employee';
    document.getElementById('viewCaseType').textContent = titleCase(item.case_type);
    document.getElementById('viewCaseStatus').textContent = titleCase((item.status || '').replaceAll('_', ' '));
    document.getElementById('viewCasePriority').textContent = titleCase(item.priority);
    document.getElementById('viewCaseAssigned').textContent = item.assigned_to_name || 'Not assigned';
    document.getElementById('viewCaseOpenedDate').textContent = item.opened_date || '-';
    document.getElementById('viewCaseDueDate').textContent = item.due_date || '-';
    document.getElementById('viewCaseSubject').textContent = item.subject || '-';
    document.getElementById('viewCaseDescription').textContent = item.description || '-';
    document.getElementById('viewCaseResolutionNotes').textContent = item.resolution_notes || 'No resolution notes yet.';
    document.getElementById('viewEditCaseBtn').onclick = function () {
        closeViewCaseModal();
        openEditCaseModal(caseId);
    };

    document.getElementById('viewCaseModal').classList.remove('hidden');
    if (typeof feather !== 'undefined') feather.replace();
}

function closeViewCaseModal() {
    document.getElementById('viewCaseModal').classList.add('hidden');
}

function useTemplate(type, subject) {
    openCreateCaseModal();
    document.getElementById('caseType').value = type;
    document.getElementById('caseSubject').value = subject || '';
    document.getElementById('caseDescription').value = `Template started for ${titleCase(type)} case: ${subject || ''}`;
}

let templatesData = @js($templates);

function openTemplateModal(templateIndex = null) {
        const modal = document.getElementById('templateModal');
        const title = document.getElementById('templateModalTitle');
        const form = document.getElementById('templateForm');
        const methodInput = document.getElementById('templateMethod');
        
        if (templateIndex !== null) {
            const template = templatesData[templateIndex];
            title.textContent = 'Edit Template';
            document.getElementById('templateId').value = templateIndex;
            document.getElementById('templateName').value = template.name;
            document.getElementById('templateType').value = template.type;
            document.getElementById('templateSubject').value = template.subject;
            document.getElementById('templateStatus').value = template.status;
            // Set form action to update route
            form.action = '{{ route('casemanagement.index') }}/templates/' + templateIndex;
            methodInput.value = 'PUT';
        } else {
            title.textContent = 'Create Template';
            form.reset();
            document.getElementById('templateId').value = '';
            // Set form action to store route
            form.action = '{{ route('casemanagement.templates.store') }}';
            methodInput.value = 'POST';
        }
        
        modal.classList.remove('hidden');
        if (typeof feather !== 'undefined') feather.replace();
    }

function closeTemplateModal() {
    document.getElementById('templateModal').classList.add('hidden');
}

function titleCase(value) {
    if (!value) return '-';
    return String(value)
        .split(' ')
        .map(word => word ? word.charAt(0).toUpperCase() + word.slice(1) : '')
        .join(' ');
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
