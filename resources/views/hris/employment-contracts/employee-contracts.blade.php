@extends('layouts.app')

@section('title', 'Contracts - ' . $employee->full_name . ' - Orvion HRIS')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employment Contracts</h1>
            <p class="text-gray-600 mt-2">{{ $employee->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('employment-contracts.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to All Contracts
            </a>
        </div>
    </div>

    <!-- Employee Profile Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center">
            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                <span class="text-indigo-600 font-bold text-xl">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name ?? '', 0, 1)) }}
                </span>
            </div>
            <div class="ml-5">
                <h2 class="text-xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                <p class="text-sm text-gray-500">{{ $employee->employee_id }} &middot; {{ $employee->position ?? 'N/A' }} &middot; {{ $employee->department ?? 'N/A' }}</p>
                @if($employee->email)
                    <p class="text-sm text-gray-500">{{ $employee->email }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Contract History ({{ $contracts->count() }})</h3>
            <span class="text-sm text-gray-500">{{ $contracts->where('effective_status', 'active')->count() }} active</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Contract #</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Title / Type</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Period</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Compensation</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contracts as $contract)
                    @php $status = $contract->effective_status; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-indigo-600">{{ $contract->formatted_contract_number }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $contract->contract_title ?: 'Employment Agreement' }}</div>
                            <div class="text-xs text-gray-500">{{ \App\Models\EmploymentContract::CONTRACT_TYPES[$contract->contract_type] ?? $contract->contract_type }}</div>
                            <div class="text-xs text-gray-400">{{ $contract->job_title }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $contract->effective_date?->format('d M, Y') }} @if($contract->expiry_date) - {{ $contract->expiry_date->format('d M, Y') }} @endif
                            @if($contract->expiry_date && in_array($status, ['active', 'renewed']))
                                <div class="text-xs {{ $contract->isExpiringSoon(60) ? 'text-yellow-600' : 'text-green-600' }}">{{ $contract->remaining_days }} days remaining</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $contract->formatted_basic_salary }}</div>
                            <div class="text-xs text-indigo-600">Total: {{ $contract->formatted_total_compensation }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-{{ $contract->status_badge_color }}-100 text-{{ $contract->status_badge_color }}-700 rounded-full text-xs font-medium uppercase">{{ $status }}</span>
                            @if($contract->renewal_count > 0)
                                <div class="text-xs text-gray-500 mt-1">{{ $contract->renewal_count }} renewal(s)</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <a href="{{ route('employment-contracts.edit', $contract->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form method="POST" action="{{ route('employment-contracts.generate-pdf', $contract->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-900" title="Download PDF">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium">No contracts found for this employee</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
