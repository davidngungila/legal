@extends('layouts.app')

@section('title', 'Training Plans - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Training Plans</h1>
            <p class="text-gray-600 mt-2">Annual and periodic training plans with budgets</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openModal('newPlanModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Training Plan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total Plans</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</h3>
            <p class="text-gray-600 text-sm">Draft</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</h3>
            <p class="text-gray-600 text-sm">Approved</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_budget'], 0) }} TZS</h3>
            <p class="text-gray-600 text-sm">Total Budget</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('training.plans') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select rounded-md border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('training.plans') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if($plans->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sessions</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($plans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $plan->name }}</div>
                            @if($plan->description)
                            <div class="text-xs text-gray-500 max-w-xs truncate">{{ $plan->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if($plan->target_department)
                            <div>Dept: {{ $plan->target_department }}</div>
                            @endif
                            @if($plan->target_category)
                            <div class="text-xs text-gray-500">Category: {{ $plan->target_category }}</div>
                            @endif
                            @if(!$plan->target_department && !$plan->target_category)
                            All employees
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if($plan->period_start)
                            {{ $plan->period_start->format('M d, Y') }} - {{ $plan->period_end ? $plan->period_end->format('M d, Y') : 'Open' }}
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm font-medium">{{ number_format($plan->budget, 0) }} {{ $plan->currency }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $plan->sessions_count }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $plan->status === 'approved' ? 'bg-green-100 text-green-800' : ($plan->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($plan->status) }}</span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                @if($plan->status === 'draft')
                                <form action="{{ route('training.plans.update', $plan->id) }}" method="POST" onsubmit="return confirm('Approve this training plan?');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $plan->name }}">
                                    <input type="hidden" name="description" value="{{ $plan->description }}">
                                    <input type="hidden" name="target_department" value="{{ $plan->target_department }}">
                                    <input type="hidden" name="target_category" value="{{ $plan->target_category }}">
                                    <input type="hidden" name="period_start" value="{{ $plan->period_start ? $plan->period_start->format('Y-m-d') : '' }}">
                                    <input type="hidden" name="period_end" value="{{ $plan->period_end ? $plan->period_end->format('Y-m-d') : '' }}">
                                    <input type="hidden" name="budget" value="{{ $plan->budget }}">
                                    <input type="hidden" name="currency" value="{{ $plan->currency }}">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium">
                                        <i data-feather="check-circle" class="w-4 h-4 mr-1"></i> Approve
                                    </button>
                                </form>
                                @endif
                                <button onclick='editPlan(@json($plan))' class="text-gray-600 hover:text-gray-800 text-sm">Edit</button>
                                <form action="{{ route('training.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Delete this training plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $plans->links() }}
        </div>
        @else
        <div class="text-center text-gray-500 py-12">No training plans yet. Create your first plan.</div>
        @endif
    </div>
</div>

<x-advanced-modal id="newPlanModal" title="New Training Plan" icon="calendar" color="indigo" size="lg">
    <form action="{{ route('training.plans.store') }}" method="POST" id="newPlanForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Department</label>
                    <select name="target_department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Category</label>
                    <input type="text" name="target_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Management">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Period Start</label>
                    <input type="date" name="period_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Period End</label>
                    <input type="date" name="period_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Budget</label>
                    <input type="number" name="budget" min="0" step="0.01" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                        <option value="KES">KES</option>
                        <option value="UGX">UGX</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newPlanModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newPlanForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Plan</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editPlanModal" title="Edit Training Plan" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editPlanForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                <input type="text" id="plan_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="plan_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Department</label>
                    <select id="plan_target_department" name="target_department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Target Category</label>
                    <input type="text" id="plan_target_category" name="target_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Period Start</label>
                    <input type="date" id="plan_period_start" name="period_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Period End</label>
                    <input type="date" id="plan_period_end" name="period_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Budget</label>
                    <input type="number" id="plan_budget" name="budget" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select id="plan_currency" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                        <option value="KES">KES</option>
                        <option value="UGX">UGX</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select id="plan_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editPlanModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editPlanForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Plan</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editPlan(plan) {
    document.getElementById('plan_name').value = plan.name;
    document.getElementById('plan_description').value = plan.description || '';
    document.getElementById('plan_target_department').value = plan.target_department || '';
    document.getElementById('plan_target_category').value = plan.target_category || '';
    document.getElementById('plan_period_start').value = plan.period_start || '';
    document.getElementById('plan_period_end').value = plan.period_end || '';
    document.getElementById('plan_budget').value = plan.budget;
    document.getElementById('plan_currency').value = plan.currency || 'TZS';
    document.getElementById('plan_status').value = plan.status || 'draft';
    document.getElementById('editPlanForm').action = '{{ route('training.plans.update', 0) }}'.replace(/\/0$/, '/' + plan.id);
    openModal('editPlanModal');
}
</script>
@endsection
