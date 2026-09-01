@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->full_name)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Employee</h1>
            <p class="text-gray-600 mt-2">Update information for {{ $employee->full_name }}</p>
        </div>
        <div>
            <a href="{{ route('employees.show', $employee->id) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </div>

    <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="client_id" value="{{ $currentClient->id }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Personal Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('first_name') border-red-500 @enderror">
                            @error('first_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('last_name') border-red-500 @enderror">
                            @error('last_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Employee must be at least 18 years old</p>
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select name="gender" id="gender" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('gender') border-red-500 @enderror">
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Employment Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" readonly
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
                            <p class="mt-1 text-xs text-gray-500">Employee ID cannot be modified</p>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" id="role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror">
                                <option value="" disabled selected>Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $employee->role) == $role->name ? 'selected' : '' }}>{{ $role->display_name ?? $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                            <select name="department" id="department" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('department') border-red-500 @enderror">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->code }}" data-department-id="{{ $dept->id }}" {{ old('department', $employee->department) == $dept->code ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position *</label>
                            <select name="position" id="position" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('position') border-red-500 @enderror">
                                <option value="" disabled>Select Position</option>
                                @php
                                    $selectedPosition = old('position', $employee->position);
                                    $positionExists = $positions->contains(fn($p) => $p->title === $selectedPosition);
                                @endphp
                                @if(!$positionExists && $selectedPosition)
                                    <option value="{{ $selectedPosition }}" selected>{{ $selectedPosition }}</option>
                                @endif
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->title }}" {{ $selectedPosition == $pos->title ? 'selected' : '' }}>{{ $pos->title }}</option>
                                @endforeach
                            </select>
                            @error('position') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">Employment Type *</label>
                            <select name="employment_type" id="employment_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('employment_type') border-red-500 @enderror">
                                @foreach($employmentTypes as $code => $name)
                                    <option value="{{ $code }}" {{ old('employment_type', $employee->employment_type) == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('employment_type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Hire Date *</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('hire_date') border-red-500 @enderror">
                            @error('hire_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" id="status" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="on_leave" {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1">Reporting To</label>
                            <select name="manager_id" id="manager_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('manager_id') border-red-500 @enderror">
                                <option value="" disabled selected>Select Manager</option>
                                @foreach(\App\Models\Employee::forCurrentClient()->where('id', '!=', $employee->id)->get() as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id', $employee->manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->first_name }} {{ $manager->last_name }} - {{ $manager->position }}</option>
                                @endforeach
                            </select>
                            @error('manager_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="work_schedule" class="block text-sm font-medium text-gray-700 mb-1">Work Schedule</label>
                            <input type="text" name="work_schedule" id="work_schedule" value="{{ old('work_schedule', $employee->work_schedule) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('work_schedule') border-red-500 @enderror">
                            @error('work_schedule') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
                            <input type="text" name="education_level" id="education_level" value="{{ old('education_level', $employee->education_level) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('education_level') border-red-500 @enderror">
                            @error('education_level') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Skills & Qualifications</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
                            <input type="text" name="skills" id="skills" value="{{ old('skills', $employee->skills ? implode(', ', $employee->skills) : '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('skills') border-red-500 @enderror"
                                placeholder="Enter skills separated by commas">
                            @error('skills') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="languages" class="block text-sm font-medium text-gray-700 mb-1">Languages</label>
                            <input type="text" name="languages" id="languages" value="{{ old('languages', $employee->languages ? implode(', ', $employee->languages) : '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('languages') border-red-500 @enderror"
                                placeholder="Enter languages separated by commas">
                            @error('languages') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="professional_qualifications" class="block text-sm font-medium text-gray-700 mb-1">Professional Qualifications</label>
                            <input type="text" name="professional_qualifications" id="professional_qualifications" value="{{ old('professional_qualifications', $employee->professional_qualifications ? implode(', ', $employee->professional_qualifications) : '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('professional_qualifications') border-red-500 @enderror"
                                placeholder="Enter qualifications separated by commas">
                            @error('professional_qualifications') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="certifications" class="block text-sm font-medium text-gray-700 mb-1">Certifications</label>
                            <input type="text" name="certifications" id="certifications" value="{{ old('certifications', $employee->certifications ? implode(', ', $employee->certifications) : '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('certifications') border-red-500 @enderror"
                                placeholder="Enter certifications separated by commas">
                            @error('certifications') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Financial Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">Base Salary *</label>
                            <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('salary') border-red-500 @enderror">
                            @error('salary') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                            <select name="currency" id="currency" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-500 @enderror">
                                <option value="TZS" {{ old('currency', $employee->currency) == 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                                <option value="USD" {{ old('currency', $employee->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency', $employee->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ old('currency', $employee->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            </select>
                            @error('currency') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="payment_frequency" class="block text-sm font-medium text-gray-700 mb-1">Payment Frequency *</label>
                            <select name="payment_frequency" id="payment_frequency" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('payment_frequency') border-red-500 @enderror">
                                <option value="monthly" {{ old('payment_frequency', $employee->payment_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="bi-weekly" {{ old('payment_frequency', $employee->payment_frequency) == 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                                <option value="weekly" {{ old('payment_frequency', $employee->payment_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                            @error('payment_frequency') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Profile Photo & Additional Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Photo</h2>
                    <div class="text-center">
                        <div class="w-32 h-32 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 overflow-hidden border-2 border-indigo-500">
                            <img id="profile_photo_preview" src="{{ $employee->profile_photo ? Storage::url($employee->profile_photo) : '' }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover {{ $employee->profile_photo ? '' : 'hidden' }}">
                            @if(!$employee->profile_photo)
                                <span id="profile_photo_placeholder" class="text-4xl font-bold text-indigo-600">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                            @endif
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*">
                        <label for="profile_photo" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors">
                            Change Photo
                        </label>
                        <p class="mt-2 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Legal IDs</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="national_id" class="block text-sm font-medium text-gray-700 mb-1">National ID *</label>
                            <input type="text" name="national_id" id="national_id" value="{{ old('national_id', $employee->national_id) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('national_id') border-red-500 @enderror">
                            @error('national_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="passport_number" class="block text-sm font-medium text-gray-700 mb-1">Passport Number</label>
                            <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $employee->passport_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('passport_number') border-red-500 @enderror">
                            @error('passport_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="tin_number" class="block text-sm font-medium text-gray-700 mb-1">TIN Number</label>
                            <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number', $employee->tin_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tin_number') border-red-500 @enderror">
                            @error('tin_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nssf_number" class="block text-sm font-medium text-gray-700 mb-1">NSSF Number</label>
                            <input type="text" name="nssf_number" id="nssf_number" value="{{ old('nssf_number', $employee->nssf_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nssf_number') border-red-500 @enderror">
                            @error('nssf_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="nhif_number" class="block text-sm font-medium text-gray-700 mb-1">NHIF Number</label>
                            <input type="text" name="nhif_number" id="nhif_number" value="{{ old('nhif_number', $employee->nhif_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nhif_number') border-red-500 @enderror">
                            @error('nhif_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Address Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $employee->address) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('address') border-red-500 @enderror">
                            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $employee->city) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-500 @enderror">
                            @error('city') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                            <input type="text" name="region" id="region" value="{{ old('region', $employee->region) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('region') border-red-500 @enderror">
                            @error('region') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $employee->postal_code) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('postal_code') border-red-500 @enderror">
                            @error('postal_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <input type="text" name="country" id="country" value="{{ old('country', $employee->country) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('country') border-red-500 @enderror">
                            @error('country') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Emergency Contact</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('emergency_contact_name') border-red-500 @enderror">
                            @error('emergency_contact_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('emergency_contact_phone') border-red-500 @enderror">
                            @error('emergency_contact_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Relationship</label>
                            <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('emergency_contact_relationship') border-red-500 @enderror">
                            @error('emergency_contact_relationship') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Bank Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $employee->bank_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('bank_name') border-red-500 @enderror">
                            @error('bank_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="bank_branch" class="block text-sm font-medium text-gray-700 mb-1">Bank Branch</label>
                            <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $employee->bank_branch) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('bank_branch') border-red-500 @enderror">
                            @error('bank_branch') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-1">Bank Account</label>
                            <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $employee->bank_account) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('bank_account') border-red-500 @enderror">
                            @error('bank_account') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile photo preview
    const profilePhotoInput = document.getElementById('profile_photo');
    const profilePhotoPreview = document.getElementById('profile_photo_preview');
    const profilePhotoPlaceholder = document.getElementById('profile_photo_placeholder');

    profilePhotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePhotoPreview.src = e.target.result;
                profilePhotoPreview.classList.remove('hidden');
                if (profilePhotoPlaceholder) {
                    profilePhotoPlaceholder.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        } else {
            // If no file selected, revert to original or placeholder
            @if($employee->profile_photo)
                profilePhotoPreview.src = "{{ Storage::url($employee->profile_photo) }}";
                profilePhotoPreview.classList.remove('hidden');
            @else
                profilePhotoPreview.classList.add('hidden');
                if (profilePhotoPlaceholder) {
                    profilePhotoPlaceholder.classList.remove('hidden');
                }
            @endif
        }
    });

    // Dependent position dropdown
    const departmentSelect = document.getElementById('department');
    const positionSelect = document.getElementById('position');
    const oldDepartment = "{{ old('department', $employee->department) }}";
    const oldPosition = "{{ old('position', $employee->position) }}";

    // Store the current client's positions as a fallback source (rendered by Blade)
    const allPositions = @json($positions->map(fn($p) => $p->title));

    async function loadPositions(departmentName) {
        if (!departmentName) {
            positionSelect.innerHTML = '<option value="" disabled selected>Select Position</option>';
            return;
        }
        // Get department id from selected option
        const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
        const departmentId = selectedOption.getAttribute('data-department-id');
        if (!departmentId) {
            positionSelect.innerHTML = '<option value="" disabled selected>Select Position</option>';
            return;
        }

        try {
            const response = await fetch(`{{ url('/employees/positions-by-department') }}/${departmentId}`);
            const data = await response.json();
            const positions = Array.isArray(data) ? data : (data.positions || []);
            positionSelect.innerHTML = '<option value="" disabled>Select Position</option>';
            if (positions.length > 0) {
                positions.forEach(pos => {
                    const option = document.createElement('option');
                    option.value = pos.title;
                    option.textContent = pos.title;
                    if (pos.title === oldPosition) {
                        option.selected = true;
                    }
                    positionSelect.appendChild(option);
                });
            } else {
                // No positions for this department - fall back to all current client positions
                positionSelect.innerHTML = '<option value="" disabled>Select Position</option>';
                allPositions.forEach(title => {
                    const option = document.createElement('option');
                    option.value = title;
                    option.textContent = title;
                    if (title === oldPosition) {
                        option.selected = true;
                    }
                    positionSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading positions:', error);
            // On failure, keep the pre-rendered options
            positionSelect.innerHTML = '<option value="" disabled>Select Position</option>';
            allPositions.forEach(title => {
                const option = document.createElement('option');
                option.value = title;
                option.textContent = title;
                if (title === oldPosition) {
                    option.selected = true;
                }
                positionSelect.appendChild(option);
            });
        }
    }

    departmentSelect.addEventListener('change', function() {
        loadPositions(this.value);
    });
});
</script>
@endsection
