@extends('layouts.app')

@section('title', 'Edit Job Vacancy - Orvion HRIS')

@section('content')
<div class="p-6">
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Job Vacancy</h1>
            <p class="text-gray-600 mt-2">Update the job vacancy details before submitting for approval</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('job-vacancy.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back
            </a>
            <a href="{{ route('job-vacancy.show', $jobVacancy) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                View
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="jobVacancyEditForm" class="p-6 space-y-8">
            <input type="hidden" name="status" id="vacancyStatus" value="{{ $jobVacancy->status }}">
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" required value="{{ $jobVacancy->company_name }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="company_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" required value="{{ $jobVacancy->job_title }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="job_title_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type of Vacancy <span class="text-red-500">*</span>
                        </label>
                        <select name="vacancy_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Type</option>
                            <option value="new_position" @selected($jobVacancy->vacancy_type === 'new_position')>New Position</option>
                            <option value="replacement" @selected($jobVacancy->vacancy_type === 'replacement')>Replacement</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="vacancy_type_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="department" required value="{{ $jobVacancy->department }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="department_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Workstation <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="workstation" required value="{{ $jobVacancy->workstation }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="workstation_error"></span>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Timeline</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Position Became Vacant <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="position_vacant_date" required value="{{ optional($jobVacancy->position_vacant_date)->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="position_vacant_date_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Application <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="application_date" required value="{{ optional($jobVacancy->application_date)->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="application_date_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Application Deadline <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="application_deadline" required value="{{ optional($jobVacancy->application_deadline)->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="application_deadline_error"></span>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8" id="replacementSection" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Replacement Details</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reasons for Replacement <span class="text-red-500">*</span>
                    </label>
                    <textarea name="replacement_reason" rows="4" id="replacementReason"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please explain the reasons for this replacement...">{{ $jobVacancy->replacement_reason }}</textarea>
                    <span class="text-red-500 text-sm hidden" id="replacement_reason_error"></span>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Job Description</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Job Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="job_description" rows="6" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Provide a detailed description of the job responsibilities and requirements...">{{ $jobVacancy->job_description }}</textarea>
                    <span class="text-red-500 text-sm hidden" id="job_description_error"></span>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Qualifications</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Age
                        </label>
                        <input type="number" name="min_age" min="18" max="65" value="{{ $jobVacancy->min_age }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="min_age_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Salary Range (Min)
                        </label>
                        <input type="number" name="salary_range_min" min="0" step="0.01" value="{{ $jobVacancy->salary_range_min }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Salary Range (Max)
                        </label>
                        <input type="number" name="salary_range_max" min="0" step="0.01" value="{{ $jobVacancy->salary_range_max }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="salary_range_max_error"></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Academic Qualifications
                        </label>
                        <textarea name="academic_qualifications" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="e.g., Bachelor's degree in relevant field...">{{ $jobVacancy->academic_qualifications }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Professional Qualifications
                        </label>
                        <textarea name="professional_qualifications" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="e.g., Professional certifications, licenses...">{{ $jobVacancy->professional_qualifications }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Other Qualifications
                        </label>
                        <textarea name="other_qualifications" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="e.g., Skills, experience, languages...">{{ $jobVacancy->other_qualifications }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Comments
                        </label>
                        <textarea name="additional_comments" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Any additional information about the position...">{{ $jobVacancy->additional_comments }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" onclick="saveAsDraft()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Save Draft
                </button>
                <div class="flex space-x-3">
                    <button type="button" onclick="window.history.back()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                        <span id="btnText">Update</span>
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
class JobVacancyEditManager {
    constructor(updateUrl) {
        this.updateUrl = updateUrl;
        this.form = document.getElementById('jobVacancyEditForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFormValidation();
        this.setupConditionalFields();
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm(false);
        });

        const vacancyType = document.querySelector('select[name="vacancy_type"]');
        vacancyType.addEventListener('change', () => this.toggleReplacementSection());

        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    setupConditionalFields() {
        this.toggleReplacementSection();
    }

    toggleReplacementSection() {
        const vacancyType = document.querySelector('select[name="vacancy_type"]').value;
        const replacementSection = document.getElementById('replacementSection');
        const replacementReason = document.getElementById('replacementReason');

        if (vacancyType === 'replacement') {
            replacementSection.style.display = 'block';
            replacementReason.required = true;
        } else {
            replacementSection.style.display = 'none';
            replacementReason.required = false;
            this.clearFieldError(replacementReason);
        }
    }

    setupFormValidation() {
        const applicationDate = document.querySelector('input[name="application_date"]');
        const applicationDeadline = document.querySelector('input[name="application_deadline"]');

        applicationDeadline.addEventListener('change', () => {
            if (applicationDate.value && applicationDeadline.value) {
                const appDate = new Date(applicationDate.value);
                const deadline = new Date(applicationDeadline.value);

                if (deadline <= appDate) {
                    this.showFieldError('application_deadline', 'Application deadline must be after application date');
                } else {
                    this.clearFieldError(applicationDeadline);
                }
            }
        });

        const salaryMin = document.querySelector('input[name="salary_range_min"]');
        const salaryMax = document.querySelector('input[name="salary_range_max"]');

        salaryMax.addEventListener('input', () => {
            if (salaryMin.value && salaryMax.value) {
                const min = parseFloat(salaryMin.value);
                const max = parseFloat(salaryMax.value);

                if (max < min) {
                    this.showFieldError('salary_range_max', 'Maximum salary must be greater than minimum salary');
                } else {
                    this.clearFieldError(salaryMax);
                }
            }
        });
    }

    validateField(field) {
        const value = (field.value || '').toString().trim();
        const fieldName = field.name;

        this.clearFieldError(field);

        if (field.required && !value) {
            this.showFieldError(fieldName, 'This field is required');
            return false;
        }

        switch (fieldName) {
            case 'min_age':
                if (value && (value < 18 || value > 65)) {
                    this.showFieldError(fieldName, 'Age must be between 18 and 65');
                    return false;
                }
                break;
            case 'salary_range_min':
            case 'salary_range_max':
                if (value && value < 0) {
                    this.showFieldError(fieldName, 'Salary must be a positive number');
                    return false;
                }
                break;
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
        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
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

        document.getElementById('vacancyStatus').value = isDraft ? 'draft' : 'draft';

        this.setLoadingState(true, isDraft);

        try {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch(this.updateUrl, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            if (response.status === 419) {
                this.showNotification('Session expired. Please refresh the page and try again.', 'error');
                return;
            }

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message || 'Job vacancy updated successfully', 'success');
                setTimeout(() => {
                    window.location.href = @json(route('job-vacancy.show', $jobVacancy));
                }, 500);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Operation failed', 'error');
                }
            }
        } catch (error) {
            console.error('Update error:', error);
            this.showNotification('An error occurred during the operation', 'error');
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
            this.btnText.textContent = isDraft ? 'Saving...' : 'Updating...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
            document.querySelector('button[onclick="saveAsDraft()"]').disabled = true;
        } else {
            this.btnText.textContent = 'Update';
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

function saveAsDraft() {
    window.jobVacancyEditManager.submitForm(true);
}

document.addEventListener('DOMContentLoaded', function() {
    window.jobVacancyEditManager = new JobVacancyEditManager(
        @json(route('job-vacancy.update', $jobVacancy))
    );
});
</script>
@endpush

