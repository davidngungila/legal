@extends('layouts.app')

@section('title', 'Life Events - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Life Events</h1>
            <p class="text-gray-600 mt-2">Track significant employee life events for benefits administration</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createLifeEventModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                New Life Event
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Events</p>
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
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="loader" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['processing']) }}</h3>
            <p class="text-gray-600 text-sm">Processing</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['completed']) }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Life Event Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lifeEvents as $lifeEvent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $lifeEvent->employee->first_name ?? 'Unknown' }} {{ $lifeEvent->employee->last_name ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $lifeEvent->employee->employee_id ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $lifeEvent->event_type)) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $lifeEvent->event_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $lifeEvent->description ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $lifeEvent->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $lifeEvent->status === 'processing' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $lifeEvent->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $lifeEvent->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($lifeEvent->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editLifeEventModal{{ $lifeEvent->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteLifeEvent({{ $lifeEvent->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="calendar" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No life events recorded</p>
                                <p class="text-sm text-gray-600 mt-2">Click "New Life Event" to record the first event</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Life Event Modal -->
<x-advanced-modal id="createLifeEventModal" title="New Life Event" description="Record an employee life event for benefits processing." icon="plus" color="indigo" size="2xl">
    <form id="createLifeEventForm" method="POST" action="{{ route('benefits.life-events.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type <span class="text-red-500">*</span></label>
                <select name="event_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="birth">Birth</option>
                    <option value="marriage">Marriage</option>
                    <option value="adoption">Adoption</option>
                    <option value="retirement">Retirement</option>
                    <option value="bereavement">Bereavement</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Details of the life event..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createLifeEventModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createLifeEventForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Event</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Life Event Modals -->
@foreach($lifeEvents as $lifeEvent)
<x-advanced-modal id="editLifeEventModal{{ $lifeEvent->id }}" title="Edit Life Event" description="Update the life event details." icon="edit" color="indigo" size="2xl">
    <form id="editLifeEventForm{{ $lifeEvent->id }}" method="POST" action="{{ route('benefits.life-events.update', $lifeEvent->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $lifeEvent->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type <span class="text-red-500">*</span></label>
                <select name="event_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="birth" {{ $lifeEvent->event_type === 'birth' ? 'selected' : '' }}>Birth</option>
                    <option value="marriage" {{ $lifeEvent->event_type === 'marriage' ? 'selected' : '' }}>Marriage</option>
                    <option value="adoption" {{ $lifeEvent->event_type === 'adoption' ? 'selected' : '' }}>Adoption</option>
                    <option value="retirement" {{ $lifeEvent->event_type === 'retirement' ? 'selected' : '' }}>Retirement</option>
                    <option value="bereavement" {{ $lifeEvent->event_type === 'bereavement' ? 'selected' : '' }}>Bereavement</option>
                    <option value="other" {{ $lifeEvent->event_type === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $lifeEvent->event_date?->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="pending" {{ $lifeEvent->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $lifeEvent->status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $lifeEvent->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $lifeEvent->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $lifeEvent->description }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editLifeEventModal{{ $lifeEvent->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editLifeEventForm{{ $lifeEvent->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function deleteLifeEvent(id) {
    if (confirm('Are you sure you want to delete this life event? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/benefits/life-events/${id}`;

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
