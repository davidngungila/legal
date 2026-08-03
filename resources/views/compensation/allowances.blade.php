@extends('layouts.app')

@section('title', 'Allowances - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Allowances</h1>
            <p class="text-gray-600 mt-2">Configure fixed or percentage-based allowances</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createAllowanceModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Add Allowance
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Allowances</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</h3>
            <p class="text-gray-600 text-sm">Active</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['monthly_cost'], 0) }}</h3>
            <p class="text-gray-600 text-sm">Monthly Allowance Cost</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="percent" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['taxable']) }}</h3>
            <p class="text-gray-600 text-sm">Taxable Allowances</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Allowance Configurations</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxable</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allowances as $allowance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $allowance->name }}</div>
                            <div class="text-xs text-gray-500">{{ $allowance->currency }} · {{ $allowance->effective_date?->format('Y-m-d') ?? 'No start date' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $allowance->employee ? $allowance->employee->first_name . ' ' . $allowance->employee->last_name : 'All Employees' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $allowance->type === 'fixed' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ ucfirst($allowance->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $allowance->type === 'fixed' ? 'TZS ' . number_format($allowance->amount, 0) : number_format($allowance->percentage, 1) . '%' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($allowance->frequency) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $allowance->is_taxable ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $allowance->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $allowance->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editAllowanceModal{{ $allowance->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteAllowance({{ $allowance->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="gift" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No allowances configured</p>
                                <p class="text-sm text-gray-600 mt-2">Click "Add Allowance" to configure the first one</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Allowance Modal -->
<x-advanced-modal id="createAllowanceModal" title="Add Allowance" description="Configure a fixed or percentage allowance." icon="plus" color="indigo" size="2xl">
    <form id="createAllowanceForm" method="POST" action="{{ route('compensation.allowances.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Allowance Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Transport Allowance">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees (Company-wide)</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                <select name="type" id="createAllowanceType" onchange="toggleCreateAllowanceFields()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="fixed">Fixed Amount</option>
                    <option value="percentage">Percentage</option>
                </select>
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
            <div id="createAllowanceAmountField">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div id="createAllowancePercentageField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="percentage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 10">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                <select name="frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="monthly" selected>Monthly</option>
                    <option value="bi-weekly">Bi-Weekly</option>
                    <option value="weekly">Weekly</option>
                    <option value="annual">Annual</option>
                    <option value="one-time">One-Time</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                <input type="date" name="effective_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="md:col-span-2 flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_taxable" id="createIsTaxable" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="createIsTaxable" class="ml-2 block text-sm text-gray-700">Taxable</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="createIsActive" value="1" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="createIsActive" class="ml-2 block text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Notes about this allowance..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createAllowanceModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createAllowanceForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Allowance</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Allowance Modals -->
@foreach($allowances as $allowance)
<x-advanced-modal id="editAllowanceModal{{ $allowance->id }}" title="Edit Allowance" description="Update the allowance configuration." icon="edit" color="indigo" size="2xl">
    <form id="editAllowanceForm{{ $allowance->id }}" method="POST" action="{{ route('compensation.allowances.update', $allowance->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Allowance Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $allowance->name }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Employees (Company-wide)</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $allowance->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                <select name="type" id="editAllowanceType{{ $allowance->id }}" onchange="toggleEditAllowanceFields({{ $allowance->id }})" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="fixed" {{ $allowance->type === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    <option value="percentage" {{ $allowance->type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="TZS" {{ $allowance->currency === 'TZS' ? 'selected' : '' }}>TZS</option>
                    <option value="USD" {{ $allowance->currency === 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ $allowance->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                    <option value="GBP" {{ $allowance->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                </select>
            </div>
            <div id="editAllowanceAmountField{{ $allowance->id }}" class="{{ $allowance->type === 'percentage' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $allowance->amount }}">
            </div>
            <div id="editAllowancePercentageField{{ $allowance->id }}" class="{{ $allowance->type === 'percentage' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="percentage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $allowance->percentage }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                <select name="frequency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="monthly" {{ $allowance->frequency === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="bi-weekly" {{ $allowance->frequency === 'bi-weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                    <option value="weekly" {{ $allowance->frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="annual" {{ $allowance->frequency === 'annual' ? 'selected' : '' }}>Annual</option>
                    <option value="one-time" {{ $allowance->frequency === 'one-time' ? 'selected' : '' }}>One-Time</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                <input type="date" name="effective_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $allowance->effective_date?->format('Y-m-d') }}">
            </div>
            <div class="md:col-span-2 flex items-center space-x-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_taxable" id="editIsTaxable{{ $allowance->id }}" value="1" {{ $allowance->is_taxable ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="editIsTaxable{{ $allowance->id }}" class="ml-2 block text-sm text-gray-700">Taxable</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="editIsActive{{ $allowance->id }}" value="1" {{ $allowance->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="editIsActive{{ $allowance->id }}" class="ml-2 block text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $allowance->description }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editAllowanceModal{{ $allowance->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editAllowanceForm{{ $allowance->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function toggleCreateAllowanceFields() {
    const type = document.getElementById('createAllowanceType').value;
    document.getElementById('createAllowanceAmountField').classList.toggle('hidden', type === 'percentage');
    document.getElementById('createAllowancePercentageField').classList.toggle('hidden', type !== 'percentage');
}

function toggleEditAllowanceFields(id) {
    const type = document.getElementById('editAllowanceType' + id).value;
    document.getElementById('editAllowanceAmountField' + id).classList.toggle('hidden', type === 'percentage');
    document.getElementById('editAllowancePercentageField' + id).classList.toggle('hidden', type !== 'percentage');
}

function deleteAllowance(id) {
    if (confirm('Are you sure you want to delete this allowance? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/compensation/allowances/${id}`;

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
