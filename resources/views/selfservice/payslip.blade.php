@extends('layouts.app')

@section('title', 'Download Payslip - LegalHR Tanzania')

@section('content')
<div class="p-6" x-data="{}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Payslips</h1>
            <p class="text-gray-600 mt-2">
                View, print and download your salary statements
                @if($currentClient) &middot; {{ $currentClient->name }} @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center">
                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                Back
            </a>
            @if($payslips->count() > 0)
            <a href="{{ route('selfservice.payslip.download-all') }}" data-no-transition
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                <i data-feather="download" class="w-4 h-4 mr-2"></i>
                Download All (PDF)
            </a>
            @endif
        </div>
    </div>

    @if(!$employee)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-yellow-800 font-semibold">Employee profile not linked</h3>
                    <p class="text-yellow-600 text-sm">
                        We could not find an employee record matching your account for the selected client.
                        Please contact HR so your profile can be linked before payslips become available.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Payslips</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['count'] }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Gross Earnings</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($stats['gross'], 0) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center">
                        <i data-feather="trending-up" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Deductions</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($stats['deductions'], 0) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center">
                        <i data-feather="minus-circle" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Net Pay</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($stats['net'], 0) }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i data-feather="wallet" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        @if($payslips->count() > 0)
            @php($latest = $breakdowns[$payslips->first()->id] ?? null)
            @if($latest)
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-8 text-white mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-blue-200 text-sm uppercase tracking-wide">Latest Payslip</p>
                        <h2 class="text-2xl font-bold mt-1">{{ $latest['period_label'] }}</h2>
                        <p class="text-blue-200 text-sm mt-1">
                            {{ $latest['employee']['code'] }} &middot; {{ $latest['employee']['department'] }}
                            &middot; Paid {{ $latest['pay_date'] ?: '-' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-left md:text-right">
                            <p class="text-3xl font-bold">TZS {{ number_format($latest['net_pay'], 0) }}</p>
                            <p class="text-sm text-blue-200">Net Pay</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="openPayslip({{ $latest['id'] }})"
                                class="w-10 h-10 rounded-lg bg-white/15 hover:bg-white/25 flex items-center justify-center" title="View">
                                <i data-feather="eye" class="w-4 h-4"></i>
                            </button>
                            <a href="{{ route('selfservice.payslip.download', $latest['id']) }}" data-no-transition
                                class="w-10 h-10 rounded-lg bg-white/15 hover:bg-white/25 flex items-center justify-center" title="Download PDF">
                                <i data-feather="download" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('selfservice.payslip') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select id="filter_year" name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <select id="filter_month" name="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" @selected((string) request('month') === (string) $m || request('month') === str_pad($m, 2, '0', STR_PAD_LEFT))>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filter_status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center justify-center">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i>
                        Apply
                    </button>
                    <a href="{{ route('selfservice.payslip') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Payslip History -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Payslip History</h2>
                <span class="text-sm text-gray-500">
                    Average net: <span class="font-semibold text-gray-700">TZS {{ number_format($stats['average_net'], 0) }}</span>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Earnings</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payslips as $payslip)
                            @php($b = $breakdowns[$payslip->id] ?? null)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $b['period_label'] ?? $payslip->payroll_period }}
                                    <div class="text-xs text-gray-400 font-normal">Paid {{ $b['pay_date'] ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-900">TZS {{ number_format($b['earnings']['basic'] ?? 0, 0) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-green-600">TZS {{ number_format($b['earnings']['gross'] ?? 0, 0) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-red-600">TZS {{ number_format($b['deductions']['total'] ?? 0, 0) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">TZS {{ number_format($b['net_pay'] ?? 0, 0) }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">{!! $payslip->status_badge !!}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="openPayslip({{ $payslip->id }})"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                                            View
                                        </button>
                                        <a href="{{ route('selfservice.payslip.show', $payslip->id) }}" target="_blank"
                                            class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                                            Print
                                        </a>
                                        <a href="{{ route('selfservice.payslip.download', $payslip->id) }}" data-no-transition
                                            class="px-3 py-1.5 text-xs font-medium rounded-md border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i data-feather="file-text" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                    <p>No payslips match your filters.</p>
                                    <p class="text-sm mt-2">Your payslips will appear here once processed by HR.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Payslip Request Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Request a Payslip</h2>

        <form method="POST" action="{{ route('selfservice.payslip.request') }}" class="space-y-4">
            @csrf
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="payroll_period" class="block text-sm font-medium text-gray-700 mb-2">Payroll Period *</label>
                    <select id="payroll_period" name="payroll_period" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Period</option>
                        @for($i = 0; $i < 12; $i++)
                            @php($period = \Carbon\Carbon::now()->subMonths($i)->format('Y-m'))
                            <option value="{{ $period }}" @selected(old('payroll_period') === $period)>{{ \Carbon\Carbon::parse($period)->format('F Y') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="send" class="w-4 h-4 inline mr-2"></i>
                        Request Payslip
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payslip Detail Modal -->
<x-advanced-modal id="payslipDetailModal" title="Payslip Details" icon="file-text" color="indigo" size="3xl">
    <div id="payslipDetailBody" class="text-sm text-gray-500">Select a payslip to view its breakdown.</div>

    <x-slot name="footer">
        <div class="flex items-center justify-between gap-3">
            <button type="button" onclick="closeModal('payslipDetailModal')"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-white transition-colors">
                Close
            </button>
            <div class="flex gap-2">
                <a id="payslipDetailPrint" href="#" target="_blank"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-white transition-colors inline-flex items-center">
                    <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print
                </a>
                <a id="payslipDetailDownload" href="#" data-no-transition
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors inline-flex items-center">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i> Download PDF
                </a>
            </div>
        </div>
    </x-slot>
</x-advanced-modal>

@push('scripts')
<script>
    window.payslipData = @json($breakdowns);

    function tzs(value) {
        return 'TZS ' + Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function esc(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function row(label, value, negative) {
        return `<tr class="border-b border-gray-100">
            <td class="py-2 pr-4 text-gray-600">${esc(label)}</td>
            <td class="py-2 text-right font-medium ${negative ? 'text-red-600' : 'text-gray-900'}">${negative ? '-' : ''}${value}</td>
        </tr>`;
    }

    function openPayslip(id) {
        const p = window.payslipData[id];
        if (!p) { showNotification('Payslip details are unavailable.', 'error'); return; }

        const e = p.earnings, d = p.deductions, c = p.employer_contributions, a = p.attendance;

        let earnings = row('Basic Salary', tzs(e.basic));
        if (e.allowances > 0) earnings += row('Allowances', tzs(e.allowances));
        if (e.bonuses > 0) earnings += row('Bonuses', tzs(e.bonuses));
        if (e.overtime > 0) earnings += row('Overtime', tzs(e.overtime));
        if (e.rest_day > 0) earnings += row('Rest Day Pay', tzs(e.rest_day));
        if (e.public_holiday > 0) earnings += row('Public Holiday Pay', tzs(e.public_holiday));
        if (e.night_allowance > 0) earnings += row('Night Allowance', tzs(e.night_allowance));
        if (a.unpaid_leave_deduction > 0) earnings += row('Unpaid Leave', tzs(a.unpaid_leave_deduction), true);
        earnings += `<tr class="border-t-2 border-gray-200"><td class="py-2 pr-4 font-semibold text-gray-900">Gross Pay</td><td class="py-2 text-right font-bold text-green-600">${tzs(e.gross)}</td></tr>`;

        let deductions = row('PAYE (Income Tax)', tzs(d.paye));
        deductions += row('NSSF (Employee 10%)', tzs(d.nssf_employee));
        if (d.heslb > 0) deductions += row('HESLB', tzs(d.heslb));
        if (d.trade_union > 0) deductions += row('Trade Union', tzs(d.trade_union));
        if (d.loan > 0) deductions += row('Loan Repayment', tzs(d.loan));
        if (d.other > 0) deductions += row('Other Deductions', tzs(d.other));
        deductions += `<tr class="border-t-2 border-gray-200"><td class="py-2 pr-4 font-semibold text-gray-900">Total Deductions</td><td class="py-2 text-right font-bold text-red-600">${tzs(d.total)}</td></tr>`;

        const employer = `<tr><td class="py-1 pr-4 text-gray-500">NSSF (Employer 10%)</td><td class="py-1 text-right text-gray-700">${tzs(c.nssf_employer)}</td></tr>
            <tr><td class="py-1 pr-4 text-gray-500">SDL (4.5%)</td><td class="py-1 text-right text-gray-700">${tzs(c.sdl)}</td></tr>
            <tr><td class="py-1 pr-4 text-gray-500">WCF (0.5%)</td><td class="py-1 text-right text-gray-700">${tzs(c.wcf)}</td></tr>`;

        const hold = p.salary_hold
            ? `<div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg"><strong>Salary Hold:</strong> ${esc(p.salary_hold_reason || 'Active hold')}</div>`
            : '';

        document.getElementById('payslipDetailBody').innerHTML = `
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">${esc(p.period_label)}</h3>
                    <p class="text-gray-500">${esc(p.employee.name)} &middot; ${esc(p.employee.code)} &middot; ${esc(p.employee.department)}</p>
                    <p class="text-gray-400 text-xs">Paid ${esc(p.pay_date || '-')} &middot; Taxable income ${tzs(p.taxable_income)}</p>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 self-start sm:self-auto">${esc((p.status || '').toUpperCase())}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Earnings</h4>
                    <table class="w-full">${earnings}</table>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Deductions</h4>
                    <table class="w-full">${deductions}</table>
                </div>
            </div>
            <div class="mt-5 p-4 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center justify-between">
                <span class="font-semibold text-indigo-900">Net Pay</span>
                <span class="text-2xl font-bold text-indigo-700">${tzs(p.net_pay)}</span>
            </div>
            <div class="mt-4">
                <h4 class="font-semibold text-gray-900 mb-1">Employer Contributions (not deducted)</h4>
                <table class="w-full">${employer}</table>
            </div>
            ${hold}
        `;

        document.getElementById('payslipDetailPrint').href = `/selfservice/payslip/${id}`;
        document.getElementById('payslipDetailDownload').href = `/selfservice/payslip/${id}/download`;

        openModal('payslipDetailModal');
        if (typeof feather !== 'undefined') { feather.replace(); }
    }

    if (typeof feather !== 'undefined') { feather.replace(); }
</script>
@endpush
@endsection
