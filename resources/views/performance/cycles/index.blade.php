@extends('layouts.app')

@section('title', 'Performance Cycles - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Performance Cycles</h1>
            <p class="text-gray-600 mt-2">Manage appraisal cycles and their periods</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('newCycleModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Cycle
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="repeat" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total Cycles</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="play" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</h3>
            <p class="text-gray-600 text-sm">Active Cycles</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="edit-3" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</h3>
            <p class="text-gray-600 text-sm">Draft Cycles</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="lock" class="w-6 h-6 text-gray-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['closed'] }}</h3>
            <p class="text-gray-600 text-sm">Closed Cycles</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">All Cycles</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cycle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Goals</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviews</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cycles as $cycle)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $cycle->cycle_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($cycle->cycle_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $cycle->period_start?->format('Y-m-d') }} → {{ $cycle->period_end?->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cycle->employee_category ?: 'All' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cycle->employee_goals_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cycle->performance_reviews_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($cycle->status === 'active') bg-green-100 text-green-800
                                @elseif($cycle->status === 'closed') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($cycle->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick='editCycle(@json($cycle))' class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <form action="{{ route('performance.cycles.destroy', $cycle->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this cycle? This will also remove its goals and reviews.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">No performance cycles yet. Create your first cycle.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $cycles->links() }}
        </div>
    </div>
</div>

<x-advanced-modal id="newCycleModal" title="New Performance Cycle" icon="repeat" color="indigo" size="lg">
    <form action="{{ route('performance.cycles.store') }}" method="POST" id="newCycleForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="cycle_name" class="block text-sm font-medium text-gray-700">Cycle Name</label>
                <input type="text" id="cycle_name" name="cycle_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Q1 2026 Appraisals" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="cycle_type" class="block text-sm font-medium text-gray-700">Cycle Type</label>
                    <select id="cycle_type" name="cycle_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                        <option value="probation">Probation</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-700">Period Start</label>
                    <input type="date" id="period_start" name="period_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-700">Period End</label>
                    <input type="date" id="period_end" name="period_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div>
                <label for="employee_category" class="block text-sm font-medium text-gray-700">Employee Category</label>
                <input type="text" id="employee_category" name="employee_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. All, Management, Operations">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newCycleModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newCycleForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Cycle</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editCycleModal" title="Edit Performance Cycle" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editCycleForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="edit_cycle_name" class="block text-sm font-medium text-gray-700">Cycle Name</label>
                <input type="text" id="edit_cycle_name" name="cycle_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_cycle_type" class="block text-sm font-medium text-gray-700">Cycle Type</label>
                    <select id="edit_cycle_type" name="cycle_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="quarterly">Quarterly</option>
                        <option value="semi_annual">Semi-Annual</option>
                        <option value="annual">Annual</option>
                        <option value="probation">Probation</option>
                    </select>
                </div>
                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="edit_period_start" class="block text-sm font-medium text-gray-700">Period Start</label>
                    <input type="date" id="edit_period_start" name="period_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label for="edit_period_end" class="block text-sm font-medium text-gray-700">Period End</label>
                    <input type="date" id="edit_period_end" name="period_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>
            <div>
                <label for="edit_employee_category" class="block text-sm font-medium text-gray-700">Employee Category</label>
                <input type="text" id="edit_employee_category" name="employee_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. All, Management, Operations">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editCycleModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editCycleForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Cycle</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editCycle(cycle) {
    document.getElementById('edit_cycle_name').value = cycle.cycle_name;
    document.getElementById('edit_cycle_type').value = cycle.cycle_type;
    document.getElementById('edit_status').value = cycle.status;
    document.getElementById('edit_period_start').value = cycle.period_start || '';
    document.getElementById('edit_period_end').value = cycle.period_end || '';
    document.getElementById('edit_employee_category').value = cycle.employee_category || '';
    document.getElementById('editCycleForm').action = '{{ route('performance.cycles.update', 0) }}'.replace(/\/0$/, '/' + cycle.id);
    openModal('editCycleModal');
}
</script>
@endsection
