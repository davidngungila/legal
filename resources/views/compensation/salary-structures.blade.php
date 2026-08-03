@extends('layouts.app')

@section('title', 'Salary Structures - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Salary Structures</h1>
            <p class="text-gray-600 mt-2">Define position-based salary bands and ranges</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createSalaryStructureModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Add Structure
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="layers" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Structures</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</h3>
            <p class="text-gray-600 text-sm">Active Structures</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['avg_mid'], 0) }}</h3>
            <p class="text-gray-600 text-sm">Average Mid Salary</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['positions']) }}</h3>
            <p class="text-gray-600 text-sm">Position Bands</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Salary Bands</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Structure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min (TZS)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mid (TZS)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max (TZS)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($structures as $structure)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $structure->name }}</div>
                            <div class="text-xs text-gray-500">{{ $structure->currency }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $structure->position ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($structure->min_salary, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($structure->mid_salary, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($structure->max_salary, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $structure->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $structure->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editSalaryStructureModal{{ $structure->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteSalaryStructure({{ $structure->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="layers" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No salary structures yet</p>
                                <p class="text-sm text-gray-600 mt-2">Click "Add Structure" to define your first salary band</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Salary Structure Modal -->
<x-advanced-modal id="createSalaryStructureModal" title="Add Salary Structure" description="Define a salary band for a position." icon="plus" color="indigo" size="2xl">
    <form id="createSalaryStructureForm" method="POST" action="{{ route('compensation.salary-structures.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Structure Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Executive Level 1">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <input type="text" name="position" list="positionList" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Select or type a position">
                <datalist id="positionList">
                    @foreach($positions as $position)
                    <option value="{{ $position }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="min_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mid Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="mid_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="max_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="TZS" selected>TZS</option>
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="createIsActive" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="createIsActive" class="ml-2 block text-sm text-gray-700">Structure is active</label>
                </div>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createSalaryStructureModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createSalaryStructureForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Structure</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Salary Structure Modals -->
@foreach($structures as $structure)
<x-advanced-modal id="editSalaryStructureModal{{ $structure->id }}" title="Edit Salary Structure" description="Update the salary band details." icon="edit" color="indigo" size="2xl">
    <form id="editSalaryStructureForm{{ $structure->id }}" method="POST" action="{{ route('compensation.salary-structures.update', $structure->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Structure Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $structure->name }}">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                <input type="text" name="position" list="positionListEdit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $structure->position }}">
                <datalist id="positionListEdit">
                    @foreach($positions as $position)
                    <option value="{{ $position }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Min Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="min_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $structure->min_salary }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mid Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="mid_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $structure->mid_salary }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Salary (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="max_salary" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $structure->max_salary }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="TZS" {{ $structure->currency === 'TZS' ? 'selected' : '' }}>TZS</option>
                    <option value="USD" {{ $structure->currency === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ $structure->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="GBP" {{ $structure->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="editIsActive{{ $structure->id }}" value="1" {{ $structure->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="editIsActive{{ $structure->id }}" class="ml-2 block text-sm text-gray-700">Structure is active</label>
                </div>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editSalaryStructureModal{{ $structure->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editSalaryStructureForm{{ $structure->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function deleteSalaryStructure(id) {
    if (confirm('Are you sure you want to delete this salary structure? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/compensation/salary-structures/${id}`;

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
