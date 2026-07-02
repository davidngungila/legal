@extends('layouts.app')

@section('title', 'Employee Details - ' . $employee->full_name)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Details</h1>
            <p class="text-gray-600 mt-2">Viewing detailed information for {{ $employee->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="edit" class="w-4 h-4 inline mr-2"></i>
                Edit Employee
            </a>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-32 h-32 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-indigo-600">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                <p class="text-gray-500">{{ $employee->position }}</p>
                <div class="mt-4">
                    <span class="px-3 py-1 bg-{{ $employee->status_badge_color }}-100 text-{{ $employee->status_badge_color }}-700 rounded-full text-sm font-medium capitalize">
                        {{ $employee->status }}
                    </span>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-100 text-left space-y-4">
                    <div class="flex items-center text-gray-600">
                        <i data-feather="mail" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->email }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="phone" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="map-pin" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">{{ $employee->city ?? 'N/A' }}, {{ $employee->country ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="calendar" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">Age: {{ $employee->age }} years</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <i data-feather="clock" class="w-4 h-4 mr-3"></i>
                        <span class="text-sm">Employment: {{ $employee->employment_duration }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Tabs -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px px-6 overflow-x-auto" id="employeeTabs">
                        <button class="tab-btn border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" data-tab="personal">
                            Personal Info
                        </button>
                        <button class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" data-tab="employment">
                            Employment Info
                        </button>
                        <button class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" data-tab="bank">
                            Bank Details
                        </button>
                        <button class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" data-tab="compliance">
                            Compliance
                        </button>
                        <button class="tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8" data-tab="documents">
                            Documents
                        </button>
                    </nav>
                </div>
                <div class="p-6">
                    <!-- Personal Info Tab -->
                    <div id="personal-tab" class="tab-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">First Name</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Name</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->last_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Gender</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium capitalize">{{ $employee->gender ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">National ID</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->national_id ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Passport Number</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->passport_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Address</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->address ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">City</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->city ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Region</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->region ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Postal Code</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->postal_code ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Country</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->country ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Emergency Contact Name</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->emergency_contact_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Emergency Contact Phone</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->emergency_contact_phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Emergency Contact Relationship</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->emergency_contact_relationship ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Info Tab -->
                    <div id="employment-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->employee_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Role</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->role ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->department }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Position</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->position }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Reporting To</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->manager ? $employee->manager->full_name : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Hire Date</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->hire_date ? $employee->hire_date->format('d M, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Employment Type</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium capitalize">{{ str_replace('_', ' ', $employee->employment_type) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Salary</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->formatted_salary }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Payment Frequency</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium capitalize">{{ str_replace('_', ' ', $employee->payment_frequency) ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Work Schedule</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->work_schedule ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Education Level</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->education_level ?? 'N/A' }}</p>
                            </div>
                            @if($employee->skills && count($employee->skills) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Skills</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($employee->skills as $skill)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($employee->languages && count($employee->languages) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Languages</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($employee->languages as $language)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">{{ $language }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($employee->professional_qualifications && count($employee->professional_qualifications) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Professional Qualifications</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($employee->professional_qualifications as $qual)
                                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">{{ $qual }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @if($employee->certifications && count($employee->certifications) > 0)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Certifications</label>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($employee->certifications as $cert)
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">{{ $cert }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bank Details Tab -->
                    <div id="bank-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bank Name</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->bank_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bank Branch</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->bank_branch ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bank Account</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->bank_account ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Compliance Tab -->
                    <div id="compliance-tab" class="tab-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">TIN Number</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->tin_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">NSSF Number</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->nssf_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">NHIF Number</label>
                                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $employee->nhif_number ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Compliance Status</label>
                                @php($compliance = $employee->getComplianceInfo())
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $compliance['has_national_id'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['has_national_id'] ? '✓ National ID' : '✗ National ID Missing' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $compliance['has_tin_number'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['has_tin_number'] ? '✓ TIN Number' : '✗ TIN Number Missing' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $compliance['has_nssf_number'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['has_nssf_number'] ? '✓ NSSF Number' : '✗ NSSF Number Missing' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $compliance['has_nhif_number'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['has_nhif_number'] ? '✓ NHIF Number' : '✗ NHIF Number Missing' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $compliance['has_valid_contract'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['has_valid_contract'] ? '✓ Valid Contract' : '✗ No Active Contract' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2 mt-4">
                                        <span class="px-3 py-1 rounded text-sm font-medium {{ $compliance['is_compliant'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $compliance['is_compliant'] ? '✓ Fully Compliant' : '✗ Not Fully Compliant' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div id="documents-tab" class="tab-content hidden">
                        @if($employee->documents && $employee->documents->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="pb-3 text-sm font-semibold text-gray-600">Document Name</th>
                                            <th class="pb-3 text-sm font-semibold text-gray-600">Type</th>
                                            <th class="pb-3 text-sm font-semibold text-gray-600">Status</th>
                                            <th class="pb-3 text-sm font-semibold text-gray-600">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($employee->documents as $doc)
                                            <tr>
                                                <td class="py-3 text-sm text-gray-900">{{ $doc->document_name }}</td>
                                                <td class="py-3 text-sm text-gray-500">{{ $doc->document_type }}</td>
                                                <td class="py-3">
                                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-medium">{{ $doc->status }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Download</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i data-feather="file" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                                <p class="text-gray-500 text-sm">No documents uploaded yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-indigo-500', 'text-indigo-600');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                });

                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Add active class to current button
                this.classList.add('border-indigo-500', 'text-indigo-600');
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');

                // Show current tab content
                document.getElementById(`${tabId}-tab`).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
