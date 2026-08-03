@extends('layouts.app')

@section('title', 'Exit Management - LegalHR')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Exit Management</h1>
            <p class="text-gray-600 mt-2">Manage employee exit processes and checklists</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Current Client:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button type="button" onclick="openModal('newExitModal')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                <i data-feather="plus" class="w-4 h-4"></i>
                Initiate Exit
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="log-out" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
            <p class="text-gray-600 text-sm">Total Exits</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['initiated']) }}</h3>
            <p class="text-gray-600 text-sm">In Progress</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_clearance']) }}</h3>
            <p class="text-gray-600 text-sm">Pending Clearance</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['completed']) }}</h3>
            <p class="text-gray-600 text-sm">Completed</p>
        </div>
    </div>

    <!-- Exit Cases Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Exit Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exit Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checklist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($exitCases as $case)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case->exit_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-xs font-medium">{{ strtoupper(substr($case->employee->first_name ?? 'E', 0, 1)) }}{{ strtoupper(substr($case->employee->last_name ?? 'E', 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $case->employee->first_name }} {{ $case->employee->last_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $case->employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \App\Models\ExitCase::EXIT_TYPES[$case->exit_type] ?? ucfirst(str_replace('_', ' ', $case->exit_type)) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $case->exit_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $totalItems = $case->checklists->count(); $doneItems = $case->checklists->where('completed', true)->count(); @endphp
                            @if($totalItems > 0)
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-1.5 mr-2">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $totalItems ? round($doneItems / $totalItems * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $doneItems }}/{{ $totalItems }}</span>
                            </div>
                            @else
                            <span class="text-xs text-gray-500">No items</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($case->status == 'initiated') bg-yellow-100 text-yellow-800
                                @elseif($case->status == 'pending_clearance') bg-blue-100 text-blue-800
                                @elseif($case->status == 'completed') bg-green-100 text-green-800
                                @elseif($case->status == 'cancelled') bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button onclick="openModal('detailExitModal{{ $case->id }}')" class="text-indigo-600 hover:text-indigo-900" title="View details">
                                    <i data-feather="eye" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openModal('editExitModal{{ $case->id }}')" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <i data-feather="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="deleteExit({{ $case->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i data-feather="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-feather="log-out" class="w-12 h-12 text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900">No exit cases found</p>
                                <p class="text-sm text-gray-600 mt-2">Click "Initiate Exit" to start the first exit process</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $exitCases->links() }}
        </div>
    </div>
</div>

<!-- New Exit Modal -->
<x-advanced-modal id="newExitModal" title="Initiate Exit Process" description="Start an exit case and generate the clearance checklist." icon="plus" color="indigo" size="lg">
    <form action="{{ route('exit.store') }}" method="POST" id="newExitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee <span class="text-red-500">*</span></label>
                <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="exit_type" class="block text-sm font-medium text-gray-700">Exit Type <span class="text-red-500">*</span></label>
                <select id="exit_type" name="exit_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(\App\Models\ExitCase::EXIT_TYPES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="exit_date" class="block text-sm font-medium text-gray-700">Exit Date</label>
                    <input type="date" id="exit_date" name="exit_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="notice_date" class="block text-sm font-medium text-gray-700">Notice Date</label>
                    <input type="date" id="notice_date" name="notice_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('newExitModal')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="newExitForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Initiate Exit</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Edit Exit Modals -->
