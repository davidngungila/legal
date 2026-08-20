@extends('layouts.app')

@section('title', 'Statutory Filings - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Statutory Filings</h1>
            <p class="text-gray-600 mt-2">Manage Tanzania statutory compliance filings (TRA, NSSF, WCF, HESLB)</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="exportFilings()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Report
            </button>
            <button onclick="showAddFilingModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Filing
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="file-text" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $filings->count() }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Total Filings</h3>
            <p class="text-sm text-gray-600">All statutory deadlines</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-2xl font-bold text-green-600">{{ $filings->where('status', 'submitted')->count() }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Submitted</h3>
            <p class="text-sm text-gray-600">Completed filings</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-2xl font-bold text-yellow-600">{{ $filings->where('status', 'pending')->where('due_date', '>=', now()->toDateString())->count() }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Pending</h3>
            <p class="text-sm text-gray-600">Upcoming deadlines</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-2xl font-bold text-red-600">{{ $filings->where('status', 'pending')->where('due_date', '<', now()->toDateString())->count() }}</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-2">Overdue</h3>
            <p class="text-sm text-gray-600">Missed deadlines</p>
        </div>
    </div>

    <!-- Filings by Authority -->
    @foreach($groupedFilings as $authority => $authorityFilings)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $authority }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $authorityFilings->count() }} filing(s) tracked</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($authorityFilings->where('status', 'pending')->where('due_date', '<', now()->toDateString())->count() > 0)
                        bg-red-100 text-red-700
                    @elseif($authorityFilings->where('status', 'pending')->count() > 0)
                        bg-yellow-100 text-yellow-700
                    @else
                        bg-green-100 text-green-700
                    @endif
                ">
                    {{ $authorityFilings->where('status', 'submitted')->count() }}/{{ $authorityFilings->count() }} Complete
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filing Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($authorityFilings as $filing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $filing->filing_type }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($filing->filing_period_start)->format('M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($filing->due_date)->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($filing->amount)
                                    TZS {{ number_format($filing->amount, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($filing->status == 'submitted') bg-green-100 text-green-800
                                @elseif($filing->status == 'late') bg-red-100 text-red-800
                                @elseif($filing->status == 'overdue') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                {{ ucfirst($filing->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewFiling({{ $filing->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                            @if($filing->status == 'pending')
                                <button onclick="showSubmitFilingModal({{ $filing->id }})" class="text-green-600 hover:text-green-900">Mark Submitted</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($groupedFilings->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-feather="file-text" class="w-8 h-8 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Statutory Filings</h3>
        <p class="text-gray-600 mb-4">Start tracking your Tanzania statutory compliance deadlines</p>
        <button onclick="showAddFilingModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
            Add First Filing
        </button>
    </div>
    @endif
</div>

<!-- Add Filing Modal -->
<div id="addFilingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Add Statutory Filing</h3>
        </div>
        <form id="addFilingForm" class="p-6 space-y-4">
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
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="hideAddFilingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">Add Filing</button>
            </div>
        </form>
    </div>
</div>

<!-- Submit Filing Modal -->
<div id="submitFilingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Mark Filing as Submitted</h3>
        </div>
        <form id="submitFilingForm" class="p-6 space-y-4">
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
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="hideSubmitFilingModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">Mark Submitted</button>
            </div>
        </form>
    </div>
</div>

<script>
function showAddFilingModal() {
    document.getElementById('addFilingModal').classList.remove('hidden');
    document.getElementById('addFilingModal').classList.add('flex');
    feather.replace();
}

function hideAddFilingModal() {
    document.getElementById('addFilingModal').classList.add('hidden');
    document.getElementById('addFilingModal').classList.remove('flex');
    document.getElementById('addFilingForm').reset();
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

function exportFilings() {
    window.location.href = '/compliance/filings/export';
}

async function viewFiling(filingId) {
    try {
        const response = await fetch(`/compliance/filings/${filingId}`);
        const result = await response.json();
        if (result.success) {
            const filing = result.filing;
            alert(`Filing Details:\n\nAuthority: ${filing.authority}\nType: ${filing.filing_type}\nPeriod: ${filing.filing_period_start} to ${filing.filing_period_end}\nDue Date: ${filing.due_date}\nAmount: TZS ${filing.amount || 'N/A'}\nStatus: ${filing.status}\nNotes: ${filing.notes || 'N/A'}`);
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Add Filing Form Submit
document.getElementById('addFilingForm').addEventListener('submit', async function(e) {
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
            alert('Filing added successfully!');
            hideAddFilingModal();
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