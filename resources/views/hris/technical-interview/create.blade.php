@extends('layouts.app')

@section('title', 'Technical Interview Assessment - Orvion HRIS')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-manrope">Technical Interview Assessment</h1>
        <p class="text-gray-600 mt-2">Conduct technical assessment for candidate evaluation</p>
    </div>

    <!-- Technical Interview Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="technicalInterviewForm" class="p-6 space-y-8">
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Number
                        </label>
                        <input type="text" id="interviewNumber" readonly
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
                            Candidate Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="candidate_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="candidate_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="job_title_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="interview_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="interview_date_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interviewer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="interviewer_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="interviewer_name_error"></span>
                    </div>
                </div>
            </div>

            <!-- Technical Assessment Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Technical Assessment Areas</h2>
                
                <!-- Business Process Knowledge -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Business Process Knowledge</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Assessment Details <span class="text-red-500">*</span>
                        </label>
                        <textarea name="business_process_knowledge" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Describe candidate's understanding of business processes, workflows, and operational procedures..."></textarea>
                        <span class="text-red-500 text-sm hidden" id="business_process_knowledge_error"></span>
                    </div>
                </div>

                <!-- Technical Skills Assessment -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Technical Skills Assessment</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Technical Skills Evaluation <span class="text-red-500">*</span>
                        </label>
                        <textarea name="technical_skills_assessment" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Evaluate candidate's technical competencies, software proficiency, tools knowledge, and technical problem-solving abilities..."></textarea>
                        <span class="text-red-500 text-sm hidden" id="technical_skills_assessment_error"></span>
                    </div>
                </div>

                <!-- Physical Capabilities -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Physical Capabilities</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Physical Requirements Assessment
                        </label>
                        <textarea name="physical_capabilities" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Assess physical fitness, ability to perform job-related physical tasks, mobility requirements, etc. (if applicable)..."></textarea>
                    </div>
                </div>

                <!-- Practical Test Results -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Practical Test Results</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Practical Test Performance
                        </label>
                        <textarea name="practical_test_results" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Describe results of any practical tests, hands-on exercises, or simulations performed during the interview..."></textarea>
                    </div>
                </div>

                <!-- Other Technical Areas -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Other Technical Areas</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Technical Assessment
                        </label>
                        <textarea name="other_technical_areas" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Any other technical competencies, certifications, or specialized skills relevant to the position..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Assessment Results Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Assessment Results</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Technical Result <span class="text-red-500">*</span>
                        </label>
                        <select name="technical_result" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Result</option>
                            <option value="pass">Pass</option>
                            <option value="fail">Fail</option>
                            <option value="na">N/A</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="technical_result_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Technical Comments
                        </label>
                        <textarea name="technical_comments" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Additional comments about the technical assessment..."></textarea>
                    </div>
                </div>
            </div>

            <!-- File Uploads Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Supporting Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Assessment Report
                        </label>
                        <input type="file" name="assessment_report" accept=".pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Upload detailed assessment report (PDF, DOC, DOCX)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Signed Document
                        </label>
                        <input type="file" name="signed_file" accept=".pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Upload signed assessment document (PDF, DOC, DOCX)</p>
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
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <span id="btnText">Submit Assessment</span>
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
// Technical Interview Management System
class TechnicalInterviewManager {
    constructor() {
        this.form = document.getElementById('technicalInterviewForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.generateInterviewNumber();
        this.setupFormValidation();
        this.setupHrInterviewSelection();
    }

    generateInterviewNumber() {
        const interviewNumberField = document.getElementById('interviewNumber');
        const prefix = 'TECHINT';
        const year = new Date().getFullYear();
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        interviewNumberField.value = `${prefix}${year}${random}`;
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
        // Textarea validation for minimum length
        const textareas = this.form.querySelectorAll('textarea[required]');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', () => {
                if (textarea.value && textarea.value.length < 50) {
                    this.showFieldError(textarea.name, 'Please provide at least 50 characters for this assessment');
                } else {
                    this.clearFieldError(textarea);
                }
            });
        });
    }

    setupHrInterviewSelection() {
        const hrInterviewSelect = document.querySelector('select[name="hr_interview_id"]');
        hrInterviewSelect.addEventListener('change', () => {
            const selectedOption = hrInterviewSelect.options[hrInterviewSelect.selectedIndex];
            const optionText = selectedOption.text;
            
            // Extract candidate name and job title from the option text
            const match = optionText.match(/(.+?) - (.+?) \(/);
            if (match) {
                document.querySelector('input[name="candidate_name"]').value = match[1].trim();
                document.querySelector('input[name="job_title"]').value = match[2].trim();
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
        if (field.tagName === 'TEXTAREA' && field.required) {
            if (value.length < 50) {
                this.showFieldError(fieldName, 'Please provide at least 50 characters for this assessment');
                return false;
            }
        }

        return true;
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

        if (!isValid) {
            this.showNotification('Please correct the errors in the form', 'error');
            return;
        }

        // Show loading state
        this.setLoadingState(true);

        try {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch('/technical-interview', {
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
                this.showNotification('Technical interview assessed successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/technical-interview';
                }, 2000);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Assessment failed', 'error');
                }
            }
        } catch (error) {
            console.error('Assessment error:', error);
            this.showNotification('An error occurred during assessment', 'error');
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
            this.btnText.textContent = 'Assessing...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
        } else {
            this.btnText.textContent = 'Submit Assessment';
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
    const form = document.getElementById('technicalInterviewForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Store draft in localStorage
    localStorage.setItem('technicalInterviewDraft', JSON.stringify(data));
    
    window.technicalInterviewManager.showNotification('Draft saved successfully', 'success');
}

// Initialize technical interview manager
document.addEventListener('DOMContentLoaded', function() {
    window.technicalInterviewManager = new TechnicalInterviewManager();
});
</script>
@endpush
