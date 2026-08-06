@extends('layouts.app')

@section('title', 'Contract Management - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Contract Management</h1>
            <p class="text-gray-600 mt-2">Overview, analytics and lifecycle management of employment contracts</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('employment-contracts.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="file-text" class="w-4 h-4 mr-2"></i>
                Contracts Dashboard
            </a>
            <form method="POST" action="{{ route('contract-management.generate-report') }}" class="flex items-center space-x-2">
                @csrf
                <input type="hidden" name="format" value="pdf">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all">All Statuses</option>
                    @foreach(\App\Models\EmploymentContract::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}">{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i>
                    Report
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2.5">
                    <i data-feather="file-text" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Total</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-2.5">
                    <i data-feather="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Active</p>
                    <p class="text-xl font-semibold text-green-600">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-2.5">
                    <i data-feather="clock" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Expiring 60d</p>
                    <p class="text-xl font-semibold text-yellow-600">{{ $stats['expiring_soon'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-2.5">
                    <i data-feather="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Expired</p>
                    <p class="text-xl font-semibold text-red-600">{{ $stats['expired'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-2.5">
                    <i data-feather="repeat" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Renewed</p>
                    <p class="text-xl font-semibold text-purple-600">{{ $stats['renewed'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-2.5">
                    <i data-feather="trending-up" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Renewal</p>
                    <p class="text-xl font-semibold text-indigo-600">{{ $stats['renewal_rate'] }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-2.5">
                    <i data-feather="trending-down" class="w-5 h-5 text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Termination</p>
                    <p class="text-xl font-semibold text-red-600">{{ $stats['termination_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Contract Type Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Contract Distribution</h3>
            <div class="relative h-56">
                <canvas id="contractTypeChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
                    @php $count = $stats['by_type']->get($typeKey, 0); @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ $typeLabel }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Requiring Attention -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                <i data-feather="alert-triangle" class="w-4 h-4 text-orange-500 mr-2"></i>
                Requiring Attention
            </h3>
            <div class="space-y-4 max-h-80 overflow-y-auto">
                @if($attention['total'] === 0)
                    <p class="text-sm text-gray-500">No contracts require attention at this time.</p>
                @else
                    @foreach($attention['expiring_soon'] as $contract)
                        <div class="flex items-start justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee?->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $contract->formatted_contract_number }} &middot; expires {{ $contract->expiry_date?->format('d M, Y') }}</p>
                            </div>
                            <span class="text-xs font-medium text-yellow-700">{{ $contract->remaining_days }}d left</span>
                        </div>
                    @endforeach
                    @foreach($attention['expired'] as $contract)
                        <div class="flex items-start justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee?->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $contract->formatted_contract_number }} &middot; expired {{ $contract->expiry_date?->format('d M, Y') }}</p>
                            </div>
                            <span class="text-xs font-medium text-red-700">Expired</span>
                        </div>
                    @endforeach
                    @foreach($attention['pending_signature'] as $contract)
                        <div class="flex items-start justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee?->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $contract->formatted_contract_number }} &middot; awaiting signature</p>
                            </div>
                            <span class="text-xs font-medium text-gray-600">Draft</span>
                        </div>
                    @endforeach
                    @foreach($attention['probation_ending'] as $contract)
                        <div class="flex items-start justify-between p-3 bg-purple-50 border border-purple-200 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $contract->employee?->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $contract->formatted_contract_number }} &middot; probation ends {{ $contract->probation_end_date?->format('d M, Y') }}</p>
                            </div>
                            <span class="text-xs font-medium text-purple-700">Probation</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                <i data-feather="calendar" class="w-4 h-4 text-purple-500 mr-2"></i>
                Upcoming Events
            </h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @php
                    $upcoming = collect($events)
                        ->filter(fn ($e) => $e['start'] >= now()->toDateString())
                        ->sortBy('start')
                        ->take(10);
                @endphp
                @if($upcoming->isEmpty())
                    <p class="text-sm text-gray-500">No upcoming events.</p>
                @else
                    @foreach($upcoming as $event)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 w-12 text-center mr-3">
                                <p class="text-sm font-bold text-indigo-600">{{ \Illuminate\Support\Carbon::parse($event['start'])->format('d') }}</p>
                                <p class="text-xs text-gray-500">{{ \Illuminate\Support\Carbon::parse($event['start'])->format('M') }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $event['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $event['contract'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('contract-management.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employee, contract #, title..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" @if(! request('status') || request('status') === 'all') selected @endif>All Status</option>
                    @foreach(\App\Models\EmploymentContract::STATUSES as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @if(request('status') === $statusKey) selected @endif>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="contract_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" @if(! request('contract_type') || request('contract_type') === 'all') selected @endif>All Contract Types</option>
                    @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}" @if(request('contract_type') === $typeKey) selected @endif>{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="expiry_date" @if(request('sort') === 'expiry_date' || ! request('sort')) selected @endif>Expiry Date</option>
                    <option value="created_at" @if(request('sort') === 'created_at') selected @endif>Newest First</option>
                    <option value="effective_date" @if(request('sort') === 'effective_date') selected @endif>Start Date</option>
                </select>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                    <i data-feather="search" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Contracts Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compensation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contracts as $contract)
                        @php $status = $contract->effective_status; @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-sm">
                                            {{ strtoupper(substr($contract->employee?->first_name ?? '?', 0, 1) . substr($contract->employee?->last_name ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $contract->employee?->full_name ?? 'Unknown Employee' }}</div>
                                        <div class="text-xs text-gray-500">{{ $contract->employee?->employee_id ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ $contract->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-indigo-600">{{ $contract->formatted_contract_number }}</div>
                                <div class="text-xs text-gray-500">{{ \App\Models\EmploymentContract::CONTRACT_TYPES[$contract->contract_type] ?? $contract->contract_type }}</div>
                                <div class="text-xs text-gray-400">{{ $contract->job_title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $contract->effective_date?->format('M d, Y') }} @if($contract->expiry_date) - {{ $contract->expiry_date->format('M d, Y') }} @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $contract->duration_months }} months</div>
                                @if(in_array($status, ['active', 'renewed']) && $contract->expiry_date)
                                    <div class="text-xs {{ $contract->isExpiringSoon(60) ? 'text-yellow-600' : 'text-green-600' }}">
                                        {{ $contract->remaining_days }} days remaining
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $contract->formatted_basic_salary }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $contract->payment_frequency)) }}</div>
                                <div class="text-xs text-indigo-600">Total: {{ $contract->formatted_total_compensation }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $contract->status_badge_color }}-100 text-{{ $contract->status_badge_color }}-700 uppercase">
                                    {{ $status }}
                                </span>
                                @if($contract->renewal_count > 0)
                                    <div class="text-xs text-gray-500 mt-1">{{ $contract->renewal_count }} renewal(s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3 items-center">
                                    <a href="{{ route('employment-contracts.employee-contracts', $contract->employee_id) }}"
                                       class="text-indigo-600 hover:text-indigo-900" title="Employee contracts">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('employment-contracts.edit', $contract->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('employment-contracts.generate-pdf', $contract->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-purple-600 hover:text-purple-900" title="Download PDF">
                                            <i data-feather="download" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @if($status === 'draft')
                                        <form method="POST" action="{{ route('contract-management.activate', $contract->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Activate">
                                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($status, ['active', 'renewed', 'expired']))
                                        <button onclick="openRenewModal({{ $contract->id }}, '{{ $contract->formatted_contract_number }}', '{{ route('contract-management.renew', $contract->id) }}')"
                                                class="text-green-600 hover:text-green-900" title="Renew">
                                            <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if(! in_array($status, ['terminated']))
                                        <button onclick="openTerminateModal({{ $contract->id }}, '{{ $contract->formatted_contract_number }}', '{{ route('contract-management.terminate', $contract->id) }}')"
                                                class="text-red-600 hover:text-red-900" title="Terminate">
                                            <i data-feather="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No contracts found</p>
                                    <p class="text-sm">No employment contracts match your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Renew Contract Modal -->
<x-advanced-modal id="renewContractModal" title="Renew Employment Contract"
    icon="refresh-cw" color="green" size="lg">
    <form id="renewContractForm" method="POST" class="space-y-4">
        @csrf
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-green-800" id="renewContractLabel">Renewing contract</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Effective Date <span class="text-red-500">*</span></label>
            <input type="date" name="new_effective_date" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Expiry Date</label>
            <input type="date" name="new_expiry_date"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Renewal Reason <span class="text-red-500">*</span></label>
            <textarea name="renewal_reason" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('renewContractModal')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="renewContractForm"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-feather="refresh-cw" class="w-4 h-4 inline mr-2"></i>
                Renew Contract
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Terminate Contract Modal -->
<x-advanced-modal id="terminateContractModal" title="Terminate Employment Contract"
    icon="alert-triangle" color="red" size="lg">
    <form id="terminateContractForm" method="POST" class="space-y-4">
        @csrf
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-800" id="terminateContractLabel">Terminating contract</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Date <span class="text-red-500">*</span></label>
            <input type="date" name="termination_date" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Reason <span class="text-red-500">*</span></label>
            <textarea name="termination_reason" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('terminateContractModal')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="terminateContractForm"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-feather="x-circle" class="w-4 h-4 inline mr-2"></i>
                Terminate Contract
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function openRenewModal(contractId, contractNumber, action) {
    document.getElementById('renewContractForm').action = action;
    document.getElementById('renewContractLabel').textContent = 'Renewing contract ' + contractNumber;
    openModal('renewContractModal');
}

function openTerminateModal(contractId, contractNumber, action) {
    document.getElementById('terminateContractForm').action = action;
    document.getElementById('terminateContractLabel').textContent = 'Terminating contract ' + contractNumber;
    openModal('terminateContractModal');
}

document.addEventListener('DOMContentLoaded', function () {
    const chartLabels = [];
    const chartData = [];
    @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
        chartLabels.push('{{ $typeLabel }}');
        chartData.push({{ $stats['by_type']->get($typeKey, 0) }});
    @endforeach

    const ctx = document.getElementById('contractTypeChart');
    if (ctx && typeof Chart !== 'undefined') {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Contracts',
                    data: chartData,
                    backgroundColor: ['#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { font: { size: 9 } } }
                }
            }
        });
    }
});
</script>
@endpush
