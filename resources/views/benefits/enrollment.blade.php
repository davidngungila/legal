@extends('layouts.app')

@section('title', 'Benefits Enrollment - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Benefits Enrollment</h1>
            <p class="text-gray-600 mt-2">Manage employee benefit plan enrollments</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createEnrollmentModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                New Enrollment
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="user-check" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Enrollments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['enrolled']) }}</h3>
            <p class="text-gray-600 text-sm">Enrolled</p>
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
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-gray-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['waived']) }}</h3>
            <p class="text-gray-600 text-sm">Waived</p>
        </div>
    </div>

    <!-- Enrollments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Enrollment Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effective Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->employee->first_name ?? 'Unknown' }} {{ $enrollment->employee->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ $enrollment->employee->employee_id ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->plan->name ?? 'Unknown Plan' }}</div>
                            <div class="text-xs text-gray-500">{{ isset($enrollment->plan->category) ? \App\Models\BenefitPlan::CATEGORIES[$enrollment->plan->category] ?? ucfirst($enrollment->plan->category) : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $enrollment->effective_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div>TZS {{ number_format(floatval($enrollment->employee_cost)) }} <span class="text-xs text-gray-500">/employee</span></div>
                            <div>TZS {{ number_format(floatval($enrollment->employer_cost)) }} <span class="text-xs text-gray-500">/employer</span></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $enrollment->status === 'enrolled' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $enrollment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $enrollment->status === 'waived' ? 'bg-gray-100 text-gray-600' : '' }}
                                {{ $enrollment->status === 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editEnrollmentModal{{ $enrollment->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteEnrollment({{ $enrollment->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="user-check" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No enrollments found</p>
                                <p class="text-sm text-gray-600 mt-2">Click "New Enrollment" to enroll the first employee</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Plan Coverage -->
    @if($plans->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Coverage by Plan</h3>
            <a href="{{ route('benefits.plans') }}" class="text-sm text-indigo-600 font-medium hover:underline">Manage Plans</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($plans as $plan)
            <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $plan->name }}</p>
                    <p class="text-xs text-gray-500">{{ \App\Models\BenefitPlan::CATEGORIES[$plan->category] ?? ucfirst($plan->category) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-gray-900">{{ number_format($planCoverage[$plan->id] ?? 0) }}</p>
                    <p class="text-xs text-gray-500">employees</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Create Enrollment Modal -->
<x-advanced-modal id="createEnrollmentModal" title="New Enrollment" description="Enroll an employee in a benefit plan." icon="plus" color="indigo" size="2xl">
    <form id="createEnrollmentForm" method="POST" action="{{ route('benefits.enrollment.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Plan <span class="text-red-500">*</span></label>
                <select name="plan_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select plan</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ \App\Models\BenefitPlan::CATEGORIES[$plan->category] ?? ucfirst($plan->category) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                <input type="date" name="effective_date" value="{{ now()->toDateString() }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitEnrollment::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="employee_cost" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employer Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="employer_cost" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createEnrollmentModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createEnrollmentForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Enrollment</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Enrollment Modals -->
@foreach($enrollments as $enrollment)
<x-advanced-modal id="editEnrollmentModal{{ $enrollment->id }}" title="Edit Enrollment" description="Update the enrollment details." icon="edit" color="indigo" size="2xl">
    <form id="editEnrollmentForm{{ $enrollment->id }}" method="POST" action="{{ route('benefits.enrollment.update', $enrollment->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $enrollment->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Plan <span class="text-red-500">*</span></label>
                <select name="plan_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ $enrollment->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }} ({{ \App\Models\BenefitPlan::CATEGORIES[$plan->category] ?? ucfirst($plan->category) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Effective Date</label>
                <input type="date" name="effective_date" value="{{ $enrollment->effective_date?->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitEnrollment::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ $enrollment->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="employee_cost" value="{{ floatval($enrollment->employee_cost) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employer Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="employer_cost" value="{{ floatval($enrollment->employer_cost) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editEnrollmentModal{{ $enrollment->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editEnrollmentForm{{ $enrollment->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function deleteEnrollment(id) {
    if (confirm('Are you sure you want to remove this enrollment? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/benefits/enrollment/${id}`;

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
