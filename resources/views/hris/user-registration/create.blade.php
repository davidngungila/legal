@extends('layouts.app')

@section('title', 'User Registration - Orvion HRIS')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-manrope">User Registration</h1>
        <p class="text-gray-600 mt-2">Create a new user account for the Orvion HRIS system</p>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="userRegistrationForm" class="p-6 space-y-6">
            <!-- Personal Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="first_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Middle Name
                        </label>
                        <input type="text" name="middle_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Surname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="surname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="surname_error"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="email_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone_number" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="phone_number_error"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" required
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
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="gender_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Project Location <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="project_location" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="project_location_error"></span>
                    </div>
                </div>
            </div>

            <!-- Professional Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Professional Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="department_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="department_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Section Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="section_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="section_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Designation/Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="designation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="designation_error"></span>
                    </div>
                </div>
            </div>

            <!-- Account Security Section -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Account Security</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="password_error"></span>
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="password_confirmation_error"></span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <span id="btnText">Register User</span>
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
// User Registration Management System
class UserRegistrationManager {
    constructor() {
        this.form = document.getElementById('userRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFormValidation();
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
                if (!this.isValidDateOfBirth(value)) {
                    this.showFieldError(fieldName, 'Date of birth must be before today');
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
        const date = new Date(dateString);
        const today = new Date();
        return date < today;
    }

    showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
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
        const fieldName = field.name;
        const errorElement = document.getElementById(`${fieldName}_error`);
        
        field.classList.remove('border-red-500');
        
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    async submitForm() {
        // Validate all fields
        const inputs = this.form.querySelectorAll('input, select');
        let isValid = true;
        
        inputs.forEach(input => {
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
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/user-registration', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('User registered successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/user-registration';
                }, 2000);
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
        Object.keys(errors).forEach(fieldName => {
            this.showFieldError(fieldName, errors[fieldName][0]);
        });
    }

    setLoadingState(loading) {
        if (loading) {
            this.btnText.textContent = 'Registering...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
        } else {
            this.btnText.textContent = 'Register User';
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

// Initialize user registration manager
document.addEventListener('DOMContentLoaded', function() {
    window.userRegistrationManager = new UserRegistrationManager();
});
</script>
@endpush
