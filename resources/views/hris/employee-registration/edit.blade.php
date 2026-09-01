@extends('layouts.app')

@section('title', 'Edit Employee Registration - Orvion HRIS')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('employee-registration.show', $employeeRegistration) }}" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="arrow-left" class="w-6 h-6"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Registration</h1>
            </div>
            <p class="text-gray-600 mt-2 ml-9">Updating information for {{ $employeeRegistration->first_name }} {{ $employeeRegistration->surname }}</p>
        </div>
    </div>

    <!-- Employee Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="editRegistrationForm" class="p-6 space-y-8" enctype="multipart/form-data" data-no-transition>
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="registrationStatus" value="{{ $employeeRegistration->status }}">

            <datalist id="tanzaniaRegions">
                <option value="Dar es Salaam">
                <option value="Arusha">
                <option value="Kilimanjaro">
                <option value="Tanga">
                <option value="Morogoro">
                <option value="Pwani (Coast)">
                <option value="Dodoma">
                <option value="Singida">
                <option value="Tabora">
                <option value="Kigoma">
                <option value="Shinyanga">
                <option value="Mwanza">
                <option value="Mara">
                <option value="Manyara">
                <option value="Lindi">
                <option value="Mtwara">
                <option value="Ruvuma">
                <option value="Iringa">
                <option value="Mbeya">
                <option value="Songwe">
                <option value="Katavi">
                <option value="Njombe">
                <option value="Geita">
                <option value="Simiyu">
                <option value="Kagera">
                <option value="Zanzibar Urban/West">
                <option value="Zanzibar North">
                <option value="Zanzibar Central/South">
                <option value="Pemba North">
                <option value="Pemba South">
            </datalist>
            
            <!-- Interview Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Interview Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employee Number
                        </label>
                        <input type="text" value="{{ $employeeRegistration->employee_number }}" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            HR Interview <span class="text-red-500">*</span>
                        </label>
                        <select name="hr_interview_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select HR Interview</option>
                            @foreach($hrInterviews as $hrInterview)
                                <option value="{{ $hrInterview->id }}" {{ $employeeRegistration->hr_interview_id == $hrInterview->id ? 'selected' : '' }}>
                                    {{ $hrInterview->candidate_name }} - {{ $hrInterview->job_title }} ({{ $hrInterview->interview_number }})
                                </option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm hidden" id="hr_interview_id_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Technical Interview
                        </label>
                        <select name="technical_interview_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Technical Interview (Optional)</option>
                            @foreach($technicalInterviews as $technicalInterview)
                                <option value="{{ $technicalInterview->id }}" {{ $employeeRegistration->technical_interview_id == $technicalInterview->id ? 'selected' : '' }}>
                                    {{ $technicalInterview->candidate_name }} - {{ $technicalInterview->job_title }} ({{ $technicalInterview->interview_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Personal Details Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Personal Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="surname" value="{{ $employeeRegistration->surname }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="surname_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" value="{{ $employeeRegistration->first_name }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="first_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name" value="{{ $employeeRegistration->middle_name }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" value="{{ $employeeRegistration->date_of_birth->format('Y-m-d') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="date_of_birth_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ $employeeRegistration->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $employeeRegistration->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $employeeRegistration->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="gender_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Birthplace <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="birthplace" value="{{ $employeeRegistration->birthplace }}" required list="tanzaniaRegions"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Type to filter regions...">
                        <span class="text-red-500 text-sm hidden" id="birthplace_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Residence Area <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="residence_area" value="{{ $employeeRegistration->residence_area }}" required list="tanzaniaRegions"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Type to filter regions...">
                        <span class="text-red-500 text-sm hidden" id="residence_area_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Permanent Residence <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="permanent_residence" value="{{ $employeeRegistration->permanent_residence }}" required list="tanzaniaRegions"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Type to filter regions...">
                        <span class="text-red-500 text-sm hidden" id="permanent_residence_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email_address" value="{{ $employeeRegistration->email_address }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="email_address_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone_number" value="{{ $employeeRegistration->phone_number }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="phone_number_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Address
                        </label>
                        <input type="text" name="postal_address" value="{{ $employeeRegistration->postal_address }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            National ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="national_id" value="{{ $employeeRegistration->national_id }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="national_id_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Passport Number
                        </label>
                        <input type="text" name="passport_number" value="{{ $employeeRegistration->passport_number }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            TIN Number
                        </label>
                        <input type="text" name="tin_number" value="{{ $employeeRegistration->tin_number }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NSSF Number
                        </label>
                        <input type="text" name="nssf_number" value="{{ $employeeRegistration->nssf_number }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NHIF Number
                        </label>
                        <input type="text" name="nhif_number" value="{{ $employeeRegistration->nhif_number }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Employment (Employee) Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Employee Employment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <select name="department" id="department" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->code }}" data-department-id="{{ $dept->id }}" {{ $employeeRegistration->department == $dept->code ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm hidden" id="department_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <select name="position" id="position" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Position</option>
                            @php
                                $selectedPosition = $employeeRegistration->position;
                                $positionExists = $positions->contains(fn($p) => $p->title === $selectedPosition);
                            @endphp
                            @if(!$positionExists && $selectedPosition)
                                <option value="{{ $selectedPosition }}" selected>{{ $selectedPosition }}</option>
                            @endif
                            @foreach($positions as $pos)
                                <option value="{{ $pos->title }}" {{ $selectedPosition == $pos->title ? 'selected' : '' }}>{{ $pos->title }}</option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm hidden" id="position_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="employment_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Employment Type</option>
                            @foreach($employmentTypes as $code => $name)
                                <option value="{{ $code }}" {{ $employeeRegistration->employment_type == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm hidden" id="employment_type_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employee Status <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Status</option>
                            <option value="active" {{ $employeeRegistration->employee_status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="probation" {{ $employeeRegistration->employee_status == 'probation' ? 'selected' : '' }}>Probation</option>
                            <option value="inactive" {{ $employeeRegistration->employee_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ $employeeRegistration->employee_status == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="terminated" {{ $employeeRegistration->employee_status == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="employee_status_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Role
                        </label>
                        <select name="role"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $employeeRegistration->role == $role->name ? 'selected' : '' }}>{{ $role->display_name ?? $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reporting To
                        </label>
                        <select name="manager_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ $employeeRegistration->manager_id == $manager->id ? 'selected' : '' }}>{{ $manager->first_name }} {{ $manager->last_name }} - {{ $manager->position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Work Schedule
                        </label>
                        <input type="text" name="work_schedule" value="{{ $employeeRegistration->work_schedule }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Education Level
                        </label>
                        <input type="text" name="education_level" value="{{ $employeeRegistration->education_level }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Skills & Qualifications Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Skills & Qualifications</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Skills
                        </label>
                        <input type="text" name="skills" value="{{ $employeeRegistration->skills ? implode(', ', $employeeRegistration->skills) : '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter skills separated by commas">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Languages
                        </label>
                        <input type="text" name="languages" value="{{ $employeeRegistration->languages ? implode(', ', $employeeRegistration->languages) : '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter languages separated by commas">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Professional Qualifications
                        </label>
                        <input type="text" name="professional_qualifications" value="{{ $employeeRegistration->professional_qualifications ? implode(', ', $employeeRegistration->professional_qualifications) : '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter qualifications separated by commas">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Certifications
                        </label>
                        <input type="text" name="certifications" value="{{ $employeeRegistration->certifications ? implode(', ', $employeeRegistration->certifications) : '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter certifications separated by commas">
                    </div>
                </div>
            </div>

            <!-- Compensation & Bank Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Compensation & Bank Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Base Salary <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="salary" value="{{ $employeeRegistration->salary }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="salary_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Currency</option>
                            <option value="TZS" {{ $employeeRegistration->currency == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                            <option value="USD" {{ $employeeRegistration->currency == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ $employeeRegistration->currency == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ $employeeRegistration->currency == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="currency_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Frequency <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_frequency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Frequency</option>
                            <option value="monthly" {{ $employeeRegistration->payment_frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="bi-weekly" {{ $employeeRegistration->payment_frequency == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="weekly" {{ $employeeRegistration->payment_frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="payment_frequency_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bank Name
                        </label>
                        <input type="text" name="bank_name" value="{{ $employeeRegistration->bank_name }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bank Branch
                        </label>
                        <input type="text" name="bank_branch" value="{{ $employeeRegistration->bank_branch }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bank Account
                        </label>
                        <input type="text" name="bank_account" value="{{ $employeeRegistration->bank_account }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Address & Emergency Contact Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Address & Emergency Contact</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ $employeeRegistration->address }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ $employeeRegistration->city }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                        <input type="text" name="region" value="{{ $employeeRegistration->region }}" list="tanzaniaRegions" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Type to filter regions...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ $employeeRegistration->postal_code }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" name="country" value="{{ $employeeRegistration->country }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" value="{{ $employeeRegistration->emergency_contact_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" value="{{ $employeeRegistration->emergency_contact_phone }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" value="{{ $employeeRegistration->emergency_contact_relationship }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Login Credentials Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Login Credentials</h2>
                <p class="text-sm text-gray-500 mb-6">These credentials are used to create the employee's login account when the registration is approved.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Login Email
                        </label>
                        <input type="email" name="login_email" value="{{ $employeeRegistration->login_email }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="login_email_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input type="password" name="password" minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Leave blank to keep unchanged">
                        <span class="text-red-500 text-sm hidden" id="password_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="password_confirmation_error"></span>
                    </div>
                </div>
            </div>

            <!-- Employment Details Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Employment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Place of Recruitment <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="place_of_recruitment" value="{{ $employeeRegistration->place_of_recruitment }}" required list="tanzaniaRegions"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Type to filter regions...">
                        <span class="text-red-500 text-sm hidden" id="place_of_recruitment_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Work Station <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="work_station" value="{{ $employeeRegistration->work_station }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="work_station_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type of Contract <span class="text-red-500">*</span>
                        </label>
                        <select name="type_of_contract" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Contract Type</option>
                            @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" {{ $employeeRegistration->type_of_contract === $typeKey ? 'selected' : '' }}>{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm hidden" id="type_of_contract_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date Employed <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_employed" value="{{ $employeeRegistration->date_employed->format('Y-m-d') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="date_employed_error"></span>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Job Descriptions <span class="text-red-500">*</span>
                    </label>
                    <textarea name="job_descriptions" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $employeeRegistration->job_descriptions }}</textarea>
                    <span class="text-red-500 text-sm hidden" id="job_descriptions_error"></span>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Terms and Conditions <span class="text-red-500">*</span>
                    </label>
                    <textarea name="terms_conditions" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $employeeRegistration->terms_conditions }}</textarea>
                    <span class="text-red-500 text-sm hidden" id="terms_conditions_error"></span>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Additional Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ranking Details
                        </label>
                        <textarea name="ranking_details" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $employeeRegistration->ranking_details }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employment History
                        </label>
                        <textarea name="employment_history" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $employeeRegistration->employment_history }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Consent and Signatures Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Consent and Signatures</h2>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="information_consent" id="information_consent" value="1" {{ $employeeRegistration->information_consent ? 'checked' : '' }} required
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="information_consent" class="ml-2 block text-sm text-gray-900">
                            I consent to the provision of my information for employment registration purposes <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <span class="text-red-500 text-sm hidden" id="information_consent_error"></span>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-signature-pad name="employee_signature" label="Employee Signature" :required="true" :existingPath="$employeeRegistration->employee_signature_path" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Signature Date
                            </label>
                            <input type="date" name="signature_date" value="{{ $employeeRegistration->signature_date ? $employeeRegistration->signature_date->format('Y-m-d') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Upload Section -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Supporting Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Signed Registration Form
                        </label>
                        @if($employeeRegistration->signed_document_path)
                            <div class="mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                                <span class="text-xs text-gray-600 truncate">Current: {{ basename($employeeRegistration->signed_document_path) }}</span>
                                <a href="{{ Storage::url($employeeRegistration->signed_document_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">View</a>
                            </div>
                        @endif
                        <input type="file" name="signed_document" accept=".pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Upload new signed registration document to replace current one (PDF, DOC, DOCX)</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between">
                <div></div>
                <div class="flex space-x-3">
                    <button type="button" onclick="window.history.back()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <span id="btnText">Update Registration</span>
                        <div id="btnLoader" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
            <!-- Saving Overlay -->
            <div id="savingOverlay" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-xl p-8 flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-indigo-600 mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">Saving information...</p>
                    <p class="text-xs text-gray-400 mt-1">Please do not close or refresh this page.</p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
class EditRegistrationManager {
    constructor() {
        this.form = document.getElementById('editRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFormValidation();
        this.initializeFeather();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });
    }

    setupFormValidation() {
        // Simple real-time validation for required fields
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    validateField(field) {
        if (field.required && !field.value.trim()) {
            this.showFieldError(field.name, 'This field is required');
            return false;
        }
        return true;
    }

    showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.getElementById(`${fieldName}_error`);
        if (field) field.classList.add('border-red-500');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    clearFieldError(field) {
        field.classList.remove('border-red-500');
        const errorElement = document.getElementById(`${field.name}_error`);
        if (errorElement) errorElement.classList.add('hidden');
    }

    async submitForm() {
        // Simple validation check before submit
        let isValid = true;
        const requiredFields = this.form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!this.validateField(field)) isValid = false;
        });

        if (!isValid) {
            Swal.fire('Validation Error', 'Please fill in all required fields correctly.', 'error');
            return;
        }

        // Prepare form data
        const formData = new FormData(this.form);
        
        // Show loading state
        this.submitBtn.disabled = true;
        this.btnText.textContent = 'Updating...';
        this.btnLoader.classList.remove('hidden');
        const overlay = document.getElementById('savingOverlay');
        if (overlay) overlay.classList.remove('hidden');

        try {
            const response = await fetch(`/employee-registration/{{ $employeeRegistration->id }}`, {
                method: 'POST', // Using POST with _method=PUT for file support
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    window.location.href = `/employee-registration/{{ $employeeRegistration->id }}`;
                });
            } else {
                this.handleErrors(data);
            }
        } catch (error) {
            console.error('Submission failed:', error);
            Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
        } finally {
            this.submitBtn.disabled = false;
            this.btnText.textContent = 'Update Registration';
            this.btnLoader.classList.add('hidden');
            if (overlay) overlay.classList.add('hidden');
        }
    }

    handleErrors(data) {
        if (data.errors) {
            Object.keys(data.errors).forEach(key => {
                this.showFieldError(key, data.errors[key][0]);
            });
            Swal.fire('Validation Error', 'Please check the form for errors.', 'error');
        } else {
            Swal.fire('Error', data.message || 'Operation failed', 'error');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new EditRegistrationManager();
});
</script>
@endpush
