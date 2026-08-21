@extends('layouts.app')

@section('title', 'Compliance Deadlines - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Compliance Deadlines</h1>
            <p class="text-gray-600 mt-2">Track and manage all Tanzania statutory compliance deadlines</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showCalendarView()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="calendar" class="w-4 h-4 inline mr-2"></i>
                Calendar View
            </button>
            <button onclick="showAddDeadlineModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Deadline
            </button>
        </div>
    </div>

    <!-- Overdue Deadlines Alert -->
    @if($overdueDeadlines->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8">
        <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-red-900">{{ $overdueDeadlines->count() }} Overdue Deadline(s)</h3>
                <p class="text-sm text-red-700">Immediate action required</p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($overdueDeadlines->take(5) as $deadline)
            <div class="flex items-center justify-between bg-white rounded-lg p-4 border border-red-200">
                <div>
                    <div class="font-medium text-gray-900">{{ $deadline->authority }} - {{ $deadline->filing_type }}</div>
                    <div class="text-sm text-gray-500">Due: {{ \Carbon\Carbon::parse($deadline->due_date)->format('d M Y') }} ({{ now()->diffInDays(\Carbon\Carbon::parse($deadline->due_date)) }} days overdue)</div>
                </div>
                <button onclick="showSubmitFilingModal({{ $deadline->id }})" class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Take Action</button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Upcoming Deadlines -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Upcoming Deadlines</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $upcomingDeadlines->count() }} deadlines in the next period</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700">
                    Next 30 Days
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filing Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($upcomingDeadlines as $deadline)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $deadline->authority }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $deadline->filing_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($deadline->filing_period_start)->format('M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($deadline->due_date)->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if(now()->diffInDays(\Carbon\Carbon::parse($deadline->due_date)) <= 3) bg-red-100 text-red-800
                                @elseif(now()->diffInDays(\Carbon\Carbon::parse($deadline->due_date)) <= 7) bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif
                            ">
                                {{ now()->diffInDays(\Carbon\Carbon::parse($deadline->due_date)) }} days
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($deadline->amount)
                                    TZS {{ number_format($deadline->amount, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <button onclick="viewFiling({{ $deadline->id }})" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="View Details">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </button>
                                <button onclick="showSubmitFilingModal({{ $deadline->id }})" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Mark Complete">
                                    <i data-feather="check-circle" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($upcomingDeadlines->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="check-circle" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">All Caught Up!</h3>
            <p class="text-gray-600">No upcoming deadlines in the next period</p>
        </div>
        @endif
    </div>

    <!-- Recently Completed -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Recently Completed</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $completedDeadlines->count() }} filings submitted recently</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                    Last 10
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Authority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filing Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($completedDeadlines as $deadline)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $deadline->authority }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $deadline->filing_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($deadline->filing_period_start)->format('M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($deadline->actual_filing_date)->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($deadline->amount)
                                    TZS {{ number_format($deadline->amount, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                @if($deadline->reference_number)
                                    {{ $deadline->reference_number }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($completedDeadlines->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="file-text" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Completed Filings</h3>
            <p class="text-gray-600">Completed statutory filings will appear here</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Deadline Modal -->
<div id="addDeadlineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-900">Add Compliance Deadline</h3>
        </div>
        <form id="addDeadlineForm" class="p-6 space-y-4 flex-1 overflow-y-auto">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Authority</label>
                <select name="authority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Authority</option>
                    <option value="TRA">TRA (Tax Revenue Authority)</option>
                    <option value="NSSF">NSSF (National Social Security Fund)</option>
                    <option value="WCF">WCF (Workers Compensation Fund)</option>
                    <option value="HESLB">HESLB (Higher Education Students Loans Board)</option>
                    <option value="OSHA">OSHA (Occupational Safety and Health Authority)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filing Type</label>
                <input type="text" name="filing_type" required placeholder="e.g., Monthly Contribution" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Type</label>
                <select name="deadline_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="annual">Annual</option>
                    <option value="adhoc">Ad-hoc</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period Start</label>
                    <input type="date" name="filing_period_start" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period End</label>
                    <input type="date" name="filing_period_end" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (TZS)</label>
                <input type="number" name="amount" step="0.01" placeholder="Optional" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </form>
        <div class="p-6 border-t border-gray-200 flex-shrink-0">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideAddDeadlineModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" form="addDeadlineForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Deadline</button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Filing Modal -->
<div id="submitFilingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-start justify-center z-50 overflow-y-auto py-4">
    <div class="bg-white rounded-xl shadow-xl max-w-xl w-full mx-4 max-h-[calc(100vh-2rem)] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0 sticky top-0 bg-white z-10">
            <h3 class="text-lg font-semibold text-gray-900">Mark Filing as Submitted</h3>
        </div>
        <form id="submitFilingForm" class="p-6 space-y-4 flex-1 overflow-y-auto">
            <input type="hidden" name="filing_id" id="submitFilingId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Actual Filing Date</label>
                <input type="date" name="actual_filing_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid (TZS)</label>
                <input type="number" name="amount" step="0.01" placeholder="Optional" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                <input type="text" name="reference_number" placeholder="Optional" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </form>
        <div class="p-6 border-t border-gray-200 flex-shrink-0">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideSubmitFilingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" form="submitFilingForm" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Mark Submitted</button>
            </div>
        </div>
    </div>
</div>

<!-- Filing Details Modal -->
<x-advanced-modal id="filingDetailsModal" title="Filing Details" icon="file-text" color="blue" size="lg">
    <div id="filingDetailsContent">
        <!-- Content loaded via JavaScript -->
    </div>
</x-advanced-modal>

<script>
function showAddDeadlineModal() {
    document.getElementById('addDeadlineModal').classList.remove('hidden');
    document.getElementById('addDeadlineModal').classList.add('flex');
    feather.replace();
}

function hideAddDeadlineModal() {
    document.getElementById('addDeadlineModal').classList.add('hidden');
    document.getElementById('addDeadlineModal').classList.remove('flex');
    document.getElementById('addDeadlineForm').reset();
}

function showSubmitFilingModal(filingId) {
    document.getElementById('submitFilingId').value = filingId;
    document.getElementById('submitFilingModal').classList.remove('hidden');
    document.getElementById('submitFilingModal').classList.add('flex');
    feather.replace();
}

function hideSubmitFilingModal() {
    document.getElementById('submitFilingModal').classList.add('hidden');
    document.getElementById('submitFilingModal').classList.remove('flex');
    document.getElementById('submitFilingForm').reset();
}

function showCalendarView() {
    alert('Calendar view feature coming soon!');
}

async function viewFiling(filingId) {
    try {
        const response = await fetch(`/compliance/filings/${filingId}`);
        const result = await response.json();
        if (result.success) {
            const filing = result.filing;
            showFilingDetailsModal(filing);
        } else {
            showNotification('Error: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

function showFilingDetailsModal(filing) {
    const formatDate = (dateStr) => {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    };
    
    const formatCurrency = (amount) => {
        if (!amount && amount !== 0) return '—';
        return 'TZS ' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };
    
    const getStatusBadge = (status) => {
        const badges = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'submitted': 'bg-green-100 text-green-800',
            'late': 'bg-red-100 text-red-800',
            'overdue': 'bg-red-100 text-red-800'
        };
        return badges[status] || 'bg-gray-100 text-gray-800';
    };
    
    const getAuthorityBadge = (authority) => {
        const colors = {
            'TRA': 'bg-blue-100 text-blue-800',
            'NSSF': 'bg-green-100 text-green-800',
            'WCF': 'bg-orange-100 text-orange-800',
            'HESLB': 'bg-purple-100 text-purple-800',
            'OSHA': 'bg-red-100 text-red-800'
        };
        return colors[authority] || 'bg-indigo-100 text-indigo-800';
    };
    
    let content = `
        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Authority</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${getAuthorityBadge(filing.authority)}">${filing.authority}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusBadge(filing.status)}">${filing.status.charAt(0).toUpperCase() + filing.status.slice(1)}</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Filing Type</span>
                    <p class="font-medium text-gray-900">${filing.filing_type || '—'}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Deadline Type</span>
                    <p class="font-medium text-gray-900 capitalize">${filing.deadline_type || '—'}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Period</span>
                    <p class="font-medium text-gray-900">${formatDate(filing.filing_period_start)} to ${formatDate(filing.filing_period_end)}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Due Date</span>
                    <p class="font-medium text-gray-900">${formatDate(filing.due_date)}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Amount</span>
                    <p class="font-medium text-gray-900">${formatCurrency(filing.amount)}</p>
                </div>
                ${filing.actual_filing_date ? `
                <div>
                    <span class="text-sm text-gray-500 block mb-1">Actual Filing Date</span>
                    <p class="font-medium text-gray-900">${formatDate(filing.actual_filing_date)}</p>
                </div>
                ` : ''}
            </div>
            
            ${filing.reference_number ? `
            <div>
                <span class="text-sm text-gray-500 block mb-1">Reference Number</span>
                <p class="font-medium text-gray-900 font-mono text-sm">${filing.reference_number}</p>
            </div>
            ` : ''}
            
            ${filing.notes ? `
            <div>
                <span class="text-sm text-gray-500 block mb-1">Notes</span>
                <div class="bg-gray-50 p-4 rounded-lg text-gray-900 whitespace-pre-wrap">${filing.notes}</div>
            </div>
            ` : ''}
            
            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                ${filing.status === 'pending' ? `
                    <button onclick="closeModal('filingDetailsModal'); showSubmitFilingModal(${filing.id})" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Mark Complete
                    </button>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('filingDetailsContent').innerHTML = content;
    openModal('filingDetailsModal');
    feather.replace();
}

function showNotification(message, type = 'info') {
    const colors = {
        'success': 'bg-green-600',
        'error': 'bg-red-600',
        'info': 'bg-indigo-600',
        'warning': 'bg-yellow-600'
    };
    const color = colors[type] || colors.info;
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${color} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('animate-fade-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add Deadline Form Submit
document.getElementById('addDeadlineForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('/compliance/filings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Deadline added successfully!');
            hideAddDeadlineModal();
            location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

// Submit Filing Form Submit
document.getElementById('submitFilingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    const filingId = data.filing_id;
    delete data.filing_id;
    
    try {
        const response = await fetch(`/compliance/filings/${filingId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Filing marked as submitted successfully!');
            hideSubmitFilingModal();
            location.reload();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

feather.replace();
</script>
@endsection