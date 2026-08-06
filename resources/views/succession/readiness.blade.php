@extends('layouts.app')

@section('title', 'Readiness Assessment - Succession Planning - LegalHR Tanzania')

@section('content')
@php
$readinessStyles = [
    'ready_now' => 'bg-green-100 text-green-700',
    'ready_1_2' => 'bg-yellow-100 text-yellow-700',
    'ready_2_3' => 'bg-blue-100 text-blue-700',
    'development' => 'bg-gray-100 text-gray-600',
];
@endphp
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Readiness Assessment</h1>
            <p class="text-gray-600 mt-2">Evaluate employee readiness for key roles</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <form method="GET" action="{{ route('succession.readiness') }}" class="flex items-center gap-2">
                <select name="department" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ $department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('succession.readiness.export', ['department' => $department]) }}" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                <i data-feather="download" class="w-4 h-4"></i>
                Export Report
            </a>
            <button onclick="openModal('createReadinessModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                New Assessment
            </button>
        </div>
    </div>

    <!-- Readiness Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">{{ number_format($stats['ready_now']) }}</div>
            <p class="text-green-100">Ready Now</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">{{ number_format($stats['ready_1_2']) }}</div>
            <p class="text-yellow-100">1-2 Years</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">{{ number_format($stats['ready_2_3']) }}</div>
            <p class="text-blue-100">2-3 Years</p>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl p-6 text-white">
            <div class="text-4xl font-bold mb-2">{{ number_format($stats['development']) }}</div>
            <p class="text-gray-100">Development Needed</p>
        </div>
    </div>

    <!-- Readiness Matrix -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Readiness Matrix</h3>
            <span class="text-sm text-gray-500">{{ $assessments->count() }} assessments</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talent Pool</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readiness</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Development Needs</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assessments as $assessment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($assessment->employee->first_name ?? 'E', 0, 1)) }}{{ strtoupper(substr($assessment->employee->last_name ?? 'E', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $assessment->employee->first_name ?? 'Unknown' }} {{ $assessment->employee->last_name ?? '' }}</div>
                                    <div class="text-xs text-gray-500">{{ $assessment->employee->department ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assessment->current_role ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assessment->target_role ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assessment->pool->name ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $readinessStyles[$assessment->readiness] ?? $readinessStyles['development'] }}">
                                {{ \App\Models\SuccessionReadiness::READINESS[$assessment->readiness] ?? ucfirst($assessment->readiness) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $assessment->development_needs ?: '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assessment->assessment_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('editReadinessModal{{ $assessment->id }}')" class="text-indigo-600 hover:text-indigo-900">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteReadiness({{ $assessment->id }})" class="text-red-600 hover:text-red-900">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="check-circle" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No readiness assessments found</p>
                                <p class="text-sm text-gray-600 mt-2">Click "New Assessment" to evaluate the first employee</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Readiness Modal -->
<x-advanced-modal id="createReadinessModal" title="New Readiness Assessment" description="Assess an employee's readiness for a target role." icon="plus" color="indigo" size="2xl">
    <form id="createReadinessForm" method="POST" action="{{ route('succession.readiness.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" id="createReadinessEmployee" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-position="{{ $employee->position }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Talent Pool</label>
                <select name="pool_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">No pool</option>
                    @foreach($pools as $pool)
                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Role</label>
                <input type="text" name="current_role" id="createReadinessRole" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Auto-filled from employee">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Role</label>
                <input type="text" name="target_role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Head of IT">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Readiness <span class="text-red-500">*</span></label>
                <select name="readiness" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\SuccessionReadiness::READINESS as $key => $label)
                    <option value="{{ $key }}" {{ $key === 'development' ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Date</label>
                <input type="date" name="assessment_date" value="{{ now()->toDateString() }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Development Needs</label>
                <textarea name="development_needs" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Skills, training or experience required..."></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createReadinessModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="createReadinessForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Assessment</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Readiness Modals -->
@foreach($assessments as $assessment)
<x-advanced-modal id="editReadinessModal{{ $assessment->id }}" title="Edit Readiness Assessment" description="Update the readiness assessment." icon="edit" color="indigo" size="2xl">
    <form id="editReadinessForm{{ $assessment->id }}" method="POST" action="{{ route('succession.readiness.update', $assessment->id) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $assessment->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Talent Pool</label>
                <select name="pool_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">No pool</option>
                    @foreach($pools as $pool)
                    <option value="{{ $pool->id }}" {{ $assessment->pool_id == $pool->id ? 'selected' : '' }}>{{ $pool->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Role</label>
                <input type="text" name="current_role" value="{{ $assessment->current_role }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Role</label>
                <input type="text" name="target_role" value="{{ $assessment->target_role }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Readiness <span class="text-red-500">*</span></label>
                <select name="readiness" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(\App\Models\SuccessionReadiness::READINESS as $key => $label)
                    <option value="{{ $key }}" {{ $assessment->readiness === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Date</label>
                <input type="date" name="assessment_date" value="{{ $assessment->assessment_date?->format('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active" {{ $assessment->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="archived" {{ $assessment->status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Development Needs</label>
                <textarea name="development_needs" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $assessment->development_needs }}</textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editReadinessModal{{ $assessment->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
            <button type="submit" form="editReadinessForm{{ $assessment->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endforeach

@push('scripts')
<script>
function deleteReadiness(id) {
    if (confirm('Are you sure you want to delete this readiness assessment? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/readiness/${id}`;

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

const readinessEmployeeSelect = document.getElementById('createReadinessEmployee');
if (readinessEmployeeSelect) {
    readinessEmployeeSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('createReadinessRole').value = selected.getAttribute('data-position') || '';
    });
}
</script>
@endpush
@endsection
