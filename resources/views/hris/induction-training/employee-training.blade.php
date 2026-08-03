@extends('layouts.app')

@section('title', 'Employee Training Records - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('induction-training.index') }}" class="hover:text-indigo-600">Induction Training</a>
                <i data-feather="chevron-right" class="w-4 h-4"></i>
                <span>Training History</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training History: {{ $employee->first_name }} {{ $employee->last_name }}</h1>
            <p class="text-gray-600 mt-2">Comprehensive training and induction records for {{ $employee->employee_id }}</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back
            </button>
            <button onclick="window.print()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="printer" class="w-4 h-4 mr-2"></i>
                Print History
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Column: Employee Summary -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                    <span class="text-blue-600 font-bold text-2xl">
                        {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $employee->position }}</p>

                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3 text-left">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Total Trainings:</span>
                        <span class="font-bold text-gray-900">{{ $trainings->count() }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Total Hours:</span>
                        <span class="font-bold text-gray-900">{{ $trainings->sum('training_duration_hours') }}h</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Success Rate:</span>
                        @php
                            $passedCount = $trainings->where('assessment_passed', true)->count();
                            $rate = $trainings->count() > 0 ? round(($passedCount / $trainings->count()) * 100) : 0;
                        @endphp
                        <span class="font-bold text-green-600">{{ $rate }}%</span>
                    </div>
                </div>
            </div>

            <!-- Training Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Training by Type</h3>
                <div class="space-y-4">
                    @foreach(['company_policies', 'safety_procedures', 'job_specific', 'compliance', 'other'] as $type)
                        @php
                            $typeCount = $trainings->where('training_type', $type)->count();
                            $percentage = $trainings->count() > 0 ? ($typeCount / $trainings->count()) * 100 : 0;
                            $color = match($type) {
                                'company_policies' => 'bg-blue-500',
                                'safety_procedures' => 'bg-red-500',
                                'job_specific' => 'bg-green-500',
                                'compliance' => 'bg-purple-500',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-600">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                                <span class="font-medium">{{ $typeCount }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="{{ $color }} h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Training Timeline -->
        <div class="lg:col-span-3 space-y-6">
            @forelse($trainings as $training)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row">
                        <!-- Left bar indicator -->
                        <div class="w-2 {{ $training->assessment_passed ? 'bg-green-500' : 'bg-red-500' }}"></div>

                        <div class="flex-1 p-6">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                                <div>
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase rounded">
                                            {{ str_replace('_', ' ', $training->training_type) }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($training->training_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $training->training_title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">Trainer: <span class="font-medium text-gray-700">{{ $training->trainer_name }}</span></p>
                                </div>
                                <div class="mt-4 md:mt-0 flex flex-col items-end space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">Duration:</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $training->training_duration_hours }} Hours</span>
                                    </div>
                                    <span class="px-3 py-1 {{ $training->assessment_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold rounded-full">
                                        {{ $training->assessment_passed ? 'Assessment Passed' : 'Assessment Failed' }}
                                    </span>
                                    <button onclick="openEditModal({{ $training->id }})"
                                            class="px-3 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold rounded-full transition-colors">
                                        <i data-feather="edit-2" class="w-3 h-3 mr-1 inline"></i>Edit
                                    </button>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Description</h4>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $training->training_description }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Assessment Details</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Score:</span>
                                            <span class="font-bold text-gray-900">{{ $training->assessment_score ?? 'N/A' }}%</span>
                                        </div>
                                        @if($training->next_training_date)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Next Scheduled:</span>
                                            <span class="font-bold text-indigo-600">{{ \Carbon\Carbon::parse($training->next_training_date)->format('M d, Y') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end space-y-2">
                                    @if($training->training_materials_path)
                                        <a href="{{ Storage::url($training->training_materials_path) }}" target="_blank"
                                           class="flex items-center text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
                                            <i data-feather="download" class="w-4 h-4 mr-2"></i>
                                            Download Training Materials
                                        </a>
                                    @endif
                                    @if($training->completion_certificate_path)
                                        <a href="{{ Storage::url($training->completion_certificate_path) }}" target="_blank"
                                           class="flex items-center text-sm text-green-600 hover:text-green-800 transition-colors">
                                            <i data-feather="award" class="w-4 h-4 mr-2"></i>
                                            View Completion Certificate
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($training->feedback_comments)
                                <div class="mt-4 p-3 border-l-4 border-indigo-100 bg-indigo-50/30 rounded-r-lg">
                                    <p class="text-xs italic text-gray-600">"{{ $training->feedback_comments }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-100 p-4 rounded-full">
                            <i data-feather="book-open" class="w-12 h-12 text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Training Records Found</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        This employee has no training history recorded in the system.
                    </p>
                    <a href="{{ route('induction-training.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                        Schedule New Training
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Edit Training Modal -->
<x-advanced-modal id="editModal" title="Edit Training Record"
                  description="Update induction training details" icon="edit" color="indigo" size="2xl">
    <form id="editForm" class="space-y-4">
        <input type="hidden" name="training_id" id="editTrainingId">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Date <span class="text-red-500">*</span></label>
                <input type="date" name="training_date" id="editTrainingDate" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Type <span class="text-red-500">*</span></label>
                <select name="training_type" id="editTrainingType" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Type</option>
                    <option value="company_policies">Company Policies</option>
                    <option value="safety_procedures">Safety Procedures</option>
                    <option value="job_specific">Job Specific</option>
                    <option value="compliance">Compliance</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Title <span class="text-red-500">*</span></label>
                <input type="text" name="training_title" id="editTrainingTitle" required maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trainer Name <span class="text-red-500">*</span></label>
                <input type="text" name="trainer_name" id="editTrainerName" required maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Hours) <span class="text-red-500">*</span></label>
                <input type="number" name="training_duration_hours" id="editDuration" required min="0.5" max="40" step="0.5"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Score (%)</label>
                <input type="number" name="assessment_score" id="editAssessmentScore" min="0" max="100" step="0.1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="editStatus" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="completed">Completed</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="editAssessmentPassed" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="editAssessmentPassed" class="ml-2 block text-sm text-gray-900">Assessment Passed</label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Next Training Date</label>
                <input type="date" name="next_training_date" id="editNextTrainingDate"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Materials (File)</label>
                <input type="file" name="training_materials" accept=".pdf,.doc,.docx,.ppt,.pptx"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Completion Certificate (File)</label>
                <input type="file" name="completion_certificate" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Training Description <span class="text-red-500">*</span></label>
                <textarea name="training_description" id="editDescription" rows="3" required maxlength="2000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Feedback Comments</label>
                <textarea name="feedback_comments" id="editFeedback" rows="2" maxlength="1000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="editNotes" rows="2" maxlength="1000"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="hideEditModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="editForm" id="editBtn"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <span id="editBtnText">Update Training</span>
                <div id="editBtnLoader" class="hidden ml-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
const employeeId = {{ $employee->id }};
const trainingsData = @json($trainings);

function openEditModal(trainingId) {
    const training = trainingsData.find(t => t.id === trainingId);
    if (!training) return;

    document.getElementById('editTrainingId').value = training.id;
    document.getElementById('editTrainingDate').value = training.training_date ? training.training_date.substring(0, 10) : '';
    document.getElementById('editTrainingType').value = training.training_type || '';
    document.getElementById('editTrainingTitle').value = training.training_title || '';
    document.getElementById('editTrainerName').value = training.trainer_name || '';
    document.getElementById('editDuration').value = training.training_duration_hours || '';
    document.getElementById('editAssessmentScore').value = training.assessment_score ?? '';
    document.getElementById('editStatus').value = training.status || 'completed';
    document.getElementById('editAssessmentPassed').checked = !!training.assessment_passed;
    document.getElementById('editNextTrainingDate').value = training.next_training_date ? training.next_training_date.substring(0, 10) : '';
    document.getElementById('editDescription').value = training.training_description || '';
    document.getElementById('editFeedback').value = training.feedback_comments || '';
    document.getElementById('editNotes').value = training.notes || '';

    openModal('editModal');
    if (typeof feather !== 'undefined') { feather.replace(); }
}

function hideEditModal() {
    closeModal('editModal');
    document.getElementById('editForm').reset();
}

async function submitEdit(event) {
    event.preventDefault();
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    formData.set('_method', 'PUT');
    formData.set('assessment_passed', document.getElementById('editAssessmentPassed').checked ? '1' : '0');

    const btnText = document.getElementById('editBtnText');
    const btnLoader = document.getElementById('editBtnLoader');
    const btn = document.getElementById('editBtn');
    btnText.textContent = 'Updating...';
    btnLoader.classList.remove('hidden');
    btn.disabled = true;

    try {
        const response = await fetch(`/induction-training/${employeeId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Training record updated successfully', 'success');
            hideEditModal();
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showNotification(result.message || 'Update failed', 'error');
        }
    } catch (error) {
        console.error('Edit error:', error);
        showNotification('An error occurred updating the training', 'error');
    } finally {
        btnText.textContent = 'Update Training';
        btnLoader.classList.add('hidden');
        btn.disabled = false;
    }
}

function showNotification(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
        return;
    }
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('editForm').addEventListener('submit', submitEdit);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
