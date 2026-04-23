@extends('layouts.app')

@section('title', 'Employee Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Management</h1>
            <p class="text-gray-600 mt-2">Manage your workforce efficiently and compliantly</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Showing employees for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="user-plus" class="w-4 h-4 inline mr-2"></i>
                Add Employee
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">On Leave</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['on_leave'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Probation</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['probation'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input type="text" id="employeeSearch" placeholder="Search employees..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select id="departmentFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                    <option value="Operations">Operations</option>
                    <option value="Sales">Sales</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                <select id="employmentTypeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="Permanent">Permanent</option>
                    <option value="Contract">Contract</option>
                    <option value="Probation">Probation</option>
                    <option value="Intern">Intern</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="On Leave">On Leave</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Terminated">Terminated</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-medium">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $employee->employee_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $employee->department }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $employee->position }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $employee->employment_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColor = match($employee->status) {
                                    'active' => 'green',
                                    'on_leave' => 'yellow',
                                    'suspended' => 'red',
                                    'terminated' => 'gray',
                                    'probation' => 'blue',
                                    default => 'gray'
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('employees.show', $employee->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                                <button onclick="generateContract({{ $employee->id }})" class="text-green-600 hover:text-green-900" title="Generate Contract">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEmployee({{ $employee->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="users" class="w-12 h-12 text-gray-400 mb-2"></i>
                                <p class="text-lg font-medium">No employees found</p>
                                <p class="text-sm">Get started by adding your first employee</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                {{ $employees->links() }}
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $employees->firstItem() }}</span> to <span class="font-medium">{{ $employees->lastItem() }}</span> of
                        <span class="font-medium">{{ $employees->total() }}</span> results
                    </p>
                </div>
                <div>
                    {{ $employees->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize Feather icons
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});

// Function to generate contract for employee
function generateContract(employeeId) {
    if (confirm('Are you sure you want to generate a contract for this employee?')) {
        fetch(`/employees/${employeeId}/generate-contract`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Contract generated successfully!');
                window.location.href = data.redirect_url;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating the contract.');
        });
    }
}

// Function to delete employee
function deleteEmployee(employeeId) {
    if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/employees/${employeeId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Function to export employees
function exportEmployees() {
    window.open('/employees/export', '_blank');
}

// Function to search employees
function searchEmployees() {
    const searchValue = document.getElementById('employeeSearch').value;
    const url = new URL(window.location);
    
    if (searchValue) {
        url.searchParams.set('search', searchValue);
    } else {
        url.searchParams.delete('search');
    }
    
    window.location.href = url.toString();
}

// Function to filter employees
function filterEmployees() {
    const department = document.getElementById('departmentFilter').value;
    const employmentType = document.getElementById('employmentTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    const url = new URL(window.location);
    
    if (department) url.searchParams.set('department', department);
    else url.searchParams.delete('department');
    
    if (employmentType) url.searchParams.set('employment_type', employmentType);
    else url.searchParams.delete('employment_type');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    window.location.href = url.toString();
}

// Event listeners for search and filters
document.getElementById('employeeSearch')?.addEventListener('input', function(e) {
    if (e.target.value === '') {
        searchEmployees();
    }
});

document.getElementById('employeeSearch')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchEmployees();
    }
});

document.getElementById('departmentFilter')?.addEventListener('change', filterEmployees);
document.getElementById('employmentTypeFilter')?.addEventListener('change', filterEmployees);
document.getElementById('statusFilter')?.addEventListener('change', filterEmployees);

// Export button functionality
document.querySelector('button:has(.fa-download)')?.addEventListener('click', exportEmployees);

// Add Employee button functionality
document.querySelector('button:has(.fa-user-plus)')?.addEventListener('click', function() {
    window.location.href = '/employees/create';
});

// Function to show employee details modal
function showEmployeeModal(employeeId) {
    fetch(`/employees/${employeeId}/details`)
        .then(response => response.json())
        .then(data => {
            // Create and show modal with employee details
            console.log('Employee details:', data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Function to refresh employee list
function refreshEmployeeList() {
    window.location.reload();
}

// Function to add bulk operations
function selectAllEmployees() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="employee_ids[]"]');
    const selectAllCheckbox = document.querySelector('input[type="checkbox"][name="select_all"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Function to handle bulk actions
function performBulkAction(action) {
    const selectedEmployees = Array.from(document.querySelectorAll('input[type="checkbox"][name="employee_ids[]"]:checked'))
        .map(checkbox => checkbox.value);
    
    if (selectedEmployees.length === 0) {
        alert('Please select at least one employee.');
        return;
    }
    
    // Implement bulk action functionality
    console.log(`Bulk ${action} for employees:`, selectedEmployees);
}

// Function to show statistics
function showStatistics() {
    fetch('/employees/statistics')
        .then(response => response.json())
        .then(data => {
            console.log('Employee statistics:', data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Initialize tooltips and other UI elements
document.addEventListener('DOMContentLoaded', function() {
    console.log('Employee management system initialized');
});
</script>
@endpush
@endsection
