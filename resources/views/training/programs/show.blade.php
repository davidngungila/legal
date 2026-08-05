@extends('layouts.app')

@section('title', $program->name . ' - Training Program')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <a href="{{ route('training.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-1"></i> Back to Programs
            </a>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope mt-2">{{ $program->name }}</h1>
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">{{ $program->category ?: 'General' }}</span>
                <span class="px-2 py-1 text-sm font-medium rounded-full {{ $program->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($program->status) }}</span>
                @if($program->is_certification)
                <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                    <i data-feather="award" class="w-3 h-3 mr-1"></i> Certification
                </span>
                @endif
            </div>
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openModal('newSessionModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Schedule Session
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $program->sessions->count() }}</h3>
            <p class="text-gray-600 text-sm">Total Sessions</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $program->sessions->sum(function ($s) { return $s->enrollments->count(); }) }}</h3>
            <p class="text-gray-600 text-sm">Total Enrollments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($program->cost, 0) }} {{ $program->currency }}</h3>
            <p class="text-gray-600 text-sm">Cost</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $program->duration_hours }} hrs</h3>
            <p class="text-gray-600 text-sm">Duration</p>
        </div>
    </div>

    @if($program->description)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">About this program</h3>
        <p class="text-gray-700">{{ $program->description }}</p>
        @if($program->provider)
        <p class="text-sm text-gray-500 mt-3">Provider: <span class="font-medium text-gray-700">{{ $program->provider }}</span></p>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Sessions</h3>
        </div>
        @if($program->sessions->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrolled</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($program->sessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <a href="{{ route('training.sessions.show', $session->id) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $session->title }}</a>
                            @if($session->instructor)
                            <p class="text-xs text-gray-500">{{ $session->instructor }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $session->plan?->name ?: '—' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if($session->start_at)
                            <div>{{ $session->start_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $session->start_at->format('H:i') }} - {{ $session->end_at ? $session->end_at->format('H:i') : '' }}</div>
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $session->venue ?: '—' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $session->enrollments->count() }}{{ $session->capacity ? ' / ' . $session->capacity : '' }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : ($session->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : ($session->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">{{ ucwords(str_replace('_', ' ', $session->status)) }}</span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('training.sessions.show', $session->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage</a>
                                <button onclick='editSession(@json($session))' class="text-gray-600 hover:text-gray-800 text-sm">Edit</button>
                                <form action="{{ route('training.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Delete this session and all its enrollments?');">
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
        @else
        <div class="text-center text-gray-500 py-12">No sessions scheduled yet. Click "Schedule Session" to add one.</div>
        @endif
    </div>
</div>

<x-advanced-modal id="newSessionModal" title="Schedule Training Session" icon="calendar" color="indigo" size="lg">
    <form action="{{ route('training.sessions.store', $program->id) }}" method="POST" id="newSessionForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Session Title</label>
                <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Training Plan</label>
                    <select name="plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No plan</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Instructor</label>
                    <input type="text" name="instructor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Venue</label>
                    <input type="text" name="venue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start</label>
                    <input type="datetime-local" name="start_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End</label>
                    <input type="datetime-local" name="end_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="scheduled">Scheduled</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newSessionModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newSessionForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Schedule Session</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<x-advanced-modal id="editSessionModal" title="Edit Training Session" icon="edit-3" color="indigo" size="lg">
    <form action="" method="POST" id="editSessionForm">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Session Title</label>
                <input type="text" id="sess_title" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Training Plan</label>
                    <select id="sess_plan_id" name="plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No plan</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" id="sess_capacity" name="capacity" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Instructor</label>
                    <input type="text" id="sess_instructor" name="instructor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Venue</label>
                    <input type="text" id="sess_venue" name="venue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start</label>
                    <input type="datetime-local" id="sess_start_at" name="start_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End</label>
                    <input type="datetime-local" id="sess_end_at" name="end_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select id="sess_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="scheduled">Scheduled</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
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
    document.getElementById('sess_title').value = session.title;
    document.getElementById('sess_plan_id').value = session.plan_id || '';
    document.getElementById('sess_capacity').value = session.capacity || 0;
    document.getElementById('sess_instructor').value = session.instructor || '';
    document.getElementById('sess_venue').value = session.venue || '';
    document.getElementById('sess_start_at').value = session.start_at ? session.start_at.replace(' ', 'T') : '';
    document.getElementById('sess_end_at').value = session.end_at ? session.end_at.replace(' ', 'T') : '';
    document.getElementById('sess_status').value = session.status || 'scheduled';
    document.getElementById('editSessionForm').action = '{{ route('training.sessions.update', 0) }}'.replace(/\/0$/, '/' + session.id);
    openModal('editSessionModal');
}
</script>
@endsection
