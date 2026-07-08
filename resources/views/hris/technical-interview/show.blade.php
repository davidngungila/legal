@extends('layouts.app')

@section('title', 'Technical Interview Details - ' . $technicalInterview->interview_number)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Technical Interview Details</h1>
            <p class="text-gray-600 mt-2">View technical assessment for {{ $technicalInterview->candidate_name }}</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            @if($technicalInterview->canBeEdited())
                <a href="{{ route('technical-interview.edit', $technicalInterview) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i data-feather="edit" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route('technical-interview.index') }}" 
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-3 py-1 rounded-full text-sm font-medium 
            {{ $technicalInterview->status === 'submitted' ? 'bg-green-100 text-green-800' : 
               ($technicalInterview->status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
               ($technicalInterview->status === 'manager_approved' ? 'bg-blue-100 text-blue-800' :
               ($technicalInterview->status === 'rejected' ? 'bg-red-100 text-red-800' :
               'bg-gray-100 text-gray-800'))) }}">
            {{ ucfirst(str_replace('_', ' ', $technicalInterview->status)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Interview Number</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $technicalInterview->interview_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Interview Date</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $technicalInterview->interview_date ? $technicalInterview->interview_date->format('d M, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Candidate Name</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $technicalInterview->candidate_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Job Title</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $technicalInterview->job_title }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Interviewer</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">{{ $technicalInterview->interviewer_name }}</p>
                    </div>
                    @if($technicalInterview->hrInterview)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">HR Interview</label>
                        <p class="mt-1 text-sm text-gray-900 font-medium">
                            {{ $technicalInterview->hrInterview->candidate_name }} - {{ $technicalInterview->hrInterview->job_title }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Technical Assessment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Technical Assessment</h2>
                
                <div class="space-y-6">
                    @if($technicalInterview->business_process_knowledge)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Business Process Knowledge</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->business_process_knowledge }}</p>
                    </div>
                    @endif

                    @if($technicalInterview->technical_skills_assessment)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Technical Skills Assessment</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->technical_skills_assessment }}</p>
                    </div>
                    @endif

                    @if($technicalInterview->physical_capabilities)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Physical Capabilities</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->physical_capabilities }}</p>
                    </div>
                    @endif

                    @if($technicalInterview->practical_test_results)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Practical Test Results</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->practical_test_results }}</p>
                    </div>
                    @endif

                    @if($technicalInterview->other_technical_areas)
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Other Technical Areas</h3>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->other_technical_areas }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Assessment Results -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Assessment Results</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Technical Result</label>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $technicalInterview->technical_result === 'pass' ? 'bg-green-100 text-green-800' :
                                   ($technicalInterview->technical_result === 'fail' ? 'bg-red-100 text-red-800' :
                                   'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($technicalInterview->technical_result ?? 'N/A') }}
                            </span>
                        </div>
                    </div>
                    @if($technicalInterview->technical_comments)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Technical Comments</label>
                        <p class="mt-1 text-sm text-gray-700 whitespace-pre-wrap">{{ $technicalInterview->technical_comments }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Supporting Documents -->
            @if($technicalInterview->assessment_report || $technicalInterview->signed_file)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Supporting Documents</h2>
                <div class="space-y-4">
                    @if($technicalInterview->assessment_report)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i data-feather="file-text" class="w-5 h-5 text-gray-500 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Assessment Report</p>
                                <p class="text-xs text-gray-500">Uploaded document</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($technicalInterview->assessment_report) }}" target="_blank" 
                           class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Download
                        </a>
                    </div>
                    @endif

                    @if($technicalInterview->signed_file)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i data-feather="file-text" class="w-5 h-5 text-gray-500 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Signed Document</p>
                                <p class="text-xs text-gray-500">Uploaded document</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($technicalInterview->signed_file) }}" target="_blank" 
                           class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Download
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Approval Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Approval Status</h2>
                <div class="space-y-4">
                    @if($technicalInterview->interviewer_id)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Interviewer</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Completed</span>
                    </div>
                    @endif
                    @if($technicalInterview->department_manager_id)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Department Manager</span>
                        @if($technicalInterview->departmentManager)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Approved</span>
                        @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">Pending</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timestamps -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Timestamps</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created At</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $technicalInterview->created_at->format('d M, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $technicalInterview->updated_at->format('d M, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($technicalInterview->status === 'submitted' && !$technicalInterview->department_manager_id)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Manager Actions</h2>
                <form action="{{ route('technical-interview.approve', $technicalInterview) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Approve Interview
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
