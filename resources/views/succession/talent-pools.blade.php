@extends('layouts.app')

@section('title', 'Talent Pools - Succession Planning - LegalHR Tanzania')

@section('content')
@php
$typeStyles = [
    'high_potential' => ['icon' => 'award', 'wrap' => 'from-blue-50 to-indigo-50 border-blue-200', 'box' => 'bg-blue-100', 'text' => 'text-blue-600', 'badge' => 'bg-blue-100 text-blue-700'],
    'future_leader' => ['icon' => 'users', 'wrap' => 'from-purple-50 to-pink-50 border-purple-200', 'box' => 'bg-purple-100', 'text' => 'text-purple-600', 'badge' => 'bg-purple-100 text-purple-700'],
    'leadership' => ['icon' => 'user-check', 'wrap' => 'from-indigo-50 to-blue-50 border-indigo-200', 'box' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-700'],
    'key_role' => ['icon' => 'target', 'wrap' => 'from-green-50 to-teal-50 border-green-200', 'box' => 'bg-green-100', 'text' => 'text-green-600', 'badge' => 'bg-green-100 text-green-700'],
    'technical' => ['icon' => 'cpu', 'wrap' => 'from-cyan-50 to-blue-50 border-cyan-200', 'box' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'badge' => 'bg-cyan-100 text-cyan-700'],
    'emerging' => ['icon' => 'trending-up', 'wrap' => 'from-orange-50 to-yellow-50 border-orange-200', 'box' => 'bg-orange-100', 'text' => 'text-orange-600', 'badge' => 'bg-orange-100 text-orange-700'],
    'custom' => ['icon' => 'layers', 'wrap' => 'from-gray-50 to-gray-100 border-gray-200', 'box' => 'bg-gray-100', 'text' => 'text-gray-600', 'badge' => 'bg-gray-100 text-gray-700'],
];
$readinessBadges = [
    'ready_now' => 'bg-green-100 text-green-800',
    'ready_1_2' => 'bg-blue-100 text-blue-800',
    'developing' => 'bg-yellow-100 text-yellow-800',
    'not_ready' => 'bg-gray-100 text-gray-600',
];
@endphp
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Talent Pools</h1>
            <p class="text-gray-600 mt-2">Identify, track and develop future leaders for key roles</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="openModal('createTalentPoolModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Create Talent Pool
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-feather="layers" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_pools']) }}</h3>
            <p class="text-gray-600 text-sm">Total Pools</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_members']) }}</h3>
            <p class="text-gray-600 text-sm">Total Members</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="award" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['high_potentials']) }}</h3>
            <p class="text-gray-600 text-sm">High Potentials</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="zap" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['ready_now']) }}</h3>
            <p class="text-gray-600 text-sm">Ready Now</p>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 max-w-md">
        <input type="text" id="poolSearch" placeholder="Search talent pools..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" oninput="filterPools(this.value)">
    </div>

    <!-- Talent Pool Cards -->
    @forelse($pools as $pool)
    @php $style = $typeStyles[$pool->type] ?? $typeStyles['custom']; $count = $pool->members->count(); $ready = $pool->members->whereIn('readiness', ['ready_now', 'ready_1_2'])->count(); $pct = $count ? round($ready / $count * 100) : 0; @endphp
    <div class="bg-gradient-to-br {{ $style['wrap'] }} rounded-xl border p-6 mb-6 pool-card" data-name="{{ strtolower($pool->name) }}">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 {{ $style['box'] }} rounded-lg flex items-center justify-center">
                    <i data-feather="{{ $style['icon'] }}" class="w-6 h-6 {{ $style['text'] }}"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $pool->name }}</h3>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $style['badge'] }}">{{ \App\Models\TalentPool::TYPES[$pool->type] }}</span>
                    </div>
                    @if($pool->description)
                    <p class="text-sm text-gray-600 mt-1">{{ $pool->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pool->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($pool->status) }}</span>
                <button onclick="openModal('editTalentPoolModal{{ $pool->id }}')" class="text-blue-600 hover:text-blue-900" title="Edit pool">
                    <i data-feather="edit-2" class="w-4 h-4"></i>
                </button>
                <button onclick="deleteTalentPool({{ $pool->id }})" class="text-red-600 hover:text-red-900" title="Delete pool">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">{{ $count }} member{{ $count == 1 ? '' : 's' }}</span>
                <span class="text-sm text-green-600 font-medium">{{ $ready }} ready now</span>
            </div>
            <span class="text-sm text-gray-600">{{ $pct }}% coverage</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-5">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
        </div>

        @if($pool->members->count())
        <div class="space-y-2 mb-4">
            @foreach($pool->members as $member)
            <div class="flex items-center space-x-3 p-3 bg-white/70 rounded-lg">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0">
                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($member->employee->first_name ?? 'T', 0, 1)) }}{{ strtoupper(substr($member->employee->last_name ?? 'P', 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $member->employee->first_name }} {{ $member->employee->last_name }}</p>
                    <p class="text-xs text-gray-500 truncate">
                        {{ $member->employee->position ?: 'No position' }}@if($member->employee->department) • {{ $member->employee->department }}@endif
                    </p>
                </div>
                @if($member->notes)
                <span class="text-xs text-gray-500 max-w-[10rem] truncate hidden md:block" title="{{ $member->notes }}">{{ $member->notes }}</span>
                @endif
                <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap {{ $readinessBadges[$member->readiness] ?? $readinessBadges['developing'] }}">{{ \App\Models\TalentPoolMember::READINESS[$member->readiness] ?? ucfirst($member->readiness) }}</span>
                <form action="{{ route('succession.talent-pools.members.update', $member->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('PATCH')
                    <select name="readiness" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" title="Change readiness">
                        @foreach(\App\Models\TalentPoolMember::READINESS as $key => $label)
                        <option value="{{ $key }}" {{ $member->readiness === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <button onclick="deleteMember({{ $member->id }})" class="text-red-500 hover:text-red-700 shrink-0" title="Remove member">
                    <i data-feather="user-x" class="w-4 h-4"></i>
                </button>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 bg-white/60 rounded-lg px-4 py-3 mb-4">No members yet. Add employees to this pool to start tracking successors.</p>
        @endif

        <button onclick="openModal('addMemberModal{{ $pool->id }}')" class="text-sm text-indigo-600 font-medium hover:underline inline-flex items-center gap-1">
            <i data-feather="plus" class="w-3.5 h-3.5"></i>
            Add Member
        </button>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <i data-feather="users" class="w-12 h-12 text-gray-400 mb-4 mx-auto"></i>
        <p class="text-lg font-medium text-gray-900">No talent pools yet</p>
        <p class="text-sm text-gray-600 mt-2">Click "Create Talent Pool" to identify your first group of future leaders</p>
    </div>
    @endforelse

    <!-- Create Talent Pool Modal -->
    <x-advanced-modal id="createTalentPoolModal" title="Create Talent Pool" description="Define a group to track potential successors." icon="plus" color="indigo" size="2xl">
        <form id="createTalentPoolForm" method="POST" action="{{ route('succession.talent-pools.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pool Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Executive Leadership">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(\App\Models\TalentPool::TYPES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="What is the purpose of this pool?"></textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('createTalentPoolModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="createTalentPoolForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Create Pool</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    <!-- Edit Talent Pool Modals -->
    @foreach($pools as $pool)
    <x-advanced-modal id="editTalentPoolModal{{ $pool->id }}" title="Edit Talent Pool" description="Update the pool details." icon="edit" color="indigo" size="2xl">
        <form id="editTalentPoolForm{{ $pool->id }}" method="POST" action="{{ route('succession.talent-pools.update', $pool->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pool Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ $pool->name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(\App\Models\TalentPool::TYPES as $key => $label)
                        <option value="{{ $key }}" {{ $pool->type === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" {{ $pool->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $pool->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $pool->description }}</textarea>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('editTalentPoolModal{{ $pool->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="editTalentPoolForm{{ $pool->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>

    <!-- Add Member Modals -->
    <x-advanced-modal id="addMemberModal{{ $pool->id }}" title="Add Member to {{ $pool->name }}" description="Add an employee to this talent pool." icon="plus" color="indigo" size="2xl">
        @php $existingIds = $pool->members->pluck('employee_id')->all(); @endphp
        <form id="addMemberForm{{ $pool->id }}" method="POST" action="{{ route('succession.talent-pools.members.store', $pool->id) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                        @if(!in_array($employee->id, $existingIds))
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})@if($employee->position) - {{ $employee->position }}@endif</option>
                        @endif
                        @endforeach
                    </select>
                    @if($employees->count() === count($existingIds))
                    <p class="mt-1 text-xs text-yellow-600">All active employees are already in this pool.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Readiness</label>
                    <select name="readiness" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(\App\Models\TalentPoolMember::READINESS as $key => $label)
                        <option value="{{ $key }}" {{ $key === 'developing' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <input type="text" name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional notes">
                </div>
            </div>
        </form>
        <x-slot:footer>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addMemberModal{{ $pool->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                <button type="submit" form="addMemberForm{{ $pool->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Member</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>
    @endforeach
</div>

@push('scripts')
<script>
function deleteTalentPool(id) {
    if (confirm('Are you sure you want to delete this talent pool and all its members? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/talent-pools/${id}`;

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

function deleteMember(id) {
    if (confirm('Remove this member from the talent pool?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/succession/members/${id}`;

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

function filterPools(query) {
    const term = query.toLowerCase().trim();
    document.querySelectorAll('.pool-card').forEach(function(card) {
        card.style.display = card.getAttribute('data-name').includes(term) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
