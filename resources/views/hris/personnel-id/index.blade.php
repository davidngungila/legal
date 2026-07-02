@extends('layouts.app')

@section('title', 'Personnel ID Applications - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Personnel ID Applications</h1>
            <p class="text-gray-600 mt-2">Manage employee ID cards and access credentials</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showAddApplicationModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Add Application
            </button>
            <button onclick="showStatisticsModal()" class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                Statistics
            </button>
            <button onclick="showRequiringAttentionModal()" class="px-4 py-2 border border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors flex items-center">
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
                    <option value="">All Departments</option>
                    @foreach($employees->pluck('department')->unique() as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
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
                        @php
                            $latestApplication = $employee->personnelIdApplications->sortByDesc('created_at')->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors employee-row" 
                            data-name="{{ $employee->first_name }} {{ $employee->last_name }}"
                            data-department="{{ $employee->department }}"
                            data-id-type="{{ $latestApplication->id_type ?? '' }}"
                            data-status="{{ $latestApplication->status ?? '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">
                                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_id }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestApplication)
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
                                    <button onclick="showEmployeeId({{ $employee->id }})" class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    @if($latestApplication)
                                        <button onclick="showEditApplicationModal({{ json_encode($latestApplication) }}, {{ $employee->id }})" class="text-purple-600 hover:text-purple-900">
                                            <i data-feather="edit" class="w-4 h-4"></i>
                                        </button>
                                        @if($latestApplication->status === 'pending')
                                            <button onclick="approveApplication({{ $employee->id }})" class="text-green-600 hover:text-green-900">
                                                <i data-feather="check" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="showRejectModal({{ $employee->id }})" class="text-red-600 hover:text-red-900">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                        @if($latestApplication->status === 'approved')
                                            <button onclick="issueApplication({{ $employee->id }})" class="text-blue-600 hover:text-blue-900">
                                                <i data-feather="send" class="w-4 h-4"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button onclick="showAddApplicationModal({{ $employee->id }})" class="text-green-600 hover:text-green-900">
                                            <i data-feather="plus" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    <button onclick="generateCard({{ $employee->id }})" class="text-gray-600 hover:text-gray-900">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="reportLost({{ $employee->id }})" class="text-red-600 hover:text-red-900">
                                        <i data-feather="alert-circle" class="w-4 h-4"></i>
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

<!-- Add Application Modal -->
<div id="addApplicationModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideAddApplicationModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="credit-card" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Add ID Application</h3>
                            <p class="text-sm text-gray-500">Create a new personnel ID card application</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideAddApplicationModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form Content -->
            <form id="addApplicationForm" class="px-6 py-6 overflow-y-auto flex-1">
                @csrf
                <div class="space-y-6">
                    <!-- Employee Selection -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="user" class="w-4 h-4 mr-2 text-indigo-600"></i>
                            Employee
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select id="addEmployeeId" name="employee_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} - {{ $employee->employee_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- ID Type and Purpose -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <i data-feather="hash" class="w-4 h-4 mr-2 text-indigo-600"></i>
                                ID Type
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select id="addIdType" name="id_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" required>
                                <option value="">Select Type</option>
                                <option value="employee_card">Employee Card</option>
                                <option value="access_card">Access Card</option>
                                <option value="visitor_card">Visitor Card</option>
                                <option value="contractor_card">Contractor Card</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <i data-feather="file-text" class="w-4 h-4 mr-2 text-indigo-600"></i>
                                Purpose
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <textarea id="addIdPurpose" name="id_purpose" rows="1" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white resize-none" required placeholder="Brief purpose description"></textarea>
                        </div>
                    </div>
                    
                    <!-- Validity Period -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="calendar" class="w-4 h-4 mr-2 text-indigo-600"></i>
                            Validity Period
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Valid From</label>
                                <input type="date" id="addValidFrom" name="valid_from" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Valid Until</label>
                                <input type="date" id="addValidUntil" name="valid_until" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Access Details -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="map" class="w-4 h-4 mr-2 text-indigo-600"></i>
                            Access Details
                        </label>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Access Areas</label>
                                <input type="text" id="addAccessAreas" name="access_areas" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" placeholder="e.g., Building A, Floor 1, Server Room">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Special Permissions</label>
                                <input type="text" id="addSpecialPermissions" name="special_permissions" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-gray-50 focus:bg-white" placeholder="e.g., Lab access, Equipment room">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Access Privileges -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="shield" class="w-4 h-4 mr-2 text-indigo-600"></i>
                            Access Privileges
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="addEmergencyAccess" name="emergency_access" class="sr-only peer">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                        <i data-feather="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Emergency Access</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="addAfterHoursAccess" name="after_hours_access" class="sr-only peer">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all flex items-center justify-center">
                                        <i data-feather="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">After Hours Access</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" onclick="hideAddApplicationModal()" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Add Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Application Modal -->
