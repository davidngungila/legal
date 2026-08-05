@extends('layouts.app')

@section('title', 'Calibration Sessions - Performance Management')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Calibration Sessions</h1>
            <p class="text-gray-600 mt-2">Align ratings across reviewers for fairness and consistency</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('newSessionModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                New Session
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="users" class="w-6 h-6 text-indigo-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</h3>
            <p class="text-gray-600 text-sm">Total Sessions</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="calendar" class="w-6 h-6 text-yellow-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['planned'] }}</h3>
            <p class="text-gray-600 text-sm">Planned</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="clock" class="w-6 h-6 text-orange-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</h3>
            <p class="text-gray-600 text-sm">Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('performance.calibration.index') }}" class="flex flex-wrap gap-4 items-end">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="form-select rounded-md border-gray-300">
                    <option value="">All Statuses</option>
                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
            </div>
            <div>
                <a href="{{ route('performance.calibration.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cycle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facilitator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->session_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->cycle?->cycle_name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->facilitatedBy?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $session->notes ?: '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($session->status === 'completed') bg-green-100 text-green-800
                                @elseif($session->status === 'pending') bg-orange-100 text-orange-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick='editSession(@json($session))' class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <form action="{{ route('performance.calibration.destroy', $session->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this calibration session?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No calibration sessions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $sessions->links() }}
        </div>
    </div>
</div>

<x-advanced-modal id="newSessionModal" title="New Calibration Session" icon="users" color="indigo" size="lg">
    <form action="{{ route('performance.calibration.store') }}" method="POST" id="newSessionForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="session_cycle_id" class="block text-sm font-medium text-gray-700">Cycle</label>
                <select id="session_cycle_id" name="cycle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}">{{ $cycle->cycle_name }} ({{ $cycle->period_start?->format('Y-m-d') }} → {{ $cycle->period_end?->format('Y-m-d') }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="session_date" class="block text-sm font-medium text-gray-700">Session Date</label>
                <input type="date" id="session_date" name="session_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="session_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="session_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="planned">Planned</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label for="session_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="session_notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newSessionModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newSessionForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Session</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editSessionModal" title="Edit Calibration Session" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editSessionForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="edit_session_cycle_id" class="block text-sm font-medium text-gray-700">Cycle</label>
                <select id="edit_session_cycle_id" name="cycle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}">{{ $cycle->cycle_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="edit_session_date" class="block text-sm font-medium text-gray-700">Session Date</label>
                <input type="date" id="edit_session_date" name="session_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="edit_session_status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="edit_session_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="planned">Planned</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label for="edit_session_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea id="edit_session_notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editSessionModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editSessionForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Session</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<script>
function editSession(session) {
    document.getElementById('edit_session_cycle_id').value = session.cycle_id;
    document.getElementById('edit_session_date').value = session.session_date || '';
    document.getElementById('edit_session_status').value = session.status;
    document.getElementById('edit_session_notes').value = session.notes || '';
    document.getElementById('editSessionForm').action = '{{ route('performance.calibration.update', 0) }}'.replace(/\/0$/, '/' + session.id);
    openModal('editSessionModal');
}
</script>
@endsection
