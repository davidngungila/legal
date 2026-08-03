@extends('layouts.app')

@section('title', 'Compensation & Benefits - LegalHR Tanzania')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Compensation & Benefits</h1>
            <p class="text-gray-600 mt-2">Manage employee salary and benefits</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('compensation.export', request()->query()) }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-flex items-center">
                <i data-feather="download" class="w-4 h-4 mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['total_salary_tzs'] ?? 0, 0) }}</h3>
            <p class="text-gray-600 text-sm">Total Salary (TZS)</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS {{ number_format($stats['avg_salary'] ?? 0, 0) }}</h3>
            <p class="text-gray-600 text-sm">Average Salary</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['employees_with_benefits'] ?? 0) }}</h3>
            <p class="text-gray-600 text-sm">Employees With Benefits</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_employees'] ?? 0) }}</h3>
            <p class="text-gray-600 text-sm">Total Employees</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('compensation.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Name, ID, email, dept, position">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Salary Range</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" step="0.01" name="min_salary" value="{{ request('min_salary') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Min">
                    <input type="number" step="0.01" name="max_salary" value="{{ request('max_salary') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Max">
                </div>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors w-full">Filter</button>
                <a href="{{ route('compensation.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Clear</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Employee Compensation</h2>
            <div id="compAlert" class="hidden text-sm px-3 py-2 rounded-lg"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payroll</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benefits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50" data-employee-row="{{ $employee->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                <div class="text-xs text-gray-500">
                                    <span>{{ $employee->employee_id ?: ('#'.$employee->id) }}</span>
                                    @if($employee->email)
                                        <span class="mx-1">•</span>
                                        <span>{{ $employee->email }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->position ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="salary-cell-{{ $employee->id }}">
                                <div class="font-medium">{{ $employee->currency ?: 'TZS' }} {{ number_format((float) ($employee->salary ?? 0), 0) }}</div>
                                <div class="text-xs text-gray-500">{{ $employee->payment_frequency ?: 'monthly' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php($lp = $lastPayrollByEmployeeId[$employee->id] ?? null)
                                @if($lp)
                                    <div class="font-medium">TZS {{ number_format((float) ($lp->net_pay ?? 0), 0) }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $lp->payroll_period)->format('F Y') }}</div>
                                @else
                                    <div class="text-gray-500 text-sm">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="benefits-cell-{{ $employee->id }}">
                                @php($benefits = is_array($employee->benefits) ? $employee->benefits : [])
                                @if(count($benefits))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($benefits, 0, 3) as $b)
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full">{{ $b }}</span>
                                        @endforeach
                                        @if(count($benefits) > 3)
                                            <span class="text-xs text-gray-500">+{{ count($benefits) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button type="button"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        onclick="openCompEdit({{ $employee->id }})">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="users" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">Add employees first, then manage their compensation here.</p>
                                    <a href="{{ route('employees.create') }}"
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                                        Add Employee
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $employees->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $employees->firstItem() }}</span> to
                            <span class="font-medium">{{ $employees->lastItem() }}</span> of
                            <span class="font-medium">{{ $employees->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
window.__compEmployees = @json($pageEmployees);

function getCompEmployee(employeeId) {
    return (window.__compEmployees || []).find((e) => Number(e.id) === Number(employeeId)) || null;
}

function showCompAlert(message, type) {
    const el = document.getElementById('compAlert');
    if (!el) return;

    el.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
    if (type === 'success') {
        el.classList.add('bg-green-50', 'text-green-700');
    } else {
        el.classList.add('bg-red-50', 'text-red-700');
    }
    el.textContent = message;

    window.clearTimeout(window.__compAlertTimeout);
    window.__compAlertTimeout = window.setTimeout(() => {
        el.classList.add('hidden');
    }, 4000);
}

function openCompEdit(employeeId) {
    const employee = getCompEmployee(employeeId);
    if (!employee) {
        showCompAlert('Employee data not available on this page. Please refresh and try again.', 'error');
        return;
    }

    document.getElementById('compEditEmployeeId').value = employee.id;
    document.getElementById('compEditTitle').textContent = `${employee.first_name || ''} ${employee.last_name || ''}`.trim() || `Employee #${employee.id}`;

    document.getElementById('compSalary').value = employee.salary ?? '';
    document.getElementById('compCurrency').value = employee.currency || 'TZS';
    document.getElementById('compFrequency').value = employee.payment_frequency || 'monthly';

    const benefitValues = new Set(Array.isArray(employee.benefits) ? employee.benefits.map(String) : []);
    document.querySelectorAll('input[name="compBenefits[]"]').forEach((cb) => {
        cb.checked = benefitValues.has(String(cb.value));
    });

    openModal('compEditModal');
}

function closeCompEdit() {
    closeModal('compEditModal');
}

function formatNumber(value) {
    const n = Number(value || 0);
    return Number.isFinite(n) ? n.toLocaleString(undefined, { maximumFractionDigits: 0 }) : '0';
}

function renderBenefitsBadges(benefits) {
    const list = Array.isArray(benefits) ? benefits.filter(Boolean).map(String) : [];
    if (!list.length) return '<span class="text-gray-500 text-sm">-</span>';

    const visible = list.slice(0, 3);
    const extra = list.length - visible.length;
    const badges = visible.map((b) => `<span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded-full">${escapeHtml(b)}</span>`).join('');
    const more = extra > 0 ? `<span class="text-xs text-gray-500">+${extra}</span>` : '';
    return `<div class="flex flex-wrap gap-1">${badges}${more}</div>`;
}

function escapeHtml(str) {
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

async function saveCompEdit() {
    const employeeId = document.getElementById('compEditEmployeeId').value;
    if (!employeeId) return;

    const salary = document.getElementById('compSalary').value;
    const currency = document.getElementById('compCurrency').value;
    const payment_frequency = document.getElementById('compFrequency').value;

    const benefits = Array.from(document.querySelectorAll('input[name="compBenefits[]"]:checked')).map((cb) => cb.value);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const saveBtn = document.getElementById('compSaveBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }

    try {
        const response = await fetch(`{{ url('/compensation/employees') }}/${employeeId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                salary: salary === '' ? null : Number(salary),
                currency,
                payment_frequency,
                benefits,
            }),
        });

        if (response.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : null;

        if (!response.ok || !data?.success) {
            const message = data?.message || (data?.error) || 'Failed to update compensation.';
            showCompAlert(message, 'error');
            return;
        }

        const updated = data.data || null;
        const salaryCell = document.getElementById(`salary-cell-${employeeId}`);
        const benefitsCell = document.getElementById(`benefits-cell-${employeeId}`);

        if (updated && salaryCell) {
            const cur = updated.currency || 'TZS';
            const sal = updated.salary ?? 0;
            const freq = updated.payment_frequency || 'monthly';
            salaryCell.innerHTML = `<div class="font-medium">${escapeHtml(cur)} ${formatNumber(sal)}</div><div class="text-xs text-gray-500">${escapeHtml(freq)}</div>`;
        }

        if (updated && benefitsCell) {
            benefitsCell.innerHTML = renderBenefitsBadges(updated.benefits || []);
        }

        const local = getCompEmployee(employeeId);
        if (local && updated) {
            local.salary = updated.salary;
            local.currency = updated.currency;
            local.payment_frequency = updated.payment_frequency;
            local.benefits = updated.benefits || [];
        }

        showCompAlert(data.message || 'Compensation updated successfully.', 'success');
        closeCompEdit();
    } catch (e) {
        showCompAlert('Network error while saving. Please try again.', 'error');
    } finally {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<x-advanced-modal id="compEditModal" title="Edit Compensation" subtitle-id="compEditTitle" description="Employee compensation details" icon="edit" color="indigo" size="xl">
    <div class="space-y-4">
        <input type="hidden" id="compEditEmployeeId">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary</label>
                    <input id="compSalary" type="number" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select id="compCurrency" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="TZS">TZS</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Frequency</label>
                <select id="compFrequency" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="monthly">Monthly</option>
                    <option value="bi-weekly">Bi-weekly</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Benefits</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Health Insurance" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Health Insurance</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Retirement / Pension" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Retirement / Pension</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Transport Allowance" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Transport Allowance</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Phone / Internet" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Phone / Internet</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Training Support" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Training Support</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="compBenefits[]" value="Other Benefit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Other Benefit</span>
                    </label>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex items-center justify-end space-x-2">
                <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="closeModal('compEditModal')">Cancel</button>
                <button type="button" id="compSaveBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" onclick="saveCompEdit()">Save</button>
            </div>
        </x-slot:footer>
    </x-advanced-modal>
@endpush
