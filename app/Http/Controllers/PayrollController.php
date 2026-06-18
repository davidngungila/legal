<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display payroll management page.
     */
    public function index()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payrolls = Payroll::where('client_id', $clientId)
            ->with(['employee', 'client'])
            ->orderBy('pay_date', 'desc')
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    public function data(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'Please select a client first.'], 400);
        }

        $query = Payroll::where('client_id', $clientId)->with('employee');

        if ($request->filled('payroll_period')) {
            $query->where('payroll_period', $request->get('payroll_period'));
        }

        $payrolls = $query->orderBy('pay_date', 'desc')->get();

        $data = $payrolls->map(function (Payroll $p) {
            $employee = $p->employee;
            $meta = $this->extractPayrollMeta($p->notes);
            $monthOfPayment = (string) ($meta['monthOfPayment'] ?? $this->formatPayrollPeriod($p->payroll_period));
            $holidayPay = (float) ($meta['holidayPay'] ?? 0);
            $heslb = (float) ($meta['heslb'] ?? 0);
            $otherDed = (float) ($meta['otherDed'] ?? 0);
            $taxablePay = (float) ($meta['taxablePay'] ?? max(0, ($p->gross_pay ?? 0) - ($p->social_security ?? 0)));
            $sdl = (float) ($meta['sdl'] ?? 0);
            $wcf = (float) ($meta['wcf'] ?? 0);
            $totalCost = (float) ($meta['totalCost'] ?? (($p->gross_pay ?? 0) + ($p->pension ?? 0) + $sdl + $wcf));
            $tradeUnion = (float) ($meta['tradeUnion'] ?? 0);
            $loanDeductions = (float) ($meta['loanDeductions'] ?? 0);
            $restDayPay = (float) ($meta['restDayPay'] ?? 0);
            $nightAllowance = (float) ($meta['nightShiftAllowance'] ?? 0);
            $workflowState = (string) ($meta['workflowState'] ?? $this->mapLegacyStatusToWorkflow($p->status));

            return [
                'payrollId' => $p->id,
                'empId' => $employee?->employee_id ?? (string) $p->employee_id,
                'name' => $employee ? trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) : 'Employee #' . $p->employee_id,
                'department' => $employee?->department ?? '-',
                'title' => $employee?->position ?? '',
                'joiningDate' => optional($employee?->hire_date)->format('Y-m-d'),
                'shift' => 'Day',
                'basicSalary' => (float) $p->basic_salary,
                'allowances' => (float) $p->allowances,
                'bonuses' => (float) $p->bonuses,
                'otHours' => (float) $p->overtime_hours,
                'otRate' => (float) ($p->overtime_rate ?? 0),
                'overtimePay' => (float) $p->overtime_pay,
                'otPay' => (float) $p->overtime_pay,
                'hourlyRate' => (float) ($meta['hourlyRate'] ?? 0),
                'dailyRate' => (float) ($meta['dailyRate'] ?? 0),
                'restDayHours' => (float) ($meta['restDayHours'] ?? 0),
                'restDayPay' => $restDayPay,
                'publicHolidayHours' => (float) ($meta['publicHolidayHours'] ?? 0),
                'holidayPay' => $holidayPay,
                'nightHours' => (float) ($meta['nightShiftHours'] ?? 0),
                'nightAllowance' => $nightAllowance,
                'unpaidLeaveDays' => (float) ($meta['unpaidLeaveDays'] ?? 0),
                'unpaidLeaveDeduction' => (float) ($meta['unpaidLeaveDeduction'] ?? 0),
                'grossPay' => (float) $p->gross_pay,
                'taxablePay' => $taxablePay,
                'paye' => (float) $p->tax_deductions,
                'nssf' => (float) $p->social_security,
                'heslb' => $heslb,
                'tradeUnion' => $tradeUnion,
                'loanDeductions' => $loanDeductions,
                'otherDed' => $otherDed,
                'totalDeduction' => (float) ($p->total_deductions ?? 0),
                'netPay' => (float) $p->net_pay,
                'employerNSSF' => (float) ($p->pension ?? 0),
                'sdl' => $sdl,
                'wcf' => $wcf,
                'totalCost' => $totalCost,
                'monthOfPayment' => $monthOfPayment,
                'payrollPeriod' => $p->payroll_period,
                'payDate' => optional($p->pay_date)->format('Y-m-d'),
                'status' => $p->status,
                'workflowState' => $workflowState,
                'salaryHoldRecommended' => (bool) ($meta['salaryHoldRecommended'] ?? false),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function generateFromAttendance(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'Please select a client first.'], 400);
        }

        $validated = $request->validate([
            'payroll_period' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'pay_date' => ['nullable', 'date'],
        ]);

        $period = $validated['payroll_period'];
        $payDate = $request->filled('pay_date') ? Carbon::parse($validated['pay_date'])->toDateString() : Carbon::createFromFormat('Y-m', $period)->endOfMonth()->toDateString();

        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->get();

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($clientId, $employees, $start, $end, $period, $payDate, &$created, &$updated) {
            foreach ($employees as $employee) {
                $attendance = Attendance::where('client_id', $clientId)
                    ->where('employee_id', $employee->id)
                    ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                    ->get();

                $metrics = $this->summarizeAttendanceMetrics($attendance);
                $computation = $this->buildPayrollComputation($employee, [
                    'allowances' => $this->calculateAllowancesFromBenefits($employee->benefits ?? []),
                    'bonuses' => 0,
                    'overtime_hours' => $metrics['overtime_hours'],
                    'rest_day_hours' => $metrics['rest_day_hours'],
                    'public_holiday_hours' => $metrics['public_holiday_hours'],
                    'night_hours' => $metrics['night_hours'],
                    'unpaid_leave_days' => $metrics['unpaid_leave_days'],
                    'trade_union' => 0,
                    'loan_deductions' => 0,
                    'other_deductions' => 0,
                    'heslb' => 0,
                    'workflow_state' => 'prepared',
                    'salary_hold_recommended' => false,
                ], $period, $payDate);

                $payload = $computation['payload'];

                $existing = Payroll::where('client_id', $clientId)
                    ->where('employee_id', $employee->id)
                    ->where('payroll_period', $period)
                    ->first();

                if ($existing) {
                    $existingMeta = $this->extractPayrollMeta($existing->notes);
                    if (($existingMeta['workflowState'] ?? '') === 'locked') {
                        continue;
                    }
                    $existing->update($payload);
                    $updated++;
                } else {
                    Payroll::create($payload);
                    $created++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated from attendance successfully.',
            'created' => $created,
            'updated' => $updated,
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'Please select a client first.'], 400);
        }

        if ((int) $payroll->client_id !== (int) $clientId) {
            return response()->json(['success' => false, 'message' => 'Payroll record not found.'], 404);
        }

        $validated = $request->validate([
            'basic_salary' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'gross_pay' => 'nullable|numeric|min:0',
            'tax_deductions' => 'nullable|numeric|min:0',
            'social_security' => 'nullable|numeric|min:0',
            'pension' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'net_pay' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $meta = $this->extractPayrollMeta($payroll->notes);
        if (($meta['workflowState'] ?? '') === 'locked') {
            return response()->json(['success' => false, 'message' => 'Locked payroll cannot be edited.'], 422);
        }

        $employee = $payroll->employee;
        $input = [
            'basic_salary' => $validated['basic_salary'] ?? $payroll->basic_salary,
            'allowances' => $validated['allowances'] ?? $payroll->allowances,
            'bonuses' => $validated['bonuses'] ?? $payroll->bonuses,
            'overtime_hours' => $validated['overtime_hours'] ?? $payroll->overtime_hours,
            'overtime_rate' => $validated['overtime_rate'] ?? ($meta['overtimeRate'] ?? $payroll->overtime_rate),
            'overtime_pay' => $validated['overtime_pay'] ?? $payroll->overtime_pay,
            'rest_day_hours' => $meta['restDayHours'] ?? 0,
            'public_holiday_hours' => $meta['publicHolidayHours'] ?? 0,
            'night_hours' => $meta['nightShiftHours'] ?? 0,
            'unpaid_leave_days' => $meta['unpaidLeaveDays'] ?? 0,
            'trade_union' => $meta['tradeUnion'] ?? 0,
            'loan_deductions' => $meta['loanDeductions'] ?? 0,
            'other_deductions' => $meta['otherDed'] ?? 0,
            'heslb' => $meta['heslb'] ?? 0,
            'workflow_state' => $meta['workflowState'] ?? $this->mapLegacyStatusToWorkflow($payroll->status),
            'salary_hold_recommended' => $meta['salaryHoldRecommended'] ?? false,
            'holiday_pay' => $meta['holidayPay'] ?? 0,
            'rest_day_pay' => $meta['restDayPay'] ?? 0,
            'night_shift_allowance' => $meta['nightShiftAllowance'] ?? 0,
        ];

        if (array_key_exists('notes', $validated) && $validated['notes']) {
            $meta = array_merge($meta, $this->extractPayrollMeta($validated['notes']));
        }

        $computation = $this->buildPayrollComputation($employee, array_merge($meta, $input), $payroll->payroll_period, optional($payroll->pay_date)->format('Y-m-d'));

        if (isset($validated['status'])) {
            $computation['payload']['status'] = $validated['status'];
        }

        $payroll->fill($computation['payload']);
        $payroll->save();

        return response()->json([
            'success' => true,
            'message' => 'Payroll updated successfully.',
        ]);
    }

    private function formatPayrollPeriod(?string $period): string
    {
        if (!$period) return '';
        try {
            return Carbon::createFromFormat('Y-m', $period)->format('F Y');
        } catch (\Throwable $e) {
            return $period;
        }
    }

    private function countWorkingDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $d = $start->copy();
        while ($d->lte($end)) {
            if (!$d->isWeekend()) {
                $count++;
            }
            $d->addDay();
        }
        return $count;
    }

    private function calculateAllowancesFromBenefits($benefits): float
    {
        $list = is_array($benefits) ? $benefits : [];
        $list = array_map(fn ($v) => strtolower(trim((string) $v)), $list);

        $map = [
            'transport allowance' => 50000,
            'phone / internet' => 30000,
            'health insurance' => 100000,
            'training support' => 20000,
        ];

        $total = 0;
        foreach ($map as $key => $amount) {
            if (in_array($key, $list, true)) {
                $total += $amount;
            }
        }

        return (float) $total;
    }

    private function extractPayrollMeta(?string $notes): array
    {
        if (!$notes) {
            return [];
        }

        $decoded = json_decode($notes, true);
        if (!is_array($decoded)) {
            return [];
        }

        $meta = $decoded['payroll_meta'] ?? $decoded;
        return is_array($meta) ? $meta : [];
    }

    private function summarizeAttendanceMetrics($attendance): array
    {
        $metrics = [
            'overtime_hours' => 0.0,
            'rest_day_hours' => 0.0,
            'public_holiday_hours' => 0.0,
            'night_hours' => 0.0,
            'unpaid_leave_days' => 0.0,
        ];

        foreach ($attendance as $record) {
            $hours = (float) ($record->total_hours ?? 0);
            if ($hours <= 0) {
                $hours = match ($record->status) {
                    'half_day' => 4.0,
                    'holiday', 'present', 'late' => 8.0,
                    default => 0.0,
                };
            }

            $metrics['overtime_hours'] += min(50, (float) ($record->overtime_hours ?? 0));

            if ($record->status === 'holiday') {
                $metrics['public_holiday_hours'] += $hours;
            }

            if (in_array($record->status, ['present', 'late', 'half_day'], true) && $record->attendance_date?->isWeekend()) {
                $metrics['rest_day_hours'] += $hours;
            }

            if ($record->status === 'absent') {
                $metrics['unpaid_leave_days'] += 1;
            }

            $metrics['night_hours'] += $this->calculateNightShiftHours($record->clock_in, $record->clock_out, $hours, (string) $record->notes);
        }

        $metrics['overtime_hours'] = min(50, $metrics['overtime_hours']);

        return $metrics;
    }

    private function calculateNightShiftHours($clockIn, $clockOut, float $fallbackHours = 0, string $notes = ''): float
    {
        if ($clockIn && $clockOut) {
            $start = Carbon::parse($clockIn);
            $end = Carbon::parse($clockOut);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            $nightMinutes = 0;
            $cursor = $start->copy()->startOfDay();
            while ($cursor->lte($end)) {
                $windowStart = $cursor->copy()->setTime(20, 0);
                $windowEnd = $cursor->copy()->addDay()->setTime(6, 0);

                $overlapStart = $start->greaterThan($windowStart) ? $start : $windowStart;
                $overlapEnd = $end->lessThan($windowEnd) ? $end : $windowEnd;

                if ($overlapEnd->greaterThan($overlapStart)) {
                    $nightMinutes += $overlapEnd->diffInMinutes($overlapStart);
                }

                $cursor->addDay();
            }

            return round($nightMinutes / 60, 2);
        }

        if (str_contains(strtolower($notes), 'night')) {
            return round($fallbackHours, 2);
        }

        return 0;
    }

    private function buildPayrollComputation(Employee $employee, array $input, string $payrollPeriod, ?string $payDate): array
    {
        $baseSalary = round((float) ($input['basic_salary'] ?? $employee->salary ?? 0), 2);
        $allowances = round((float) ($input['allowances'] ?? 0), 2);
        $bonuses = round((float) ($input['bonuses'] ?? 0), 2);
        $overtimeHours = min(50, round((float) ($input['overtime_hours'] ?? 0), 2));
        $restDayHours = round((float) ($input['rest_day_hours'] ?? 0), 2);
        $publicHolidayHours = round((float) ($input['public_holiday_hours'] ?? 0), 2);
        $nightShiftHours = round((float) ($input['night_hours'] ?? 0), 2);
        $unpaidLeaveDays = round((float) ($input['unpaid_leave_days'] ?? 0), 2);

        $hourlyRate = round($baseSalary / (4.333 * 45), 4);
        $dailyRate = round($baseSalary / (4.333 * 6), 4);
        $overtimeRate = round($hourlyRate * 1.5, 4);
        $overtimePay = round($overtimeHours * $overtimeRate, 2);
        $restDayPay = round($hourlyRate * 2.0 * $restDayHours, 2);
        $holidayPay = round($hourlyRate * 2.0 * $publicHolidayHours, 2);
        $nightShiftAllowance = round($hourlyRate * 0.05 * $nightShiftHours, 2);
        $unpaidLeaveDeduction = round($dailyRate * $unpaidLeaveDays, 2);

        $grossPay = round($baseSalary + $allowances + $bonuses + $overtimePay + $restDayPay + $holidayPay + $nightShiftAllowance - $unpaidLeaveDeduction, 2);
        $grossPay = max(0, $grossPay);

        $employeeNssf = round($grossPay * 0.10, 2);
        $employerNssf = round($grossPay * 0.10, 2);
        $taxableIncome = round(max(0, $grossPay - $employeeNssf), 2);
        $paye = $this->calculatePaye($taxableIncome);
        $wcf = round($grossPay * 0.005, 2);
        $sdl = round((float) ($input['sdl'] ?? ($grossPay * 0.035)), 2);

        $heslb = round((float) ($input['heslb'] ?? 0), 2);
        if ($heslb <= 0 && filter_var($input['heslb_applicable'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $heslb = round($taxableIncome * 0.15, 2);
        }

        $tradeUnion = round((float) ($input['trade_union'] ?? 0), 2);
        if ($tradeUnion <= 0 && !empty($input['trade_union_rate'])) {
            $tradeUnion = round($grossPay * (((float) $input['trade_union_rate']) / 100), 2);
        }

        $loanDeductions = round((float) ($input['loan_deductions'] ?? 0), 2);
        $otherDed = round((float) ($input['other_deductions'] ?? 0), 2);
        $otherDeductions = round($heslb + $tradeUnion + $loanDeductions + $otherDed, 2);
        $totalDeductions = round($paye + $employeeNssf + $wcf + $otherDeductions, 2);
        $netPay = round(max(0, $grossPay - $totalDeductions), 2);

        $workflowState = $this->normalizeWorkflowState((string) ($input['workflow_state'] ?? 'prepared'));
        $salaryHoldRecommended = filter_var($input['salary_hold_recommended'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $alerts = [];
        if (($input['overtime_hours'] ?? 0) > 50) {
            $alerts[] = 'Overtime exceeded 50 hours and was capped automatically.';
        }
        if ($salaryHoldRecommended) {
            $alerts[] = 'Performance appraisal incomplete. Salary hold recommended.';
        }

        $meta = [
            'hourlyRate' => $hourlyRate,
            'dailyRate' => $dailyRate,
            'overtimeRate' => $overtimeRate,
            'restDayHours' => $restDayHours,
            'restDayPay' => $restDayPay,
            'publicHolidayHours' => $publicHolidayHours,
            'holidayPay' => $holidayPay,
            'nightShiftHours' => $nightShiftHours,
            'nightShiftAllowance' => $nightShiftAllowance,
            'unpaidLeaveDays' => $unpaidLeaveDays,
            'unpaidLeaveDeduction' => $unpaidLeaveDeduction,
            'taxablePay' => $taxableIncome,
            'heslb' => $heslb,
            'tradeUnion' => $tradeUnion,
            'loanDeductions' => $loanDeductions,
            'otherDed' => $otherDed,
            'wcf' => $wcf,
            'sdl' => $sdl,
            'workflowState' => $workflowState,
            'salaryHoldRecommended' => $salaryHoldRecommended,
            'alerts' => $alerts,
            'monthOfPayment' => $this->formatPayrollPeriod($payrollPeriod),
            'totalCost' => round($grossPay + $employerNssf + $wcf + $sdl, 2),
        ];

        return [
            'payload' => [
                'client_id' => session('current_client_id'),
                'employee_id' => $employee->id,
                'payroll_period' => $payrollPeriod,
                'pay_date' => $payDate ?: Carbon::createFromFormat('Y-m', $payrollPeriod)->endOfMonth()->toDateString(),
                'basic_salary' => $baseSalary,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $overtimeRate,
                'overtime_pay' => $overtimePay,
                'allowances' => $allowances,
                'bonuses' => $bonuses,
                'gross_pay' => $grossPay,
                'tax_deductions' => $paye,
                'social_security' => $employeeNssf,
                'pension' => $employerNssf,
                'other_deductions' => $otherDeductions,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'status' => $this->mapWorkflowToLegacyStatus($workflowState),
                'notes' => json_encode(['payroll_meta' => $meta], JSON_UNESCAPED_SLASHES),
            ],
            'meta' => $meta,
        ];
    }

    private function calculatePaye(float $taxableIncome): float
    {
        if ($taxableIncome <= 270000) {
            return 0.0;
        }
        if ($taxableIncome <= 520000) {
            return round(($taxableIncome - 270000) * 0.08, 2);
        }
        if ($taxableIncome <= 760000) {
            return round(20000 + (($taxableIncome - 520000) * 0.20), 2);
        }
        if ($taxableIncome <= 1000000) {
            return round(68000 + (($taxableIncome - 760000) * 0.25), 2);
        }

        return round(128000 + (($taxableIncome - 1000000) * 0.30), 2);
    }

    private function normalizeWorkflowState(string $status): string
    {
        return match (strtolower($status)) {
            'draft', 'prepared' => 'prepared',
            'processed', 'reviewed' => 'reviewed',
            'paid', 'approved' => 'approved',
            'locked' => 'locked',
            'reversed', 'cancelled' => 'reversed',
            default => 'prepared',
        };
    }

    private function mapWorkflowToLegacyStatus(string $workflowState): string
    {
        return match ($workflowState) {
            'reviewed' => 'processed',
            'approved', 'locked' => 'paid',
            'reversed' => 'cancelled',
            default => 'draft',
        };
    }

    private function mapLegacyStatusToWorkflow(?string $status): string
    {
        return $this->normalizeWorkflowState((string) $status);
    }

    /**
     * Show CSV upload form.
     */
    public function showUploadForm()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $client = Client::find($clientId);
        return view('payroll.upload', compact('client'));
    }

    /**
     * Process CSV upload and save to database.
     */
    public function uploadCsv(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'payroll_period' => 'required|string|max:50',
            'pay_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $payrollPeriod = $request->input('payroll_period');
            $payDate = $request->input('pay_date');

            // Read CSV file
            $csvData = $this->readCsvFile($file);
            
            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or invalid.'
                ], 400);
            }

            // Process and save payroll data
            $result = $this->processPayrollData($csvData, $clientId, $payrollPeriod, $payDate);

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$result['processed']} payroll records. Updated: {$result['updated']}. Skipped: {$result['skipped']}.",
                'processed' => $result['processed'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read CSV file and return data array.
     */
    private function readCsvFile($file)
    {
        $csvData = [];
        $header = [];
        $rowNumber = 0;

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // First row is header
                if (empty($header)) {
                    $header = array_map('strtolower', array_map('trim', $row));
                    continue;
                }

                // Combine header with row data
                $rowData = array_combine($header, $row);
                $rowData['_row_number'] = $rowNumber;
                $csvData[] = $rowData;
            }
            fclose($handle);
        }

        return $csvData;
    }

    /**
     * Process payroll data and save to database.
     */
    private function processPayrollData($csvData, $clientId, $payrollPeriod, $payDate)
    {
        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($csvData as $row) {
                try {
                    // Validate required fields
                    if (empty($row['employee_id']) && empty($row['email'])) {
                        $errors[] = "Row {$row['_row_number']}: Employee ID or Email is required";
                        $skipped++;
                        continue;
                    }

                    // Find employee
                    $employee = $this->findEmployee($row, $clientId);
                    
                    if (!$employee) {
                        $errors[] = "Row {$row['_row_number']}: Employee not found";
                        $skipped++;
                        continue;
                    }

                    // Check if payroll already exists for this employee and period
                    $existingPayroll = Payroll::where('client_id', $clientId)
                        ->where('employee_id', $employee->id)
                        ->where('payroll_period', $payrollPeriod)
                        ->first();

                    // Prepare payroll data
                    $payrollData = $this->preparePayrollData($row, $employee, $clientId, $payrollPeriod, $payDate);

                    if ($existingPayroll) {
                        $existingMeta = $this->extractPayrollMeta($existingPayroll->notes);
                        if (($existingMeta['workflowState'] ?? '') === 'locked') {
                            $errors[] = "Row {$row['_row_number']}: Payroll is locked for this period.";
                            $skipped++;
                            continue;
                        }

                        $existingPayroll->fill($payrollData);
                        $existingPayroll->save();
                        $updated++;
                    } else {
                        Payroll::create($payrollData);
                    }

                    $processed++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$row['_row_number']}: " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('Transaction failed: ' . $e->getMessage());
        }

        return [
            'processed' => $processed,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }

    /**
     * Find employee by ID or email.
     */
    private function findEmployee($row, $clientId)
    {
        // Try to find by employee_id first
        if (!empty($row['employee_id'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where(function($query) use ($row) {
                    $query->where('employee_id', $row['employee_id'])
                          ->orWhere('id', $row['employee_id']);
                })
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        // Try to find by email
        if (!empty($row['email'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $row['email'])
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        // Try to find by name combination
        if (!empty($row['first_name']) && !empty($row['last_name'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where('first_name', 'LIKE', trim($row['first_name']))
                ->where('last_name', 'LIKE', trim($row['last_name']))
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Prepare payroll data from CSV row.
     */
    private function preparePayrollData($row, $employee, $clientId, $payrollPeriod, $payDate)
    {
        $input = [
            'basic_salary' => $this->parseDecimal($row['basic_salary'] ?? ($employee->salary ?? 0)),
            'allowances' => $this->parseDecimal($row['allowances'] ?? 0),
            'bonuses' => $this->parseDecimal($row['bonuses'] ?? 0),
            'overtime_hours' => $this->parseDecimal($row['overtime_hours'] ?? 0),
            'rest_day_hours' => $this->parseDecimal($row['rest_day_hours'] ?? 0),
            'public_holiday_hours' => $this->parseDecimal($row['public_holiday_hours'] ?? $row['ph_hours'] ?? 0),
            'night_hours' => $this->parseDecimal($row['night_hours'] ?? 0),
            'unpaid_leave_days' => $this->parseDecimal($row['unpaid_leave_days'] ?? 0),
            'trade_union' => $this->parseDecimal($row['trade_union'] ?? 0),
            'trade_union_rate' => $this->parseDecimal($row['trade_union_rate'] ?? 0),
            'loan_deductions' => $this->parseDecimal($row['loan_deductions'] ?? 0),
            'other_deductions' => $this->parseDecimal($row['other_deductions'] ?? 0),
            'heslb' => $this->parseDecimal($row['heslb'] ?? 0),
            'heslb_applicable' => $row['heslb_applicable'] ?? false,
            'sdl' => $this->parseDecimal($row['sdl'] ?? 0),
            'workflow_state' => $row['workflow_state'] ?? 'prepared',
            'salary_hold_recommended' => $row['salary_hold_recommended'] ?? false,
        ];

        $computed = $this->buildPayrollComputation($employee, $input, $payrollPeriod, $payDate);
        $payload = $computed['payload'];
        $payload['client_id'] = $clientId;
        $payload['employee_id'] = $employee->id;

        return $payload;
    }

    /**
     * Parse decimal value from CSV.
     */
    private function parseDecimal($value)
    {
        // Remove currency symbols, commas, and spaces
        $cleaned = preg_replace('/[^\d.-]/', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $filename = "payroll_template_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'employee_id',
                'email',
                'first_name',
                'last_name',
                'basic_salary',
                'overtime_hours',
                'rest_day_hours',
                'public_holiday_hours',
                'night_hours',
                'unpaid_leave_days',
                'allowances',
                'bonuses',
                'trade_union',
                'loan_deductions',
                'heslb',
                'other_deductions',
                'salary_hold_recommended',
                'workflow_state',
                'notes'
            ]);
            
            // Sample row
            fputcsv($file, [
                'EMP001',
                'john.doe@company.com',
                'John',
                'Doe',
                '500000',
                '10',
                '8',
                '8',
                '12',
                '1',
                '20000',
                '10000',
                '15000',
                '20000',
                '0',
                '5000',
                'false',
                'prepared',
                'Sample notes'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show payroll details.
     */
    public function show($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payroll = Payroll::where('client_id', $clientId)
            ->with(['employee', 'client'])
            ->findOrFail($id);

        return view('payroll.show', compact('payroll'));
    }

    /**
     * Update payroll status.
     */
    public function updateStatus(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,processed,paid,cancelled,prepared,reviewed,approved,locked,reversed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status.',
                'errors' => $validator->errors()
            ], 422);
        }

        $payroll = Payroll::where('client_id', $clientId)->findOrFail($id);
        $meta = $this->extractPayrollMeta($payroll->notes);
        $currentWorkflow = $this->normalizeWorkflowState((string) ($meta['workflowState'] ?? $payroll->status));
        $targetWorkflow = $this->normalizeWorkflowState($request->input('status'));

        $allowedTransitions = [
            'prepared' => ['reviewed', 'reversed'],
            'reviewed' => ['prepared', 'approved', 'reversed'],
            'approved' => ['reviewed', 'locked', 'reversed'],
            'locked' => ['reversed'],
            'reversed' => ['prepared'],
        ];

        if ($currentWorkflow !== $targetWorkflow && !in_array($targetWorkflow, $allowedTransitions[$currentWorkflow] ?? [], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payroll workflow transition.',
            ], 422);
        }

        $meta['workflowState'] = $targetWorkflow;
        $meta['lastWorkflowActionAt'] = now()->toDateTimeString();
        $meta['lastWorkflowActionBy'] = Auth::id();
        $meta['alerts'] = $meta['alerts'] ?? [];

        $payroll->status = $this->mapWorkflowToLegacyStatus($targetWorkflow);
        $payroll->notes = json_encode(['payroll_meta' => $meta], JSON_UNESCAPED_SLASHES);
        $payroll->save();

        return response()->json([
            'success' => true,
            'message' => 'Payroll status updated successfully.',
            'payroll' => $payroll
        ]);
    }

    /**
     * Delete payroll record.
     */
    public function destroy($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payroll = Payroll::where('client_id', $clientId)->findOrFail($id);
        $payroll->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll record deleted successfully.');
    }

    public function reports()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);
        return view('payroll.reports', ['currentClient' => $currentClient]);
    }
}
