@extends('layouts.app')

@section('title', 'Employee Onboarding - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Onboarding</h1>
            <p class="text-gray-600 mt-2">Manage new employee onboarding process</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Current Client</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $client->name ?? 'No Client Selected' }}</p>
                    </div>
                </div>
            </div>
            <button onclick="exportOnboardingReport()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button onclick="showStartOnboardingModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Start Onboarding
            </button>
        </div>
    </div>

    <!-- Onboarding Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+{{ $stats['new_hires_this_month'] }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active_onboarding'] }}</h3>
            <p class="text-gray-600 text-sm">Active Onboarding</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">{{ $stats['completion_rate'] }}%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['completed_this_month'] }}</h3>
            <p class="text-gray-600 text-sm">Completed This Month</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">Pending</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pending_documentation'] }}</h3>
            <p class="text-gray-600 text-sm">Pending Documentation</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">Overdue</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['overdue_probation'] }}</h3>
            <p class="text-gray-600 text-sm">Overdue Probations</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] }}</h3>
            <p class="text-gray-600 text-sm">Total Employees</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Active Onboarding Processes -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Active Onboarding Processes</h3>
                    <a href="javascript:void(0)" onclick="showViewAllOnboardingModal()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                @if($onboardingEmployees->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($onboardingEmployees as $employee)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow" data-employee-id="{{ $employee->id }}">
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                {{ $employee->probation_end_date && $employee->probation_end_date->isPast() ? 'Overdue' : 'In Progress' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $employee->onboarding_progress['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <h4 class="font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $employee->position }} · {{ $employee->department }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Overall Progress</span>
                                <span class="font-medium">{{ $employee->onboarding_progress['percentage'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $employee->onboarding_progress['percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Checklist</span>
                                <span class="font-medium">{{ $employee->onboarding_progress['checklist_completed'] ?? 0 }}/{{ $employee->onboarding_progress['checklist_total'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $employee->onboarding_progress['checklist_total'] > 0 ? round(($employee->onboarding_progress['checklist_completed'] / $employee->onboarding_progress['checklist_total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Hired: {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}</span>
                            <button onclick="manageOnboarding({{ $employee->id }})" class="text-indigo-600 hover:text-indigo-800 font-medium">Manage →</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <i data-feather="users" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Onboarding</h3>
                    <p class="text-gray-600 mb-4">Employees in probation will appear here</p>
                    <button onclick="showStartOnboardingModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                        Start First Onboarding
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Quick Actions & Completed -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    <span class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                        <i data-feather="zap" class="w-4 h-4 text-indigo-600"></i>
                    </span>
                </div>
                <div class="space-y-3">
                    <button onclick="showStartOnboardingModal()" class="group w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-left hover:border-indigo-300 hover:bg-indigo-50/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-sm shadow-indigo-200 group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                <i data-feather="user-plus" class="w-5 h-5 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm group-hover:text-indigo-700 transition-colors">Start New Onboarding</p>
                                <p class="text-xs text-gray-500 mt-0.5">Add new employee to onboarding</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-indigo-600 group-hover:translate-x-1 transition-all duration-200 ml-2"></i>
                        </div>
                    </button>
                    <a href="{{ route('documents.index') }}" class="group w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-left hover:border-purple-300 hover:bg-purple-50/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 shadow-sm shadow-purple-200 group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                <i data-feather="file-text" class="w-5 h-5 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm group-hover:text-purple-700 transition-colors">Manage Documents</p>
                                <p class="text-xs text-gray-500 mt-0.5">Upload & verify onboarding docs</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-purple-600 group-hover:translate-x-1 transition-all duration-200 ml-2"></i>
                        </div>
                    </a>
                    <a href="{{ route('employees.index') }}" class="group w-full px-4 py-3.5 bg-white border border-gray-200 rounded-xl text-left hover:border-green-300 hover:bg-green-50/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3 shadow-sm shadow-emerald-200 group-hover:scale-110 transition-transform duration-200 flex-shrink-0">
                                <i data-feather="users" class="w-5 h-5 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 text-sm group-hover:text-emerald-700 transition-colors">Employee Directory</p>
                                <p class="text-xs text-gray-500 mt-0.5">View all employees</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all duration-200 ml-2"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recently Completed Onboarding -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Recently Completed</h3>
                    <a href="javascript:void(0)" onclick="showViewAllCompletedModal()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                </div>
                @if($completedEmployees->count() > 0)
                <div class="space-y-4">
                    @foreach($completedEmployees as $employee)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">{{ substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <h4 class="font-medium text-gray-900 text-sm">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                                <p class="text-xs text-gray-500">{{ $employee->position }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                            <p class="text-xs text-gray-500 mt-1">{{ $employee->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="check-circle" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                    <p class="text-sm">No recently completed onboarding</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onboarding Checklist Template (Global) -->
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Standard Onboarding Checklist Template</h3>
            <button onclick="showChecklistTemplateModal()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Customize Template</button>
        </div>
        <div class="space-y-4">
            @php
                $categories = [
                    'orientation' => 'Day 1: Orientation',
                    'training' => 'Week 1: Department Integration',
                    'documentation' => 'Month 1: Full Integration',
                    'compliance' => 'Compliance & Documentation',
                ];
            @endphp
            @foreach($categories as $key => $title)
            @php
                $tasks = \App\Models\OnboardingChecklist::getDefaultChecklist();
                $categoryTasks = collect($tasks)->where('category', $key);
            @endphp
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">{{ $title }}</h4>
                <div class="space-y-2">
                    @foreach($categoryTasks as $task)
                    <div class="flex items-center">
                        <input type="checkbox" class="form-checkbox mr-3 text-indigo-600" disabled>
                        <label class="text-sm text-gray-700">{{ $task['task_name'] }}</label>
                        @if(!$task['is_required'])
                        <span class="ml-2 px-1.5 py-0.5 bg-gray-100 text-gray-500 text-xs rounded">Optional</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Start Onboarding Modal -->
<div id="startOnboardingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Start New Onboarding</h3>
            <button onclick="closeStartOnboardingModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <form id="startOnboardingForm" class="p-6 space-y-4 flex-1 overflow-y-auto">
            <input type="hidden" name="probation_period_days" id="probationPeriodDays" value="90">
            
            <!-- Select New Hire from Recruitment -->
            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                <label class="block text-sm font-medium text-indigo-900 mb-1">
                    <i data-feather="user-check" class="w-4 h-4 inline mr-1"></i>Select New Hire <span class="text-xs font-normal text-indigo-500">(from approved recruitment records)</span>
                </label>
                <select id="newHireSelect" onchange="onSelectNewHire(this.value)"
                    class="w-full px-3 py-2 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">Manual Entry — fill details below</option>
                </select>
                <p class="text-xs text-gray-500 mt-1.5">Choosing a new hire auto-fills their details. You can still edit any field.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" id="firstNameInput" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" id="lastNameInput" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="emailInput" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="phone" id="phoneInput" maxlength="20"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
                    <select name="position" id="positionSelect" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Position</option>
                        @foreach($positions as $pos)
                        <option value="{{ $pos }}">{{ $pos }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                    <select name="department" id="departmentSelect" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hire Date <span class="text-red-500">*</span></label>
                    <input type="date" name="hire_date" id="hireDate" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Probation Period (days)</label>
                    <input type="number" name="probation_period_days" id="probationPeriodInput" value="90" min="30" max="180"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary (TZS) <span class="text-red-500">*</span></label>
                    <input type="number" name="salary" required min="0" step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract Type <span class="text-red-500">*</span></label>
                    <select name="contract_type" id="contractTypeSelect" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="permanent">Permanent</option>
                        <option value="contract">Contract</option>
                        <option value="intern">Intern</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeStartOnboardingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Start Onboarding</button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Onboarding Modal (Checklist Detail) -->
<div id="manageOnboardingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900" id="manageModalTitle">Manage Onboarding</h3>
                <p class="text-sm text-gray-500" id="manageModalSubtitle"></p>
            </div>
            <button onclick="closeManageOnboardingModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="p-6 flex-1 overflow-y-auto" id="manageModalContent">
            <!-- Content loaded via JavaScript -->
        </div>
        <div class="p-6 border-t border-gray-200 flex-shrink-0 flex justify-end space-x-3">
            <button onclick="closeManageOnboardingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Close</button>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div id="uploadDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Upload Onboarding Document</h3>
            <button onclick="closeUploadDocumentModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <form id="uploadDocumentForm" class="p-6 space-y-4 flex-1 overflow-y-auto" enctype="multipart/form-data">
            <input type="hidden" name="employee_id" id="uploadDocumentEmployeeId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label>
                <select name="document_type" id="uploadDocumentType" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Document Type</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document Name <span class="text-red-500">*</span></label>
                <input type="text" name="document_name" required maxlength="255"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g., NIDA Card Copy">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Document Number</label>
                <input type="text" name="document_number" maxlength="100"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="e.g., 1990123456789">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                    <input type="date" name="issue_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="uploadFileDropZone">
                    <input type="file" name="file" id="uploadDocumentFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden">
                    <i data-feather="upload-cloud" class="w-10 h-10 text-gray-400 mx-auto mb-2"></i>
                    <p class="text-gray-600 mb-2">Drag & drop a file here, or click to browse</p>
                    <p class="text-sm text-gray-400">PDF, JPG, PNG, DOC, DOCX up to 10MB</p>
                    <button type="button" onclick="document.getElementById('uploadDocumentFile').click()"
                        class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Choose File
                    </button>
                </div>
                <div id="uploadSelectedFileInfo" class="hidden mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file" class="w-5 h-5 text-indigo-600" id="uploadFileIcon"></i>
                        <div>
                            <p class="font-medium text-gray-900" id="uploadFileName"></p>
                            <p class="text-sm text-gray-500" id="uploadFileSize"></p>
                        </div>
                    </div>
                    <button type="button" onclick="clearUploadFile()" class="text-red-600 hover:text-red-800">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="is_required" value="1" checked
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Required document</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeUploadDocumentModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Upload Document</button>
            </div>
        </form>
    </div>
</div>

<!-- Checklist Template Modal -->
<div id="checklistTemplateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clipboard" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Onboarding Checklist Template</h3>
                    <p class="text-sm text-gray-500">Customize tasks for all future onboardings</p>
                </div>
            </div>
            <button onclick="closeChecklistTemplateModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="p-6 flex-1 overflow-y-auto" id="templateEditorContainer">
            <div class="flex items-center justify-center py-12">
                <i data-feather="loader" class="w-8 h-8 text-indigo-500 animate-spin"></i>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex-shrink-0 flex items-center justify-between">
            <button onclick="resetChecklistTemplate()" id="resetTemplateBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm flex items-center space-x-2">
                <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                <span>Reset to Default</span>
            </button>
            <div class="flex space-x-3">
                <button onclick="closeChecklistTemplateModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button onclick="saveChecklistTemplate()" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                    <i data-feather="save" class="w-4 h-4"></i>
                    <span>Save Template</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View All Active Onboarding Modal -->
<div id="viewAllOnboardingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Active Onboarding Processes</h3>
                    <p class="text-sm text-gray-500">{{ $onboardingEmployees->count() }} employee(s) currently onboarding</p>
                </div>
            </div>
            <button onclick="closeViewAllOnboardingModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="p-6 flex-1 overflow-y-auto">
            @if($onboardingEmployees->count() > 0)
            <div class="space-y-3">
                @foreach($onboardingEmployees as $employee)
                @php $progress = $employee->onboarding_progress; @endphp
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center flex-1 min-w-0">
                        <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-medium">{{ substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $employee->position }} · {{ $employee->department }}</p>
                            <div class="flex items-center mt-1.5">
                                <div class="w-32 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $progress['percentage'] }}%"></div>
                                </div>
                                <span class="ml-2 text-xs text-gray-500">{{ $progress['percentage'] }}%</span>
                                @if($employee->probation_end_date && $employee->probation_end_date->isPast())
                                <span class="ml-2 px-1.5 py-0.5 bg-red-100 text-red-700 text-xs rounded">Overdue</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button onclick="closeViewAllOnboardingModal(); manageOnboarding({{ $employee->id }})" class="ml-4 px-3 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors flex-shrink-0">
                        Manage
                    </button>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i data-feather="user-plus" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500">No active onboarding processes</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- View All Recently Completed Modal -->
<div id="viewAllCompletedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recently Completed Onboarding</h3>
                    <p class="text-sm text-gray-500">Employees who completed onboarding in the last 30 days</p>
                </div>
            </div>
            <button onclick="closeViewAllCompletedModal()" class="w-9 h-9 rounded-xl bg-white/70 hover:bg-white border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all flex items-center justify-center">
                <i data-feather="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>
        <div class="p-6 flex-1 overflow-y-auto">
            @if($completedEmployees->count() > 0)
            <div class="space-y-3">
                @foreach($completedEmployees as $employee)
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center min-w-0">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-medium">{{ substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 min-w-0">
                            <h4 class="font-semibold text-gray-900 text-sm truncate">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $employee->position }} · {{ $employee->department }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                        <p class="text-xs text-gray-400 mt-1">{{ $employee->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i data-feather="check-circle" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500">No recently completed onboardings</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let currentManageEmployeeId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set default hire date to today
    const today = new Date().toISOString().split('T')[0];
    const hireDateInput = document.getElementById('hireDate');
    if (hireDateInput && !hireDateInput.value) {
        hireDateInput.value = today;
    }
    
    // Initialize upload drop zone
    initUploadDropZone();
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

// Start Onboarding Modal
let newHiresCache = [];

async function showStartOnboardingModal() {
    document.getElementById('startOnboardingModal').classList.remove('hidden');
    document.getElementById('startOnboardingModal').classList.add('flex');
    
    // Load approved new hires from recruitment
    loadNewHires();
    feather.replace();
}

async function loadNewHires() {
    const select = document.getElementById('newHireSelect');
    if (!select) return;
    
    // Reset to manual entry while loading
    select.innerHTML = '<option value="">Loading new hires...</option>';
    
    try {
        const response = await fetch('/onboarding/new-hires', {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();
        
        if (result.success) {
            newHiresCache = result.new_hires || [];
            
            let html = '<option value="">Manual Entry — fill details below</option>';
            for (const hire of newHiresCache) {
                const label = `${hire.first_name} ${hire.last_name}` +
                    (hire.position ? ` — ${hire.position}` : '') +
                    (hire.employee_number ? ` (${hire.employee_number})` : '');
                html += `<option value="${hire.id}">${escapeHtml(label)}</option>`;
            }
            select.innerHTML = html;
            
            if (newHiresCache.length === 0) {
                select.innerHTML += '<option value="" disabled>No approved new hires found in recruitment</option>';
            }
        } else {
            select.innerHTML = '<option value="">Manual Entry — fill details below</option>';
        }
    } catch (error) {
        select.innerHTML = '<option value="">Manual Entry — fill details below</option>';
    }
}

function onSelectNewHire(registrationId) {
    if (!registrationId) return;
    
    const hire = newHiresCache.find(h => h.id == registrationId);
    if (!hire) return;
    
    document.getElementById('firstNameInput').value = hire.first_name || '';
    document.getElementById('lastNameInput').value = hire.last_name || '';
    document.getElementById('emailInput').value = hire.email || '';
    document.getElementById('phoneInput').value = hire.phone || '';
    
    // Position: add option if not in list
    const positionSelect = document.getElementById('positionSelect');
    if (hire.position) {
        let exists = Array.from(positionSelect.options).some(opt => opt.value === hire.position);
        if (!exists) {
            const opt = document.createElement('option');
            opt.value = hire.position;
            opt.textContent = hire.position + ' (from recruitment)';
            positionSelect.appendChild(opt);
        }
        positionSelect.value = hire.position;
    }
    
    if (hire.hire_date) {
        document.getElementById('hireDate').value = hire.hire_date;
    }
    
    if (hire.contract_type) {
        document.getElementById('contractTypeSelect').value = hire.contract_type;
    }
    
    showNotification(`Auto-filled details for ${hire.first_name} ${hire.last_name}. Review and complete the remaining fields.`, 'info');
}

function closeStartOnboardingModal() {
    document.getElementById('startOnboardingModal').classList.add('hidden');
    document.getElementById('startOnboardingModal').classList.remove('flex');
    document.getElementById('startOnboardingForm').reset();
    // Reset hire date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('hireDate').value = today;
    document.getElementById('probationPeriodInput').value = 90;
}

// View All Modals
function showViewAllOnboardingModal() {
    document.getElementById('viewAllOnboardingModal').classList.remove('hidden');
    document.getElementById('viewAllOnboardingModal').classList.add('flex');
    feather.replace();
}

function closeViewAllOnboardingModal() {
    document.getElementById('viewAllOnboardingModal').classList.add('hidden');
    document.getElementById('viewAllOnboardingModal').classList.remove('flex');
}

function showViewAllCompletedModal() {
    document.getElementById('viewAllCompletedModal').classList.remove('hidden');
    document.getElementById('viewAllCompletedModal').classList.add('flex');
    feather.replace();
}

function closeViewAllCompletedModal() {
    document.getElementById('viewAllCompletedModal').classList.add('hidden');
    document.getElementById('viewAllCompletedModal').classList.remove('flex');
}

// Manage Onboarding Modal
async function manageOnboarding(employeeId) {
    currentManageEmployeeId = employeeId;
    document.getElementById('manageOnboardingModal').classList.remove('hidden');
    document.getElementById('manageOnboardingModal').classList.add('flex');
    
    try {
        const response = await fetch(`/onboarding/progress/${employeeId}`);
        const result = await response.json();
        
        if (result.success) {
            renderManageModal(result.employee, result.progress, result.checklist);
        } else {
            showNotification('Error loading onboarding details', 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function renderManageModal(employee, progress, checklist) {
    document.getElementById('manageModalTitle').textContent = `${employee.first_name} ${employee.last_name} - Onboarding`;
    document.getElementById('manageModalSubtitle').textContent = `${employee.position} · ${employee.department} · ${progress.percentage}% Complete`;
    
    const categoryColors = {
        'orientation': 'blue',
        'training': 'green',
        'documentation': 'purple',
        'compliance': 'orange',
    };
    
    const categoryLabels = {
        'orientation': 'Day 1: Orientation',
        'training': 'Week 1: Department Integration',
        'documentation': 'Month 1: Full Integration',
        'compliance': 'Compliance & Documentation',
    };
    
    // Get documents for this employee
    let documentsHtml = '';
    if (employee.documents && employee.documents.length > 0) {
        documentsHtml = employee.documents.map(doc => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">${doc.document_name}</p>
                        <p class="text-xs text-gray-500">${doc.document_type.replace(/_/g, ' ')} ${doc.document_number ? '· ' + doc.document_number : ''}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        ${doc.status === 'verified' ? 'bg-green-100 text-green-800' : 
                          doc.status === 'rejected' ? 'bg-red-100 text-red-800' : 
                          'bg-yellow-100 text-yellow-800'}">
                        ${doc.status.charAt(0).toUpperCase() + doc.status.slice(1).replace('_', ' ')}
                    </span>
                    ${doc.status !== 'verified' ? `
                        <button class="verify-btn px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200" data-doc-id="${doc.id}" data-approved="true">Verify</button>
                        <button class="verify-btn px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200" data-doc-id="${doc.id}" data-approved="false">Reject</button>
                    ` : ''}
                    <button class="delete-doc-btn px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200" data-doc-id="${doc.id}">
                        <i data-feather="trash-2" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
        `).join('');
    } else {
        documentsHtml = `
            <div class="text-center py-8 text-gray-500">
                <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                <p class="text-sm">No documents uploaded yet</p>
                <button class="upload-doc-btn mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm" data-employee-id="${employee.id}">
                    <i data-feather="upload-cloud" class="w-4 h-4 inline mr-2"></i>
                    Upload First Document
                </button>
            </div>
        `;
    }
    
    let html = `
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-gray-900">Overall Progress: ${progress.percentage}%</h4>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: ${progress.percentage}%"></div>
            </div>
            <div class="flex justify-between text-sm text-gray-500 mt-2">
                <span>Fields: ${Object.values(progress.field_progress).reduce((a,b)=>a+b,0)}/55%</span>
                <span>Checklist: ${progress.checklist_progress}/35%</span>
                <span>Documents: ${progress.document_progress}/15%</span>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button class="manage-tab active border-b-2 border-indigo-600 text-indigo-600 py-2 px-1 text-sm font-medium" data-tab="checklist">Checklist</button>
                <button class="manage-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium" data-tab="documents">Documents</button>
                <button class="manage-tab border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium" data-tab="contract">Contract</button>
            </nav>
        </div>
        
        <!-- Checklist Tab -->
        <div id="tab-content-checklist" class="manage-tab-content">
            <div class="space-y-6">
    `;
    
    for (const [category, tasks] of Object.entries(checklist)) {
        const completedCount = tasks.filter(t => t.is_completed).length;
        const totalCount = tasks.length;
        const catColor = categoryColors[category] || 'gray';
        const catLabel = categoryLabels[category] || category;
        
        html += `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-semibold text-gray-900 flex items-center">
                        <span class="w-2 h-2 rounded-full mr-2 bg-${catColor}-500"></span>
                        ${catLabel}
                    </h5>
                    <span class="text-sm text-gray-500">${completedCount}/${totalCount} completed</span>
                </div>
                <div class="space-y-2">
        `;
        
        tasks.forEach(task => {
            html += `
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <input type="checkbox" 
                        class="checklist-checkbox form-checkbox mr-3 text-indigo-600 w-5 h-5 cursor-pointer"
                        data-checklist-id="${task.id}"
                        ${task.is_completed ? 'checked' : ''}>
                    <div class="flex-1">
                        <label class="text-sm text-gray-700 ${task.is_completed ? 'line-through text-gray-400' : ''}">${task.task_name}</label>
                        ${!task.is_required ? '<span class="ml-2 px-1.5 py-0.5 bg-gray-100 text-gray-500 text-xs rounded">Optional</span>' : ''}
                    </div>
                    ${task.is_completed ? '<span class="text-xs text-green-600">✓ Done</span>' : '<span class="text-xs text-gray-400">Pending</span>'}
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    html += `
            </div>
        </div>
        
        <!-- Documents Tab -->
        <div id="tab-content-documents" class="manage-tab-content hidden">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-semibold text-gray-900">Onboarding Documents</h5>
                <button class="upload-doc-btn px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors flex items-center space-x-1" data-employee-id="${employee.id}">
                    <i data-feather="upload-cloud" class="w-4 h-4"></i>
                    <span>Upload Document</span>
                </button>
            </div>
            <div id="documentsList" class="space-y-3">
                ${documentsHtml}
            </div>
        </div>
    `;
    
    // Contract Tab Content
    let contractHtml = `
        <div id="tab-content-contract" class="manage-tab-content hidden">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-semibold text-gray-900">Employment Contract</h5>
                <button class="generate-contract-btn px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm transition-colors flex items-center space-x-1" data-employee-id="${employee.id}">
                    <i data-feather="file-plus" class="w-4 h-4"></i>
                    <span>Generate Contract</span>
                </button>
            </div>
            <div id="contractContent" class="space-y-3">
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                    <p class="text-sm">No contract generated yet</p>
                    <button class="generate-contract-btn mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm" data-employee-id="${employee.id}">
                        <i data-feather="file-plus" class="w-4 h-4 inline mr-2"></i>
                        Generate Employment Contract
                    </button>
                </div>
            </div>
        </div>
    `;
    
    html += contractHtml;
    
    document.getElementById('manageModalContent').innerHTML = html;
    feather.replace();
    
    // Attach event listeners after content is injected
    attachManageModalListeners(employee.id);
}

function closeManageOnboardingModal() {
    document.getElementById('manageOnboardingModal').classList.add('hidden');
    document.getElementById('manageOnboardingModal').classList.remove('flex');
    currentManageEmployeeId = null;
}

// Tab switching
function showManageTab(tabName) {
    document.querySelectorAll('.manage-tab').forEach(tab => {
        tab.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    document.querySelectorAll('.manage-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    const activeTab = document.getElementById('tab-' + tabName);
    const activeContent = document.getElementById('tab-content-' + tabName);
    
    if (activeTab) {
        activeTab.classList.add('active', 'border-indigo-600', 'text-indigo-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }
    if (activeContent) {
        activeContent.classList.remove('hidden');
    }
}

// Upload Document Modal
function showUploadDocumentModal(employeeId) {
    document.getElementById('uploadDocumentModal').classList.remove('hidden');
    document.getElementById('uploadDocumentModal').classList.add('flex');
    document.getElementById('uploadDocumentForm').reset();
    document.getElementById('uploadDocumentEmployeeId').value = employeeId;
    
    // Reset file display
    document.getElementById('uploadFileDropZone').classList.remove('hidden');
    document.getElementById('uploadSelectedFileInfo').classList.add('hidden');
    document.getElementById('uploadDocumentFile').value = '';
    
    // Load document types
    fetch('/onboarding/document-types')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('uploadDocumentType');
            select.innerHTML = '<option value="">Select Document Type</option>';
            for (const [key, label] of Object.entries(data.types)) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = label;
                select.appendChild(option);
            }
        });
    
    // Re-initialize drag-and-drop for upload modal
    initUploadDropZone();
    
    feather.replace();
}

// Initialize upload drop zone event listeners (upload document modal)
function initUploadDropZone() {
    const uploadDropZone = document.getElementById('uploadFileDropZone');
    const uploadFileInput = document.getElementById('uploadDocumentFile');
    
    if (!uploadDropZone || !uploadFileInput) return;
    
    // Remove existing listeners by cloning
    const newDropZone = uploadDropZone.cloneNode(true);
    uploadDropZone.parentNode.replaceChild(newDropZone, uploadDropZone);
    const newFileInput = newDropZone.querySelector('#uploadDocumentFile');
    
    // File input change
    newFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            displayUploadSelectedFile(file);
        }
    });
    
    attachDropZoneEvents(newDropZone, 'uploadDocumentFile', displayUploadSelectedFile);
}

// Shared drag-and-drop event attachment
function attachDropZoneEvents(dropZone, fileInputId, onFileSelected) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, function() {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, function() {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        }, false);
    });
    
    dropZone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        if (file) {
            const fileInput = document.getElementById(fileInputId);
            if (fileInput) {
                try {
                    fileInput.files = dt.files;
                } catch (err) {
                    // DataTransfer files assignment not supported
                }
            }
            onFileSelected(file);
        }
    }, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function closeUploadDocumentModal() {
    document.getElementById('uploadDocumentModal').classList.add('hidden');
    document.getElementById('uploadDocumentModal').classList.remove('flex');
    document.getElementById('uploadDocumentForm').reset();
}

// Document Upload
document.getElementById('uploadDocumentForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const employeeId = formData.get('employee_id');
    
    try {
        const response = await fetch(`/onboarding/${employeeId}/documents`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            closeUploadDocumentModal();
            // Refresh the documents list
            if (currentManageEmployeeId) {
                manageOnboarding(currentManageEmployeeId);
            }
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                showNotification(firstError, 'error');
            } else {
                showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
});

// Verify Document
async function verifyDocument(documentId, approved) {
    try {
        const response = await fetch(`/onboarding/documents/${documentId}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ approved: approved })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            if (currentManageEmployeeId) {
                manageOnboarding(currentManageEmployeeId);
            }
        } else {
            showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Delete Document
async function deleteDocument(documentId) {
    if (!confirm('Are you sure you want to delete this document?')) {
        return;
    }
    
    try {
        const response = await fetch(`/onboarding/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            if (currentManageEmployeeId) {
                manageOnboarding(currentManageEmployeeId);
            }
        } else {
            showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Generate Contract
async function generateContract(employeeId) {
    try {
        showNotification('Generating contract...', 'info');
        
        const response = await fetch(`/onboarding/${employeeId}/contract/generate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Contract generated successfully!', 'success');
            // Refresh the manage modal to show the contract
            if (currentManageEmployeeId) {
                manageOnboarding(currentManageEmployeeId);
            }
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                showNotification(firstError, 'error');
            } else {
                showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Checklist Template Modal
const TEMPLATE_CATEGORIES = {
    orientation: { title: 'Day 1: Orientation', color: 'blue' },
    training: { title: 'Week 1: Department Integration', color: 'green' },
    documentation: { title: 'Month 1: Full Integration', color: 'purple' },
    compliance: { title: 'Compliance & Documentation', color: 'orange' }
};

async function showChecklistTemplateModal() {
    document.getElementById('checklistTemplateModal').classList.remove('hidden');
    document.getElementById('checklistTemplateModal').classList.add('flex');
    
    const container = document.getElementById('templateEditorContainer');
    container.innerHTML = '<div class="flex items-center justify-center py-12"><i data-feather="loader" class="w-8 h-8 text-indigo-500 animate-spin"></i></div>';
    feather.replace();
    
    try {
        const response = await fetch('/onboarding/checklist-template', {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();
        
        if (result.success) {
            renderTemplateEditor(result.tasks, result.is_customized);
        } else {
            container.innerHTML = '<div class="text-center py-12"><p class="text-red-500">' + (result.error || 'Failed to load template') + '</p></div>';
        }
    } catch (error) {
        container.innerHTML = '<div class="text-center py-12"><p class="text-red-500">Error: ' + error.message + '</p></div>';
    }
}

function renderTemplateEditor(tasks, isCustomized) {
    const container = document.getElementById('templateEditorContainer');
    let html = '';
    
    if (isCustomized) {
        html += '<div class="mb-4 px-4 py-2.5 bg-green-50 border border-green-200 rounded-lg flex items-center space-x-2"><i data-feather="check-circle" class="w-4 h-4 text-green-600"></i><span class="text-sm text-green-700">This client has a customized template. New onboardings will use it.</span></div>';
    } else {
        html += '<div class="mb-4 px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg flex items-center space-x-2"><i data-feather="info" class="w-4 h-4 text-blue-600"></i><span class="text-sm text-blue-700">Showing the default template. Edit tasks below and save to customize it for this client.</span></div>';
    }
    
    for (const [key, cat] of Object.entries(TEMPLATE_CATEGORIES)) {
        const categoryTasks = tasks.filter(t => t.category === key);
        
        html += `
        <div class="border border-gray-200 rounded-lg p-4 mb-4" data-category="${key}">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-900 flex items-center">
                    <span class="w-2 h-2 rounded-full mr-2 bg-${cat.color}-500"></span>
                    ${cat.title}
                    <span class="ml-2 text-xs font-normal text-gray-400 template-count">${categoryTasks.length} task(s)</span>
                </h4>
                <button type="button" onclick="addTemplateTask('${key}')" class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-xs font-medium hover:bg-indigo-100 transition-colors flex items-center space-x-1">
                    <i data-feather="plus" class="w-3.5 h-3.5"></i>
                    <span>Add Task</span>
                </button>
            </div>
            <div class="space-y-2 template-tasks">`;
        
        if (categoryTasks.length === 0) {
            html += '<p class="text-sm text-gray-400 italic py-1">No tasks in this category yet.</p>';
        }
        
        for (const task of categoryTasks) {
            html += buildTemplateTaskRow(task);
        }
        
        html += `
            </div>
        </div>`;
    }
    
    container.innerHTML = html;
    feather.replace();
}

function buildTemplateTaskRow(task) {
    const required = task.is_required === true || task.is_required === 'true' || task.is_required === 1;
    return `
        <div class="template-task-row flex items-center space-x-2 group">
            <input type="text" value="${escapeHtml(task.task_name)}" placeholder="Task description..."
                class="task-name-input flex-1 px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
            <label class="flex items-center space-x-1.5 cursor-pointer flex-shrink-0" title="Required task">
                <input type="checkbox" class="task-required-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" ${required ? 'checked' : ''}>
                <span class="text-xs text-gray-500">Required</span>
            </label>
            <button type="button" onclick="removeTemplateTask(this)" title="Remove task"
                class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-600 hover:bg-red-50 transition-colors opacity-0 group-hover:opacity-100">
                <i data-feather="trash-2" class="w-4 h-4"></i>
            </button>
        </div>`;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML.replace(/"/g, '&quot;');
}

function addTemplateTask(category) {
    const section = document.querySelector(`[data-category="${category}"] .template-tasks`);
    if (!section) return;
    
    // Remove empty message if present
    const emptyMsg = section.querySelector('p.italic');
    if (emptyMsg) emptyMsg.remove();
    
    const temp = document.createElement('div');
    temp.innerHTML = buildTemplateTaskRow({ task_name: '', is_required: true });
    const row = temp.firstElementChild;
    section.appendChild(row);
    
    // Focus the new input
    const input = row.querySelector('.task-name-input');
    if (input) input.focus();
    
    updateTemplateCounts();
    feather.replace();
}

function removeTemplateTask(btn) {
    const row = btn.closest('.template-task-row');
    if (row) {
        row.remove();
        updateTemplateCounts();
    }
}

function updateTemplateCounts() {
    document.querySelectorAll('[data-category]').forEach(section => {
        const count = section.querySelectorAll('.template-task-row').length;
        const countEl = section.querySelector('.template-count');
        if (countEl) countEl.textContent = `${count} task(s)`;
        
        const tasksContainer = section.querySelector('.template-tasks');
        if (count === 0 && !tasksContainer.querySelector('p.italic')) {
            tasksContainer.innerHTML = '<p class="text-sm text-gray-400 italic py-1">No tasks in this category yet.</p>';
        } else if (count > 0) {
            const emptyMsg = tasksContainer.querySelector('p.italic');
            if (emptyMsg) emptyMsg.remove();
        }
    });
}

async function saveChecklistTemplate() {
    const rows = document.querySelectorAll('#templateEditorContainer .template-task-row');
    const tasks = [];
    let invalid = false;
    
    rows.forEach(row => {
        const nameInput = row.querySelector('.task-name-input');
        const requiredCheckbox = row.querySelector('.task-required-checkbox');
        const category = row.closest('[data-category]').dataset.category;
        const name = nameInput.value.trim();
        
        if (!name) {
            nameInput.classList.add('border-red-400', 'bg-red-50');
            invalid = true;
        } else {
            nameInput.classList.remove('border-red-400', 'bg-red-50');
            tasks.push({
                task_name: name,
                category: category,
                is_required: requiredCheckbox.checked
            });
        }
    });
    
    if (invalid) {
        showNotification('Some tasks have empty names. Fill them in or remove them.', 'error');
        return;
    }
    
    if (tasks.length === 0) {
        showNotification('Add at least one task before saving.', 'error');
        return;
    }
    
    try {
        const response = await fetch('/onboarding/checklist-template', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ tasks: tasks })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            closeChecklistTemplateModal();
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                showNotification(firstError, 'error');
            } else {
                showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

async function resetChecklistTemplate() {
    if (!confirm('Reset the checklist template to the default tasks? Your customizations will be lost.')) {
        return;
    }
    
    try {
        const response = await fetch('/onboarding/checklist-template/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            renderTemplateEditor(result.tasks, false);
        } else {
            showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function closeChecklistTemplateModal() {
    document.getElementById('checklistTemplateModal').classList.add('hidden');
    document.getElementById('checklistTemplateModal').classList.remove('flex');
}

// Toggle Checklist Item
async function toggleChecklistItem(checklistId, checkbox) {
    try {
        const response = await fetch(`/onboarding/checklist/${checklistId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update UI
            const label = checkbox.closest('.flex').querySelector('label');
            const statusSpan = checkbox.closest('.flex').querySelector('span:last-child');
            
            if (result.item.is_completed) {
                label.classList.add('line-through', 'text-gray-400');
                label.classList.remove('text-gray-700');
                statusSpan.textContent = '✓ Done';
                statusSpan.className = 'text-xs text-green-600';
            } else {
                label.classList.remove('line-through', 'text-gray-400');
                label.classList.add('text-gray-700');
                statusSpan.textContent = 'Pending';
                statusSpan.className = 'text-xs text-gray-400';
            }
            
            // Update progress if manage modal is open
            if (currentManageEmployeeId) {
                manageOnboarding(currentManageEmployeeId);
            }
            
            showNotification(result.item.is_completed ? 'Task completed' : 'Task marked incomplete', 'success');
        } else {
            checkbox.checked = !checkbox.checked;
            showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (error) {
        checkbox.checked = !checkbox.checked;
        showNotification('Error: ' + error.message, 'error');
    }
}

// Form Submission
document.getElementById('startOnboardingForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/onboarding/start', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            closeStartOnboardingModal();
            location.reload();
        } else {
            if (result.errors) {
                const firstError = Object.values(result.errors)[0][0];
                showNotification(firstError, 'error');
            } else {
                showNotification('Error: ' + (result.error || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
});

// Export Report - downloads CSV of onboarding report
function exportOnboardingReport() {
    showNotification('Preparing export...', 'info');
    window.location.href = '{{ route('onboarding.export') }}';
}

function displayUploadSelectedFile(file) {
    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Invalid file type. Only PDF, JPG, PNG, DOC, DOCX allowed.', 'error');
        return;
    }
    
    // Validate file size (10MB)
    if (file.size > 10 * 1024 * 1024) {
        showNotification('File size must be less than 10MB.', 'error');
        return;
    }
    
    document.getElementById('uploadFileDropZone').classList.add('hidden');
    document.getElementById('uploadSelectedFileInfo').classList.remove('hidden');
    
    let icon = 'file';
    if (file.type === 'application/pdf') icon = 'file-text';
    else if (file.type.startsWith('image/')) icon = 'image';
    
    document.getElementById('uploadFileIcon').setAttribute('data-feather', icon);
    document.getElementById('uploadFileName').textContent = file.name;
    document.getElementById('uploadFileSize').textContent = formatFileSize(file.size);
    feather.replace();
}

function clearUploadFile() {
    document.getElementById('uploadDocumentFile').value = '';
    document.getElementById('uploadSelectedFileInfo').classList.add('hidden');
    document.getElementById('uploadFileDropZone').classList.remove('hidden');
}

// Format file size
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Notification helper
function showNotification(message, type = 'info') {
    const colors = {
        'success': 'bg-green-600',
        'error': 'bg-red-600',
        'info': 'bg-indigo-600',
        'warning': 'bg-yellow-600'
    };
    const color = colors[type] || colors.info;
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${color} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Attach event listeners for manage modal (called after content injection)
function attachManageModalListeners(employeeId) {
    // Checklist checkboxes
    document.querySelectorAll('.checklist-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checklistId = this.dataset.checklistId;
            toggleChecklistItem(checklistId, this);
        });
    });
    
    // Document verify buttons
    document.querySelectorAll('.verify-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.dataset.docId;
            const approved = this.dataset.approved === 'true';
            verifyDocument(docId, approved);
        });
    });
    
    // Document delete buttons
    document.querySelectorAll('.delete-doc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const docId = this.dataset.docId;
            deleteDocument(docId);
        });
    });
    
    // Upload document buttons
    document.querySelectorAll('.upload-doc-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const empId = this.dataset.employeeId || employeeId;
            showUploadDocumentModal(empId);
        });
    });
    
    // Generate contract buttons
    document.querySelectorAll('.generate-contract-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const empId = this.dataset.employeeId || employeeId;
            generateContract(empId);
        });
    });
    
    // Tab switching
    document.querySelectorAll('.manage-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            showManageTab(tabName);
        });
    });
    
    // Re-initialize feather icons for dynamically added content
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

// Make functions globally accessible
window.manageOnboarding = manageOnboarding;
window.showUploadDocumentModal = showUploadDocumentModal;
window.closeUploadDocumentModal = closeUploadDocumentModal;
window.verifyDocument = verifyDocument;
window.deleteDocument = deleteDocument;
window.generateContract = generateContract;
window.showManageTab = showManageTab;
window.toggleChecklistItem = toggleChecklistItem;
window.showStartOnboardingModal = showStartOnboardingModal;
window.closeStartOnboardingModal = closeStartOnboardingModal;
window.showChecklistTemplateModal = showChecklistTemplateModal;
window.closeChecklistTemplateModal = closeChecklistTemplateModal;
window.addTemplateTask = addTemplateTask;
window.removeTemplateTask = removeTemplateTask;
window.saveChecklistTemplate = saveChecklistTemplate;
window.resetChecklistTemplate = resetChecklistTemplate;
window.closeManageOnboardingModal = closeManageOnboardingModal;
window.showNotification = showNotification;
window.formatFileSize = formatFileSize;
window.clearUploadFile = clearUploadFile;
window.displayUploadSelectedFile = displayUploadSelectedFile;
window.showViewAllOnboardingModal = showViewAllOnboardingModal;
window.closeViewAllOnboardingModal = closeViewAllOnboardingModal;
window.showViewAllCompletedModal = showViewAllCompletedModal;
window.closeViewAllCompletedModal = closeViewAllCompletedModal;
window.exportOnboardingReport = exportOnboardingReport;

</script>
@endpush

@endsection