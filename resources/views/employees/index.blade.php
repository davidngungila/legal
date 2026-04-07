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
                    <p class="text-2xl font-bold text-gray-900">248</p>
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
                    <p class="text-2xl font-bold text-gray-900">235</p>
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
                    <p class="text-2xl font-bold text-gray-900">8</p>
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
                    <p class="text-2xl font-bold text-gray-900">5</p>
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
                    <!-- Employee rows will be dynamically inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </button>
                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of
                        <span class="font-medium">248</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i data-feather="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-50 text-sm font-medium text-indigo-600">
                            1
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            2
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            3
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            ...
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            50
                        </button>
                        <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i data-feather="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Sample employee data with all fields and client assignments
const employees = [
    {
        name: 'John Doe',
        email: 'john.doe@company.com',
        id: 'EMP001',
        department: 'IT',
        position: 'Senior Developer',
        type: 'Permanent',
        start_date: '2022-01-15',
        status: 'active',
        clientId: '1' // ABC Manufacturing Ltd
    },
    {
        name: 'Jane Smith',
        email: 'jane.smith@company.com',
        id: 'EMP002',
        department: 'HR',
        position: 'HR Manager',
        type: 'Permanent',
        start_date: '2021-03-20',
        status: 'active',
        clientId: '1' // ABC Manufacturing Ltd
    },
    {
        name: 'Michael Johnson',
        email: 'michael.j@company.com',
        id: 'EMP003',
        department: 'Finance',
        position: 'Accountant',
        type: 'Contract',
        start_date: '2022-06-10',
        status: 'active',
        clientId: '2' // XYZ Construction Co
    },
    {
        name: 'Sarah Williams',
        email: 'sarah.w@company.com',
        id: 'EMP004',
        department: 'Operations',
        position: 'Operations Manager',
        type: 'Permanent',
        start_date: '2020-11-05',
        status: 'on_leave',
        clientId: '2' // XYZ Construction Co
    },
    {
        name: 'David Brown',
        email: 'david.b@company.com',
        id: 'EMP005',
        department: 'Sales',
        position: 'Sales Executive',
        type: 'Probation',
        start_date: '2023-01-10',
        status: 'active',
        clientId: '3' // Tanzania Mining Corp
    },
    {
        name: 'Emily Chen',
        email: 'emily.chen@company.com',
        id: 'EMP006',
        department: 'IT',
        position: 'Software Engineer',
        type: 'Permanent',
        start_date: '2022-08-15',
        status: 'active',
        clientId: '3' // Tanzania Mining Corp
    },
    {
        name: 'Robert Wilson',
        email: 'robert.w@company.com',
        id: 'EMP007',
        department: 'Operations',
        position: 'Logistics Manager',
        type: 'Permanent',
        start_date: '2021-12-01',
        status: 'active',
        clientId: '4' // East Africa Logistics
    },
    {
        name: 'Lisa Anderson',
        email: 'lisa.a@company.com',
        id: 'EMP008',
        department: 'Finance',
        position: 'Finance Director',
        type: 'Permanent',
        start_date: '2020-05-20',
        status: 'active',
        clientId: '4' // East Africa Logistics
    }
];

let filteredEmployees = [];

// Initialize filters
document.addEventListener('DOMContentLoaded', function() {
    // Listen for client changes
    document.addEventListener('clientChanged', function(event) {
        console.log('Client changed, reloading employees for:', event.detail.clientId);
        loadEmployees();
    });
    
    // Add event listeners to all filter inputs using IDs
    const searchInput = document.getElementById('employeeSearch');
    const departmentSelect = document.getElementById('departmentFilter');
    const employmentTypeSelect = document.getElementById('employmentTypeFilter');
    const statusSelect = document.getElementById('statusFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', filterEmployees);
    }
    
    if (departmentSelect) {
        departmentSelect.addEventListener('change', filterEmployees);
    }
    
    if (employmentTypeSelect) {
        employmentTypeSelect.addEventListener('change', filterEmployees);
    }
    
    if (statusSelect) {
        statusSelect.addEventListener('change', filterEmployees);
    }
    
    // Initial render
    loadEmployees();
});