<div id="editApplicationModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideEditApplicationModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="edit-2" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Edit ID Application</h3>
                            <p class="text-sm text-gray-500">Update personnel ID card application details</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideEditApplicationModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form Content -->
            <form id="editApplicationForm" class="px-6 py-6 overflow-y-auto flex-1">
                @csrf
                <input type="hidden" id="editApplicationId" name="application_id">
                <input type="hidden" id="editEmployeeId" name="employee_id">
                <div class="space-y-6">
                    <!-- ID Type and Purpose -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <i data-feather="hash" class="w-4 h-4 mr-2 text-purple-600"></i>
                                ID Type
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select id="editIdType" name="id_type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" required>
                                <option value="">Select Type</option>
                                <option value="employee_card">Employee Card</option>
                                <option value="access_card">Access Card</option>
                                <option value="visitor_card">Visitor Card</option>
                                <option value="contractor_card">Contractor Card</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center text-sm font-semibold text-gray-700">
                                <i data-feather="file-text" class="w-4 h-4 mr-2 text-purple-600"></i>
                                Purpose
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <textarea id="editIdPurpose" name="id_purpose" rows="1" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white resize-none" required placeholder="Brief purpose description"></textarea>
                        </div>
                    </div>
                    
                    <!-- Validity Period -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="calendar" class="w-4 h-4 mr-2 text-purple-600"></i>
                            Validity Period
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Valid From</label>
                                <input type="date" id="editValidFrom" name="valid_from" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Valid Until</label>
                                <input type="date" id="editValidUntil" name="valid_until" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="activity" class="w-4 h-4 mr-2 text-purple-600"></i>
                            Status
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <select id="editStatus" name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="issued">Issued</option>
                            <option value="expired">Expired</option>
                            <option value="lost">Lost</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    
                    <!-- Access Details -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="map" class="w-4 h-4 mr-2 text-purple-600"></i>
                            Access Details
                        </label>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Access Areas</label>
                                <input type="text" id="editAccessAreas" name="access_areas" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" placeholder="e.g., Building A, Floor 1, Server Room">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Special Permissions</label>
                                <input type="text" id="editSpecialPermissions" name="special_permissions" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-gray-50 focus:bg-white" placeholder="e.g., Lab access, Equipment room">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Access Privileges -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="shield" class="w-4 h-4 mr-2 text-purple-600"></i>
                            Access Privileges
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="editEmergencyAccess" name="emergency_access" class="sr-only peer">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-purple-600 peer-checked:border-purple-600 transition-all flex items-center justify-center">
                                        <i data-feather="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Emergency Access</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="editAfterHoursAccess" name="after_hours_access" class="sr-only peer">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-purple-600 peer-checked:border-purple-600 transition-all flex items-center justify-center">
                                        <i data-feather="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">After Hours Access</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" onclick="hideEditApplicationModal()" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div id="statisticsModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideStatisticsModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="bar-chart-2" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">ID Statistics</h3>
                            <p class="text-sm text-gray-500">Overview of personnel ID card applications</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideStatisticsModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-6 overflow-y-auto flex-1">
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Total Applications:</span>
                        <span class="text-sm font-bold text-gray-900" id="modalTotalApplications">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-xl">
                        <span class="text-sm text-gray-600">Pending Applications:</span>
                        <span class="text-sm font-bold text-yellow-700" id="modalPendingApplications">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl">
                        <span class="text-sm text-gray-600">Approved Applications:</span>
                        <span class="text-sm font-bold text-blue-700" id="modalApprovedApplications">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl">
                        <span class="text-sm text-gray-600">Issued Cards:</span>
                        <span class="text-sm font-bold text-green-700" id="modalIssuedCards">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Expired Cards:</span>
                        <span class="text-sm font-bold text-gray-700" id="modalExpiredCards">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl">
                        <span class="text-sm text-gray-600">Lost Cards:</span>
                        <span class="text-sm font-bold text-red-700" id="modalLostCards">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-orange-50 rounded-xl">
                        <span class="text-sm text-gray-600">Damaged Cards:</span>
                        <span class="text-sm font-bold text-orange-700" id="modalDamagedCards">-</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-xl">
                        <span class="text-sm text-gray-600">Expiring Soon:</span>
                        <span class="text-sm font-bold text-purple-700" id="modalExpiringSoon">-</span>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4 mt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                        <i data-feather="pie-chart" class="w-4 h-4 mr-2 text-blue-600"></i>
                        By ID Type
                    </h4>
                    <div class="space-y-2" id="idTypeStats">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <div class="flex justify-end">
                    <button onclick="hideStatisticsModal()" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition-all font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requiring Attention Modal -->
