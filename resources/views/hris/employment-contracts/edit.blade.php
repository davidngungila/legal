@extends('layouts.app')

@section('title', 'Edit Employment Contract - Orvion HRIS')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit Employment Contract</h1>
            <p class="text-gray-600 mt-2">
                {{ $contract->formatted_contract_number }}
                <span class="px-2 py-0.5 ml-2 bg-{{ $contract->status_badge_color }}-100 text-{{ $contract->status_badge_color }}-700 rounded-full text-xs font-medium uppercase">{{ $contract->effective_status }}</span>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('employment-contracts.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back to Contracts
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employment-contracts.update', $contract->id) }}" enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        @csrf
        @method('PUT')

        @include('hris.employment-contracts._form-fields', ['contract' => $contract, 'employees' => $employees])

        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('employment-contracts.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function initContractAutoFillEdit() {
    const employeeSelect = document.getElementById('contract_employee_id');
    if (!employeeSelect) return;

    function fillFromEmployee(option) {
        if (!option || !option.value) return;

        const values = {
            job_title: option.dataset.position || '',
            department: option.dataset.department || '',
            basic_salary: option.dataset.salary || '',
            salary_currency: option.dataset.currency || 'TZS',
            payment_frequency: option.dataset.paymentFrequency || '',
            bank_name: option.dataset.bankName || '',
            bank_account_number: option.dataset.bankAccount || '',
            bank_account_name: option.dataset.fullName || '',
            effective_date: option.dataset.hireDate || '',
            probation_end_date: option.dataset.probationEndDate || '',
            work_schedule: option.dataset.workSchedule || '',
            work_location: [option.dataset.region, option.dataset.city, option.dataset.address]
                .filter(Boolean).join(', '),
            reporting_line: option.dataset.reportingTo || ''
        };

        ['job_title', 'department', 'basic_salary', 'salary_currency', 'payment_frequency',
         'bank_name', 'bank_account_number', 'bank_account_name', 'effective_date',
         'probation_end_date', 'work_schedule', 'work_location', 'reporting_line'
        ].forEach(name => {
            const field = employeeSelect.form.querySelector(`[name="${name}"]`);
            if (field && values[name] !== undefined) {
                field.value = values[name];
            }
        });
    }

    employeeSelect.addEventListener('change', () => {
        fillFromEmployee(employeeSelect.selectedOptions[0]);
    });

    fillFromEmployee(employeeSelect.selectedOptions[0]);
}

initContractAutoFillEdit();
</script>
@endpush