function loadEmployees() {
    // Get current client
    const currentClient = getCurrentClient();
    
    // Filter employees by current client
    filteredEmployees = employees.filter(employee => employee.clientId === currentClient.id);
    
    // Apply existing filters
    filterEmployees();
}

function filterEmployees() {
    // Use getElementById for reliable selection
    const searchInput = document.getElementById('employeeSearch');
    const departmentSelect = document.getElementById('departmentFilter');
    const employmentTypeSelect = document.getElementById('employmentTypeFilter');
    const statusSelect = document.getElementById('statusFilter');
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const selectedDepartment = departmentSelect ? departmentSelect.value : '';
    const selectedEmploymentType = employmentTypeSelect ? employmentTypeSelect.value : '';
    const selectedStatus = statusSelect ? statusSelect.value : '';
    
    // Get current client and filter by client first
    const currentClient = getCurrentClient();
    let clientFilteredEmployees = employees.filter(employee => employee.clientId === currentClient.id);
    
    console.log('Filter values:', { searchTerm, selectedDepartment, selectedEmploymentType, selectedStatus, currentClient: currentClient.id });
    
    filteredEmployees = clientFilteredEmployees.filter(employee => {
        const matchesSearch = !searchTerm || 
            employee.name.toLowerCase().includes(searchTerm) ||
            employee.email.toLowerCase().includes(searchTerm) ||
            employee.id.toLowerCase().includes(searchTerm) ||
            employee.position.toLowerCase().includes(searchTerm);
        
        const matchesDepartment = !selectedDepartment || employee.department === selectedDepartment;
        const matchesEmploymentType = !selectedEmploymentType || employee.type === selectedEmploymentType;
        const matchesStatus = !selectedStatus || 
            (selectedStatus === 'Active' && employee.status === 'active') ||
            (selectedStatus === 'On Leave' && employee.status === 'on_leave') ||
            (selectedStatus === 'Suspended' && employee.status === 'suspended') ||
            (selectedStatus === 'Terminated' && employee.status === 'terminated');
        
        return matchesSearch && matchesDepartment && matchesEmploymentType && matchesStatus;
    });
    
    console.log('Filtered employees:', filteredEmployees.length);
    renderEmployees();
}

function renderEmployees() {
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (filteredEmployees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i data-feather="users" class="w-12 h-12 text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium">No employees found</p>
                        <p class="text-sm">Try adjusting your search criteria</p>
                    </div>
                </td>
            </tr>
        `;
        feather.replace();
        return;
    }
    
    filteredEmployees.forEach(employee => {
        const statusBadge = getStatusBadge(employee.status);
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-600">${employee.name.split(' ').map(n => n[0]).join('')}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${employee.name}</div>
                        <div class="text-sm text-gray-500">${employee.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${employee.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${employee.department}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${employee.position}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${employee.type}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${formatDate(employee.start_date)}</td>
            <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                    <button class="text-indigo-600 hover:text-indigo-900" title="View">
                        <i data-feather="eye" class="w-4 h-4"></i>
                    </button>
                    <button class="text-blue-600 hover:text-blue-900" title="Edit">
                        <i data-feather="edit-2" class="w-4 h-4"></i>
                    </button>
                    <button class="text-green-600 hover:text-green-900" title="Documents">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                    </button>
                    <button class="text-purple-600 hover:text-purple-900" title="Performance">
                        <i data-feather="trending-up" class="w-4 h-4"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    // Re-initialize feather icons
    feather.replace();
    
    // Update pagination info
    updatePaginationInfo();
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
        'on_leave': '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">On Leave</span>',
        'suspended': '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>',
        'terminated': '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Terminated</span>'
    };
    return badges[status] || badges['active'];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function updatePaginationInfo() {
    const paginationInfo = document.querySelector('p.text-sm.text-gray-700');
    if (paginationInfo) {
        const showingCount = Math.min(filteredEmployees.length, 5);
        paginationInfo.innerHTML = `Showing <span class="font-medium">1</span> to <span class="font-medium">${showingCount}</span> of <span class="font-medium">${filteredEmployees.length}</span> results`;
    }
}
</script>
@endpush
@endsection