<div id="requiringAttentionModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideRequiringAttentionModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-red-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Applications Requiring Attention</h3>
                            <p class="text-sm text-gray-500">Review applications that need action</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideRequiringAttentionModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-6 overflow-y-auto flex-1">
                <div id="requiringAttentionList" class="space-y-3 max-h-96 overflow-y-auto">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <div class="flex justify-end">
                    <button onclick="hideRequiringAttentionModal()" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition-all font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Application Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideRejectModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-orange-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="x-circle" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Reject Application</h3>
                            <p class="text-sm text-gray-500">Provide a reason for rejection</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideRejectModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form Content -->
            <form id="rejectForm" class="px-6 py-6 overflow-y-auto flex-1">
                @csrf
                <input type="hidden" id="rejectEmployeeId" name="employee_id">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="message-square" class="w-4 h-4 mr-2 text-red-600"></i>
                            Rejection Reason
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea id="rejectReason" name="reason" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all bg-gray-50 focus:bg-white resize-none" required placeholder="Please explain why this application is being rejected..."></textarea>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" onclick="hideRejectModal()" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i data-feather="x" class="w-4 h-4 mr-2"></i>
                        Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Lost Modal -->
<div id="reportLostModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="hideReportLostModal()"></div>
    
    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale100 opacity-100 max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-red-50 to-pink-50 rounded-t-2xl flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="alert-circle" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Report Lost ID Card</h3>
                            <p class="text-sm text-gray-500">Report a lost or missing personnel ID card</p>
                        </div>
                    </div>
                    <button type="button" onclick="hideReportLostModal()" class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center justify-center group">
                        <i data-feather="x" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form Content -->
            <form id="reportLostForm" class="px-6 py-6 overflow-y-auto flex-1">
                @csrf
                <input type="hidden" id="lostEmployeeId" name="employee_id">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="calendar" class="w-4 h-4 mr-2 text-red-600"></i>
                            Lost Date
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="date" name="lost_date" id="lostDate" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all bg-gray-50 focus:bg-white">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="map-pin" class="w-4 h-4 mr-2 text-red-600"></i>
                            Lost Location
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" name="lost_location" id="lostLocation" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all bg-gray-50 focus:bg-white" placeholder="e.g., Office building, parking lot">
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <i data-feather="file-text" class="w-4 h-4 mr-2 text-red-600"></i>
                            Circumstances
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea name="circumstances" id="circumstances" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Describe how the ID card was lost..."></textarea>
                    </div>
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-4 space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="police_report_filed" id="police_report_filed" class="sr-only peer">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-red-600 peer-checked:border-red-600 transition-all flex items-center justify-center">
                                    <i data-feather="check" class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                            <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Police Report Filed</span>
                        </label>
                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Police Report Number</label>
                            <input type="text" name="police_report_number" id="policeReportNumber"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all bg-gray-50 focus:bg-white" placeholder="Enter report number if filed">
                        </div>
                    </div>
                </div>
                
                <!-- Footer Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" onclick="hideReportLostModal()"
                            class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="reportLostBtn"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
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

        // Add application form
        const addApplicationForm = document.getElementById('addApplicationForm');
        addApplicationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.addApplication();
        });

        // Edit application form
        const editApplicationForm = document.getElementById('editApplicationForm');
        editApplicationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.editApplication();
        });

        // Reject form
        const rejectForm = document.getElementById('rejectForm');
        rejectForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.rejectApplication();
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
                document.getElementById('problemCards').textContent = (stats.lost_cards || 0) + (stats.damaged_cards || 0);

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
            'employee_card': 'Employee Card',
            'access_card': 'Access Card',
            'visitor_card': 'Visitor Card',
            'contractor_card': 'Contractor Card'
        };
        return labels[type] || type;
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const departmentFilter = document.getElementById('workStationFilter').value;
        const idTypeFilter = document.getElementById('idTypeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const department = row.dataset.department;
            const idType = row.dataset.idType;
            const status = row.dataset.status;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesDepartment = !departmentFilter || department === departmentFilter;
            const matchesIdType = !idTypeFilter || idType === idTypeFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesDepartment && matchesIdType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async addApplication() {
        const form = document.getElementById('addApplicationForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/personnel-id', {
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
                hideAddApplicationModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to add application', 'error');
            }
        } catch (error) {
            console.error('Error adding application:', error);
            this.showNotification('Failed to add application', 'error');
        }
    }

    async editApplication() {
        const form = document.getElementById('editApplicationForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const employeeId = data.employee_id;
        
        try {
            const response = await fetch(`/personnel-id/${employeeId}`, {
                method: 'PUT',
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
                hideEditApplicationModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to update application', 'error');
            }
        } catch (error) {
            console.error('Error updating application:', error);
            this.showNotification('Failed to update application', 'error');
        }
    }

    async approveApplication(employeeId) {
        if (!confirm('Are you sure you want to approve this application?')) return;
        
        try {
            const response = await fetch(`/personnel-id/${employeeId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            if (result.success) {
                this.showNotification(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to approve application', 'error');
            }
        } catch (error) {
            console.error('Error approving application:', error);
            this.showNotification('Failed to approve application', 'error');
        }
    }

    async rejectApplication() {
        const form = document.getElementById('rejectForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const employeeId = data.employee_id;
        
        try {
            const response = await fetch(`/personnel-id/${employeeId}/reject`, {
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
                hideRejectModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to reject application', 'error');
            }
        } catch (error) {
            console.error('Error rejecting application:', error);
            this.showNotification('Failed to reject application', 'error');
        }
    }

    async issueApplication(employeeId) {
        if (!confirm('Are you sure you want to issue this ID?')) return;
        
        try {
            const response = await fetch(`/personnel-id/${employeeId}/issue`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            if (result.success) {
                this.showNotification(result.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(result.message || 'Failed to issue ID', 'error');
            }
        } catch (error) {
            console.error('Error issuing ID:', error);
            this.showNotification('Failed to issue ID', 'error');
        }
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
    document.getElementById('statisticsModal').classList.add('flex');
}

function hideStatisticsModal() {
    document.getElementById('statisticsModal').classList.add('hidden');
    document.getElementById('statisticsModal').classList.remove('flex');
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
                        <div class="text-sm font-medium">${employee.first_name || ''} ${employee.last_name || ''}</div>
                        <div class="text-xs text-gray-500">${employee.employee_number || ''}</div>
                        <div class="text-xs text-orange-600">${employee.status}</div>
                    `;
                    list.appendChild(item);
                });
            }
            
            document.getElementById('requiringAttentionModal').classList.remove('hidden');
            document.getElementById('requiringAttentionModal').classList.add('flex');
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
    document.getElementById('requiringAttentionModal').classList.remove('flex');
}

function showAddApplicationModal(employeeId = null) {
    if (employeeId) {
        document.getElementById('addEmployeeId').value = employeeId;
    }
    document.getElementById('addApplicationModal').classList.remove('hidden');
    document.getElementById('addApplicationModal').classList.add('flex');
}

function hideAddApplicationModal() {
    document.getElementById('addApplicationModal').classList.add('hidden');
    document.getElementById('addApplicationModal').classList.remove('flex');
    document.getElementById('addApplicationForm').reset();
}

function showEditApplicationModal(application, employeeId) {
    document.getElementById('editApplicationId').value = application.id;
    document.getElementById('editEmployeeId').value = employeeId;
    document.getElementById('editIdType').value = application.id_type;
    document.getElementById('editIdPurpose').value = application.id_purpose;
    document.getElementById('editValidFrom').value = application.valid_from;
    document.getElementById('editValidUntil').value = application.valid_until;
    document.getElementById('editStatus').value = application.status;
    document.getElementById('editAccessAreas').value = application.access_areas;
    document.getElementById('editSpecialPermissions').value = application.special_permissions;
    document.getElementById('editEmergencyAccess').checked = application.emergency_access;
    document.getElementById('editAfterHoursAccess').checked = application.after_hours_access;

    document.getElementById('editApplicationModal').classList.remove('hidden');
    document.getElementById('editApplicationModal').classList.add('flex');
}

function hideEditApplicationModal() {
    document.getElementById('editApplicationModal').classList.add('hidden');
    document.getElementById('editApplicationModal').classList.remove('flex');
}

function showRejectModal(employeeId) {
    document.getElementById('rejectEmployeeId').value = employeeId;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
    document.getElementById('rejectForm').reset();
}

function showReportLostModal(employeeId) {
    document.getElementById('lostEmployeeId').value = employeeId;
    document.getElementById('reportLostModal').classList.remove('hidden');
    document.getElementById('reportLostModal').classList.add('flex');
}

function hideReportLostModal() {
    document.getElementById('reportLostModal').classList.add('hidden');
    document.getElementById('reportLostModal').classList.remove('flex');
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

function approveApplication(employeeId) {
    window.personnelIdManager.approveApplication(employeeId);
}

function issueApplication(employeeId) {
    window.personnelIdManager.issueApplication(employeeId);
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
