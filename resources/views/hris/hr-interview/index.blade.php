@extends('layouts.app')

@section('title', 'HR Competency Interviews - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">HR Competency Interviews</h1>
            <p class="text-gray-600 mt-2">Manage and track competency interview assessments</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('hr-interview.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                New Interview
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search interviews..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="hr_approved">HR Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <select id="recommendationFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Recommendations</option>
                    <option value="accepted">Accepted</option>
                    <option value="not_accepted">Not Accepted</option>
                    <option value="waiting_list">Waiting List</option>
                </select>
            </div>
            <div>
                <select id="jobTitleFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Job Titles</option>
                    @foreach($interviews->pluck('job_title')->unique() as $jobTitle)
                        <option value="{{ $jobTitle }}">{{ $jobTitle }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Interviews Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Interview Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Candidate Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Assessment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Recommendation
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="interviewsTableBody">
                    @forelse($interviews as $interview)
                        <tr class="hover:bg-gray-50 transition-colors interview-row" 
                            data-candidate="{{ $interview->candidate_name }}"
                            data-job="{{ $interview->job_title }}"
                            data-status="{{ $interview->status }}"
                            data-recommendation="{{ $interview->recruiter_recommendation }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">
                                                {{ strtoupper(substr($interview->candidate_name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $interview->candidate_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $interview->interview_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $interview->interview_date->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $interview->job_title }}</div>
                                <div class="text-sm text-gray-500">{{ $interview->place_of_recruitment }}</div>
                                <div class="text-sm text-gray-500">{{ $interview->total_years_experience }} years exp.</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Overall: {{ $interview->overall_rating }}/5</div>
                                <div class="text-sm text-gray-500">{{ $interview->getOverallAssessment() }}</div>
                                <div class="text-xs text-gray-400">Avg: {{ $interview->getCompetencyAverage() }}/5</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $interview->getRecommendationColor() }}-100 text-{{ $interview->getRecommendationColor() }}-800">
                                    {{ $interview->getRecommendationLabel() }}
                                </span>
                                @if($interview->recommended_job_title)
                                    <div class="text-xs text-gray-500 mt-1">{{ $interview->recommended_job_title }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($interview->status)
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
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('hr-interview.show', $interview) }}"
                                       class="inline-flex items-center px-2 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-medium">
                                        <i data-feather="eye" class="w-3 h-3 mr-1"></i>
                                        View
                                    </a>
                                    @if($interview->canBeEdited())
                                        <a href="{{ route('hr-interview.edit', $interview) }}"
                                           class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-medium">
                                            <i data-feather="edit-2" class="w-3 h-3 mr-1"></i>
                                            Edit
                                        </a>
                                    @endif
                                    @if($interview->status === 'draft')
                                        <button onclick="submitInterview({{ $interview->id }})"
                                                class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-700 hover:bg-green-100 text-xs font-medium">
                                            <i data-feather="send" class="w-3 h-3 mr-1"></i>
                                            Submit
                                        </button>
                                    @endif
                                    @if($interview->status === 'submitted')
                                        <button onclick="approveInterview({{ $interview->id }})"
                                                class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-700 hover:bg-green-100 text-xs font-medium">
                                            <i data-feather="check-circle" class="w-3 h-3 mr-1"></i>
                                            Approve
                                        </button>
                                        <button onclick="rejectInterview({{ $interview->id }})"
                                                class="inline-flex items-center px-2 py-1 rounded bg-red-50 text-red-700 hover:bg-red-100 text-xs font-medium">
                                            <i data-feather="x-circle" class="w-3 h-3 mr-1"></i>
                                            Reject
                                        </button>
                                    @endif
                                    @if($interview->status === 'hr_approved')
                                        <button onclick="generatePdf({{ $interview->id }})"
                                                class="inline-flex items-center px-2 py-1 rounded bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-medium">
                                            <i data-feather="file-text" class="w-3 h-3 mr-1"></i>
                                            PDF
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
                                    <p class="text-lg font-medium">No interviews found</p>
                                    <p class="text-sm">Get started by conducting your first competency interview.</p>
                                    <a href="{{ route('hr-interview.create') }}" 
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Create Interview
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($interviews->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $interviews->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $interviews->firstItem() }}</span> to 
                            <span class="font-medium">{{ $interviews->lastItem() }}</span> of 
                            <span class="font-medium">{{ $interviews->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $interviews->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// HR Interview Management System
class HrInterviewManager {
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
        searchInput.addEventListener('input', () => this.filterInterviews());

        // Filter functionality
        const statusFilter = document.getElementById('statusFilter');
        statusFilter.addEventListener('change', () => this.filterInterviews());

        const recommendationFilter = document.getElementById('recommendationFilter');
        recommendationFilter.addEventListener('change', () => this.filterInterviews());

        const jobTitleFilter = document.getElementById('jobTitleFilter');
        jobTitleFilter.addEventListener('change', () => this.filterInterviews());
    }

    filterInterviews() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const recommendationFilter = document.getElementById('recommendationFilter').value;
        const jobTitleFilter = document.getElementById('jobTitleFilter').value;
        const interviewRows = document.querySelectorAll('.interview-row');

        interviewRows.forEach(row => {
            const candidate = row.dataset.candidate.toLowerCase();
            const job = row.dataset.job.toLowerCase();
            const status = row.dataset.status;
            const recommendation = row.dataset.recommendation;

            const matchesSearch = !searchTerm || candidate.includes(searchTerm) || job.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesRecommendation = !recommendationFilter || recommendation === recommendationFilter;
            const matchesJobTitle = !jobTitleFilter || job === jobTitleFilter;

            if (matchesSearch && matchesStatus && matchesRecommendation && matchesJobTitle) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
}

async function submitInterview(interviewId) {
    if (!confirm('Submit this interview for approval?')) {
        return;
    }

    try {
        const response = await fetch(`/hr-interview/${interviewId}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (result.success) {
            showNotification(result.message || 'Interview submitted for approval', 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Interview submission error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

// Approve interview function
async function approveInterview(interviewId) {
    if (!confirm('Are you sure you want to approve this interview?')) {
        return;
    }

    try {
        const response = await fetch(`/hr-interview/${interviewId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Interview approved successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Interview approval error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

// Reject interview function
async function rejectInterview(interviewId) {
    const reason = prompt('Please provide a reason for rejection:');
    
    if (!reason) {
        return;
    }

    try {
        const response = await fetch(`/hr-interview/${interviewId}/reject`, {
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
            showNotification('Interview rejected successfully', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Interview rejection error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

// Generate PDF function
async function generatePdf(interviewId) {
    try {
        const response = await fetch(`/hr-interview/${interviewId}/generate-pdf`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('PDF report generated successfully', 'success');
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

// Initialize HR interview manager
document.addEventListener('DOMContentLoaded', function() {
    window.hrInterviewManager = new HrInterviewManager();
});
</script>
@endpush
