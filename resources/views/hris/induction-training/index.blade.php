@extends('layouts.app')

@section('title', 'Induction Training - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Induction Training</h1>
            <p class="text-gray-600 mt-2">Manage employee induction and training programs</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showStatisticsModal()"
                    class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                Statistics
            </button>
            <button onclick="showRecordModal()"
                    class="px-4 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition-colors flex items-center">
                <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i>
                Record Training
            </button>
            <button onclick="showScheduleModal()"
                    class="px-4 py-2 border border-teal-300 text-teal-700 rounded-lg hover:bg-teal-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Schedule Training
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
                    <i data-feather="award" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Trained</p>
                    <p class="text-2xl font-semibold text-gray-900" id="trainedCount">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-2xl font-semibold text-gray-900" id="completionRate">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="clock" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Hours</p>
                    <p class="text-2xl font-semibold text-gray-900" id="avgHours">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Upcoming</p>
                    <p class="text-2xl font-semibold text-gray-900" id="upcomingCount">-</p>
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
                <select id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($allEmployees->pluck('department')->filter()->unique()->sort() as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="trainingTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Training Types</option>
                    <option value="company_policies">Company Policies</option>
                    <option value="safety_procedures">Safety Procedures</option>
                    <option value="job_specific">Job Specific</option>
                    <option value="compliance">Compliance</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="not_started">Not Started</option>
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
                            Training Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Latest Training
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hours Completed
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
                    @php $requiredModules = 4; $requiredHours = 32; @endphp
                    @forelse($employees as $employee)
                        @php
                            $completedCount = $employee->inductionTrainings->where('status', 'completed')->count();
                            $percentage = $requiredModules > 0 ? ($completedCount / $requiredModules) * 100 : 0;
                            $latestTraining = $employee->inductionTrainings->sortByDesc('training_date')->first();
                            $nextTraining = $employee->inductionTrainings->where('status', 'scheduled')->sortBy('training_date')->first();
                            $totalHours = $employee->inductionTrainings->sum('training_duration_hours');
                            $rowStatus = $percentage >= 100 ? 'completed' : ($completedCount > 0 ? 'in_progress' : 'not_started');
                            $statusClass = $rowStatus === 'completed' ? 'bg-green-100 text-green-800'
                                : ($rowStatus === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800');
                            $statusLabel = $rowStatus === 'completed' ? 'Completed'
                                : ($rowStatus === 'in_progress' ? 'In Progress' : 'Not Started');
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors employee-row"
                            data-name="{{ strtolower($employee->first_name . ' ' . $employee->last_name) }}"
                            data-department="{{ strtolower($employee->department ?? '') }}"
                            data-training-type="{{ $latestTraining?->training_type ?? '' }}"
                            data-status="{{ $rowStatus }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-sm">
                                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->department }}@if($employee->position) &middot; {{ $employee->position }}@endif</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 0) }}% Complete</div>
                                <div class="text-xs text-gray-400">{{ $completedCount }} of {{ $requiredModules }} modules</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestTraining)
                                    <div class="text-sm text-gray-900">{{ Str::headline($latestTraining->training_type) }}</div>
                                    <div class="text-sm text-gray-500">Date: {{ $latestTraining->training_date->format('Y-m-d') }}</div>
                                    <div class="text-xs {{ $latestTraining->assessment_passed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $latestTraining->assessment_passed ? 'Score: ' . ($latestTraining->assessment_score ?? 'N/A') . '%' : 'Not passed' }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400 italic">No training recorded</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ number_format($totalHours, 1) }} hours</div>
                                <div class="text-sm text-gray-500">Target: {{ $requiredHours }} hours</div>
                                <div class="text-xs text-blue-600">{{ number_format($requiredHours > 0 ? ($totalHours / $requiredHours) * 100 : 0, 1) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if($nextTraining)
                                    <div class="text-xs text-gray-500 mt-1">Next: {{ Str::headline($nextTraining->training_type) }} ({{ $nextTraining->training_date->format('Y-m-d') }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="window.location.href='/induction-training/employee/{{ $employee->id }}'" title="View training history"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="showRecordModal({{ $employee->id }})" title="Record training"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg">
                                        <i data-feather="plus" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="showScheduleModal({{ $employee->id }})" title="Schedule training"
                                            class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg">
                                        <i data-feather="calendar" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="generateCertificate({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}')" title="Generate certificate"
                                            class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg">
                                        <i data-feather="award" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="showMaterialsModal({{ $employee->id }}, '{{ $employee->first_name }} {{ $employee->last_name }}')" title="Upload materials"
                                            class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg">
                                        <i data-feather="upload" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="award" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">No active employees to manage training for.</p>
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

<!-- Record / Edit Training Modal -->
<x-advanced-modal id="recordModal" title="Record Training" title-id="recordModalTitle"
                  description="Record or edit induction training details" icon="award" color="green" size="2xl">
    <form id="recordForm" class="space-y-4">
        <input type="hidden" name="employee_id" id="recordEmployeeId">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" id="recordEmployeeSelect" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Employee</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Date <span class="text-red-500">*</span></label>
                <input type="date" name="training_date" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Type <span class="text-red-500">*</span></label>
                <select name="training_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Type</option>
                    <option value="company_policies">Company Policies</option>
                    <option value="safety_procedures">Safety Procedures</option>
                    <option value="job_specific">Job Specific</option>
                    <option value="compliance">Compliance</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Title <span class="text-red-500">*</span></label>
                <input type="text" name="training_title" required maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trainer Name <span class="text-red-500">*</span></label>
                <input type="text" name="trainer_name" required maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Hours) <span class="text-red-500">*</span></label>
                <input type="number" name="training_duration_hours" required min="0.5" max="40" step="0.5"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Score (%)</label>
                <input type="number" name="assessment_score" min="0" max="100" step="0.1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="completed">Completed</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="assessmentPassed" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="assessmentPassed" class="ml-2 block text-sm text-gray-900">Assessment Passed</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Next Training Date</label>
                <input type="date" name="next_training_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Materials (File)</label>
                <input type="file" name="training_materials" accept=".pdf,.doc,.docx,.ppt,.pptx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Completion Certificate (File)</label>
                <input type="file" name="completion_certificate" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Description <span class="text-red-500">*</span></label>
                <textarea name="training_description" rows="3" required maxlength="2000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Comments</label>
                <textarea name="feedback_comments" rows="2" maxlength="1000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" maxlength="1000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideRecordModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="recordForm" id="recordBtn"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <span id="recordBtnText">Save Training</span>
                <div id="recordBtnLoader" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Statistics Modal -->
<x-advanced-modal id="statisticsModal" title="Training Statistics"
                  description="Overview of training coverage and completion" icon="bar-chart-2" color="indigo" size="lg">
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Total Employees:</span>
            <span class="text-sm font-medium" id="modalTotalEmployees">-</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Employees Trained:</span>
            <span class="text-sm font-medium" id="modalTrainedCount">-</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Completion Rate:</span>
            <span class="text-sm font-medium" id="modalCompletionRate">-</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Average Hours:</span>
            <span class="text-sm font-medium" id="modalAvgHours">-</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Upcoming Trainings:</span>
            <span class="text-sm font-medium" id="modalUpcomingCount">-</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">Overdue Trainings:</span>
            <span class="text-sm font-medium" id="modalOverdueCount">-</span>
        </div>
        <div class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Training Types</h4>
            <div class="space-y-2" id="trainingTypesStats">
                <!-- Will be populated dynamically -->
            </div>
        </div>
    </div>
    <x-slot:footer>
        <button onclick="hideStatisticsModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Close
        </button>
    </x-slot:footer>
</x-advanced-modal>

<!-- Schedule Training Modal -->
<x-advanced-modal id="scheduleModal" title="Schedule Training"
                  description="Schedule induction training for employees" icon="calendar" color="blue" size="lg">
    <form id="scheduleForm" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Employees</label>
            <div class="flex items-center mb-2">
                <input type="checkbox" id="selectAllEmployees" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="selectAllEmployees" class="ml-2 text-sm text-gray-700 font-medium">Select All</label>
            </div>
            <div class="border rounded-lg p-3 max-h-40 overflow-y-auto" id="employeeCheckboxes">
                <!-- Will be populated dynamically -->
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Training Type</label>
            <select name="training_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select Type</option>
                <option value="company_policies">Company Policies</option>
                <option value="safety_procedures">Safety Procedures</option>
                <option value="job_specific">Job Specific</option>
                <option value="compliance">Compliance</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Training Title</label>
            <input type="text" name="training_title" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                <input type="date" name="scheduled_date" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trainer Name</label>
                <input type="text" name="trainer_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Hours)</label>
                <input type="number" name="estimated_duration_hours" required min="0.5" max="40" step="0.5"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideScheduleModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="scheduleForm" id="scheduleBtn"
                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors flex items-center">
                <span id="scheduleBtnText">Schedule</span>
                <div id="scheduleBtnLoader" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Calendar Modal -->
<x-advanced-modal id="calendarModal" title="Training Calendar"
                  description="Upcoming scheduled training sessions" icon="calendar" color="purple" size="xl">
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

<!-- Materials Upload Modal -->
<x-advanced-modal id="materialsModal" title="Upload Training Materials"
                  description="Attach training materials for the selected employee" icon="upload" color="orange" size="lg">
    <form id="materialsForm" class="space-y-4">
        <input type="hidden" id="materialsEmployeeId">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
            <div class="text-sm font-medium text-gray-900" id="materialsEmployeeName">-</div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Materials File <span class="text-red-500">*</span></label>
            <input type="file" name="materials_file" id="materialsFile" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
            <textarea name="materials_description" rows="3" required maxlength="500"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Describe the training materials..."></textarea>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideMaterialsModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="materialsForm" id="materialsBtn"
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center">
                <span id="materialsBtnText">Upload</span>
                <div id="materialsBtnLoader" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
// Induction Training Management System
class InductionTrainingManager {
    constructor() {
        this.employeesData = @json($allEmployees->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name]));
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
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterEmployees());

        ['departmentFilter', 'trainingTypeFilter', 'statusFilter'].forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterEmployees());
        });

        const recordForm = document.getElementById('recordForm');
        recordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitRecordTraining();
        });

        const scheduleForm = document.getElementById('scheduleForm');
        scheduleForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitSchedule();
        });

        const materialsForm = document.getElementById('materialsForm');
        materialsForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitMaterials();
        });

        const selectAll = document.getElementById('selectAllEmployees');
        if (selectAll) {
            selectAll.addEventListener('change', () => {
                const checkboxes = document.querySelectorAll('#employeeCheckboxes input[name="employee_ids[]"]');
                checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
            });
        }

        const recordEmployeeSelect = document.getElementById('recordEmployeeSelect');
        recordEmployeeSelect.addEventListener('change', () => {
            document.getElementById('recordEmployeeId').value = recordEmployeeSelect.value;
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/induction-training/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;

                document.getElementById('totalEmployees').textContent = stats.total_employees;
                document.getElementById('trainedCount').textContent = stats.employees_trained;
                document.getElementById('completionRate').textContent = stats.training_completion_rate + '%';
                document.getElementById('avgHours').textContent = stats.average_training_hours;
                document.getElementById('upcomingCount').textContent = stats.upcoming_trainings;

                document.getElementById('modalTotalEmployees').textContent = stats.total_employees;
                document.getElementById('modalTrainedCount').textContent = stats.employees_trained;
                document.getElementById('modalCompletionRate').textContent = stats.training_completion_rate + '%';
                document.getElementById('modalAvgHours').textContent = stats.average_training_hours;
                document.getElementById('modalUpcomingCount').textContent = stats.upcoming_trainings;
                document.getElementById('modalOverdueCount').textContent = stats.overdue_trainings;

                const typesContainer = document.getElementById('trainingTypesStats');
                typesContainer.innerHTML = '';
                const types = stats.training_types || {};
                const entries = Object.entries(types);
                if (entries.length === 0) {
                    typesContainer.innerHTML = '<p class="text-sm text-gray-400">No training records yet.</p>';
                } else {
                    entries.forEach(([type, count]) => {
                        const typeLabel = this.getTrainingTypeLabel(type);
                        typesContainer.innerHTML += `
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">${typeLabel}:</span>
                                <span class="text-sm font-medium">${count}</span>
                            </div>
                        `;
                    });
                }
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    getTrainingTypeLabel(type) {
        const labels = {
            'company_policies': 'Company Policies',
            'safety_procedures': 'Safety Procedures',
            'job_specific': 'Job Specific',
            'compliance': 'Compliance',
            'other': 'Other'
        };
        return labels[type] || (type || 'All').replace(/_/g, ' ');
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const departmentFilter = document.getElementById('departmentFilter').value.toLowerCase();
        const trainingTypeFilter = document.getElementById('trainingTypeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name || '';
            const department = row.dataset.department || '';
            const trainingType = row.dataset.trainingType || '';
            const status = row.dataset.status || '';

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesDepartment = !departmentFilter || department === departmentFilter;
            const matchesTrainingType = !trainingTypeFilter || trainingType === trainingTypeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesDepartment && matchesTrainingType && matchesStatus) ? '' : 'none';
        });
    }

    async submitRecordTraining() {
        const form = document.getElementById('recordForm');
        const formData = new FormData(form);

        formData.set('assessment_passed', document.getElementById('assessmentPassed').checked ? '1' : '0');

        const employeeId = document.getElementById('recordEmployeeId').value;
        const isEdit = !!formData.get('training_id');

        this.setRecordLoadingState(true);

        try {
            const url = isEdit
                ? `/induction-training/${employeeId}`
                : '/induction-training';
            const response = await fetch(url, {
                method: isEdit ? 'POST' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                hideRecordModal();
                setTimeout(() => window.location.reload(), 1200);
            } else {
                this.showNotification(result.message || 'Operation failed', 'error');
            }
        } catch (error) {
            console.error('Record training error:', error);
            this.showNotification('An error occurred saving the training', 'error');
        } finally {
            this.setRecordLoadingState(false);
        }
    }

    setRecordLoadingState(loading) {
        const btnText = document.getElementById('recordBtnText');
        const btnLoader = document.getElementById('recordBtnLoader');
        const btn = document.getElementById('recordBtn');

        if (loading) {
            btnText.textContent = 'Saving...';
            btnLoader.classList.remove('hidden');
            btn.disabled = true;
        } else {
            btnText.textContent = btn.dataset.editMode ? 'Update Training' : 'Save Training';
            btnLoader.classList.add('hidden');
            btn.disabled = false;
        }
    }

    async submitSchedule() {
        const form = document.getElementById('scheduleForm');
        const data = Object.fromEntries(new FormData(form).entries());

        const selectedEmployees = [];
        const checkboxes = form.querySelectorAll('input[name="employee_ids[]"]:checked');
        checkboxes.forEach(checkbox => {
            selectedEmployees.push(checkbox.value);
        });

        if (selectedEmployees.length === 0) {
            this.showNotification('Please select at least one employee', 'error');
            return;
        }

        data.employee_ids = selectedEmployees;

        this.setScheduleLoadingState(true);

        try {
            const response = await fetch('/induction-training/schedule-training', {
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
                hideScheduleModal();
                setTimeout(() => window.location.reload(), 1200);
            } else {
                this.showNotification(result.message || 'Scheduling failed', 'error');
            }
        } catch (error) {
            console.error('Scheduling error:', error);
            this.showNotification('An error occurred during scheduling', 'error');
        } finally {
            this.setScheduleLoadingState(false);
        }
    }

    setScheduleLoadingState(loading) {
        const btnText = document.getElementById('scheduleBtnText');
        const btnLoader = document.getElementById('scheduleBtnLoader');
        const scheduleBtn = document.getElementById('scheduleBtn');

        if (loading) {
            btnText.textContent = 'Scheduling...';
            btnLoader.classList.remove('hidden');
            scheduleBtn.disabled = true;
        } else {
            btnText.textContent = 'Schedule';
            btnLoader.classList.add('hidden');
            scheduleBtn.disabled = false;
        }
    }

    async submitMaterials() {
        const form = document.getElementById('materialsForm');
        const formData = new FormData(form);
        const employeeId = document.getElementById('materialsEmployeeId').value;

        if (!employeeId || !formData.get('materials_file') || !formData.get('materials_description')) {
            this.showNotification('Please provide a file and description', 'error');
            return;
        }

        this.setMaterialsLoadingState(true);

        try {
            const response = await fetch(`/induction-training/${employeeId}/upload-materials`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                hideMaterialsModal();
            } else {
                this.showNotification(result.message || 'Upload failed', 'error');
            }
        } catch (error) {
            console.error('Materials upload error:', error);
            this.showNotification('An error occurred during upload', 'error');
        } finally {
            this.setMaterialsLoadingState(false);
        }
    }

    setMaterialsLoadingState(loading) {
        const btnText = document.getElementById('materialsBtnText');
        const btnLoader = document.getElementById('materialsBtnLoader');
        const btn = document.getElementById('materialsBtn');

        if (loading) {
            btnText.textContent = 'Uploading...';
            btnLoader.classList.remove('hidden');
            btn.disabled = true;
        } else {
            btnText.textContent = 'Upload';
            btnLoader.classList.add('hidden');
            btn.disabled = false;
        }
    }

    async generateCertificate(employeeId) {
        try {
            const response = await fetch(`/induction-training/${employeeId}/generate-certificate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                let message = 'Certificate generation failed';
                try {
                    const result = await response.json();
                    message = result.message || message;
                } catch (e) { /* not JSON */ }
                this.showNotification(message, 'error');
                return;
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `certificate-${employeeId}.pdf`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
            this.showNotification('Certificate downloaded', 'success');
        } catch (error) {
            console.error('Certificate generation error:', error);
            this.showNotification('An error occurred generating the certificate', 'error');
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

// Modal functions
function showStatisticsModal() {
    openModal('statisticsModal');
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideStatisticsModal() {
    closeModal('statisticsModal');
}

function showRecordModal(employeeId = null) {
    const form = document.getElementById('recordForm');
    form.reset();
    document.getElementById('recordEmployeeId').value = employeeId || '';
    document.getElementById('recordEmployeeSelect').value = employeeId || '';
    document.getElementById('recordModalTitle').textContent = 'Record Training';
    document.getElementById('recordBtnText').textContent = 'Save Training';
    document.getElementById('recordBtn').dataset.editMode = '';
    openModal('recordModal');
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideRecordModal() {
    closeModal('recordModal');
}

function showScheduleModal(employeeId = null) {
    openModal('scheduleModal');
    loadEmployeeCheckboxes(employeeId);
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideScheduleModal() {
    closeModal('scheduleModal');
    document.getElementById('scheduleForm').reset();
}

function showCalendarModal() {
    openModal('calendarModal');
    loadCalendarEvents();
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideCalendarModal() {
    closeModal('calendarModal');
}

function showMaterialsModal(employeeId, employeeName) {
    openModal('materialsModal');
    document.getElementById('materialsEmployeeId').value = employeeId;
    document.getElementById('materialsEmployeeName').textContent = employeeName;
    document.getElementById('materialsForm').reset();
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideMaterialsModal() {
    closeModal('materialsModal');
}

function generateCertificate(employeeId) {
    window.inductionTrainingManager.generateCertificate(employeeId);
}

async function loadEmployeeCheckboxes(preselectedId = null) {
    try {
        const response = await fetch('/induction-training/requiring-training');
        const result = await response.json();

        const container = document.getElementById('employeeCheckboxes');
        document.getElementById('selectAllEmployees').checked = false;

        if (result.success && result.employees.length > 0) {
            container.innerHTML = '';
            result.employees.forEach(employee => {
                const wrapper = document.createElement('div');
                wrapper.className = 'flex items-center';
                wrapper.innerHTML = `
                    <input type="checkbox" name="employee_ids[]" value="${employee.id}"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-900">${employee.full_name} (${employee.employee_id})</label>
                `;
                container.appendChild(wrapper);
                if (preselectedId && String(employee.id) === String(preselectedId)) {
                    wrapper.querySelector('input').checked = true;
                }
            });
        } else {
            container.innerHTML = '<p class="text-sm text-gray-400">All employees have completed training.</p>';
        }
    } catch (error) {
        console.error('Failed to load employees:', error);
    }
}

async function loadCalendarEvents() {
    try {
        const response = await fetch('/induction-training/calendar');
        const result = await response.json();

        const container = document.getElementById('calendarEvents');
        container.innerHTML = '';

        if (result.success && result.events.length > 0) {
            result.events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'p-3 bg-gray-50 rounded-lg';
                const employeeNames = (event.employee_names || []).join(', ');
                eventDiv.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">${event.title}</div>
                            <div class="text-xs text-gray-500 mt-0.5">${event.start} &middot; ${event.employees_count} employee${event.employees_count !== 1 ? 's' : ''}</div>
                            ${event.trainer ? `<div class="text-xs text-gray-400">Trainer: ${event.trainer}</div>` : ''}
                            ${employeeNames ? `<div class="text-xs text-gray-400 mt-0.5">${employeeNames}</div>` : ''}
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 whitespace-nowrap">
                            ${window.inductionTrainingManager.getTrainingTypeLabel(event.type)}
                        </span>
                    </div>
                `;
                container.appendChild(eventDiv);
            });
        } else {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i data-feather="calendar" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                    <p class="text-sm text-gray-500">No upcoming scheduled trainings.</p>
                </div>
            `;
        }

        if (typeof feather !== 'undefined') { feather.replace(); }
    } catch (error) {
        console.error('Failed to load calendar events:', error);
    }
}

// Initialize induction training manager
document.addEventListener('DOMContentLoaded', function() {
    window.inductionTrainingManager = new InductionTrainingManager();
});
</script>
@endpush
