@extends('layouts.app')

@section('title', 'Employee Registration - Orvion HRIS')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Registration</h1>
        <p class="text-gray-600 mt-2">Register passed candidates as employees in the system</p>
    </div>

    <!-- Employee Registration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="employeeRegistrationForm" class="p-6 space-y-8">
            <!-- Interview Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Interview Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                        <select name="hr_interview_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select HR Interview</option>
                            @foreach($hrInterviews as $hrInterview)
                                <option value="{{ $hrInterview->id }}">
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
                                <option value="{{ $technicalInterview->id }}">
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
                        <input type="text" name="surname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="surname_error"></span>
                    </div>
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
                            Birthplace <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="birthplace" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="birthplace_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Residence Area <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="residence_area" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="residence_area_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Permanent Residence <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="permanent_residence" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="permanent_residence_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email_address" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="email_address_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone_number" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="phone_number_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Address
                        </label>
                        <input type="text" name="postal_address"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                        <input type="text" name="place_of_recruitment" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="place_of_recruitment_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Work Station <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="work_station" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="work_station_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type of Contract <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="type_of_contract" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="type_of_contract_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date Employed <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_employed" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="date_employed_error"></span>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Job Descriptions <span class="text-red-500">*</span>
                    </label>
                    <textarea name="job_descriptions" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Describe the job responsibilities, duties, and requirements..."></textarea>
                    <span class="text-red-500 text-sm hidden" id="job_descriptions_error"></span>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Terms and Conditions <span class="text-red-500">*</span>
                    </label>
                    <textarea name="terms_conditions" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Specify employment terms, conditions, and any special arrangements..."></textarea>
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
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Any ranking or performance evaluation details..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employment History
                        </label>
                        <textarea name="employment_history" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Previous employment history if applicable..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Consent and Signatures Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Consent and Signatures</h2>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="information_consent" id="information_consent" required
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="information_consent" class="ml-2 block text-sm text-gray-900">
                            I consent to the provision of my information for employment registration purposes <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <span class="text-red-500 text-sm hidden" id="information_consent_error"></span>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Employee Signature
                            </label>
                            <input type="text" name="employee_signature"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Type signature or upload signed document">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Signature Date
                            </label>
                            <input type="date" name="signature_date"
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
                        <input type="file" name="signed_document" accept=".pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Upload signed registration document (PDF, DOC, DOCX)</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between">
                <button type="button" onclick="saveAsDraft()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Save as Draft
                </button>
                <div class="flex space-x-3">
                    <button type="button" onclick="window.history.back()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="generateAndDownloadPdf()"
                            class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors">
                        Generate PDF
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
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
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Employee Registration Management System
class EmployeeRegistrationManager {
    constructor() {
        this.form = document.getElementById('employeeRegistrationForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.generateEmployeeNumber();
        this.setupFormValidation();
        this.setupInterviewSelection();
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
        const emailInput = document.querySelector('input[name="email_address"]');
        emailInput.addEventListener('input', () => {
            if (emailInput.value && !this.isValidEmail(emailInput.value)) {
                this.showFieldError('email_address', 'Please enter a valid email address');
            } else {
                this.clearFieldError(emailInput);
            }
        });

        // Phone validation
        const phoneInput = document.querySelector('input[name="phone_number"]');
        phoneInput.addEventListener('input', () => {
            if (phoneInput.value && !this.isValidPhone(phoneInput.value)) {
                this.showFieldError('phone_number', 'Please enter a valid phone number');
            } else {
                this.clearFieldError(phoneInput);
            }
        });

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
        const hrInterviewSelect = document.querySelector('select[name="hr_interview_id"]');
        hrInterviewSelect.addEventListener('change', () => {
            const selectedOption = hrInterviewSelect.options[hrInterviewSelect.selectedIndex];
            const optionText = selectedOption.text;
            
            // Extract candidate name and job title from the option text
            const match = optionText.match(/(.+?) - (.+?) \(/);
            if (match) {
                // Auto-fill some fields from HR interview data
                // In a real implementation, you would fetch the HR interview data via AJAX
                // For now, just fill the basic fields
                const candidateName = match[1].trim();
                const jobTitle = match[2].trim();
                
                // You could split the candidate name if it's in "First Last" format
                const nameParts = candidateName.split(' ');
                if (nameParts.length >= 2) {
                    document.querySelector('input[name="first_name"]').value = nameParts[0];
                    document.querySelector('input[name="surname"]').value = nameParts.slice(1).join(' ');
                } else {
                    document.querySelector('input[name="first_name"]').value = candidateName;
                }
                
                document.querySelector('input[name="work_station"]').value = jobTitle;
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
            case 'email_address':
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
            case 'date_employed':
                if (!this.isValidDate(value, fieldName)) {
                    const errorMessage = fieldName === 'date_of_birth' ? 
                        'Date of birth must be before today' : 
                        'Date employed must be today or before';
                    this.showFieldError(fieldName, errorMessage);
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
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        // Check consent checkbox
        const consentCheckbox = document.getElementById('information_consent');
        if (!consentCheckbox.checked) {
            this.showFieldError('information_consent', 'You must consent to the information provision');
            isValid = false;
        }

        if (!isValid) {
            this.showNotification('Please correct the errors in the form', 'error');
            return;
        }

        // Show loading state
        this.setLoadingState(true);

        try {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());
            
            // Handle checkbox
            data.information_consent = consentCheckbox.checked;

            const response = await fetch('/employee-registration', {
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
                this.showNotification('Employee registered successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/employee-registration';
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
            this.btnText.textContent = 'Register Employee';
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

// Save as draft function
function saveAsDraft() {
    const form = document.getElementById('employeeRegistrationForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Store draft in localStorage
    localStorage.setItem('employeeRegistrationDraft', JSON.stringify(data));
    
    window.employeeRegistrationManager.showNotification('Draft saved successfully', 'success');
}

// Generate and download PDF function
function generateAndDownloadPdf() {
    // This would generate a PDF and trigger download
    // For now, just show a notification
    window.employeeRegistrationManager.showNotification('PDF generation feature coming soon', 'info');
}

// Initialize employee registration manager
document.addEventListener('DOMContentLoaded', function() {
    window.employeeRegistrationManager = new EmployeeRegistrationManager();
});
</script>
@endpush
