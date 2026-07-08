@extends('layouts.app')

@section('title', 'Edit Technical Interview - ' . $technicalInterview->interview_number)

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Technical Interview Assessment</h1>
        <p class="text-gray-600 mt-2">Update technical assessment for {{ $technicalInterview->candidate_name }}</p>
    </div>

    <!-- Technical Interview Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="technicalInterviewForm" action="{{ route('technical-interview.update', $technicalInterview) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="interviewStatus" value="{{ $technicalInterview->status }}">
            
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Number
                        </label>
                        <input type="text" id="interviewNumber" readonly value="{{ $technicalInterview->interview_number }}"
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
                                <option value="{{ $hrInterview->id }}" {{ $technicalInterview->hr_interview_id == $hrInterview->id ? 'selected' : '' }}>
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
                        <input type="text" name="candidate_name" required value="{{ old('candidate_name', $technicalInterview->candidate_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="candidate_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" required value="{{ old('job_title', $technicalInterview->job_title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="job_title_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="interview_date" required value="{{ old('interview_date', $technicalInterview->interview_date ? $technicalInterview->interview_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="interview_date_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interviewer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="interviewer_name" required value="{{ old('interviewer_name', $technicalInterview->interviewer_name) }}"
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
                                  placeholder="Describe candidate's understanding of business processes, workflows, and operational procedures...">{{ old('business_process_knowledge', $technicalInterview->business_process_knowledge) }}</textarea>
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
                                  placeholder="Evaluate candidate's technical competencies, software proficiency, tools knowledge, and technical problem-solving abilities...">{{ old('technical_skills_assessment', $technicalInterview->technical_skills_assessment) }}</textarea>
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
                                  placeholder="Assess physical fitness, ability to perform job-related physical tasks, mobility requirements, etc. (if applicable)...">{{ old('physical_capabilities', $technicalInterview->physical_capabilities) }}</textarea>
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
                                  placeholder="Describe results of any practical tests, hands-on exercises, or simulations performed during the interview...">{{ old('practical_test_results', $technicalInterview->practical_test_results) }}</textarea>
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
                                  placeholder="Any other technical competencies, certifications, or specialized skills relevant to the position...">{{ old('other_technical_areas', $technicalInterview->other_technical_areas) }}</textarea>
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
                            <option value="pass" {{ $technicalInterview->technical_result === 'pass' ? 'selected' : '' }}>Pass</option>
                            <option value="fail" {{ $technicalInterview->technical_result === 'fail' ? 'selected' : '' }}>Fail</option>
                            <option value="na" {{ $technicalInterview->technical_result === 'na' ? 'selected' : '' }}>N/A</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="technical_result_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Technical Comments
                        </label>
                        <textarea name="technical_comments" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Additional comments about the technical assessment...">{{ old('technical_comments', $technicalInterview->technical_comments) }}</textarea>
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
                        @if($technicalInterview->assessment_report_path)
                        <p class="text-xs text-gray-500 mt-1">
                            Current: <a href="{{ Storage::url($technicalInterview->assessment_report_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">View existing file</a>
                        </p>
                        @else
                        <p class="text-xs text-gray-500 mt-1">Upload detailed assessment report (PDF, DOC, DOCX)</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Signed Document
                        </label>
                        <input type="file" name="signed_file" accept=".pdf,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @if($technicalInterview->signed_file_path)
                        <p class="text-xs text-gray-500 mt-1">
                            Current: <a href="{{ Storage::url($technicalInterview->signed_file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">View existing file</a>
                        </p>
                        @else
                        <p class="text-xs text-gray-500 mt-1">Upload signed assessment document (PDF, DOC, DOCX)</p>
                        @endif
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
                    <a href="{{ route('technical-interview.show', $technicalInterview) }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <span id="btnText">Update Assessment</span>
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
        this.setupFormValidation();
        this.setupHrInterviewSelection();
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

    async submitForm(isDraft = false) {
        // Only validate required fields if not saving as draft
        const inputs = this.form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        if (!isDraft) {
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                this.showNotification('Please correct the errors in the form', 'error');
                return;
            }
        }

        // Set the status based on action
        document.getElementById('interviewStatus').value = isDraft ? 'draft' : 'submitted';

        // Show loading state
        this.setLoadingState(true, isDraft);

        try {
            const formData = new FormData(this.form);
            // Handle file upload
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
                this.showNotification(result.message || 'Interview successfully updated!', 'success');
                setTimeout(() => {
                    window.location.href = '/technical-interview';
                }, 500);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Update failed', 'error');
                }
            }
        } catch (error) {
            console.error('Submission error:', error);
            this.showNotification('An error occurred during submission', 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    displayServerErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            this.showFieldError(fieldName, errors[fieldName][0]);
        });
    }

    setLoadingState(loading, isDraft = false) {
        if (loading) {
            this.btnText.textContent = isDraft ? 'Saving Draft...' : 'Updating...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
            document.querySelector('button[onclick="saveAsDraft()"]').disabled = true;
        } else {
            this.btnText.textContent = 'Update Assessment';
            this.btnLoader.classList.add('hidden');
            this.submitBtn.disabled = false;
            document.querySelector('button[onclick="saveAsDraft()"]').disabled = false;
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
    window.technicalInterviewManager.submitForm(true);
}

// Initialize technical interview manager
document.addEventListener('DOMContentLoaded', function() {
    window.technicalInterviewManager = new TechnicalInterviewManager();
});
</script>
@endpush
