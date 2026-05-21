@extends('layouts.app')

@section('title', 'User Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">User Management</h1>
            <p class="text-gray-600 mt-2">Manage system users and their access</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('user-registration.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Register New User
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search users..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($users->pluck('department_name')->unique() as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact Information
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Professional Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors user-row" 
                            data-name="{{ $user->full_name }}"
                            data-email="{{ $user->email }}"
                            data-department="{{ $user->department_name }}"
                            data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-medium">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->surname, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                <div class="text-sm text-gray-500">{{ $user->phone_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->designation }}</div>
                                <div class="text-sm text-gray-500">{{ $user->department_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('user-registration.edit', $user) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @if($user->is_active)
                                        <button onclick="toggleUserStatus({{ $user->id }}, false)" 
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Deactivate User">
                                            <i data-feather="user-x" class="w-4 h-4"></i>
                                        </button>
                                    @else
                                        <button onclick="toggleUserStatus({{ $user->id }}, true)" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Activate User">
                                            <i data-feather="user-check" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="users" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No users found</p>
                                    <p class="text-sm">Get started by registering your first user.</p>
                                    <a href="{{ route('user-registration.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Register User
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $users->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $users->firstItem() }}</span> to 
                            <span class="font-medium">{{ $users->lastItem() }}</span> of 
                            <span class="font-medium">{{ $users->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// User Management System
class UserManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterUsers());

        // Department filter
        const departmentFilter = document.getElementById('departmentFilter');
        departmentFilter.addEventListener('change', () => this.filterUsers());

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', () => this.filterUsers());
    }

    filterUsers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const departmentFilter = document.getElementById('departmentFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const userRows = document.querySelectorAll('.user-row');

        userRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const email = row.dataset.email.toLowerCase();
            const department = row.dataset.department;
            const status = row.dataset.status;

            const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesDepartment = !departmentFilter || department === departmentFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesDepartment && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

// Toggle user status function
async function toggleUserStatus(userId, activate) {
    const action = activate ? 'activate' : 'deactivate';
    
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
        return;
    }

    try {
        const response = await fetch(`/user-registration/${userId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification(result.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Status toggle error:', error);
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

// Initialize user manager
document.addEventListener('DOMContentLoaded', function() {
    window.userManager = new UserManager();
});
</script>
@endpush
