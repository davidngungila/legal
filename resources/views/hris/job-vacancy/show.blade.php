@extends('layouts.app')

@section('title', 'Job Vacancy Details - Orvion HRIS')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('job-vacancy.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="arrow-left" class="w-6 h-6"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Job Vacancy Details</h1>
            </div>
            <p class="text-gray-600 mt-2 ml-9">{{ $jobVacancy->job_title }} • {{ $jobVacancy->company_name }}</p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            @if($jobVacancy->status === 'draft')
                <a href="{{ route('job-vacancy.edit', $jobVacancy) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i data-feather="edit-2" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <button onclick="submitVacancy({{ $jobVacancy->id }})"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i data-feather="send" class="w-4 h-4 mr-2"></i>
                    Submit for Approval
                </button>
            @endif

            @if($jobVacancy->status === 'hr_approved' && !$jobVacancy->isExpired())
                <button onclick="closeVacancy({{ $jobVacancy->id }})"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                    <i data-feather="x-circle" class="w-4 h-4 mr-2"></i>
                    Close Vacancy
                </button>
            @endif
        </div>
    </div>

    @php
        $statusClasses = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-yellow-100 text-yellow-800',
            'supervisor_approved' => 'bg-blue-100 text-blue-800',
            'manager_recommended' => 'bg-purple-100 text-purple-800',
            'hr_approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'closed' => 'bg-gray-100 text-gray-800',
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="briefcase" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Vacancy Information
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$jobVacancy->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucwords(str_replace('_', ' ', $jobVacancy->status)) }}
                    </span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Company Name</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Job Title</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->job_title }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Vacancy Type</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->vacancy_type === 'replacement' ? 'Replacement' : 'New Position' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Department</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->department }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Workstation</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->workstation }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Minimum Age</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->min_age ?? '-' }}</p>
                    </div>
                </div>
                @if($jobVacancy->vacancy_type === 'replacement' && $jobVacancy->replacement_reason)
                    <div class="px-6 pb-6">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Replacement Reason</label>
                        <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->replacement_reason }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="calendar" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Timeline
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Position Became Vacant</label>
                        <p class="text-sm font-semibold text-gray-900">{{ optional($jobVacancy->position_vacant_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Application Date</label>
                        <p class="text-sm font-semibold text-gray-900">{{ optional($jobVacancy->application_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Application Deadline</label>
                        <p class="text-sm font-semibold text-gray-900">{{ optional($jobVacancy->application_deadline)->format('d M Y') }}</p>
                        @if($jobVacancy->application_deadline)
                            @if($jobVacancy->isExpired())
                                <p class="text-xs text-red-600 font-medium mt-1">Expired</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">{{ $jobVacancy->days_until_deadline }} days left</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="file-text" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Job Description & Qualifications
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Job Description</label>
                        <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->job_description }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Academic Qualifications</label>
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->academic_qualifications ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Professional Qualifications</label>
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->professional_qualifications ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Other Qualifications</label>
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->other_qualifications ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Additional Comments</label>
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $jobVacancy->additional_comments ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="dollar-sign" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Compensation
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Salary Range</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $jobVacancy->salary_range }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="users" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Tracking
                    </h2>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Initiated By</span>
                        <span class="font-semibold text-gray-900">{{ optional($jobVacancy->initiator)->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Created At</span>
                        <span class="font-semibold text-gray-900">{{ optional($jobVacancy->created_at)->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Updated At</span>
                        <span class="font-semibold text-gray-900">{{ optional($jobVacancy->updated_at)->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="paperclip" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Files
                    </h2>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Shortlisted File</span>
                        @if($jobVacancy->shortlisted_file_path)
                            <a class="text-indigo-600 hover:text-indigo-800 font-semibold" href="{{ asset('storage/' . $jobVacancy->shortlisted_file_path) }}" target="_blank" rel="noopener noreferrer">View</a>
                        @else
                            <span class="text-gray-900">-</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Signed File</span>
                        @if($jobVacancy->signed_file_path)
                            <a class="text-indigo-600 hover:text-indigo-800 font-semibold" href="{{ asset('storage/' . $jobVacancy->signed_file_path) }}" target="_blank" rel="noopener noreferrer">View</a>
                        @else
                            <span class="text-gray-900">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function submitVacancy(vacancyId) {
    if (!confirm('Submit this job vacancy for approval?')) {
        return;
    }

    try {
        const response = await fetch(`/job-vacancy/${vacancyId}/submit`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification(result.message || 'Job vacancy submitted for approval', 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification(result.message || 'Operation failed', 'error');
        }
    } catch (error) {
        console.error('Vacancy submission error:', error);
        showNotification('An error occurred during the operation', 'error');
    }
}

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
            setTimeout(() => window.location.reload(), 1200);
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

document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush

