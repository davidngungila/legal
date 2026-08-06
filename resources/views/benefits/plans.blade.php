@extends('layouts.app')

@section('title', 'Benefits Plans - LegalHR Tanzania')

@section('content')
@php
$categoryStyles = [
    'health' => ['icon' => 'heart', 'wrap' => 'from-green-50 to-teal-50 border-green-200', 'box' => 'bg-green-100', 'text' => 'text-green-600'],
    'retirement' => ['icon' => 'dollar-sign', 'wrap' => 'from-blue-50 to-indigo-50 border-blue-200', 'box' => 'bg-blue-100', 'text' => 'text-blue-600'],
    'wellness' => ['icon' => 'activity', 'wrap' => 'from-purple-50 to-pink-50 border-purple-200', 'box' => 'bg-purple-100', 'text' => 'text-purple-600'],
    'additional' => ['icon' => 'gift', 'wrap' => 'from-yellow-50 to-orange-50 border-yellow-200', 'box' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
];
$categories = array_keys(\App\Models\BenefitPlan::CATEGORIES);
@endphp
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Benefits Plans</h1>
            <p class="text-gray-600 mt-2">Manage company benefits offerings</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createPlanModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Create Plan
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="package" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_plans']) }}</h3>
            <p class="text-gray-600 text-sm">Total Plans</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_plans']) }}</h3>
            <p class="text-gray-600 text-sm">Active Plans</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</h3>
            <p class="text-gray-600 text-sm">Total Employees</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['employees_with_benefits']) }}</h3>
            <p class="text-gray-600 text-sm">Employees with Benefits</p>
        </div>
    </div>

    <!-- Plan Categories -->
    @foreach($categories as $category)
    @php $style = $categoryStyles[$category] ?? $categoryStyles['health']; $categoryPlans = $grouped->get($category, collect()); @endphp
    <div class="bg-gradient-to-br {{ $style['wrap'] }} rounded-xl border p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 {{ $style['box'] }} rounded-lg flex items-center justify-center">
                    <i data-feather="{{ $style['icon'] }}" class="w-6 h-6 {{ $style['text'] }}"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">{{ \App\Models\BenefitPlan::CATEGORIES[$category] }}</h3>
                    <p class="text-sm text-gray-600">{{ $categoryPlans->count() }} plan{{ $categoryPlans->count() == 1 ? '' : 's' }}</p>
                </div>
            </div>
            <span class="text-sm text-gray-600">{{ number_format($categoryCoverage[$category] ?? 0) }} employees covered</span>
        </div>

        @if($categoryPlans->isEmpty())
        <p class="text-sm text-gray-500 bg-white/60 rounded-lg px-4 py-3">No plans in this category yet.</p>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryPlans as $plan)
            @php
                $costText = $plan->mandatory
                    ? 'Mandatory'
                    : (floatval($plan->cost) > 0
                        ? 'TZS ' . number_format(floatval($plan->cost)) . ' ' . (\App\Models\BenefitPlan::COST_PERIODS[$plan->cost_period] ?? '')
                        : 'Free');
            @endphp
            <div class="p-4 bg-white rounded-lg border border-gray-200">
                <div class="flex items-start justify-between mb-1">
                    <h4 class="font-medium text-gray-900">{{ $plan->name }}</h4>
                    <div class="flex items-center space-x-2">
                        <button onclick="openModal('editPlanModal{{ $plan->id }}')" class="text-blue-600 hover:text-blue-900" title="Edit">
                            <i data-feather="edit-2" class="w-4 h-4"></i>
                        </button>
                        <button onclick="deletePlan({{ $plan->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                @if($plan->provider)
                <p class="text-xs text-gray-500 mb-1">{{ $plan->provider }}</p>
                @endif
                @if($plan->description)
                <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $plan->description }}</p>
                @endif
                <div class="flex items-center justify-between mt-3">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $costText }}
                        @if($plan->coverage)
                        <span class="text-xs text-gray-500 font-normal">• {{ $plan->coverage }}</span>
                        @endif
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $plan->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($plan->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach

    <!-- Enrollment Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Enrollment Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Employees with Benefits</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['employees_with_benefits']) }}</p>
            </div>
            @foreach($categories as $category)
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">{{ \App\Models\BenefitPlan::CATEGORIES[$category] }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($categoryCoverage[$category] ?? 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Create Plan Modal -->
<x-advanced-modal id="createPlanModal" title="Create Benefit Plan" description="Add a new benefits offering for this client." icon="plus" color="indigo" size="2xl">
    <form id="createPlanForm" method="POST" action="{{ route('benefits.plans.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Comprehensive Health Plan">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitPlan::CATEGORIES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                <input type="text" name="provider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Jubilee Insurance">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="cost" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Period</label>
                <select name="cost_period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitPlan::COST_PERIODS as $key => $label)
                    <option value="{{ $key }}" {{ $key === 'monthly' ? 'selected' : '' }}>{{ $label ?: 'One-off' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Coverage</label>
                <input type="text" name="coverage" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 80% coverage">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="mandatory" value="1" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Mandatory plan (e.g. NSSF, statutory schemes)</span>
                </label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Details about what this plan covers..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createPlanModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createPlanForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Plan</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Plan Modals -->
@foreach($plans as $plan)
<x-advanced-modal id="editPlanModal{{ $plan->id }}" title="Edit Benefit Plan" description="Update the plan details." icon="edit" color="indigo" size="2xl">
    <form id="editPlanForm{{ $plan->id }}" method="POST" action="{{ route('benefits.plans.update', $plan->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Plan Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ $plan->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitPlan::CATEGORIES as $key => $label)
                    <option value="{{ $key }}" {{ $plan->category === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                <input type="text" name="provider" value="{{ $plan->provider }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost (TZS)</label>
                <input type="number" step="0.01" min="0" name="cost" value="{{ floatval($plan->cost) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Period</label>
                <select name="cost_period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\BenefitPlan::COST_PERIODS as $key => $label)
                    <option value="{{ $key }}" {{ $plan->cost_period === $key ? 'selected' : '' }}>{{ $label ?: 'One-off' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Coverage</label>
                <input type="text" name="coverage" value="{{ $plan->coverage }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $plan->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center space-x-3">
                    <input type="checkbox" name="mandatory" value="1" {{ $plan->mandatory ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Mandatory plan (e.g. NSSF, statutory schemes)</span>
                </label>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $plan->description }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editPlanModal{{ $plan->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editPlanForm{{ $plan->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function deletePlan(id) {
    if (confirm('Are you sure you want to delete this benefit plan? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/benefits/plans/${id}`;

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
