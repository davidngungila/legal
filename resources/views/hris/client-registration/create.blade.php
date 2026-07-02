@extends('layouts.app')

@section('title', 'Client Registration - Orvion HRIS')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-manrope">Client Registration</h1>
        <p class="text-gray-600 mt-2 text-sm md:text-base">Register a new client/employer in the Orvion HRIS system</p>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="clientRegistrationForm" class="p-4 md:p-6 lg:p-8 space-y-6 md:space-y-8">
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-6 md:pb-8">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="employer_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="employer_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employer Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="employer_number" id="employerNumber" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                               placeholder="Will be generated automatically">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contact Person <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contact_person" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="contact_person_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contact Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="contact_phone" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="contact_phone_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contact Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="contact_email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="contact_email_error"></span>
                    </div>
                </div>
            </div>

            <!-- Registration Numbers Section -->
            <div class="border-b border-gray-200 pb-6 md:pb-8">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Registration Numbers & Certificates</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                TIN Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tin_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="tin_number_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                TIN Certificate
                            </label>
                            <input type="file" name="tin_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                OSHA Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="osha_registration" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="osha_registration_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                OSHA Certificate
                            </label>
                            <input type="file" name="osha_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NHIF Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nhif_registration" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="nhif_registration_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NHIF Certificate
                            </label>
                            <input type="file" name="nhif_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                WCF Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="wcf_registration" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="wcf_registration_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                WCF Certificate
                            </label>
                            <input type="file" name="wcf_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-4 md:mt-6">
                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                VAT Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="vat_registration_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="vat_registration_number_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                VAT Certificate
                            </label>
                            <input type="file" name="vat_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div class="space-y-3 md:space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NSSF Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nssf_registration" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-red-500 text-sm hidden" id="nssf_registration_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NSSF Certificate
                            </label>
                            <input type="file" name="nssf_certificate" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="border-b border-gray-200 pb-6 md:pb-8">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Contact Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="phone_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mobile <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="mobile" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="mobile_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="email_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fax
                        </label>
                        <input type="tel" name="fax"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Address
                        </label>
                        <input type="text" name="postal_address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Location Information Section -->
            <div>
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6">Location Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Region <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="region" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="region_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            District <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="district" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="district_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Location <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="location" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="location_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Road
                        </label>
                        <input type="text" name="road"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Street
                        </label>
                        <input type="text" name="street"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Plot
                        </label>
                        <input type="text" name="plot"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Block
                        </label>
                        <input type="text" name="block"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4 md:pt-6 border-t border-gray-200">
                <button type="button" onclick="window.history.back()"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center">
                    <span id="btnText">Register Client</span>
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
// Client Registration Management System
class ClientRegistrationManager {
    constructor() {
        this.form = document.getElementById('clientRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.generateEmployerNumber();
        this.setupFormValidation();
    }

    generateEmployerNumber() {
        const employerNumberField = document.getElementById('employerNumber');
        const prefix = 'EMP';
        const year = new Date().getFullYear();
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        employerNumberField.value = `${prefix}${year}${random}`;
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        // Real-time validation
        const inputs = this.form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    setupFormValidation() {
        // Email validation
        const emailInputs = this.form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.value && !this.isValidEmail(input.value)) {
                    this.showFieldError(input.name, 'Please enter a valid email address');
                } else {
                    this.clearFieldError(input);
                }
            });
        });

        // Phone validation
        const phoneInputs = this.form.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.value && !this.isValidPhone(input.value)) {
                    this.showFieldError(input.name, 'Please enter a valid phone number');
                } else {
                    this.clearFieldError(input);
                }
            });
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
            case 'contact_email':
            case 'email':
                if (!this.isValidEmail(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid email address');
                    return false;
                }
                break;
            case 'contact_phone':
            case 'phone':
            case 'mobile':
                if (!this.isValidPhone(value)) {
                    this.showFieldError(fieldName, 'Please enter a valid phone number');
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
        // Validate all required fields
        const inputs = this.form.querySelectorAll('input[required]');
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

            console.log('Submitting registration with FormData');

            const response = await fetch('/client-registration', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                    // Don't set Content-Type when sending FormData - browser will set it with boundary
                },
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);

            const result = await response.json();
            console.log('Response data:', result);
            console.log('Validation errors:', result.errors);

            if (result.success) {
                this.showNotification(`Client registered successfully! Employer Number: ${result.employer_number}`, 'success');
                setTimeout(() => {
                    window.location.href = '/client-registration';
                }, 2000);
            } else {
                if (result.errors) {
                    console.log('Displaying server errors:', result.errors);
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Registration failed', 'error');
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            console.error('Error details:', error.message);
            this.showNotification('An error occurred during registration: ' + error.message, 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    displayServerErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            const errorMessage = Array.isArray(errors[fieldName]) ? errors[fieldName][0] : errors[fieldName];
            this.showFieldError(fieldName, errorMessage);
            console.log(`${fieldName}: ${errorMessage}`);
        });
    }

    setLoadingState(loading) {
        if (loading) {
            this.btnText.textContent = 'Registering...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
        } else {
            this.btnText.textContent = 'Register Client';
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

// Initialize client registration manager
document.addEventListener('DOMContentLoaded', function() {
    window.clientRegistrationManager = new ClientRegistrationManager();
});
</script>
@endpush
