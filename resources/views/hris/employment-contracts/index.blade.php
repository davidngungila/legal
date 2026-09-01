@extends('layouts.app')

@section('title', 'Employment Contracts - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Employment Contracts</h1>
            <p class="text-gray-600 mt-2">Manage comprehensive employment agreements and contracts</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('contract-management.index') }}"
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="settings" class="w-4 h-4 mr-2"></i>
                Contract Management
            </a>
            @hasPermission('employment_contract.create')
            <button onclick="openModal('createContractModal')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                New Contract
            </button>
            @endhasPermission
        </div>
    </div>

    @if ($errors->any())
        <div id="validationErrors" class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <h3 class="text-sm font-semibold text-red-800 flex items-center mb-2">
                <i data-feather="alert-circle" class="w-4 h-4 mr-2"></i>
                Please fix the following errors:
            </h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 mb-8">
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
                <div class="flex-shrink-0 bg-gray-200 rounded-lg p-2.5">
                    <i data-feather="edit-3" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Draft</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['draft'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-2.5">
                    <i data-feather="users" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Employees</p>
                    <p class="text-xl font-semibold text-purple-600">{{ $stats['employees_covered'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-2.5">
                    <i data-feather="trending-up" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Avg Salary</p>
                    <p class="text-lg font-semibold text-indigo-600">{{ number_format($stats['average_salary'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Requiring Attention -->
    @if($attention['total'] > 0)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-orange-800 flex items-center">
                    <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i>
                    {{ $attention['total'] }} contract{{ $attention['total'] > 1 ? 's' : '' }} requiring attention
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <p class="text-xs font-medium text-orange-700">Expiring Soon (60d)</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $attention['expiring_soon']->count() }}</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <p class="text-xs font-medium text-orange-700">Expired</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $attention['expired']->count() }}</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <p class="text-xs font-medium text-orange-700">Pending Signature</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $attention['pending_signature']->count() }}</p>
                </div>
                <div class="bg-white rounded-lg p-3 border border-orange-200">
                    <p class="text-xs font-medium text-orange-700">Probation Ending (30d)</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $attention['probation_ending']->count() }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('employment-contracts.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, contract #, title..."
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
            <div>
                <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" @if(! request('department') || request('department') === 'all') selected @endif>All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" @if(request('department') === $dept) selected @endif>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="created_at" @if(request('sort') === 'created_at' || ! request('sort')) selected @endif>Newest First</option>
                    <option value="effective_date" @if(request('sort') === 'effective_date') selected @endif>Start Date</option>
                    <option value="expiry_date" @if(request('sort') === 'expiry_date') selected @endif>Expiry Date</option>
                    <option value="basic_salary" @if(request('sort') === 'basic_salary') selected @endif>Basic Salary</option>
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
                                @if($contract->probation_end_date && in_array($status, ['active', 'renewed']))
                                    <div class="text-xs text-gray-400 mt-1">Probation: {{ $contract->probation_end_date->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3 items-center">
                                    <a href="{{ route('employment-contracts.employee-contracts', $contract->employee_id) }}"
                                       class="text-indigo-600 hover:text-indigo-900" title="Employee contracts">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    @hasPermission('employment_contract.edit')
                                    <a href="{{ route('employment-contracts.edit', $contract->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    @endhasPermission
                                    @hasPermission('employment_contract.manage')
                                    <form method="POST" action="{{ route('employment-contracts.generate-pdf', $contract->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-purple-600 hover:text-purple-900" title="Download PDF">
                                            <i data-feather="download" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    @endhasPermission
                                    @hasPermission('employment_contract.edit')
                                    @if($status === 'draft')
                                        <form method="POST" action="{{ route('employment-contracts.activate', $contract->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Activate">
                                                <i data-feather="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($status, ['active', 'renewed', 'expired']))
                                        <button onclick="openRenewModal({{ $contract->id }}, '{{ $contract->formatted_contract_number }}')"
                                                class="text-green-600 hover:text-green-900" title="Renew">
                                            <i data-feather="refresh-cw" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @if(! in_array($status, ['terminated']))
                                        <button onclick="openTerminateModal({{ $contract->id }}, '{{ $contract->formatted_contract_number }}')"
                                                class="text-red-600 hover:text-red-900" title="Terminate">
                                            <i data-feather="x-circle" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                    @endhasPermission
                                    @hasPermission('employment_contract.manage')
                                    <button onclick="openUploadModal({{ $contract->id }}, '{{ $contract->formatted_contract_number }}')"
                                            class="text-gray-500 hover:text-gray-700" title="Upload document">
                                        <i data-feather="upload" class="w-4 h-4"></i>
                                    </button>
                                    @endhasPermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employment contracts found</p>
                                    <p class="text-sm">Create your first employment contract to get started.</p>
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

<!-- Create Contract Modal -->
<x-advanced-modal id="createContractModal" title="Create Employment Contract"
    description="Comprehensive employment agreement" icon="file-plus" color="indigo" size="6xl">
    <form id="createContractForm" method="POST" action="{{ route('employment-contracts.store') }}" enctype="multipart/form-data">
        @csrf
        @include('hris.employment-contracts._form-fields', ['contract' => null, 'employees' => $employees])
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('createContractModal')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="createContractForm"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Create Contract
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Renew Contract Modal -->
<x-advanced-modal id="renewContractModal" title="Renew Employment Contract"
    icon="refresh-cw" color="green" size="lg">
    <form id="renewContractForm" method="POST" class="space-y-4">
        <input type="hidden" name="contract_id" id="renewContractId">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">New Basic Salary</label>
            <input type="number" name="new_basic_salary" min="0" step="0.01" placeholder="Leave blank to keep current"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Renewal Reason <span class="text-red-500">*</span></label>
            <textarea name="renewal_reason" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Terms Changes</label>
            <textarea name="terms_changes" rows="3"
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
        <input type="hidden" name="contract_id" id="terminateContractId">
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-800" id="terminateContractLabel">Terminating contract</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Date <span class="text-red-500">*</span></label>
            <input type="date" name="termination_date" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Type <span class="text-red-500">*</span></label>
            <select name="termination_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="resignation">Resignation</option>
                <option value="dismissal">Dismissal</option>
                <option value="retirement">Retirement</option>
                <option value="contract_expiry">Contract Expiry</option>
                <option value="mutual_agreement">Mutual Agreement</option>
                <option value="redundancy">Redundancy</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Reason <span class="text-red-500">*</span></label>
            <textarea name="termination_reason" rows="3" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Final Pay Date</label>
                <input type="date" name="final_pay_date"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Final Settlement Amount</label>
                <input type="number" name="final_settlement_amount" min="0" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div class="space-y-2">
            <div class="flex items-center">
                <input type="checkbox" name="handover_completed" id="terminate_handover"
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="terminate_handover" class="ml-2 block text-sm text-gray-900">Handover Completed</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="clearance_completed" id="terminate_clearance"
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="terminate_clearance" class="ml-2 block text-sm text-gray-900">Clearance Completed</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="exit_interview_completed" id="terminate_exit_interview"
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="terminate_exit_interview" class="ml-2 block text-sm text-gray-900">Exit Interview Completed</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="reference_letter_provided" id="terminate_reference_letter"
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="terminate_reference_letter" class="ml-2 block text-sm text-gray-900">Reference Letter Provided</label>
            </div>
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

<!-- Upload Document Modal -->
<x-advanced-modal id="uploadDocumentModal" title="Upload Contract Document"
    icon="upload" color="blue" size="lg">
    <form id="uploadDocumentForm" method="POST" action="#" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="contract_id" id="uploadContractId">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label>
            <select name="document_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="contract_document">Contract Document</option>
                <option value="signed_contract">Signed Contract</option>
                <option value="witness_signature">Witness Signature</option>
                <option value="renewal_document">Renewal Document</option>
                <option value="amendment">Amendment</option>
                <option value="termination_notice">Termination Notice</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
            <input type="file" name="document_file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
            <p class="text-xs text-gray-500 mt-1">Max 10MB. Allowed: PDF, DOC, DOCX, JPG, PNG.</p>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('uploadDocumentModal')"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" form="uploadDocumentForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-feather="upload" class="w-4 h-4 inline mr-2"></i>
                Upload Document
            </button>
        </div>
    </x-slot:footer>
</x-advanced-modal>
@endsection

@push('scripts')
<script>
function openRenewModal(contractId, contractNumber) {
    document.getElementById('renewContractId').value = contractId;
    document.getElementById('renewContractLabel').textContent = 'Renewing contract ' + contractNumber;
    document.getElementById('renewContractForm').action = '{{ url('/employment-contracts') }}/' + contractId + '/renew';
    openModal('renewContractModal');
}

function openTerminateModal(contractId, contractNumber) {
    document.getElementById('terminateContractId').value = contractId;
    document.getElementById('terminateContractLabel').textContent = 'Terminating contract ' + contractNumber;
    document.getElementById('terminateContractForm').action = '{{ url('/employment-contracts') }}/' + contractId + '/terminate';
    openModal('terminateContractModal');
}

function openUploadModal(contractId, contractNumber) {
    document.getElementById('uploadContractId').value = contractId;
    document.getElementById('uploadDocumentForm').action = '{{ url('/employment-contracts') }}/' + contractId + '/upload-document';
    openModal('uploadDocumentModal');
}

function initContractAutoFill() {
    const employeeSelect = document.getElementById('contract_employee_id');
    if (!employeeSelect) return;

    const prefillTargets = {
        position: 'job_title',
        department: 'department',
        salary: 'basic_salary',
        currency: 'salary_currency',
        paymentFrequency: 'payment_frequency',
        bankName: 'bank_name',
        bankAccount: 'bank_account_number',
        hireDate: 'effective_date',
        probationEndDate: 'probation_end_date',
        workSchedule: 'work_schedule'
    };

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

    // Auto-fill once on load if editing (employee already selected)
    fillFromEmployee(employeeSelect.selectedOptions[0]);
}

initContractAutoFill();

@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        openModal('createContractModal');
    });
@endif
</script>
@endpush
