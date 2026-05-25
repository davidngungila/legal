@extends('layouts.app')

@section('title', 'Employee Registrations - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Registrations</h1>
            <p class="text-gray-600 mt-2">Manage registered employees and their information</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('employee-registration.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Register Employee
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form action="{{ route('employee-registration.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search employees..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="status" id="statusFilter" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <select name="work_station" id="workStationFilter" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Work Stations</option>
                    @foreach(\App\Models\EmployeeRegistration::pluck('work_station')->unique() as $workStation)
                        <option value="{{ $workStation }}" {{ request('work_station') == $workStation ? 'selected' : '' }}>{{ $workStation }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="contract_type" id="contractTypeFilter" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Contract Types</option>
                    @foreach(\App\Models\EmployeeRegistration::pluck('type_of_contract')->unique() as $contractType)
                        <option value="{{ $contractType }}" {{ request('contract_type') == $contractType ? 'selected' : '' }}>{{ $contractType }}</option>
                    @endforeach
                </select>
            </div>
        </form>
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
                            Contact Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employment Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Interviews
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
                            data-email="{{ $employee->email_address }}"
                            data-workstation="{{ $employee->work_station }}"
                            data-contract="{{ $employee->type_of_contract }}"
                            data-status="{{ $employee->status }}">
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
                                        <div class="text-sm text-gray-500">{{ $employee->birthplace }}</div>
                                        <div class="text-xs text-gray-400">{{ $employee->date_of_birth->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->email_address }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->phone_number }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->residence_area }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->permanent_residence }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->work_station }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->type_of_contract }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->place_of_recruitment }}</div>
                                <div class="text-xs text-gray-400">Employed: {{ $employee->date_employed->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs space-y-1">
                                    @if($employee->hrInterview)
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-purple-400 rounded-full mr-1"></span>
                                            <span>HR: {{ $employee->hrInterview->interview_number }}</span>
                                        </div>
                                    @endif
                                    @if($employee->technicalInterview)
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-orange-400 rounded-full mr-1"></span>
                                            <span>Tech: {{ $employee->technicalInterview->interview_number }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($employee->status)
                                    @case('draft')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                        @break
                                    @case('submitted')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Submitted
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('employee-registration.show', $employee) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($employee->status === 'draft')
                                        <a href="{{ route('employee-registration.edit', $employee) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i data-feather="edit-2" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    @if($employee->status === 'submitted')
                                        <button onclick="approveEmployee({{ $employee->id }})" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Approve">
                                            <i data-feather="check-circle" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="rejectEmployee({{ $employee->id }})" 
                                                class="text-red-600 hover:text-red-900"
                                                title="Reject">
                                            <i data-feather="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if($employee->status === 'approved')
                                        <button onclick="generatePdf({{ $employee->id }})" 
                                                class="text-purple-600 hover:text-purple-900"
                                                title="Generate PDF">
                                            <i data-feather="file-text" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="users" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employee registrations found</p>
                                    <p class="text-sm">Get started by registering your first employee.</p>
                                    <a href="{{ route('employee-registration.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Register Employee
                                    </a>
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
@endsection

@push('scripts')
<script>
// Employee Registration Management System
class EmployeeRegistrationManager {
    constructor() {
        this.init();
    }

    init() {
        this.initializeFeather();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Approve employee function
async function approveEmployee(employeeId) {
    if (!confirm('Are you sure you want to approve this employee registration?')) {
        return;
    }

    try {
        const response = await fetch(`/employee-registration/${employeeId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Employee registration approved successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Employee approval error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

// Reject employee function
async function rejectEmployee(employeeId) {
    const reason = prompt('Please provide a reason for rejection:');
    
    if (!reason) {
        return;
    }

    try {
        const response = await fetch(`/employee-registration/${employeeId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Employee registration rejected successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Employee rejection error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

// Generate PDF function
async function generatePdf(employeeId) {
    try {
        const response = await fetch(`/employee-registration/${employeeId}/generate-pdf`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Employee registration PDF generated successfully', 'success');
            // You could trigger a download here if needed
            if (result.download_url) {
                window.open(result.download_url, '_blank');
            }
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('PDF generation error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

function showNotification(message, type = 'info') {
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

// Initialize employee registration manager
document.addEventListener('DOMContentLoaded', function() {
    window.employeeRegistrationManager = new EmployeeRegistrationManager();
});
</script>
@endpush