@foreach($exitCases as $case)
<x-advanced-modal id="editExitModal{{ $case->id }}" title="Edit Exit Case" description="Update the exit case details." icon="edit" color="indigo" size="lg">
    <form action="{{ route('exit.update', $case->id) }}" method="POST" id="editExitForm{{ $case->id }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="edit_employee_id{{ $case->id }}" class="block text-sm font-medium text-gray-700">Employee <span class="text-red-500">*</span></label>
                <select id="edit_employee_id{{ $case->id }}" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $case->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="edit_exit_type{{ $case->id }}" class="block text-sm font-medium text-gray-700">Exit Type <span class="text-red-500">*</span></label>
                <select id="edit_exit_type{{ $case->id }}" name="exit_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(\App\Models\ExitCase::EXIT_TYPES as $key => $label)
                    <option value="{{ $key }}" {{ $case->exit_type == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="edit_exit_date{{ $case->id }}" class="block text-sm font-medium text-gray-700">Exit Date</label>
                    <input type="date" id="edit_exit_date{{ $case->id }}" name="exit_date" value="{{ $case->exit_date?->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="edit_notice_date{{ $case->id }}" class="block text-sm font-medium text-gray-700">Notice Date</label>
                    <input type="date" id="edit_notice_date{{ $case->id }}" name="notice_date" value="{{ $case->notice_date?->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label for="edit_reason{{ $case->id }}" class="block text-sm font-medium text-gray-700">Reason</label>
                <textarea id="edit_reason{{ $case->id }}" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $case->reason }}</textarea>
            </div>
            <div>
                <label for="edit_status{{ $case->id }}" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="edit_status{{ $case->id }}" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(\App\Models\ExitCase::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ $case->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeModal('editExitModal{{ $case->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
            <button type="submit" form="editExitForm{{ $case->id }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </x-slot:footer>
</x-advanced-modal>

<!-- Detail Exit Modals -->
<x-advanced-modal id="detailExitModal{{ $case->id }}" title="Exit Case: {{ $case->exit_number }}" description="Manage checklist, settlement and status." icon="eye" color="indigo" size="2xl">
    <div class="space-y-6">
        <!-- Case summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs text-gray-500">Employee</label>
                <p class="text-sm font-medium text-gray-900">{{ $case->employee->first_name }} {{ $case->employee->last_name }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Exit Type</label>
                <p class="text-sm font-medium text-gray-900">{{ \App\Models\ExitCase::EXIT_TYPES[$case->exit_type] ?? ucfirst(str_replace('_', ' ', $case->exit_type)) }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Exit Date</label>
                <p class="text-sm font-medium text-gray-900">{{ $case->exit_date?->format('M d, Y') ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-xs text-gray-500">Status</label>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
            </div>
        </div>
        @if($case->reason)
        <div>
            <label class="block text-xs text-gray-500">Reason</label>
            <p class="text-sm text-gray-700">{{ $case->reason }}</p>
        </div>
        @endif

        <!-- Status transition -->
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Update Status</h4>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('exit.status', $case->id) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach(\App\Models\ExitCase::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ $case->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">Set Status</button>
                </form>
            </div>
        </div>

        <!-- Checklist -->
        <div class="border-t border-gray-200 pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Clearance Checklist</h4>
                <span class="text-xs text-gray-500">{{ $case->checklists->where('completed', true)->count() }}/{{ $case->checklists->count() }} completed</span>
            </div>
            @if($case->checklists->count())
            <ul class="space-y-2 mb-4">
                @foreach($case->checklists as $item)
                <li class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                    <div class="flex items-center space-x-3">
                        <form action="{{ route('exit.checklist.toggle', [$case->id, $item->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="flex items-center space-x-2 text-left">
                                <span class="w-4 h-4 rounded border {{ $item->completed ? 'bg-green-600 border-green-600' : 'border-gray-300' }} inline-flex items-center justify-center">
                                    @if($item->completed)
                                    <i data-feather="check" class="w-3 h-3 text-white"></i>
                                    @endif
                                </span>
                                <span class="text-sm {{ $item->completed ? 'text-gray-500 line-through' : 'text-gray-800' }}">{{ $item->item_name }}</span>
                            </button>
                        </form>
                        @if($item->completed_at)
                        <span class="text-xs text-gray-500">by {{ $item->completedBy?->name ?? 'admin' }}</span>
                        @endif
                    </div>
                    <form action="{{ route('exit.checklist.destroy', [$case->id, $item->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Remove this checklist item?')">
                            <i data-feather="x" class="w-4 h-4"></i>
                        </button>
                    </form>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm text-gray-500 mb-4">No checklist items yet.</p>
            @endif
            <form action="{{ route('exit.checklist.store', $case->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <input type="text" name="item_name" placeholder="New checklist item..." class="flex-1 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <input type="text" name="category" placeholder="Category" class="w-32 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">Add</button>
            </form>
        </div>

        <!-- Settlement -->
        <div class="border-t border-gray-200 pt-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900">Exit Settlement</h4>
                @if($case->settlement && $case->settlement->status === 'paid')
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                @endif
            </div>
            <form action="{{ route('exit.settlement.store', $case->id) }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-500">Final Salary</label>
                        <input type="number" step="0.01" min="0" name="final_salary" value="{{ $case->settlement?->final_salary }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Leave Pay</label>
                        <input type="number" step="0.01" min="0" name="leave_pay" value="{{ $case->settlement?->leave_pay }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Notice Pay</label>
                        <input type="number" step="0.01" min="0" name="notice_pay" value="{{ $case->settlement?->notice_pay }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Bonus Pay</label>
                        <input type="number" step="0.01" min="0" name="bonus_pay" value="{{ $case->settlement?->bonus_pay }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Other</label>
                        <input type="number" step="0.01" min="0" name="other_payments" value="{{ $case->settlement?->other_payments }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                @if($case->settlement)
                <div class="text-sm text-gray-700 mb-3">Total: TZS {{ number_format($case->settlement->total_settlement, 2) }}</div>
                @endif
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                        {{ $case->settlement ? 'Update Settlement' : 'Save Settlement' }}
                    </button>
                    @if($case->settlement && $case->settlement->status !== 'paid')
                    <button type="submit" form="markPaidForm{{ $case->id }}" class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors">Mark Paid</button>
                    @endif
                </div>
            </form>
            @if($case->settlement && $case->settlement->status !== 'paid')
            <form id="markPaidForm{{ $case->id }}" action="{{ route('exit.settlement.paid', $case->id) }}" method="POST" class="hidden">
                @csrf
            </form>
            @endif
        </div>
    </div>
</x-advanced-modal>
@endforeach

<script>
function deleteExit(id) {
    if (confirm('Are you sure you want to delete this exit case? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/exit/${id}`;

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

feather.replace();
</script>
@endsection
