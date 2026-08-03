@extends('layouts.app')

@section('title', 'Merit Review - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Merit Review</h1>
            <p class="text-gray-600 mt-2">Record employee salary increments based on performance</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createMeritReviewModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                New Merit Review
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Reviews</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['approved']) }}</h3>
            <p class="text-gray-600 text-sm">Approved</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending']) }}</h3>
            <p class="text-gray-600 text-sm">Pending</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_increment'], 1) }}%</h3>
            <p class="text-gray-600 text-sm">Avg Increment</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Merit Reviews</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Increment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $review->employee->first_name ?? 'Unknown' }} {{ $review->employee->last_name ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $review->employee->position ?? 'Employee' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $review->review_period ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full {{ $review->rating >= 4 ? 'bg-green-100 text-green-800' : ($review->rating >= 3 ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $review->rating }} / 5
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">TZS {{ number_format($review->old_salary, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">TZS {{ number_format($review->new_salary, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-green-600 font-medium">+TZS {{ number_format($review->increment_amount, 0) }}</span>
                            <span class="text-xs text-gray-500">({{ number_format($review->increment_percent, 1) }}%)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editMeritReviewModal{{ $review->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteMeritReview({{ $review->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="file-text" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No merit reviews yet</p>
                                <p class="text-sm text-gray-600 mt-2">Click "New Merit Review" to record the first increment</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Merit Review Modal -->
<x-advanced-modal id="createMeritReviewModal" title="New Merit Review" description="Record a performance-based salary increment." icon="plus" color="indigo" size="2xl">
    <form id="createMeritReviewForm" method="POST" action="{{ route('compensation.merit-review.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" id="createMeritEmployee" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary ?? 0 }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
                <p id="createMeritCurrentSalary" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Review Period</label>
                <input type="text" name="review_period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 2026" value="{{ now()->format('Y') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span class="text-red-500">*</span></label>
                <select name="rating" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="5">5 - Outstanding</option>
                    <option value="4">4 - Excellent</option>
                    <option value="3" selected>3 - Good</option>
                    <option value="2">2 - Needs Improvement</option>
                    <option value="1">1 - Poor</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="new_salary" id="createMeritNewSalary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                <p id="createMeritPreview" class="mt-1 text-sm text-gray-500"></p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reviewer Notes</label>
                <textarea name="reviewer_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Performance summary and rationale..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createMeritReviewModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createMeritReviewForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Review</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Merit Review Modals -->
@foreach($reviews as $review)
<x-advanced-modal id="editMeritReviewModal{{ $review->id }}" title="Edit Merit Review" description="Update the review and salary increment." icon="edit" color="indigo" size="2xl">
    <form id="editMeritReviewForm{{ $review->id }}" method="POST" action="{{ route('compensation.merit-review.update', $review->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" id="editMeritEmployee{{ $review->id }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary ?? 0 }}" {{ $review->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Review Period</label>
                <input type="text" name="review_period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $review->review_period }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span class="text-red-500">*</span></label>
                <select name="rating" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="5" {{ $review->rating == 5 ? 'selected' : '' }}>5 - Outstanding</option>
                    <option value="4" {{ $review->rating == 4 ? 'selected' : '' }}>4 - Excellent</option>
                    <option value="3" {{ $review->rating == 3 ? 'selected' : '' }}>3 - Good</option>
                    <option value="2" {{ $review->rating == 2 ? 'selected' : '' }}>2 - Needs Improvement</option>
                    <option value="1" {{ $review->rating == 1 ? 'selected' : '' }}>1 - Poor</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">New Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="new_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $review->new_salary }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ $review->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reviewer Notes</label>
                <textarea name="reviewer_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $review->reviewer_notes }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editMeritReviewModal{{ $review->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editMeritReviewForm{{ $review->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('createMeritEmployee');
    const salaryText = document.getElementById('createMeritCurrentSalary');
    const newSalary = document.getElementById('createMeritNewSalary');
    const preview = document.getElementById('createMeritPreview');

    function updateCreatePreview() {
        const old = parseFloat(select.selectedOptions[0]?.dataset?.salary || 0);
        salaryText.textContent = old > 0 ? `Current salary: TZS ${Number(old).toLocaleString()}` : '';
        const nw = parseFloat(newSalary.value || 0);
        if (nw > 0) {
            const inc = nw - old;
            const pct = old > 0 ? (inc / old) * 100 : 0;
            preview.textContent = `Increment: TZS ${Number(inc).toLocaleString()} (${pct.toFixed(1)}%)`;
            preview.className = 'mt-1 text-sm ' + (inc > 0 ? 'text-green-600' : 'text-gray-500');
        } else {
            preview.textContent = '';
        }
    }

    if (select) select.addEventListener('change', updateCreatePreview);
    if (newSalary) newSalary.addEventListener('input', updateCreatePreview);
});
</script>
<script>
function deleteMeritReview(id) {
    if (confirm('Are you sure you want to delete this merit review? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/compensation/merit-review/${id}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
