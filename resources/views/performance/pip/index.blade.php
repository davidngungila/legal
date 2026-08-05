@extends('layouts.app')

@section('title', 'PIP - Performance Improvement Plans')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Performance Improvement Plans</h1>
            <p class="text-gray-600 mt-2">Help underperforming employees get back on track</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('newPipModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="activity" class="w-4 h-4 inline mr-2"></i>
                New PIP
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="activity" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total PIPs</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="play" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</h3>
            <p class="text-gray-600 text-sm">Active PIPs</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="check-circle" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['terminated'] }}</h3>
            <p class="text-gray-600 text-sm">Terminated</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('performance.pip.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select rounded-md border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('performance.pip.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($plans as $plan)
        <div id="pip-{{ $plan->id }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm font-medium">{{ substr($plan->employee?->first_name ?? 'E', 0, 1) }}{{ substr($plan->employee?->last_name ?? 'E', 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $plan->employee?->full_name }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $plan->start_date?->format('Y-m-d') }} → {{ $plan->end_date?->format('Y-m-d') }} • Review: {{ ucfirst($plan->review_frequency) }}
                        </p>
                        @if($plan->triggerAppraisal)
                        <p class="text-xs text-gray-500 mt-1">Triggered from appraisal #{{ $plan->triggerAppraisal->id }} ({{ $plan->triggerAppraisal->rating }}/5)</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($plan->status === 'completed') bg-green-100 text-green-800
                        @elseif($plan->status === 'terminated') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($plan->status) }}
                    </span>
                    <button onclick='editPip(@json($plan))' class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</button>
                    <form action="{{ route('performance.pip.destroy', $plan->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this PIP and its reviews?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                    </form>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Objectives</h4>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $plan->pip_objectives }}</p>
                @if($plan->outcome)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Outcome</h4>
                    <p class="text-sm text-gray-700">{{ $plan->outcome }}</p>
                </div>
                @endif
            </div>

            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">Progress Reviews ({{ $plan->pipReviews->count() }})</h4>
                    <button onclick="openPipReviewModal({{ $plan->id }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">+ Add Review</button>
                </div>
                @if($plan->pipReviews->count())
                <div class="space-y-3">
                    @foreach($plan->pipReviews as $pipReview)
                    <div class="border border-gray-200 rounded-lg p-4 flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $pipReview->reviewer?->name ?? 'Reviewer' }}</span>
                                <span class="text-xs text-gray-500">{{ $pipReview->review_date?->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex items-center space-x-2 mb-2">
                                @php
                                    $progressColor = $pipReview->progress_rating >= 60 ? 'bg-green-600' : ($pipReview->progress_rating >= 40 ? 'bg-yellow-600' : 'bg-red-600');
                                @endphp
                                <div class="w-full max-w-xs bg-gray-200 rounded-full h-2">
                                    <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $pipReview->progress_rating }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ $pipReview->progress_rating }}%</span>
                            </div>
                            @if($pipReview->comments)
                            <p class="text-sm text-gray-600">{{ $pipReview->comments }}</p>
                            @endif
                            @if($pipReview->action_items)
                            <p class="text-xs text-gray-500 mt-1"><strong>Action items:</strong> {{ $pipReview->action_items }}</p>
                            @endif
                        </div>
                        <form action="{{ route('performance.pip.reviews.destroy', [$plan->id, $pipReview->id]) }}" method="POST" onsubmit="return confirm('Delete this progress review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500">No progress reviews yet.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
            No performance improvement plans found.
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $plans->links() }}
    </div>
</div>

<x-advanced-modal id="newPipModal" title="New Performance Improvement Plan" icon="activity" color="red" size="lg">
    <form action="{{ route('performance.pip.store') }}" method="POST" id="newPipForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="pip_employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="pip_employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pip_trigger_appraisal_id" class="block text-sm font-medium text-gray-700">Trigger Appraisal</label>
                <select id="pip_trigger_appraisal_id" name="trigger_appraisal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">None</option>
                    @foreach($reviews as $review)
                    <option value="{{ $review->id }}">#{{ $review->id }} — {{ $review->employee?->full_name }} ({{ $review->rating }}/5, {{ $review->review_date?->format('Y-m-d') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="pip_objectives" class="block text-sm font-medium text-gray-700">PIP Objectives</label>
                <textarea id="pip_objectives" name="pip_objectives" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Describe the improvement objectives and expectations" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="pip_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="pip_start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="pip_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="pip_end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="pip_review_frequency" class="block text-sm font-medium text-gray-700">Review Frequency</label>
                    <select id="pip_review_frequency" name="review_frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="weekly">Weekly</option>
                        <option value="biweekly" selected>Bi-Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div>
                    <label for="pip_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="pip_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newPipModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newPipForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create PIP</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editPipModal" title="Edit PIP" icon="edit-3" color="red" size="lg">
    <form action="" method="POST" id="editPipForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="edit_pip_objectives" class="block text-sm font-medium text-gray-700">PIP Objectives</label>
                <textarea id="edit_pip_objectives" name="pip_objectives" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_pip_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="edit_pip_start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="edit_pip_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="edit_pip_end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_pip_review_frequency" class="block text-sm font-medium text-gray-700">Review Frequency</label>
                    <select id="edit_pip_review_frequency" name="review_frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="weekly">Weekly</option>
                        <option value="biweekly">Bi-Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div>
                    <label for="edit_pip_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit_pip_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="edit_pip_outcome" class="block text-sm font-medium text-gray-700">Outcome</label>
                <textarea id="edit_pip_outcome" name="outcome" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Final outcome of the plan"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editPipModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editPipForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update PIP</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="pipReviewModal" title="Add Progress Review" icon="clipboard" color="blue" size="lg">
    <form action="" method="POST" id="pipReviewForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="pip_review_date" class="block text-sm font-medium text-gray-700">Review Date</label>
                <input type="date" id="pip_review_date" name="review_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="pip_review_progress" class="block text-sm font-medium text-gray-700">Progress Rating (%)</label>
                <input type="number" id="pip_review_progress" name="progress_rating" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="pip_review_comments" class="block text-sm font-medium text-gray-700">Comments</label>
                <textarea id="pip_review_comments" name="comments" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label for="pip_review_action_items" class="block text-sm font-medium text-gray-700">Action Items</label>
                <textarea id="pip_review_action_items" name="action_items" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('pipReviewModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="pipReviewForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Review</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editPip(plan) {
    document.getElementById('edit_pip_objectives').value = plan.pip_objectives || '';
    document.getElementById('edit_pip_start_date').value = plan.start_date || '';
    document.getElementById('edit_pip_end_date').value = plan.end_date || '';
    document.getElementById('edit_pip_review_frequency').value = plan.review_frequency || 'biweekly';
    document.getElementById('edit_pip_status').value = plan.status || 'active';
    document.getElementById('edit_pip_outcome').value = plan.outcome || '';
    document.getElementById('editPipForm').action = '{{ route('performance.pip.update', 0) }}'.replace(/\/0$/, '/' + plan.id);
    openModal('editPipModal');
}

function openPipReviewModal(pipId) {
    document.getElementById('pipReviewForm').reset();
    document.getElementById('pip_review_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('pipReviewForm').action = '{{ route('performance.pip.reviews.store', ['pip' => ':id']) }}'.replace(':id', pipId);
    openModal('pipReviewModal');
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const expand = params.get('expand');
    if (expand) {
        const el = document.getElementById('pip-' + expand);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection
