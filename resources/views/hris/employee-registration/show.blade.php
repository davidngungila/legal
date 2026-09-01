@extends('layouts.app')

@section('title', 'Employee Registration Details - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('employee-registration.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="arrow-left" class="w-6 h-6"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Registration Details</h1>
            </div>
            <p class="text-gray-600 mt-2 ml-9">Viewing details for {{ $employeeRegistration->first_name }} {{ $employeeRegistration->surname }}</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            @if($employeeRegistration->status === 'draft')
                <a href="{{ route('employee-registration.edit', $employeeRegistration) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i data-feather="edit-2" class="w-4 h-4 mr-2"></i>
                    Edit Registration
                </a>
            @endif
            
            @if($employeeRegistration->status === 'submitted')
                <button onclick="approveRegistration({{ $employeeRegistration->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                    Approve
                </button>
                <button onclick="rejectRegistration({{ $employeeRegistration->id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center">
                    <i data-feather="x-circle" class="w-4 h-4 mr-2"></i>
                    Reject
                </button>
            @endif

            @if($employeeRegistration->status === 'approved')
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                    <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                    Print
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="user" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Personal Information
                    </h2>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'submitted' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$employeeRegistration->status] }}">
                        {{ ucfirst($employeeRegistration->status) }}
                    </span>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Full Name</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->first_name }} {{ $employeeRegistration->middle_name }} {{ $employeeRegistration->surname }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Employee Number</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->employee_number }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->email_address }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Phone Number</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->phone_number }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date of Birth</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->date_of_birth->format('d F Y') }} ({{ $employeeRegistration->age }} years)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Gender</label>
                        <p class="text-sm font-semibold text-gray-900">{{ ucfirst($employeeRegistration->gender) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Birthplace</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->birthplace }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Residence</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->residence_area }}</p>
                        <p class="text-xs text-gray-500 mt-1">Permanent: {{ $employeeRegistration->permanent_residence }}</p>
                    </div>
                </div>
            </div>

            <!-- Employment Information Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="briefcase" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Employment Details
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Work Station</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->work_station }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Contract Type</label>
                        <p class="text-sm font-semibold text-gray-900">{{ \App\Models\EmploymentContract::CONTRACT_TYPES[$employeeRegistration->type_of_contract] ?? $employeeRegistration->type_of_contract }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Date Employed</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->date_employed->format('d F Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Recruitment Place</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $employeeRegistration->place_of_recruitment }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Job Descriptions</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">{{ $employeeRegistration->job_descriptions }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Terms & Conditions</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">{{ $employeeRegistration->terms_conditions }}</div>
                    </div>
                </div>
            </div>

            <!-- History and Ranking -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="trending-up" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        History & Ranking
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Employment History</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">{{ $employeeRegistration->employment_history ?? 'No history provided' }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Ranking Details</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm text-gray-700 whitespace-pre-wrap">{{ $employeeRegistration->ranking_details ?? 'No ranking details provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Interviews & Workflow -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="check-square" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Workflow Status
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Registered By</label>
                        <div class="flex items-center mt-1">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs mr-2">
                                {{ strtoupper(substr($employeeRegistration->creator->first_name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900">{{ $employeeRegistration->creator->first_name ?? 'System' }} {{ $employeeRegistration->creator->last_name ?? '' }}</p>
                                <p class="text-gray-500 text-xs">{{ $employeeRegistration->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($employeeRegistration->approver)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Processed By</label>
                            <div class="flex items-center mt-1">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xs mr-2">
                                    {{ strtoupper(substr($employeeRegistration->approver->first_name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="text-sm">
                                    <p class="font-semibold text-gray-900">{{ $employeeRegistration->approver->first_name }} {{ $employeeRegistration->approver->last_name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $employeeRegistration->approved_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Linked Interviews</h3>
                        <div class="space-y-3">
                            @if($employeeRegistration->hrInterview)
                                <a href="#" class="flex items-center p-2 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors group">
                                    <i data-feather="file-text" class="w-4 h-4 text-purple-600 mr-2"></i>
                                    <div class="text-xs">
                                        <p class="font-bold text-purple-900 group-hover:text-purple-700">HR Interview</p>
                                        <p class="text-purple-600">{{ $employeeRegistration->hrInterview->interview_number }}</p>
                                    </div>
                                    <i data-feather="external-link" class="w-3 h-3 ml-auto text-purple-400"></i>
                                </a>
                            @endif
                            @if($employeeRegistration->technicalInterview)
                                <a href="#" class="flex items-center p-2 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors group">
                                    <i data-feather="file-text" class="w-4 h-4 text-orange-600 mr-2"></i>
                                    <div class="text-xs">
                                        <p class="font-bold text-orange-900 group-hover:text-orange-700">Technical Interview</p>
                                        <p class="text-orange-600">{{ $employeeRegistration->technicalInterview->interview_number }}</p>
                                    </div>
                                    <i data-feather="external-link" class="w-3 h-3 ml-auto text-orange-400"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center">
                        <i data-feather="paperclip" class="w-5 h-5 mr-2 text-indigo-600"></i>
                        Documents & Signature
                    </h2>
                </div>
                <div class="p-6">
                    @if($employeeRegistration->employee_signature_path)
                        <div class="mb-4 p-4 rounded-lg border border-gray-200">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Employee Signature</p>
                            <img src="{{ Storage::url($employeeRegistration->employee_signature_path) }}"
                                 alt="Employee Signature"
                                 class="max-h-24 border border-gray-200 rounded bg-white">
                            @if($employeeRegistration->signature_date)
                                <p class="text-xs text-gray-500 mt-1">Signed on {{ $employeeRegistration->signature_date->format('d M Y') }}</p>
                            @endif
                        </div>
                    @endif
                    @if($employeeRegistration->signed_document_path)
                        <div class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors group cursor-pointer">
                            <i data-feather="file" class="w-8 h-8 text-indigo-400 mr-3"></i>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-sm font-semibold text-gray-900 truncate">Signed Registration Form</p>
                                <p class="text-xs text-gray-500">Uploaded on {{ $employeeRegistration->updated_at->format('d M Y') }}</p>
                            </div>
                            <a href="{{ Storage::url($employeeRegistration->signed_document_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                <i data-feather="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @endif
                    @if(!$employeeRegistration->employee_signature_path && !$employeeRegistration->signed_document_path)
                        <div class="text-center py-6">
                            <i data-feather="file-minus" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">No documents uploaded</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function approveRegistration(id) {
        Swal.fire({
            title: 'Approve Registration?',
            text: 'This will finalize the employee registration.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                processAction(id, 'approve');
            }
        });
    }

    function rejectRegistration(id) {
        Swal.fire({
            title: 'Reject Registration?',
            text: 'Please provide a reason for rejection:',
            input: 'textarea',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject it!',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                processAction(id, 'reject', result.value);
            }
        });
    }

    function processAction(id, action, reason = null) {
        fetch(`/employee-registration/${id}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    }
</script>
@endpush
@endsection
