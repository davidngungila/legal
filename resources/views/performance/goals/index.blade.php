@extends('layouts.app')

@section('title', 'Goals - Performance Management')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Goals</h1>
            <p class="text-gray-600 mt-2">Set and track employee goals with measurable KPIs</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('newGoalModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="target" class="w-4 h-4 inline mr-2"></i>
                New Goal
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="target" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total Goals</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['approved'] }}</h3>
            <p class="text-gray-600 text-sm">Approved Goals</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="send" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['submitted'] }}</h3>
            <p class="text-gray-600 text-sm">Submitted Goals</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="edit-3" class="w-6 h-6 text-gray-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</h3>
            <p class="text-gray-600 text-sm">Draft Goals</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('performance.goals.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cycle</label>
                <select name="cycle_id" class="form-select rounded-md border-gray-300">
                    <option value="">All Cycles</option>
                    @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}" {{ request('cycle_id') == $cycle->id ? 'selected' : '' }}>{{ $cycle->cycle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select name="employee_id" class="form-select rounded-md border-gray-300">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select rounded-md border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('performance.goals.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($goals as $goal)
        <div id="goal-{{ $goal->id }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm font-medium">{{ substr($goal->employee?->first_name ?? 'E', 0, 1) }}{{ substr($goal->employee?->last_name ?? 'E', 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $goal->goal_title }}</h3>
                        <p class="text-sm text-gray-600">{{ $goal->employee?->full_name }} • {{ $goal->cycle?->cycle_name ?? 'No cycle' }}</p>
                        @if($goal->description)
                        <p class="text-sm text-gray-600 mt-1">{{ $goal->description }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($goal->status === 'approved') bg-green-100 text-green-800
                        @elseif($goal->status === 'submitted') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($goal->status) }}
                    </span>
                    @if($goal->approvedBy)
                    <span class="text-xs text-gray-500">by {{ $goal->approvedBy->name }}</span>
                    @endif
                    <button onclick='editGoal(@json($goal))' class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</button>
                    <form action="{{ route('performance.goals.destroy', $goal->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this goal and its KPIs?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">KPIs ({{ $goal->kpis->count() }} • {{ $goal->weight_total }}% weight)</h4>
                    <button onclick='openKpiModal(@json($goal))' class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">+ Add KPI</button>
                </div>
                @if($goal->kpis->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($goal->kpis as $kpi)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-900">{{ $kpi->kpi_description }}</td>
                                <td class="px-4 py-2 text-gray-900">{{ $kpi->target ?: '—' }}</td>
                                <td class="px-4 py-2 text-gray-900">{{ $kpi->weight }}%</td>
                                <td class="px-4 py-2 text-gray-900">{{ $kpi->measurement_unit ?: '—' }}</td>
                                <td class="px-4 py-2 text-gray-900">{{ $kpi->deadline?->format('Y-m-d') ?: '—' }}</td>
                                <td class="px-4 py-2">
                                    <button onclick='editKpi(@json($kpi), @json($goal->id))' class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                    <form action="{{ route('performance.goals.kpis.destroy', [$goal->id, $kpi->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this KPI?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-500">No KPIs added yet. Add measurable KPIs to track progress.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
            No goals found. Create your first goal to get started.
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $goals->links() }}
    </div>
</div>

<x-advanced-modal id="newGoalModal" title="New Goal" icon="target" color="indigo" size="lg">
    <form action="{{ route('performance.goals.store') }}" method="POST" id="newGoalForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="goal_employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                <select id="goal_employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="goal_cycle_id" class="block text-sm font-medium text-gray-700">Cycle</label>
                <select id="goal_cycle_id" name="cycle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">No cycle</option>
                    @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}">{{ $cycle->cycle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="goal_title" class="block text-sm font-medium text-gray-700">Goal Title</label>
                <input type="text" id="goal_title" name="goal_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Improve customer satisfaction" required>
            </div>
            <div>
                <label for="goal_description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="goal_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label for="goal_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="goal_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newGoalModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newGoalForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Goal</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editGoalModal" title="Edit Goal" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editGoalForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="edit_goal_title" class="block text-sm font-medium text-gray-700">Goal Title</label>
                <input type="text" id="edit_goal_title" name="goal_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="edit_goal_description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="edit_goal_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label for="edit_goal_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="edit_goal_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editGoalModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editGoalForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Goal</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="kpiModal" title="KPI" icon="check-square" color="blue" size="lg">
    <form action="" method="POST" id="kpiForm">
        @csrf
        <input type="hidden" id="kpi_method" name="_method" value="POST">
        <div class="space-y-4">
            <div>
                <label for="kpi_description" class="block text-sm font-medium text-gray-700">KPI Description</label>
                <textarea id="kpi_description" name="kpi_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Reduce response time to under 2 hours" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="kpi_target" class="block text-sm font-medium text-gray-700">Target</label>
                    <input type="text" id="kpi_target" name="target" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 95%">
                </div>
                <div>
                    <label for="kpi_weight" class="block text-sm font-medium text-gray-700">Weight (%)</label>
                    <input type="number" id="kpi_weight" name="weight" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="kpi_measurement_unit" class="block text-sm font-medium text-gray-700">Measurement Unit</label>
                    <input type="text" id="kpi_measurement_unit" name="measurement_unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. %, hours, count">
                </div>
                <div>
                    <label for="kpi_deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                    <input type="date" id="kpi_deadline" name="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('kpiModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="kpiForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save KPI</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editGoal(goal) {
    document.getElementById('edit_goal_title').value = goal.goal_title;
    document.getElementById('edit_goal_description').value = goal.description || '';
    document.getElementById('edit_goal_status').value = goal.status;
    document.getElementById('editGoalForm').action = '{{ route('performance.goals.update', 0) }}'.replace(/\/0$/, '/' + goal.id);
    openModal('editGoalModal');
}

function openKpiModal(goal) {
    document.getElementById('kpiForm').reset();
    document.getElementById('kpi_method').value = 'POST';
    document.getElementById('kpiForm').action = '{{ route('performance.goals.kpis.store', 0) }}'.replace(/\/0$/, '/' + goal.id);
    openModal('kpiModal');
}

function editKpi(kpi, goalId) {
    document.getElementById('kpi_description').value = kpi.kpi_description;
    document.getElementById('kpi_target').value = kpi.target || '';
    document.getElementById('kpi_weight').value = kpi.weight;
    document.getElementById('kpi_measurement_unit').value = kpi.measurement_unit || '';
    document.getElementById('kpi_deadline').value = kpi.deadline || '';
    document.getElementById('kpi_method').value = 'PUT';
    document.getElementById('kpiForm').action = '{{ route('performance.goals.kpis.update', [0, 0]) }}'.replace(/\/0\/0$/, '/' + goalId + '/' + kpi.id);
    openModal('kpiModal');
}

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const expand = params.get('expand');
    if (expand) {
        const el = document.getElementById('goal-' + expand);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection
