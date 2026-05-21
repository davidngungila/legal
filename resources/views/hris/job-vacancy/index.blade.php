@extends('layouts.app')

@section('title', 'Job Vacancies - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Job Vacancies</h1>
            <p class="text-gray-600 mt-2">Manage job vacancies and recruitment processes</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('job-vacancy.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                New Vacancy
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search vacancies..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="supervisor_approved">Supervisor Approved</option>
                    <option value="manager_recommended">Manager Recommended</option>
                    <option value="hr_approved">HR Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div>
                <select id="departmentFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($vacancies->pluck('department')->unique() as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="new_position">New Position</option>
                    <option value="replacement">Replacement</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Vacancies Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vacancy Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Timeline
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Salary
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="vacanciesTableBody">
                    @forelse($vacancies as $vacancy)
                        <tr class="hover:bg-gray-50 transition-colors vacancy-row" 
                            data-title="{{ $vacancy->job_title }}"
                            data-company="{{ $vacancy->company_name }}"
                            data-department="{{ $vacancy->department }}"
                            data-status="{{ $vacancy->status }}"
                            data-type="{{ $vacancy->vacancy_type }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-sm">
                                                {{ strtoupper(substr($vacancy->job_title, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $vacancy->job_title }}</div>
                                        <div class="text-sm text-gray-500">{{ $vacancy->company_name }}</div>
                                        <div class="flex items-center mt-1">
                                            @if($vacancy->vacancy_type === 'replacement')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                    <i data-feather="refresh-cw" class="w-3 h-3 mr-1"></i>
                                                    Replacement
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i>
                                                    New Position
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    Applied: {{ $vacancy->application_date->format('d M Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Deadline: {{ $vacancy->application_deadline->format('d M Y') }}
                                </div>
                                @if($vacancy->isExpired())
                                    <span class="text-xs text-red-600 font-medium">Expired</span>
                                @elseif($vacancy->days_until_deadline <= 7)
                                    <span class="text-xs text-yellow-600 font-medium">{{ $vacancy->days_until_deadline }} days left</span>
                                @else
                                    <span class="text-xs text-green-600 font-medium">{{ $vacancy->days_until_deadline }} days left</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $vacancy->department }}</div>
                                <div class="text-sm text-gray-500">{{ $vacancy->workstation }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $vacancy->salary_range }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($vacancy->status)
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
                                    @case('supervisor_approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Supervisor Approved
                                        </span>
                                        @break
                                    @case('manager_recommended')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Manager Recommended
                                        </span>
                                        @break
                                    @case('hr_approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            HR Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                        @break
                                    @case('closed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Closed
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('job-vacancy.show', $vacancy) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($vacancy->status === 'draft')
                                        <a href="{{ route('job-vacancy.edit', $vacancy) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            <i data-feather="edit-2" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    @if($vacancy->status === 'hr_approved' && !$vacancy->isExpired())
                                        <button onclick="closeVacancy({{ $vacancy->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Close Vacancy">
                                            <i data-feather="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="briefcase" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No job vacancies found</p>
                                    <p class="text-sm">Get started by creating your first job vacancy.</p>
                                    <a href="{{ route('job-vacancy.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Create Vacancy
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vacancies->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $vacancies->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $vacancies->firstItem() }}</span> to 
                            <span class="font-medium">{{ $vacancies->lastItem() }}</span> of 
                            <span class="font-medium">{{ $vacancies->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $vacancies->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Job Vacancy Management System
class JobVacancyManager {
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
        searchInput.addEventListener('input', () => this.filterVacancies());

        // Filter functionality
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', () => this.filterVacancies());

        const departmentFilter = document.getElementById('departmentFilter');
        departmentFilter.addEventListener('change', () => this.filterVacancies());

        const typeFilter = document.getElementById('typeFilter');
        typeFilter.addEventListener('change', () => this.filterVacancies());
    }

    filterVacancies() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const departmentFilter = document.getElementById('departmentFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const vacancyRows = document.querySelectorAll('.vacancy-row');

        vacancyRows.forEach(row => {
            const title = row.dataset.title.toLowerCase();
            const company = row.dataset.company.toLowerCase();
            const department = row.dataset.department;
            const status = row.dataset.status;
            const type = row.dataset.type;

            const matchesSearch = !searchTerm || title.includes(searchTerm) || company.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesDepartment = !departmentFilter || department === departmentFilter;
            const matchesType = !typeFilter || type === typeFilter;

            if (matchesSearch && matchesStatus && matchesDepartment && matchesType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

// Close vacancy function
async function closeVacancy(vacancyId) {
    if (!confirm('Are you sure you want to close this job vacancy?')) {
        return;
    }

    try {
        const response = await fetch(`/job-vacancy/${vacancyId}/close`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Job vacancy closed successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Vacancy closure error:', error);
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

// Initialize job vacancy manager
document.addEventListener('DOMContentLoaded', function() {
    window.jobVacancyManager = new JobVacancyManager();
});
</script>
@endpush
