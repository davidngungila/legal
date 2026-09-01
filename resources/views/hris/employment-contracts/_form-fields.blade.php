@php
    $c = $contract ?? null;
    $value = function ($field) use ($c) {
        return old($field, $c ? $c->{$field} : null);
    };
    $checked = function ($field) use ($c) {
        return (bool) old($field, $c ? $c->{$field} : false);
    };
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Employee & Contract Identity -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3">Contract Details</h4>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Employee <span class="text-red-500">*</span></label>
        <select name="employee_id" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Select Employee</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}"
                    @if((int) $value('employee_id') === $employee->id) selected @endif>
                    {{ $employee->full_name }} - {{ $employee->employee_id }} ({{ $employee->position ?? 'N/A' }})
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Number</label>
        <input type="text" name="contract_number" value="{{ $value('contract_number') }}"
               placeholder="Auto-generated if blank"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Title</label>
        <input type="text" name="contract_title" value="{{ $value('contract_title') }}"
               placeholder="e.g. Employment Agreement"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Type <span class="text-red-500">*</span></label>
        <select name="contract_type" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach(\App\Models\EmploymentContract::CONTRACT_TYPES as $typeKey => $typeLabel)
                <option value="{{ $typeKey }}" @if($value('contract_type') === $typeKey) selected @endif>{{ $typeLabel }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach(\App\Models\EmploymentContract::STATUSES as $statusKey => $statusLabel)
                <option value="{{ $statusKey }}" @if($value('status') === $statusKey) selected @endif>{{ $statusLabel }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Effective Date <span class="text-red-500">*</span></label>
        <input type="date" name="effective_date" required value="{{ $value('effective_date') ? \Illuminate\Support\Carbon::parse($value('effective_date'))->format('Y-m-d') : '' }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
        <input type="date" name="expiry_date" value="{{ $value('expiry_date') ? \Illuminate\Support\Carbon::parse($value('expiry_date'))->format('Y-m-d') : '' }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Probation End Date</label>
        <input type="date" name="probation_end_date" value="{{ $value('probation_end_date') ? \Illuminate\Support\Carbon::parse($value('probation_end_date'))->format('Y-m-d') : '' }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Performance Review Frequency</label>
        <select name="performance_review_frequency"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Not Set</option>
            @foreach(\App\Models\EmploymentContract::REVIEW_FREQUENCIES as $freqKey => $freqLabel)
                <option value="{{ $freqKey }}" @if($value('performance_review_frequency') === $freqKey) selected @endif>{{ $freqLabel }}</option>
            @endforeach
        </select>
    </div>

    <!-- Position -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Position & Work</h4>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
        <input type="text" name="job_title" required value="{{ $value('job_title') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
        <input type="text" name="department" required value="{{ $value('department') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Reporting Line</label>
        <input type="text" name="reporting_line" value="{{ $value('reporting_line') }}"
               placeholder="Reports to..."
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Work Location <span class="text-red-500">*</span></label>
        <input type="text" name="work_location" required value="{{ $value('work_location') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Work Schedule</label>
        <input type="text" name="work_schedule" value="{{ $value('work_schedule') }}"
               placeholder="e.g. Mon-Fri 8:00am - 5:00pm"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Compensation -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Compensation</h4>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Currency <span class="text-red-500">*</span></label>
        <input type="text" name="salary_currency" required maxlength="3" value="{{ $value('salary_currency') ?: 'TZS' }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Frequency <span class="text-red-500">*</span></label>
        <select name="payment_frequency" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            @foreach(\App\Models\EmploymentContract::PAYMENT_FREQUENCIES as $freqKey => $freqLabel)
                <option value="{{ $freqKey }}" @if($value('payment_frequency') === $freqKey) selected @endif>{{ $freqLabel }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary <span class="text-red-500">*</span></label>
        <input type="number" name="basic_salary" required min="0" step="0.01" value="{{ $value('basic_salary') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Housing Allowance</label>
        <input type="number" name="housing_allowance" min="0" step="0.01" value="{{ $value('housing_allowance') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Transport Allowance</label>
        <input type="number" name="transport_allowance" min="0" step="0.01" value="{{ $value('transport_allowance') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Meal Allowance</label>
        <input type="number" name="meal_allowance" min="0" step="0.01" value="{{ $value('meal_allowance') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Other Allowances</label>
        <input type="number" name="other_allowances" min="0" step="0.01" value="{{ $value('other_allowances') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Payment Method -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Payment Details</h4>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
        <select name="payment_method"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Select Method</option>
            <option value="bank_transfer" @if($value('payment_method') === 'bank_transfer') selected @endif>Bank Transfer</option>
            <option value="cash" @if($value('payment_method') === 'cash') selected @endif>Cash</option>
            <option value="cheque" @if($value('payment_method') === 'cheque') selected @endif>Cheque</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
        <input type="text" name="bank_name" value="{{ $value('bank_name') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Name</label>
        <input type="text" name="bank_account_name" value="{{ $value('bank_account_name') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
        <input type="text" name="bank_account_number" value="{{ $value('bank_account_number') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Working Hours / Week</label>
        <input type="number" name="working_hours_per_week" min="1" max="80" step="0.5" value="{{ $value('working_hours_per_week') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Overtime Rate (multiplier)</label>
        <input type="number" name="overtime_rate" min="1" max="5" step="0.1" value="{{ $value('overtime_rate') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Leave & Benefits -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Leave & Benefits</h4>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Annual Leave (days) <span class="text-red-500">*</span></label>
        <input type="number" name="leave_entitlement_days" required min="0" max="365" value="{{ $value('leave_entitlement_days') ?: 21 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sick Leave (days)</label>
        <input type="number" name="sick_leave_days" min="0" max="365" value="{{ $value('sick_leave_days') ?: 0 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Public Holidays (days)</label>
        <input type="number" name="public_holidays" min="0" max="30" value="{{ $value('public_holidays') ?: 0 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Maternity Leave (weeks)</label>
        <input type="number" name="maternity_leave_weeks" min="0" max="52" value="{{ $value('maternity_leave_weeks') ?: 0 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Paternity Leave (weeks)</label>
        <input type="number" name="paternity_leave_weeks" min="0" max="52" value="{{ $value('paternity_leave_weeks') ?: 0 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notice Period (days) <span class="text-red-500">*</span></label>
        <input type="number" name="notice_period_days" required min="1" max="365" value="{{ $value('notice_period_days') ?: 30 }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Benefits Package</label>
        <textarea name="benefits_package" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $value('benefits_package') }}</textarea>
    </div>

    <!-- Contractual Clauses -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Contractual Clauses</h4>
    </div>
    <div class="md:col-span-2 space-y-3">
        <div class="flex items-center">
            <input type="checkbox" name="confidentiality_clause" id="confidentiality_clause" @if($checked('confidentiality_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="confidentiality_clause" class="ml-2 block text-sm text-gray-900">Confidentiality Clause</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="non_compete_clause" id="non_compete_clause" @if($checked('non_compete_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="non_compete_clause" class="ml-2 block text-sm text-gray-900">Non-Compete Clause</label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Non-Compete Duration (months)</label>
                <input type="number" name="non_compete_duration_months" min="1" max="60" value="{{ $value('non_compete_duration_months') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Non-Compete Restriction</label>
                <input type="text" name="non_compete_restriction" value="{{ $value('non_compete_restriction') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="intellectual_property_clause" id="intellectual_property_clause" @if($checked('intellectual_property_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="intellectual_property_clause" class="ml-2 block text-sm text-gray-900">Intellectual Property Clause</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="data_protection_clause" id="data_protection_clause" @if($checked('data_protection_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="data_protection_clause" class="ml-2 block text-sm text-gray-900">Data Protection Clause</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="health_and_safety_clause" id="health_and_safety_clause" @if($checked('health_and_safety_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="health_and_safety_clause" class="ml-2 block text-sm text-gray-900">Health & Safety Clause</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="training_development_clause" id="training_development_clause" @if($checked('training_development_clause')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="training_development_clause" class="ml-2 block text-sm text-gray-900">Training & Development Clause</label>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="company_policies_acknowledgment" id="company_policies_acknowledgment" @if($checked('company_policies_acknowledgment')) checked @endif
                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="company_policies_acknowledgment" class="ml-2 block text-sm text-gray-900">Company Policies Acknowledgment</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Termination Clause</label>
            <textarea name="termination_clause" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $value('termination_clause') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Grievance Procedure</label>
            <textarea name="grievance_procedure" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $value('grievance_procedure') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Disciplinary Procedure</label>
            <textarea name="disciplinary_procedure" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $value('disciplinary_procedure') }}</textarea>
        </div>
    </div>

    <!-- Documents -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Documents</h4>
    </div>
    @if($c)
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contract Document</label>
        <input type="file" name="contract_document" accept=".pdf,.doc,.docx"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
        @if($c->contract_document_path)
            <p class="text-xs text-gray-500 mt-1">Existing file stored. Upload to replace.</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Signed Contract</label>
        <input type="file" name="signed_contract" accept=".pdf,.doc,.docx"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
        @if($c->signed_contract_path)
            <p class="text-xs text-gray-500 mt-1">Existing file stored. Upload to replace.</p>
        @endif
    </div>
    @endif
    <div class="md:col-span-2">
        <x-signature-pad name="employee_signature" label="Employee Signature" :required="false" :existingPath="$c->employee_signature_path ?? ''" />
    </div>
    <div class="md:col-span-2">
        <x-signature-pad name="employer_signature" label="Employer Signature" :required="false" :existingPath="$c->employer_signature_path ?? ''" />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Witness Name</label>
        <input type="text" name="witness_name" value="{{ $value('witness_name') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Witness Title</label>
        <input type="text" name="witness_title" value="{{ $value('witness_title') }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Witness Signature</label>
        <input type="file" name="witness_signature" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
        @if($c && $c->witness_signature_path)
            <p class="text-xs text-gray-500 mt-1">Existing file stored. Upload to replace.</p>
        @endif
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
        <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider mb-3 mt-2">Notes</h4>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
        <textarea name="notes" rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ $value('notes') }}</textarea>
    </div>
</div>
