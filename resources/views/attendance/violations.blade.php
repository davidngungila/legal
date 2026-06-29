@extends('layouts.app')

@section('title', 'Attendance Violations - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Attendance Violations</h1>
            <p class="text-gray-600 mt-2">Track and manage attendance violations</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Managing violations for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Violations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_violations'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Open Violations</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['open_violations'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Late Arrivals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['late_arrivals'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Early Departures</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['early_departures'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="log-out" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Absenteeism</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['absenteeism'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="user-x" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Violations</label>
                <div class="relative">
                    <input type="text" id="violation-search" placeholder="Search by employee name" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select id="violation-employee-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Violation Type</label>
                <select id="violation-type-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="late_arrival">Late Arrival</option>
                    <option value="early_departure">Early Departure</option>
                    <option value="absenteeism">Absenteeism</option>
                    <option value="daily_work_limit">Daily Work Limit</option>
                    <option value="weekly_work_limit">Weekly Work Limit</option>
                    <option value="monthly_overtime_limit">Monthly Overtime Limit</option>
                    <option value="weekly_rest_violation">Weekly Rest Violation</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="violation-status-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All</option>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Violations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="violations-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Violation Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action Triggered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($violations as $violation)
                    <tr class="violation-row" 
                        data-employee-name="{{ $violation->employee ? strtolower($violation->employee->first_name . ' ' . $violation->employee->last_name) : '' }}"
                        data-employee-id="{{ $violation->employee_id }}"
                        data-violation-type="{{ $violation->violation_type }}"
                        data-violation-status="{{ $violation->status }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium text-xs">
                                        {{ $violation->employee ? substr($violation->employee->first_name, 0, 1) . substr($violation->employee->last_name, 0, 1) : 'N/A' }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $violation->employee ? $violation->employee->first_name . ' ' . $violation->employee->last_name : 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $violation->employee->employee_id ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($violation->violation_type == 'late_arrival') bg-yellow-100 text-yellow-800
                                @elseif($violation->violation_type == 'early_departure') bg-orange-100 text-orange-800
                                @elseif($violation->violation_type == 'absenteeism') bg-red-100 text-red-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $violation->violation_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $violation->violation_date->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ Str::limit($violation->details, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $violation->action_triggered ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $violation->action_triggered ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $violation->status == 'open' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($violation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" data-bs-toggle="modal" data-bs-target="#editViolationModal{{ $violation->id }}">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                @if($violation->status == 'open')
                                <button class="text-green-600 hover:text-green-900" onclick="closeViolation({{ $violation->id }})">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="alert-triangle" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No violations found</p>
                                <p class="text-sm text-gray-600 mt-2">Great job keeping attendance violations at bay!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($violations->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $violations->links() }}
        </div>
        @endif
    </div>

    <!-- Edit Violation Modals -->
    @foreach($violations as $violation)
    <div class="modal fade" id="editViolationModal{{ $violation->id }}" tabindex="-1" aria-labelledby="editViolationModalLabel{{ $violation->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('attendance.violations.update', $violation) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold" id="editViolationModalLabel{{ $violation->id }}">Edit Violation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Details</label>
                                <textarea name="details" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $violation->details }}</textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="open" {{ $violation->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="closed" {{ $violation->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Violation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter functionality
    function filterViolations() {
        const search = document.getElementById('violation-search').value.toLowerCase();
        const employeeId = document.getElementById('violation-employee-filter').value;
        const type = document.getElementById('violation-type-filter').value;
        const status = document.getElementById('violation-status-filter').value;
        
        document.querySelectorAll('.violation-row').forEach(row => {
            const nameMatch = row.dataset.employeeName.includes(search);
            const employeeMatch = !employeeId || row.dataset.employeeId === employeeId;
            const typeMatch = !type || row.dataset.violationType === type;
            const statusMatch = !status || row.dataset.violationStatus === status;
            
            if (nameMatch && employeeMatch && typeMatch && statusMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    document.getElementById('violation-search').addEventListener('input', filterViolations);
    document.getElementById('violation-employee-filter').addEventListener('change', filterViolations);
    document.getElementById('violation-type-filter').addEventListener('change', filterViolations);
    document.getElementById('violation-status-filter').addEventListener('change', filterViolations);
});

function closeViolation(id) {
    if (confirm('Are you sure you want to close this violation?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/attendance/violations/' + id + '/close';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
