@extends('layouts.app')

@section('title', 'Loans - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employee Loans</h1>
            <p class="text-gray-600 mt-2">Manage employee loan advances and repayments</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createLoanModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                New Loan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Loans</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="activity" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</h3>
            <p class="text-gray-600 text-sm">Active Loans</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['settled']) }}</h3>
            <p class="text-gray-600 text-sm">Settled</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['outstanding'], 0) }}</h3>
            <p class="text-gray-600 text-sm">Outstanding Balance</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Loan Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $loan->employee->first_name ?? 'Unknown' }} {{ $loan->employee->last_name ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $loan->employee->employee_id ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $loan->loan_type)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">TZS {{ number_format($loan->principal_amount, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">TZS {{ number_format($loan->installment_amount, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">TZS {{ number_format($loan->remaining_balance, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loan->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $loan->status === 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $loan->status === 'settled' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $loan->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $loan->status === 'defaulted' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editLoanModal{{ $loan->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteLoan({{ $loan->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="file-text" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No loans recorded</p>
                                <p class="text-sm text-gray-600 mt-2">Click "New Loan" to record the first employee loan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Loan Modal -->
<x-advanced-modal id="createLoanModal" title="New Loan" description="Record a loan with repayment schedule." icon="plus" color="indigo" size="2xl">
    <form id="createLoanForm" method="POST" action="{{ route('compensation.loans.store') }}">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Loan Type <span class="text-red-500">*</span></label>
                <select name="loan_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="personal">Personal</option>
                    <option value="emergency">Emergency</option>
                    <option value="education">Education</option>
                    <option value="medical">Medical</option>
                    <option value="housing">Housing</option>
                    <option value="vehicle">Vehicle</option>
                    <option value="salary_advance">Salary Advance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Principal Amount (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="principal_amount" id="createLoanPrincipal" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="interest_rate" id="createLoanInterest" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Installment Amount (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="installment_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active">Active</option>
                    <option value="overdue">Overdue</option>
                    <option value="settled">Settled</option>
                    <option value="defaulted">Defaulted</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <p id="createLoanPreview" class="text-sm text-gray-500"></p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Additional notes..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createLoanModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createLoanForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Loan</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Loan Modals -->
@foreach($loans as $loan)
<x-advanced-modal id="editLoanModal{{ $loan->id }}" title="Edit Loan" description="Update the loan details." icon="edit" color="indigo" size="2xl">
    <form id="editLoanForm{{ $loan->id }}" method="POST" action="{{ route('compensation.loans.update', $loan->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $loan->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loan Type <span class="text-red-500">*</span></label>
                <select name="loan_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="personal" {{ $loan->loan_type === 'personal' ? 'selected' : '' }}>Personal</option>
                    <option value="emergency" {{ $loan->loan_type === 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="education" {{ $loan->loan_type === 'education' ? 'selected' : '' }}>Education</option>
                    <option value="medical" {{ $loan->loan_type === 'medical' ? 'selected' : '' }}>Medical</option>
                    <option value="housing" {{ $loan->loan_type === 'housing' ? 'selected' : '' }}>Housing</option>
                    <option value="vehicle" {{ $loan->loan_type === 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                    <option value="salary_advance" {{ $loan->loan_type === 'salary_advance' ? 'selected' : '' }}>Salary Advance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Principal Amount (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="principal_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->principal_amount }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" name="interest_rate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->interest_rate }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Installment Amount (TZS) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="installment_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->installment_amount }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remaining Balance (TZS)</label>
                <input type="number" step="0.01" min="0" name="remaining_balance" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->remaining_balance }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->start_date?->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                <input type="date" name="end_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ $loan->end_date?->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active" {{ $loan->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="overdue" {{ $loan->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="settled" {{ $loan->status === 'settled' ? 'selected' : '' }}>Settled</option>
                    <option value="defaulted" {{ $loan->status === 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $loan->notes }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editLoanModal{{ $loan->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editLoanForm{{ $loan->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const principal = document.getElementById('createLoanPrincipal');
    const interest = document.getElementById('createLoanInterest');
    const preview = document.getElementById('createLoanPreview');

    function updatePreview() {
        const p = parseFloat(principal.value || 0);
        const r = parseFloat(interest.value || 0);
        if (p > 0) {
            const total = p + (p * r / 100);
            preview.textContent = `Total repayable (principal + ${r}% interest): TZS ${Number(total).toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            preview.className = 'text-sm text-gray-600';
        } else {
            preview.textContent = '';
        }
    }

    if (principal) principal.addEventListener('input', updatePreview);
    if (interest) interest.addEventListener('input', updatePreview);
});
</script>
<script>
function deleteLoan(id) {
    if (confirm('Are you sure you want to delete this loan record? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/compensation/loans/${id}`;

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
