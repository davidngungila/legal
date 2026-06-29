@extends('layouts.app')

@section('title', 'Shift Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Shift Management</h1>
            <p class="text-gray-600 mt-2">Manage shift patterns and schedules</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Managing shifts for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" data-bs-toggle="modal" data-bs-target="#createShiftModal">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Shift
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Shifts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_shifts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Shifts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_shifts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Night Shifts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['night_shifts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="moon" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Inactive Shifts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['inactive_shifts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Shifts</label>
                <div class="relative">
                    <input type="text" id="shift-search" placeholder="Search by shift name" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <i data-feather="search" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="shift-status-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select id="shift-type-filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Types</option>
                    <option value="night">Night Shift</option>
                    <option value="day">Day Shift</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Shifts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="shifts-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Break Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowance Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($shiftPatterns as $shift)
                    <tr class="shift-row" 
                        data-shift-name="{{ strtolower($shift->shift_name) }}"
                        data-shift-status="{{ $shift->is_active ? 'active' : 'inactive' }}"
                        data-shift-type="{{ $shift->is_night_shift ? 'night' : 'day' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i data-feather="clock" class="w-5 h-5 text-indigo-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $shift->shift_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $shift->break_duration }} mins
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($shift->allowance_rate, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $shift->is_night_shift ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $shift->is_night_shift ? 'Night' : 'Day' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $shift->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $shift->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-indigo-600 hover:text-indigo-900" data-bs-toggle="modal" data-bs-target="#editShiftModal{{ $shift->id }}">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" onclick="deleteShift({{ $shift->id }})">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="clock" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No shifts found</p>
                                <p class="text-sm text-gray-600 mt-2">Get started by adding your first shift</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Shift Modal -->
    <div class="modal fade" id="createShiftModal" tabindex="-1" aria-labelledby="createShiftModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('attendance.shifts.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold" id="createShiftModalLabel">Add New Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Shift Name <span class="text-red-500">*</span></label>
                                <input type="text" name="shift_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Morning Shift">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Break Duration (Minutes) <span class="text-red-500">*</span></label>
                                <input type="number" name="break_duration" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0" value="60">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Allowance Rate</label>
                                <input type="number" name="allowance_rate" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0">
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_night_shift" id="isNightShift" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="isNightShift" class="ml-2 block text-sm text-gray-700">
                                            Night Shift
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="isActiveShift" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="isActiveShift" class="ml-2 block text-sm text-gray-700">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Shift Modals -->
    @foreach($shiftPatterns as $shift)
    <div class="modal fade" id="editShiftModal{{ $shift->id }}" tabindex="-1" aria-labelledby="editShiftModalLabel{{ $shift->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('attendance.shifts.update', $shift) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title text-lg font-semibold" id="editShiftModalLabel{{ $shift->id }}">Edit Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Shift Name <span class="text-red-500">*</span></label>
                                <input type="text" name="shift_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $shift->shift_name }}">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Break Duration (Minutes) <span class="text-red-500">*</span></label>
                                <input type="number" name="break_duration" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0" value="{{ $shift->break_duration }}">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Allowance Rate</label>
                                <input type="number" name="allowance_rate" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" min="0" value="{{ $shift->allowance_rate }}">
                            </div>
                            
                            <div class="md:col-span-2">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_night_shift" id="isNightShift{{ $shift->id }}" {{ $shift->is_night_shift ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="isNightShift{{ $shift->id }}" class="ml-2 block text-sm text-gray-700">
                                            Night Shift
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="isActiveShift{{ $shift->id }}" {{ $shift->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        <label for="isActiveShift{{ $shift->id }}" class="ml-2 block text-sm text-gray-700">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Update Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Filter functionality
    function filterShifts() {
        const search = document.getElementById('shift-search').value.toLowerCase();
        const status = document.getElementById('shift-status-filter').value;
        const type = document.getElementById('shift-type-filter').value;
        
        document.querySelectorAll('.shift-row').forEach(row => {
            const nameMatch = row.dataset.shiftName.includes(search);
            const statusMatch = !status || row.dataset.shiftStatus === status;
            const typeMatch = !type || row.dataset.shiftType === type;
            
            if (nameMatch && statusMatch && typeMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    document.getElementById('shift-search').addEventListener('input', filterShifts);
    document.getElementById('shift-status-filter').addEventListener('change', filterShifts);
    document.getElementById('shift-type-filter').addEventListener('change', filterShifts);
});

function deleteShift(id) {
    if (confirm('Are you sure you want to delete this shift? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/attendance/shifts/' + id;
        
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
