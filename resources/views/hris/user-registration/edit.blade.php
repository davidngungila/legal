@extends('layouts.app')

@section('title', 'Edit User Registration - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit User Registration</h1>
            <p class="text-gray-600 mt-2">Update user account information</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('user-registration.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="userRegistrationForm" action="{{ route('user-registration.update', $user->id) }}" method="POST" class="p-6 space-y-6" novalidate>
            @csrf
            @method('PUT')
            
            <!-- Personal Information Section -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('first_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('middle_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="surname" value="{{ old('surname', $user->surname) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('surname')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('phone_number')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" id="dateOfBirth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('date_of_birth')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Must be between 120 years ago and today</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Project Location <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="project_location" value="{{ old('project_location', $user->project_location) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('project_location')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Professional Information Section -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="department_name" value="{{ old('department_name', $user->department_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('department_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Section Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="section_name" value="{{ old('section_name', $user->section_name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('section_name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Designation/Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('designation')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" id="roleSelect" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->display_name ?? $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    @if($isSuperAdmin)
                    <div id="companyContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Company <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id" id="clientSelect" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Company</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $user->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Security Section -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Security</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" name="password" id="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Leave blank to keep current password">
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters (leave blank to keep current)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Leave blank to keep current password">
                        @error('password_confirmation')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="isActive" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                    @error('is_active')
                        <span class="text-red-500 text-sm ml-2">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('user-registration.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                    <span id="btnText">Update User</span>
                    <div id="btnLoader" class="hidden ml-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// User Registration Edit Management System
class UserRegistrationEditManager {
    constructor() {
        this.form = document.getElementById('userRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        this.roleSelect = document.getElementById('roleSelect');
        this.companyContainer = document.getElementById('companyContainer');
        
        this.init();
    }

    init() {
        this.setupDateConstraints();
        this.setupEventListeners();
        this.setupFormValidation();
        this.handleRoleChange(this.roleSelect.value);
    }

    setupDateConstraints() {
        const dateOfBirthInput = document.getElementById('dateOfBirth');
        if (!dateOfBirthInput) return;
        
        // Function to format date as YYYY-MM-DD in local time
        const formatDateLocal = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        // Set max date to today (local time)
        const today = new Date();
        const todayFormatted = formatDateLocal(today);
        dateOfBirthInput.max = todayFormatted;
        
        // Set min date to 120 years ago (local time)
        const minDate = new Date();
        minDate.setFullYear(minDate.getFullYear() - 120);
        const minDateFormatted = formatDateLocal(minDate);
        dateOfBirthInput.min = minDateFormatted;
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        // Real-time validation
        const inputs = this.form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
        
        // Hide company when role is admin
        if (this.roleSelect) {
            this.roleSelect.addEventListener('change', (e) => {
                this.handleRoleChange(e.target.value);
            });
        }
    }
    
    handleRoleChange(roleId) {
        // Find selected role name
        const selectedOption = this.roleSelect.options[this.roleSelect.selectedIndex];
        const roleName = selectedOption.textContent.toLowerCase();
        
        if (this.companyContainer) {
            // If selected role has admin, hide company container
            if (roleName.includes('admin')) {
                this.companyContainer.style.display = 'none';
                const clientSelect = document.getElementById('clientSelect');
                if (clientSelect) {
                    clientSelect.required = false;
                }
            } else {
                this.companyContainer.style.display = 'block';
                const clientSelect = document.getElementById('clientSelect');
                if (clientSelect) {
                    clientSelect.required = true;
                }
            }
        }
    }

    setupFormValidation() {
        // Password confirmation validation
        const password = document.getElementById('password');
        const passwordConfirm = document.querySelector('input[name="password_confirmation"]');
        
        passwordConfirm.addEventListener('input', () => {
            if (passwordConfirm.value && password.value !== passwordConfirm.value) {
                this.showFieldError('password_confirmation', 'Passwords do not match');
            } else {
                this.clearFieldError(passwordConfirm);
            }
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name;
        
        // Clear previous errors
        this.clearFieldError(field);

        // Skip password validation if empty (optional for edit)
        if (fieldName === 'password' || fieldName === 'password_confirmation') {
            if (!value) return true; // Password is optional for edit
        }

        // Required field validation
        if (field.required && !value) {
            this.showFieldError(fieldName, 'This field is required');
            return false;
        }

        // Specific field validations
        switch (fieldName) {
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid email address');
                    return false;
                }
                break;
            case 'phone_number':
                if (!this.isValidPhone(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid phone number');
                    return false;
                }
                break;
            case 'date_of_birth':
                const dobValidation = this.isValidDateOfBirth(value);
                if (dobValidation !== true) {
                    this.showFieldError(fieldName, dobValidation);
                    return false;
                }
                break;
            case 'password':
                if (value.length < 8) {
                    this.showFieldError(fieldName, 'Password must be at least 8 characters');
                    return false;
                }
                break;
            case 'password_confirmation':
                const password = document.getElementById('password').value;
                if (value !== password) {
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

    isValidDateOfBirth(dateString) {
        if (!dateString) return 'Date of birth is required';
        
        // Parse date as local date
        const [year, month, day] = dateString.split('-').map(Number);
        const date = new Date(year, month - 1, day);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Check if date is in future
        if (date > today) {
            return 'Date of birth cannot be in the future';
        }
        
        // Check if date is older than 120 years
        const minDate = new Date();
        minDate.setFullYear(minDate.getFullYear() - 120);
        minDate.setHours(0, 0, 0, 0);
        
        if (date < minDate) {
            return 'Date of birth cannot be more than 120 years ago';
        }
        
        return true;
    }

    showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.querySelector(`[name="${fieldName}"]`).nextElementSibling;
        
        if (field) {
            field.classList.add('border-red-500');
        }
        
        if (errorElement && errorElement.classList.contains('text-red-500')) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    clearFieldError(field) {
        const fieldName = field.name;
        const errorElement = field.nextElementSibling;
        
        field.classList.remove('border-red-500');
        
        if (errorElement && errorElement.classList.contains('text-red-500')) {
            errorElement.classList.add('hidden');
        }
    }

    async submitForm() {
        // Validate all fields
        const inputs = this.form.querySelectorAll('input, select');
        let isValid = true;
        
        inputs.forEach(input => {
            // Skip company select if hidden
            if (input.name === 'client_id' && 
                this.companyContainer && 
                this.companyContainer.style.display === 'none') {
                return;
            }
            
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            this.showNotification('Please correct the errors in the form', 'error');
            return;
        }

        // Show loading state
        this.setLoadingState(true);

        try {
            const formData = new FormData(this.form);
            
            // Remove client_id if company container is hidden
            if (this.companyContainer && 
                this.companyContainer.style.display === 'none') {
                formData.delete('client_id');
            }

            const response = await fetch(this.form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('User updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/user-registration';
                }, 2000);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Update failed', 'error');
                }
            }
        } catch (error) {
            console.error('Update error:', error);
            this.showNotification('An error occurred during update', 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    displayServerErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            this.showFieldError(fieldName, errors[fieldName][0]);
        });
    }

    setLoadingState(loading) {
        if (loading) {
            this.btnText.textContent = 'Updating...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
        } else {
            this.btnText.textContent = 'Update User';
            this.btnLoader.classList.add('hidden');
            this.submitBtn.disabled = false;
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

// Initialize user registration edit manager
document.addEventListener('DOMContentLoaded', function() {
    window.userRegistrationEditManager = new UserRegistrationEditManager();
});
</script>
@endpush
