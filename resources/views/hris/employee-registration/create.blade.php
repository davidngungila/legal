@extends('layouts.app')

@section('title', 'Register from Interview - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-manrope">Register from Interview</h1>
        <p class="text-gray-600 mt-2">Register passed candidates as employees in the system</p>
    </div>

    <!-- Step Progress -->
    <div class="mb-8">
        <div class="flex flex-wrap items-center gap-2" id="stepProgress">
            @php
                $steps = [
                    1 => 'Interview',
                    2 => 'Personal',
                    3 => 'Employment',
                    4 => 'Skills',
                    5 => 'Compensation',
                    6 => 'Legal & Address',
                    7 => 'Login',
                    8 => 'Consent',
                ];
            @endphp
            @foreach($steps as $num => $label)
                <div class="flex items-center">
                    <div data-step-badge="{{ $num }}" class="flex items-center space-x-2 px-3 py-1.5 rounded-full border text-xs font-semibold transition-colors {{ $num === 1 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-500 border-gray-300' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] bg-white/20">{{ $num }}</span>
                        <span>{{ $label }}</span>
                    </div>
                    @if(!$loop->last)
                        <svg class="w-4 h-4 mx-1 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Employee Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="employeeRegistrationForm" class="p-6" autocomplete="off" data-no-transition>
            <input type="hidden" name="status" id="registrationStatus" value="submitted">

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

            <!-- Step 1: Interview Information -->
            <section data-step="1">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">1. Interview Information</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employee Number
                            </label>
                            <input type="text" id="employeeNumber" readonly
                                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                                   placeholder="Will be generated automatically">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                HR Interview <span class="text-red-500">*</span>
                            </label>
                            <select name="hr_interview_id" required data-required-step="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select HR Interview</option>
                                @foreach($hrInterviews as $hrInterview)
                                    <option value="{{ $hrInterview->id }}"
                                            data-candidate-name="{{ $hrInterview->candidate_name }}"
                                            data-job-title="{{ $hrInterview->job_title }}"
                                            data-recommended-title="{{ $hrInterview->recommended_job_title }}"
                                            data-birthplace="{{ $hrInterview->birthplace }}"
                                            data-residence="{{ $hrInterview->residence }}"
                                            data-place-of-recruitment="{{ $hrInterview->place_of_recruitment }}"
                                            data-current-salary="{{ $hrInterview->current_salary }}">
                                        {{ $hrInterview->candidate_name }} - {{ $hrInterview->job_title }} ({{ $hrInterview->interview_number }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-red-500 text-sm hidden" id="hr_interview_id_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Technical Interview <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <select name="technical_interview_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Technical Interview (Optional)</option>
                                @foreach($technicalInterviews as $technicalInterview)
                                    <option value="{{ $technicalInterview->id }}" data-hr-interview-id="{{ $technicalInterview->hr_interview_id }}">
                                        {{ $technicalInterview->candidate_name }} - {{ $technicalInterview->job_title }} ({{ $technicalInterview->interview_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 2: Personal Details -->
            <section data-step="2" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">2. Personal Details</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Surname <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="surname" required data-required-step="2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="surname_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" required data-required-step="2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="first_name_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Middle Name <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="middle_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Date of Birth <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_of_birth" required data-required-step="2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="date_of_birth_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Gender <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" required data-required-step="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="gender_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Birthplace <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="birthplace" required data-required-step="2" list="tanzaniaRegions"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Type to filter regions...">
                            <span class="text-red-500 text-sm hidden" id="birthplace_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Residence Area <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="residence_area" required data-required-step="2" list="tanzaniaRegions"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Type to filter regions...">
                            <span class="text-red-500 text-sm hidden" id="residence_area_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Permanent Residence <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="permanent_residence" required data-required-step="2" list="tanzaniaRegions"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Type to filter regions...">
                            <span class="text-red-500 text-sm hidden" id="permanent_residence_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email_address" required data-required-step="2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="email_address_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone_number" required data-required-step="2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="phone_number_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Postal Address <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="postal_address"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 3: Employment Details -->
            <section data-step="3" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">3. Employment Details</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Place of Recruitment <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="place_of_recruitment" required data-required-step="3" list="tanzaniaRegions"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Type to filter regions...">
                            <span class="text-red-500 text-sm hidden" id="place_of_recruitment_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Work Station <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="work_station" required data-required-step="3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="work_station_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Type of Contract <span class="text-red-500">*</span>
                            </label>
                            <select name="type_of_contract" required data-required-step="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Contract Type</option>
                                @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
                                    <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                @endforeach
                            </select>
                            <span class="text-red-500 text-sm hidden" id="type_of_contract_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Date Employed <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_employed" required data-required-step="3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="date_employed_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Department <span class="text-red-500">*</span>
                            </label>
                            <select name="department" id="department" required data-required-step="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->code }}" data-department-id="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-red-500 text-sm hidden" id="department_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Position <span class="text-red-500">*</span>
                            </label>
                            <select name="position" id="position" required data-required-step="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Position</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->title }}">{{ $pos->title }}</option>
                                @endforeach
                            </select>
                            <span class="text-red-500 text-sm hidden" id="position_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employment Type <span class="text-red-500">*</span>
                            </label>
                            <select name="employment_type" required data-required-step="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Employment Type</option>
                                @foreach($employmentTypes as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <span class="text-red-500 text-sm hidden" id="employment_type_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employee Status <span class="text-red-500">*</span>
                            </label>
                            <select name="employee_status" required data-required-step="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="probation">Probation</option>
                                <option value="inactive">Inactive</option>
                                <option value="on_leave">On Leave</option>
                                <option value="terminated">Terminated</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="employee_status_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Role <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <select name="role"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->display_name ?? $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reporting To <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <select name="manager_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->first_name }} {{ $manager->last_name }} - {{ $manager->position }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Work Schedule <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="work_schedule"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Education Level <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="education_level"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Job Descriptions <span class="text-red-500">*</span>
                        </label>
                        <textarea name="job_descriptions" rows="4" required data-required-step="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Describe the job responsibilities, duties, and requirements..."></textarea>
                        <span class="text-red-500 text-sm hidden" id="job_descriptions_error"></span>
                    </div>
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Terms and Conditions <span class="text-red-500">*</span>
                        </label>
                        <textarea name="terms_conditions" rows="4" required data-required-step="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Specify employment terms, conditions, and any special arrangements..."></textarea>
                        <span class="text-red-500 text-sm hidden" id="terms_conditions_error"></span>
                    </div>
                </div>
            </section>

            <!-- Step 4: Skills & Qualifications -->
            <section data-step="4" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">4. Skills & Qualifications</h2>
                        <span class="text-xs text-gray-400">All fields in this step are <span class="text-gray-500 font-semibold">optional</span>. Separate values with commas.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Skills <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="skills"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. Communication, Leadership, Python">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Languages <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="languages"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. English, Swahili, French">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Professional Qualifications <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="professional_qualifications"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. CPA, ACCA, PMP">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Certifications <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="certifications"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="e.g. AWS Certified, Cisco CCNA, Google Analyst">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 5: Compensation & Bank -->
            <section data-step="5" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">5. Compensation & Bank Details</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Base Salary <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="salary" required data-required-step="5"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="salary_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Currency <span class="text-red-500">*</span>
                            </label>
                            <select name="currency" required data-required-step="5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Currency</option>
                                <option value="TZS">TZS - Tanzanian Shilling</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="currency_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Payment Frequency <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_frequency" required data-required-step="5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Frequency</option>
                                <option value="monthly">Monthly</option>
                                <option value="bi-weekly">Bi-Weekly</option>
                                <option value="weekly">Weekly</option>
                            </select>
                            <span class="text-red-500 text-sm hidden" id="payment_frequency_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bank Name <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="bank_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bank Branch <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="bank_branch"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bank Account <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="bank_account"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 6: Legal IDs & Address -->
            <section data-step="6" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">6. Legal IDs, Address & Emergency Contact</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Legal IDs</h3>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    National ID <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="national_id" required data-required-step="6"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="text-red-500 text-sm hidden" id="national_id_error"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Passport Number <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="passport_number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    TIN Number <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="tin_number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NSSF Number <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="nssf_number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NHIF Number <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" name="nhif_number"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Address Information <span class="text-gray-400 font-normal">(Optional)</span></h3>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                                <input type="text" name="region" list="tanzaniaRegions" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Type to filter regions...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" name="postal_code" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" name="country" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Emergency Contact <span class="text-gray-400 font-normal">(Optional)</span></h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                <input type="text" name="emergency_contact_relationship" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 7: Login Credentials -->
            <section data-step="7" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">7. Login Credentials</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">These credentials will be used to create the employee's login account when the registration is approved.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Login Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="login_email" required data-required-step="7"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Used to log in to the system">
                            <span class="text-red-500 text-sm hidden" id="login_email_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" required data-required-step="7" minlength="8"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Minimum 8 characters">
                            <span class="text-red-500 text-sm hidden" id="password_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" required data-required-step="7"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="password_confirmation_error"></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 8: Consent & Review -->
            <section data-step="8" class="hidden">
                <div class="border-b border-gray-200 pb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">8. Consent, Additional Info & Review</h2>
                        <span class="text-xs text-gray-400">Fields with <span class="text-red-500">*</span> are required</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ranking Details <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea name="ranking_details" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Any ranking or performance evaluation details..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employment History <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea name="employment_history" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Previous employment history if applicable..."></textarea>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Review Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="reviewSummary">
                            <div class="text-xs text-gray-500">Select an HR interview to preview candidate details.</div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="information_consent" id="information_consent" value="1" required data-required-step="8"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="information_consent" class="ml-2 block text-sm text-gray-900">
                                I consent to the provision of my information for employment registration purposes <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <span class="text-red-500 text-sm hidden" id="information_consent_error"></span>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-signature-pad name="employee_signature" label="Employee Signature" :required="true" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Signature Date <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="date" name="signature_date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Navigation & Actions -->
            <div class="mt-8 flex items-center justify-between">
                <button type="button" id="prevBtn" onclick="employeeRegistrationManager.goToStep(currentStep - 1)"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    &larr; Previous
                </button>

                <div class="flex space-x-3">
                    <button type="button" id="draftBtn" onclick="employeeRegistrationManager.saveAsDraft()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Save as Draft
                    </button>
                    <button type="button" onclick="window.history.back()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="nextBtn" onclick="employeeRegistrationManager.nextStep()"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Next &rarr;
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center hidden">
                        <span id="btnText">Register Employee</span>
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
                    <p id="savingOverlayText" class="text-sm font-medium text-gray-700">Saving information...</p>
                    <p class="text-xs text-gray-400 mt-1">Please do not close or refresh this page.</p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Employee Registration Multi-Step Wizard
const TOTAL_STEPS = 8;
let currentStep = 1;

class EmployeeRegistrationManager {
    constructor() {
        this.form = document.getElementById('employeeRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.allPositions = @json($positions->map(fn($p) => $p->title));

        this.init();
    }

    init() {
        this.goToStep(1);
        this.setupEventListeners();
        this.generateEmployeeNumber();
        this.setupFormValidation();
        this.setupInterviewSelection();
        this.setupDepartmentPosition();
        this.updateNavigation();
    }

    goToStep(step) {
        if (step < 1 || step > TOTAL_STEPS) return;
        currentStep = step;

        document.querySelectorAll('section[data-step]').forEach(sec => {
            sec.classList.add('hidden');
        });
        const active = document.querySelector(`section[data-step="${currentStep}"]`);
        if (active) active.classList.remove('hidden');

        // Re-initialize any signature pads in the now-visible step (they are
        // skipped while hidden, which would otherwise leave a 0-size canvas)
        active.querySelectorAll('[data-signature-pad]').forEach(container => {
            const pad = window.signaturePads?.[container.dataset.name];
            if (pad) pad.resizeCanvas();
        });

        document.querySelectorAll('[data-step-badge]').forEach(badge => {
            const n = parseInt(badge.dataset.stepBadge);
            badge.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'bg-green-500', 'text-gray-500', 'border-green-500', 'bg-white', 'border-gray-300');
            if (n < currentStep) {
                badge.classList.add('bg-green-500', 'text-white', 'border-green-500');
            } else if (n === currentStep) {
                badge.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            } else {
                badge.classList.add('bg-white', 'text-gray-500', 'border-gray-300');
            }
        });

        this.updateNavigation();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    updateNavigation() {
        this.prevBtn.disabled = currentStep === 1;
        if (currentStep === TOTAL_STEPS) {
            this.nextBtn.classList.add('hidden');
            this.submitBtn.classList.remove('hidden');
        } else {
            this.nextBtn.classList.remove('hidden');
            this.submitBtn.classList.add('hidden');
        }
    }

    nextStep() {
        if (!this.validateStep(currentStep)) {
            this.showNotification('Please complete required fields in this step before continuing', 'error');
            return;
        }
        this.goToStep(currentStep + 1);
    }

    validateStep(step) {
        let valid = true;
        const fields = this.form.querySelectorAll(`[data-required-step="${step}"]`);
        fields.forEach(field => {
            if (!this.validateField(field)) valid = false;
        });
        return valid;
    }

    generateEmployeeNumber() {
        const employeeNumberField = document.getElementById('employeeNumber');
        const prefix = 'EMP';
        const year = new Date().getFullYear();
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        employeeNumberField.value = `${prefix}${year}${random}`;
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        // Real-time validation
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    setupFormValidation() {
        // Email validation
        const emailInput = this.form.querySelector('input[name="email_address"]');
        if (emailInput) {
            emailInput.addEventListener('input', () => {
                if (emailInput.value && !this.isValidEmail(emailInput.value)) {
                    this.showFieldError('email_address', 'Please enter a valid email address');
                } else {
                    this.clearFieldError(emailInput);
                }
            });
        }

        // Login email validation
        const loginEmailInput = this.form.querySelector('input[name="login_email"]');
        if (loginEmailInput) {
            loginEmailInput.addEventListener('input', () => {
                if (loginEmailInput.value && !this.isValidEmail(loginEmailInput.value)) {
                    this.showFieldError('login_email', 'Please enter a valid email address');
                } else {
                    this.clearFieldError(loginEmailInput);
                }
            });
        }

        // Password confirmation
        const passwordInput = this.form.querySelector('input[name="password"]');
        const confirmInput = this.form.querySelector('input[name="password_confirmation"]');
        if (passwordInput && confirmInput) {
            confirmInput.addEventListener('blur', () => {
                if (confirmInput.value && passwordInput.value !== confirmInput.value) {
                    this.showFieldError('password_confirmation', 'Passwords do not match');
                } else {
                    this.clearFieldError(confirmInput);
                }
            });
        }

        // Phone validation
        const phoneInput = this.form.querySelector('input[name="phone_number"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                if (phoneInput.value && !this.isValidPhone(phoneInput.value)) {
                    this.showFieldError('phone_number', 'Please enter a valid phone number');
                } else {
                    this.clearFieldError(phoneInput);
                }
            });
        }

        // Date validation
        const dateInputs = this.form.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('change', () => {
                if (input.value && !this.isValidDate(input.value, input.name)) {
                    const errorMessage = input.name === 'date_of_birth' ?
                        'Date of birth must be before today' :
                        'Date employed must be today or before';
                    this.showFieldError(input.name, errorMessage);
                } else {
                    this.clearFieldError(input);
                }
            });
        });
    }

    setupInterviewSelection() {
        const hrInterviewSelect = this.form.querySelector('select[name="hr_interview_id"]');
        hrInterviewSelect.addEventListener('change', () => {
            const selectedOption = hrInterviewSelect.options[hrInterviewSelect.selectedIndex];

            // Split candidate name into first name + surname
            const candidateName = selectedOption.getAttribute('data-candidate-name') || '';
            if (candidateName) {
                const nameParts = candidateName.trim().split(/\s+/);
                if (nameParts.length >= 2) {
                    this.setFieldValue('first_name', nameParts[0]);
                    this.setFieldValue('surname', nameParts.slice(1).join(' '));
                } else {
                    this.setFieldValue('first_name', candidateName);
                }
            } else {
                this.setFieldValue('first_name', '');
                this.setFieldValue('surname', '');
            }

            // Fill position (prefer recommended job title, fall back to job title) if it exists as an option
            const jobTitle = selectedOption.getAttribute('data-job-title') || '';
            const recommendedTitle = selectedOption.getAttribute('data-recommended-title') || '';
            const positionSelect = this.form.querySelector('select[name="position"]');
            const preferredTitle = recommendedTitle || jobTitle;
            this.selectPosition(preferredTitle);

            // Work station from job title (or recommended title)
            this.setFieldValue('work_station', preferredTitle || jobTitle);

            // Region fields from the HR interview
            this.setFieldValue('birthplace', selectedOption.getAttribute('data-birthplace') || '');
            this.setFieldValue('residence_area', selectedOption.getAttribute('data-residence') || '');
            this.setFieldValue('permanent_residence', selectedOption.getAttribute('data-residence') || '');
            this.setFieldValue('place_of_recruitment', selectedOption.getAttribute('data-place-of-recruitment') || '');

            // Salary from current salary at interview (fill if empty)
            const salaryValue = selectedOption.getAttribute('data-current-salary') || '';
            if (salaryValue) {
                this.setFieldValue('salary', salaryValue);
            }

            // Auto-select the matching technical interview for this HR interview
            const technicalSelect = this.form.querySelector('select[name="technical_interview_id"]');
            const hrId = selectedOption.value;
            let technicalMatched = false;
            if (technicalSelect && hrId) {
                Array.from(technicalSelect.options).forEach(option => {
                    if (String(option.getAttribute('data-hr-interview-id')) === String(hrId)) {
                        technicalSelect.value = option.value;
                        technicalMatched = true;
                    }
                });
                if (!technicalMatched) {
                    technicalSelect.value = '';
                }
            }

            this.updateReviewSummary();
        });
    }

    setFieldValue(name, value) {
        const field = this.form.querySelector(`[name="${name}"]`);
        if (field) field.value = value !== null && value !== undefined ? value : '';
    }

    selectPosition(title) {
        if (!title) return;
        const positionSelect = this.form.querySelector('select[name="position"]');
        if (!positionSelect) return;
        const match = Array.from(positionSelect.options).find(option => option.value.toLowerCase() === title.toLowerCase());
        if (match) {
            positionSelect.value = match.value;
        }
    }

    setupDepartmentPosition() {
        const departmentSelect = this.form.querySelector('select[name="department"]');
        const positionSelect = this.form.querySelector('select[name="position"]');

        departmentSelect.addEventListener('change', function () {
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            const departmentId = selectedOption.getAttribute('data-department-id');

            positionSelect.innerHTML = '<option value="">Select Position</option>';
            if (!departmentId) return;

            fetch(`{{ url('/employees/positions-by-department') }}/${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    const positions = Array.isArray(data) ? data : (data.positions || []);
                    if (positions.length > 0) {
                        positions.forEach(pos => {
                            const option = document.createElement('option');
                            option.value = pos.title;
                            option.textContent = pos.title;
                            positionSelect.appendChild(option);
                        });
                    } else {
                        employeeRegistrationManager.allPositions.forEach(title => {
                            const option = document.createElement('option');
                            option.value = title;
                            option.textContent = title;
                            positionSelect.appendChild(option);
                        });
                    }
                })
                .catch(() => {
                    employeeRegistrationManager.allPositions.forEach(title => {
                        const option = document.createElement('option');
                        option.value = title;
                        option.textContent = title;
                        positionSelect.appendChild(option);
                    });
                });
        });
    }

    updateReviewSummary() {
        const summary = document.getElementById('reviewSummary');
        const hrSelect = this.form.querySelector('select[name="hr_interview_id"]');
        const selected = hrSelect.options[hrSelect.selectedIndex];
        if (!selected || !selected.value) {
            summary.innerHTML = '<div class="text-xs text-gray-500">Select an HR interview to preview candidate details.</div>';
            return;
        }
        const candidateName = selected.getAttribute('data-candidate-name') || '';
        const jobTitle = selected.getAttribute('data-job-title') || '';
        summary.innerHTML = `
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Candidate</p>
                <p class="text-sm font-medium text-gray-900">${candidateName}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Position</p>
                <p class="text-sm font-medium text-gray-900">${jobTitle}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Department</p>
                <p class="text-sm font-medium text-gray-900">${this.form.querySelector('select[name="department"]').selectedOptions[0]?.text || '-'}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">Employment Type</p>
                <p class="text-sm font-medium text-gray-900">${this.form.querySelector('select[name="employment_type"]').selectedOptions[0]?.text || '-'}</p>
            </div>
        `;
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;

        // Clear previous errors
        this.clearFieldError(field);

        if (field.type === 'checkbox') {
            if (field.required && !field.checked) {
                this.showFieldError(fieldName, 'This field is required');
                return false;
            }
            return true;
        }

        // Required field validation
        if (field.required && !value) {
            this.showFieldError(fieldName, 'This field is required');
            return false;
        }

        // Specific field validations
        switch (fieldName) {
            case 'email_address':
            case 'login_email':
                if (value && !this.isValidEmail(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid email address');
                    return false;
                }
                break;
            case 'phone_number':
                if (value && !this.isValidPhone(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid phone number');
                    return false;
                }
                break;
            case 'date_of_birth':
            case 'date_employed':
                if (value && !this.isValidDate(value, fieldName)) {
                    const errorMessage = fieldName === 'date_of_birth' ?
                        'Date of birth must be before today' :
                        'Date employed must be today or before';
                    this.showFieldError(fieldName, errorMessage);
                    return false;
                }
                break;
            case 'password':
                if (value && value.length < 8) {
                    this.showFieldError(fieldName, 'Password must be at least 8 characters');
                    return false;
                }
                break;
            case 'password_confirmation':
                const password = this.form.querySelector('input[name="password"]');
                if (value && password && value !== password.value) {
                    this.showFieldError(fieldName, 'Passwords do not match');
                    return false;
                }
                break;
        }

        return true;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^[\d\s\-\+\(\)]+$/.test(phone) && phone.length >= 10;
    }

    isValidDate(dateString, fieldName) {
        const date = new Date(dateString);
        const today = new Date();

        if (fieldName === 'date_of_birth') {
            return date < today;
        } else {
            return date <= today;
        }
    }

    showFieldError(fieldName, message) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.getElementById(`${fieldName}_error`);

        if (field) {
            field.classList.add('border-red-500');
        }

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    clearFieldError(field) {
        const fieldName = typeof field === 'string' ? field : field.name;
        const actualField = typeof field === 'string' ? this.form.querySelector(`[name="${field}"]`) : field;
        const errorElement = document.getElementById(`${fieldName}_error`);

        if (actualField) {
            actualField.classList.remove('border-red-500');
        }

        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    async saveAsDraft() {
        await this.submitForm(true);
    }

    async submitForm(isDraft = false) {
        // Validate all required fields when not saving as draft
        if (!isDraft) {
            let isValid = true;
            for (let step = 1; step <= TOTAL_STEPS; step++) {
                if (!this.validateStep(step)) {
                    isValid = false;
                    this.goToStep(step);
                    break;
                }
            }
            if (!isValid) {
                this.showNotification('Please correct the errors in the form', 'error');
                return;
            }
        }

        // Set the status based on action
        document.getElementById('registrationStatus').value = isDraft ? 'draft' : 'submitted';

        // Show loading state
        this.setLoadingState(true, isDraft);

        try {
            const formData = new FormData(this.form);
            const response = await fetch('/employee-registration', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message || 'Registration successfully saved!', 'success');
                setTimeout(() => {
                    window.location.href = '/employee-registration';
                }, 500);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Registration failed', 'error');
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            this.showNotification('An error occurred during registration', 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    displayServerErrors(errors) {
        const errorFields = Object.keys(errors);
        const firstStep = errorFields
            .map(name => {
                const field = this.form.querySelector(`[name="${name}"]`);
                return field ? parseInt(field.dataset.requiredStep || 0) : 0;
            })
            .filter(n => n > 0)
            .sort((a, b) => a - b)[0];

        errorFields.forEach(fieldName => {
            this.showFieldError(fieldName, errors[fieldName][0]);
        });

        if (firstStep) {
            this.goToStep(firstStep);
        }
    }

    setLoadingState(loading, isDraft = false) {
        const overlay = document.getElementById('savingOverlay');
        const overlayText = document.getElementById('savingOverlayText');
        const draftBtn = document.getElementById('draftBtn');

        if (loading) {
            this.btnText.textContent = isDraft ? 'Saving Draft...' : 'Registering...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
            if (draftBtn) draftBtn.disabled = true;
            if (overlayText) overlayText.textContent = isDraft ? 'Saving draft...' : 'Registering employee...';
            if (overlay) overlay.classList.remove('hidden');
        } else {
            this.btnText.textContent = 'Register Employee';
            this.btnLoader.classList.add('hidden');
            this.submitBtn.disabled = false;
            if (draftBtn) draftBtn.disabled = false;
            if (overlay) overlay.classList.add('hidden');
        }
    }

    showNotification(message, type = 'info') {
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
}

// Initialize employee registration manager
document.addEventListener('DOMContentLoaded', function() {
    window.employeeRegistrationManager = new EmployeeRegistrationManager();
    window.currentStep = 1;
});
</script>
@endpush