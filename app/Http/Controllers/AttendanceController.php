<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceMonthlySummary;
use App\Models\AttendanceViolation;
use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\PublicHoliday;
use App\Models\ShiftPattern;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private const WORKED_STATUS_CODES = ['9', '12', 'M'];

    private const LEAVE_STATUS_CODES = ['AL', 'SLF', 'SLH', 'UL'];

    /** Legacy `status` values that mean the employee worked that day. */
    private const WORKED_LEGACY_STATUSES = ['present', 'late', 'holiday', 'half_day', 'mission'];

    /** Legacy `status` values that mean the employee was absent that day. */
    private const ABSENT_LEGACY_STATUSES = ['absent'];

    /** Legacy `status` values that mean the employee was on leave that day. */
    private const LEAVE_LEGACY_STATUSES = ['on_leave'];

    private function dayIsWorked(Attendance $record): bool
    {
        if (in_array($record->status_code, self::WORKED_STATUS_CODES, true)) {
            return true;
        }

        $legacy = (string) $record->status;

        if (in_array($legacy, self::ABSENT_LEGACY_STATUSES, true)
            || in_array($legacy, self::LEAVE_LEGACY_STATUSES, true)) {
            return false;
        }

        if ($record->status_code === 'A' || in_array($record->status_code, self::LEAVE_STATUS_CODES, true)) {
            return false;
        }

        return in_array($legacy, self::WORKED_LEGACY_STATUSES, true) || (float) $record->total_hours > 0;
    }

    private function dayIsAbsent(Attendance $record): bool
    {
        return $record->status_code === 'A'
            || ($record->status_code === null && in_array((string) $record->status, self::ABSENT_LEGACY_STATUSES, true));
    }

    private function dayIsLeave(Attendance $record): bool
    {
        return in_array($record->status_code, self::LEAVE_STATUS_CODES, true)
            || ($record->status_code === null && in_array((string) $record->status, self::LEAVE_LEGACY_STATUSES, true));
    }

    private function dayIsLate(Attendance $record): bool
    {
        return (int) $record->late_minutes > 0 || (string) $record->status === 'late';
    }

    private function statusColor(Attendance $record): string
    {
        if ($record->status_code === 'A' || $this->dayIsAbsent($record)) {
            return 'red';
        }

        if ($this->dayIsLeave($record)) {
            return 'blue';
        }

        if ($this->dayIsWorked($record)) {
            return 'green';
        }

        return 'gray';
    }
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();
        
        // Get employee_id from request
        $selectedEmployeeId = $request->filled('employee_id') ? (int)$request->get('employee_id') : null;

        $currentClient = Client::find($clientId);
        if (!$currentClient) {
            return redirect()->route('dashboard')->with('error', 'Selected client not found.');
        }

        $this->ensureReferenceData($clientId, $date->year);

        // Get all employees for filter dropdown
        $allEmployees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        
        $employees = $allEmployees;
        if ($selectedEmployeeId) {
            $employees = $allEmployees->where('id', $selectedEmployeeId)->values();
        }

        $this->ensureEmployeeShiftAssignments($clientId, $employees);

        $shiftAssignments = $this->getShiftAssignmentsForDate($clientId, $date);

        $attendanceByEmployeeId = Attendance::with(['shiftPattern', 'violations'])
            ->where('client_id', $clientId)
            ->whereDate('attendance_date', $date->toDateString())
            ->get()
            ->keyBy('employee_id');

        $rows = $employees->values()->map(function ($employee, $index) use ($attendanceByEmployeeId, $shiftAssignments) {
            $record = $attendanceByEmployeeId->get($employee->id);
            $shift = $shiftAssignments->get($employee->id);
            $violationFlags = collect($record?->violation_flags ?? []);

            return [
                'serial' => $index + 1,
                'employee' => $employee,
                'attendance' => $record,
                'shift' => $shift,
                'employee_info' => [
                    'employee_id' => $employee->employee_id ?: ('#' . $employee->id),
                    'employee_name' => trim($employee->first_name . ' ' . $employee->last_name),
                    'job_title' => $employee->position ?: '-',
                    'department' => $employee->department ?: '-',
                    'joining_date' => $employee->hire_date?->format('Y-m-d') ?: '-',
                    'place_of_work' => $employee->city ?: ($employee->region ?: 'Main Office'),
                ],
                'violation_flags' => $violationFlags->all(),
            ];
        });

        $summary = $this->buildDailySummary($attendanceByEmployeeId, $employees->count());
        $monthlySummaries = $this->refreshMonthlySummaries(
            $clientId,
            $monthStart,
            $monthEnd,
            $employees->pluck('id')->all()
        );
        $calendar = $this->buildCalendar($clientId, $date, $date);
        $violations = AttendanceViolation::with('employee')
            ->where('client_id', $clientId)
            ->when($selectedEmployeeId, function($q) use ($selectedEmployeeId) {
                return $q->where('employee_id', $selectedEmployeeId);
            })
            ->whereBetween('violation_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->latest('violation_date')
            ->limit(12)
            ->get();
        $approvalQueue = Attendance::with(['employee', 'shiftPattern'])
            ->where('client_id', $clientId)
            ->when($selectedEmployeeId, function($q) use ($selectedEmployeeId) {
                return $q->where('employee_id', $selectedEmployeeId);
            })
            ->where('workflow_status', 'pending_approval')
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderByDesc('attendance_date')
            ->limit(10)
            ->get();
        $shiftPatterns = ShiftPattern::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->map(function (ShiftPattern $pattern) use ($shiftAssignments) {
                return [
                    'pattern' => $pattern,
                    'assigned_count' => $shiftAssignments->filter(fn ($assignment) => (int) $assignment->shift_pattern_id === (int) $pattern->id)->count(),
                ];
            });
        $payrollMetrics = $this->buildPayrollFeedMetrics($clientId, $monthStart, $monthEnd);
        $statusReference = $this->attendanceStatusReference();
        $publicHolidays = PublicHoliday::where(function ($query) use ($clientId) {
                $query->where('client_id', $clientId)->orWhereNull('client_id');
            })
            ->whereBetween('holiday_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('holiday_date')
            ->get();

        return view('attendance.index', [
            'currentClient' => $currentClient,
            'rows' => $rows,
            'date' => $date->toDateString(),
            'summary' => $summary,
            'calendar' => $calendar,
            'monthlySummaries' => $monthlySummaries,
            'violations' => $violations,
            'selectedEmployeeId' => $selectedEmployeeId,
            'allEmployees' => $allEmployees,
            'approvalQueue' => $approvalQueue,
            'shiftPatterns' => $shiftPatterns,
            'payrollMetrics' => $payrollMetrics,
            'statusReference' => $statusReference,
            'publicHolidays' => $publicHolidays,
            'payrollPeriod' => $date->format('Y-m'),
        ]);
    }

    public function upsert(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'No client selected.'], 400);
        }

        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'attendance_date' => 'required|date',
            'status' => 'nullable|string',
            'status_code' => 'nullable|string',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'total_hours' => 'nullable|numeric|min:0|max:24',
            'source' => 'nullable|string|max:30',
            'workflow_status' => 'nullable|string|max:30',
            'manual_entry' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if ((int) $employee->client_id !== (int) $clientId) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $workDate = Carbon::parse($validated['attendance_date'])->startOfDay();
        $this->ensureReferenceData($clientId, $workDate->year);

        $statusCode = $this->resolveStatusCode($validated['status_code'] ?? $validated['status'] ?? null);
        if (!$statusCode) {
            return response()->json(['success' => false, 'message' => 'Invalid attendance status code.'], 422);
        }

        $attendance = DB::transaction(function () use ($clientId, $employee, $workDate, $validated, $statusCode) {
            $attendance = $this->persistAttendance($clientId, $employee, $workDate, array_merge($validated, [
                'status_code' => $statusCode,
            ]));
            $this->refreshMonthlySummaryForEmployee($clientId, $employee, $workDate->copy()->startOfMonth(), $workDate->copy()->endOfMonth());

            return $attendance->fresh(['shiftPattern', 'violations']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'data' => [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'attendance_date' => $attendance->attendance_date?->format('Y-m-d'),
                'status' => $attendance->status,
                'status_code' => $attendance->status_code,
                'status_label' => $attendance->status_code_label,
                'clock_in' => $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : null,
                'clock_out' => $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : null,
                'total_hours' => (float) $attendance->total_hours,
                'overtime_hours' => (float) $attendance->overtime_hours,
                'ordinary_hours' => (float) $attendance->ordinary_hours,
                'rest_day_hours' => (float) $attendance->rest_day_hours,
                'ph_hours' => (float) $attendance->ph_hours,
                'night_hours' => (float) $attendance->night_hours,
                'workflow_status' => $attendance->workflow_status,
                'late_minutes' => (int) $attendance->late_minutes,
                'early_departure_minutes' => (int) $attendance->early_departure_minutes,
                'violation_flags' => $attendance->violation_flags ?? [],
            ],
        ]);
    }

    public function calendar(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json(['success' => false, 'message' => 'Please select a client first.'], 400);
        }

        $monthDate = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        $selectedDate = $request->filled('selected_date')
            ? Carbon::parse($request->get('selected_date'))->startOfDay()
            : $monthDate->copy();

        $this->ensureReferenceData($clientId, $monthDate->year);

        $calendar = $this->buildCalendar($clientId, $monthDate, $selectedDate);

        return response()->json([
            'success' => true,
            'calendar' => $calendar,
        ]);
    }

    public function importTimesheet(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('attendance.index')->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'timesheet' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $validated['timesheet']->store('tmp');
        $fullPath = Storage::path($path);

        $handle = fopen($fullPath, 'r');
        if (!$handle) {
            Storage::delete($path);
            return redirect()->route('attendance.index')->with('error', 'Failed to read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            Storage::delete($path);
            return redirect()->route('attendance.index')->with('error', 'The uploaded CSV is empty.');
        }

        $columns = array_map(function ($h) {
            $h = strtolower(trim((string) $h));
            $h = preg_replace('/\s+/', '_', $h);
            return $h;
        }, $header);

        $required = ['employee_id', 'date'];
        foreach ($required as $req) {
            if (!in_array($req, $columns, true)) {
                fclose($handle);
                Storage::delete($path);
                return redirect()->route('attendance.index')->with('error', "Missing required column: {$req}. Expected: employee_id, date, and status or status_code.");
            }
        }

        $statusColumn = in_array('status_code', $columns, true) ? 'status_code' : (in_array('status', $columns, true) ? 'status' : null);
        if (!$statusColumn) {
            fclose($handle);
            Storage::delete($path);
            return redirect()->route('attendance.index')->with('error', 'Missing required status column. Include either status or status_code.');
        }

        $touchedPeriods = [];
        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && trim((string) $row[0]) === '') {
                continue;
            }

            $assoc = [];
            foreach ($columns as $i => $col) {
                $assoc[$col] = isset($row[$i]) ? trim((string) $row[$i]) : null;
            }

            $employee = $this->resolveEmployee($clientId, $assoc);
            if (!$employee) {
                $skipped++;
                $errors[] = 'Employee not found for row (employee_id=' . ($assoc['employee_id'] ?? '') . ')';
                if (count($errors) > 10) break;
                continue;
            }

            $date = $this->parseDate($assoc['date'] ?? null);
            if (!$date) {
                $skipped++;
                $errors[] = 'Invalid date for employee_id=' . ($assoc['employee_id'] ?? '') . ' (date=' . ($assoc['date'] ?? '') . ')';
                if (count($errors) > 10) break;
                continue;
            }

            $workDate = $this->parseDate($assoc['date'] ?? null);
            if (!$workDate) {
                $skipped++;
                $errors[] = 'Invalid date for employee_id=' . ($assoc['employee_id'] ?? '') . ' (date=' . ($assoc['date'] ?? '') . ')';
                if (count($errors) > 10) break;
                continue;
            }

            $statusCode = $this->resolveStatusCode($assoc[$statusColumn] ?? null);
            if (!$statusCode) {
                $skipped++;
                $errors[] = 'Invalid status for employee_id=' . ($assoc['employee_id'] ?? '') . ' (' . $statusColumn . '=' . ($assoc[$statusColumn] ?? '') . ')';
                if (count($errors) > 10) break;
                continue;
            }

            $clockIn = $this->normalizeTime($assoc['clock_in'] ?? ($assoc['check_in'] ?? null));
            $clockOut = $this->normalizeTime($assoc['clock_out'] ?? ($assoc['check_out'] ?? null));
            $notes = $assoc['notes'] ?? null;
            $source = $assoc['source'] ?? 'manual';
            $manualEntry = filter_var($assoc['manual_entry'] ?? true, FILTER_VALIDATE_BOOLEAN);

            $existing = Attendance::where('client_id', $clientId)
                ->where('employee_id', $employee->id)
                ->whereDate('attendance_date', $workDate)
                ->first();

            $attendance = $this->persistAttendance($clientId, $employee, Carbon::parse($workDate), [
                'status_code' => $statusCode,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'total_hours' => $assoc['total_hours'] ?? null,
                'notes' => $notes,
                'source' => $source,
                'manual_entry' => $manualEntry,
                'workflow_status' => $assoc['workflow_status'] ?? null,
            ], $existing);

            if ($existing) {
                $updated++;
            } else {
                $imported++;
            }

            $periodKey = $employee->id . '-' . Carbon::parse($workDate)->format('Y-m');
            $touchedPeriods[$periodKey] = [
                'employee' => $employee,
                'month_start' => Carbon::parse($workDate)->startOfMonth(),
                'month_end' => Carbon::parse($workDate)->endOfMonth(),
            ];
        }

        fclose($handle);
        Storage::delete($path);

        foreach ($touchedPeriods as $period) {
            $this->refreshMonthlySummaryForEmployee(
                $clientId,
                $period['employee'],
                $period['month_start'],
                $period['month_end']
            );
        }

        $message = "Timesheet import completed. New: {$imported}, Updated: {$updated}, Skipped: {$skipped}.";
        if (!empty($errors)) {
            $message .= ' Issues: ' . implode(' | ', array_slice($errors, 0, 5));
        }

        return redirect()->route('attendance.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $clientId = session('current_client_id');
        $employees = Employee::where('client_id', $clientId)->where('status', 'active')->get(['id', 'employee_id', 'first_name', 'last_name']);

        $filename = 'timesheet_template_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($employees) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['employee_id', 'date', 'status_code', 'clock_in', 'clock_out', 'total_hours', 'notes', 'source', 'manual_entry']);

            foreach ($employees->take(3) as $emp) {
                fputcsv($handle, [
                    $emp->employee_id,
                    now()->toDateString(),
                    '9',
                    '08:00',
                    '17:00',
                    '8.0',
                    'Regular attendance',
                    'manual',
                    'true',
                ]);
                fputcsv($handle, [
                    $emp->employee_id,
                    now()->addDay()->toDateString(),
                    'AL',
                    '',
                    '',
                    '0',
                    'Annual Leave',
                    'manual',
                    'true',
                ]);
                fputcsv($handle, [
                    $emp->employee_id,
                    now()->addDays(2)->toDateString(),
                    'A',
                    '',
                    '',
                    '0',
                    'Absent - no reason',
                    'manual',
                    'true',
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['--- STATUS CODE REFERENCE ---']);
            fputcsv($handle, ['Code', 'Meaning']);
            fputcsv($handle, ['9', 'Present / Ordinary Hours']);
            fputcsv($handle, ['12', 'Overtime (12-hour shift)']);
            fputcsv($handle, ['A', 'Absent']);
            fputcsv($handle, ['AL', 'Annual Leave']);
            fputcsv($handle, ['SLF', 'Sick Leave Full Pay']);
            fputcsv($handle, ['SLH', 'Sick Leave Half Pay']);
            fputcsv($handle, ['UL', 'Unpaid Leave']);
            fputcsv($handle, ['M', 'Official Mission']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function timesheets(Request $request)
    {
        $clientId = (int) session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $data = $this->loadTimesheetData($clientId, $request);

        return view('attendance.timesheets', [
            'currentClient' => $data['currentClient'],
            'timesheetData' => $data['timesheetData'],
            'employees' => $data['employees'],
            'departments' => $data['departments'],
            'monthDate' => $data['monthStart']->copy(),
            'monthLabel' => $data['monthStart']->format('F Y'),
            'prevMonth' => $data['monthStart']->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $data['monthStart']->copy()->addMonth()->format('Y-m'),
            'stats' => $data['stats'],
        ]);
    }

    /**
     * Load every employee with live per-month metrics in a single batched
     * attendance query (no N+1), then apply the requested filters.
     *
     * Multi-tenant safe: everything is scoped to the active client.
     */
    private function loadTimesheetData(int $clientId, Request $request): array
    {
        $currentClient = Client::find($clientId);
        if (!$currentClient) {
            abort(404, 'Selected client not found.');
        }

        $monthStart = $this->resolveMonthDate($request, 'month');
        $monthEnd = $monthStart->copy()->endOfMonth();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $departments = Department::where('client_id', $clientId)
            ->orderBy('name')
            ->get();

        $attendance = Attendance::where('client_id', $clientId)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('attendance_date')
            ->get()
            ->groupBy('employee_id');

        $rows = $employees->map(function (Employee $employee) use ($attendance, $monthStart) {
            return $this->buildTimesheetRow($employee, $attendance->get($employee->id, collect()), $monthStart);
        });

        $rows = $this->filterTimesheetRows($rows, $request);

        return [
            'currentClient' => $currentClient,
            'monthStart' => $monthStart->copy()->startOfMonth(),
            'monthEnd' => $monthEnd,
            'employees' => $employees,
            'departments' => $departments,
            'timesheetData' => $rows,
            'stats' => $this->summarizeTimesheetStats($rows),
        ];
    }

    /**
     * Compute per-employee monthly metrics from live attendance records,
     * using the exact same status-code semantics as payroll.
     */
    private function buildTimesheetRow(Employee $employee, Collection $records, Carbon $monthStart): array
    {
        $workedDays = $records->filter(fn (Attendance $r) => $this->dayIsWorked($r))->count();
        $absentDays = $records->filter(fn (Attendance $r) => $this->dayIsAbsent($r))->count();
        $leaveDays = $records->filter(fn (Attendance $r) => $this->dayIsLeave($r))->count();
        $lateDays = $records->filter(fn (Attendance $r) => $this->dayIsLate($r))->count();

        $ordinaryHours = round((float) $records->sum('ordinary_hours'), 2);
        $restDayHours = round((float) $records->sum('rest_day_hours'), 2);
        $phHours = round((float) $records->sum('ph_hours'), 2);
        $overtimeHours = round((float) $records->sum('overtime_hours'), 2);
        $nightHours = round((float) $records->sum('night_hours'), 2);

        $accounted = $workedDays + $absentDays + $leaveDays;
        $attendanceRate = $accounted > 0 ? round(($workedDays / $accounted) * 100, 1) : null;

        return [
            'employee' => $employee,
            'total_days' => $monthStart->daysInMonth,
            'recorded_days' => $records->count(),
            'worked_days' => $workedDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'leave_days' => $leaveDays,
            'leave_breakdown' => [
                'AL' => $records->filter(fn (Attendance $r) => $r->status_code === 'AL' || $r->status_code === null && $r->status === 'on_leave')->count(),
                'SLF' => $records->where('status_code', 'SLF')->count(),
                'SLH' => $records->where('status_code', 'SLH')->count(),
                'UL' => $records->where('status_code', 'UL')->count(),
            ],
            'attendance_rate' => $attendanceRate,
            'ordinary_hours' => $ordinaryHours,
            'rest_day_hours' => $restDayHours,
            'ph_hours' => $phHours,
            'overtime_hours' => $overtimeHours,
            'night_hours' => $nightHours,
            'total_paid_hours' => round($ordinaryHours + $restDayHours + $phHours + $overtimeHours, 2),
            'has_absence' => $absentDays > 0,
            'has_leave' => $leaveDays > 0,
            'has_late' => $lateDays > 0,
            'has_overtime' => $overtimeHours > 0,
            'low_attendance' => $accounted > 0 && $attendanceRate < 90,
            'no_records' => $records->isEmpty(),
        ];
    }

    private function summarizeTimesheetStats(Collection $rows): array
    {
        $worked = $rows->sum('worked_days');
        $absent = $rows->sum('absent_days');
        $leave = $rows->sum('leave_days');
        $accounted = $worked + $absent + $leave;

        return [
            'total_employees' => $rows->count(),
            'total_worked_days' => $worked,
            'total_absent_days' => $absent,
            'total_leave_days' => $leave,
            'total_late_days' => $rows->sum('late_days'),
            'attendance_rate' => $accounted > 0 ? round(($worked / $accounted) * 100, 1) : 0,
            'total_overtime_hours' => round((float) $rows->sum('overtime_hours'), 2),
            'total_night_hours' => round((float) $rows->sum('night_hours'), 2),
            'total_paid_hours' => round((float) $rows->sum('total_paid_hours'), 2),
            'employees_with_absence' => $rows->where('has_absence', true)->count(),
            'employees_with_low_attendance' => $rows->where('low_attendance', true)->count(),
        ];
    }

    private function filterTimesheetRows(Collection $rows, Request $request): Collection
    {
        $search = strtolower(trim((string) $request->query('search')));
        $department = trim((string) $request->query('department'));
        $employeeStatus = trim((string) $request->query('employment_status'));
        $status = (string) $request->query('status');

        return $rows->filter(function (array $row) use ($search, $department, $employeeStatus, $status) {
            $employee = $row['employee'];

            if ($department !== '' && ($employee->department ?? '') !== $department) {
                return false;
            }

            if ($employeeStatus !== '' && ($employee->status ?? '') !== $employeeStatus) {
                return false;
            }

            if ($search !== '') {
                $name = strtolower(trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')));
                $empId = strtolower((string) ($employee->employee_id ?? ''));
                if (!str_contains($name, $search) && !str_contains($empId, $search)) {
                    return false;
                }
            }

            return match ($status) {
                'has_absence' => (bool) $row['has_absence'],
                'has_leave' => (bool) $row['has_leave'],
                'has_overtime' => (bool) $row['has_overtime'],
                'has_late' => (bool) $row['has_late'],
                'low_attendance' => (bool) $row['low_attendance'],
                'no_records' => (bool) $row['no_records'],
                default => true,
            };
        })->values();
    }

    /**
     * Safely resolve the "?month=YYYY-MM" query value; falls back to the
     * current month for missing or out-of-range values.
     */
    private function resolveMonthDate(Request $request, string $key): Carbon
    {
        $raw = trim((string) $request->query($key));

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $raw)) {
            try {
                $date = Carbon::createFromFormat('Y-m', $raw)->startOfMonth();
                if ($date->year >= 2000 && $date->lte(now()->addMonths(12)->endOfMonth())) {
                    return $date;
                }
            } catch (\Throwable $e) {
                // fall through to default
            }
        }

        return now()->startOfMonth();
    }

    /**
     * Stream a filtered CSV export of the month's timesheet summary.
     */
    public function timesheetExport(Request $request)
    {
        $clientId = (int) session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $data = $this->loadTimesheetData($clientId, $request);
        $filename = 'timesheets_' . $data['monthStart']->format('Y-m') . '.csv';

        $callback = function () use ($data) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Monthly Timesheet - ' . $data['monthStart']->format('F Y')]);
            fputcsv($handle, ['Generated: ' . now()->format('Y-m-d H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Employee ID', 'Name', 'Department', 'Position', 'Employee Status',
                'Worked Days', 'Absent Days', 'Annual Leave', 'Sick Full Pay',
                'Sick Half Pay', 'Unpaid Leave', 'Late Days', 'Attendance Rate %',
                'Ordinary Hrs', 'Rest Day Hrs', 'Public Holiday Hrs',
                'Overtime Hrs', 'Night Hrs', 'Total Paid Hrs',
            ]);

            foreach ($data['timesheetData'] as $row) {
                $e = $row['employee'];
                $lb = $row['leave_breakdown'];

                fputcsv($handle, [
                    $e->employee_id ?: $e->id,
                    trim(($e->first_name ?? '') . ' ' . ($e->last_name ?? '')),
                    $e->department ?? '',
                    $e->position ?? '',
                    $e->status ?? '',
                    $row['worked_days'],
                    $row['absent_days'],
                    $lb['AL'],
                    $lb['SLF'],
                    $lb['SLH'],
                    $lb['UL'],
                    $row['late_days'],
                    $row['attendance_rate'] !== null ? number_format($row['attendance_rate'], 1) : 'N/A',
                    number_format($row['ordinary_hours'], 2),
                    number_format($row['rest_day_hours'], 2),
                    number_format($row['ph_hours'], 2),
                    number_format($row['overtime_hours'], 2),
                    number_format($row['night_hours'], 2),
                    number_format($row['total_paid_hours'], 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * JSON endpoint powering the per-employee daily breakdown modal.
     * Every day of the month is returned so the front-end can render an
     * accurate calendar even when a day has no attendance record.
     */
    public function timesheetDetail(Request $request, Employee $employee)
    {
        $clientId = (int) session('current_client_id');
        if (!$clientId) {
            return response()->json(['error' => 'Please select a client first.'], 403);
        }

        if ((int) $employee->client_id !== $clientId) {
            abort(403);
        }

        $monthStart = $this->resolveMonthDate($request, 'month');
        $monthEnd = $monthStart->copy()->endOfMonth();

        $records = Attendance::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->orderBy('attendance_date')
            ->get()
            ->keyBy(fn (Attendance $r) => $r->attendance_date->toDateString());

        $days = [];
        for ($d = 1; $d <= $monthStart->daysInMonth; $d++) {
            $date = $monthStart->copy()->day($d);
            $record = $records->get($date->toDateString());

            $days[] = $record
                ? $this->serializeAttendanceDay($record, $date)
                : $this->emptyAttendanceDay($date);
        }

        $raw = $this->buildTimesheetRow($employee, collect($records->all()), $monthStart);

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'name' => trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
                'department' => $employee->department,
                'position' => $employee->position,
                'status' => $employee->status,
                'hire_date' => $employee->hire_date?->toDateString(),
            ],
            'month_label' => $monthStart->format('F Y'),
            'summary' => [
                'total_days' => $raw['total_days'],
                'recorded_days' => $raw['recorded_days'],
                'worked_days' => $raw['worked_days'],
                'absent_days' => $raw['absent_days'],
                'leave_days' => $raw['leave_days'],
                'late_days' => $raw['late_days'],
                'attendance_rate' => $raw['attendance_rate'],
                'overtime_hours' => $raw['overtime_hours'],
                'night_hours' => $raw['night_hours'],
                'total_paid_hours' => $raw['total_paid_hours'],
            ],
            'days' => $days,
        ]);
    }

    private function serializeAttendanceDay(Attendance $record, Carbon $date): array
    {
        $statusLabel = $record->status_code
            ? $record->getStatusCodeLabelAttribute()
            : (self::LEGACY_STATUS_LABELS[$record->status] ?? 'Present');

        return [
            'date' => $date->toDateString(),
            'day' => (int) $date->format('j'),
            'weekday' => $date->format('l'),
            'is_weekend' => $date->isWeekend(),
            'status_code' => $record->status_code,
            'status_label' => $statusLabel,
            'color' => $this->statusColor($record),
            'clock_in' => $record->clock_in ? $record->clock_in->format('H:i') : null,
            'clock_out' => $record->clock_out ? $record->clock_out->format('H:i') : null,
            'total_hours' => round((float) $record->total_hours, 2),
            'ordinary_hours' => round((float) $record->ordinary_hours, 2),
            'overtime_hours' => round((float) $record->overtime_hours, 2),
            'rest_day_hours' => round((float) $record->rest_day_hours, 2),
            'ph_hours' => round((float) $record->ph_hours, 2),
            'night_hours' => round((float) $record->night_hours, 2),
            'late_minutes' => (int) $record->late_minutes,
            'early_departure_minutes' => (int) $record->early_departure_minutes,
            'source' => $record->source,
            'notes' => $record->notes,
        ];
    }

    private const LEGACY_STATUS_LABELS = [
        'present' => 'Present',
        'late' => 'Late',
        'holiday' => 'Holiday',
        'half_day' => 'Half Day',
        'mission' => 'Official Mission',
        'on_leave' => 'On Leave',
        'absent' => 'Absent',
    ];

    private function emptyAttendanceDay(Carbon $date): array
    {
        return [
            'date' => $date->toDateString(),
            'day' => (int) $date->format('j'),
            'weekday' => $date->format('l'),
            'is_weekend' => $date->isWeekend(),
            'status_code' => null,
            'status_label' => 'No Record',
            'color' => 'gray',
            'clock_in' => null,
            'clock_out' => null,
            'total_hours' => 0,
            'ordinary_hours' => 0,
            'overtime_hours' => 0,
            'rest_day_hours' => 0,
            'ph_hours' => 0,
            'night_hours' => 0,
            'late_minutes' => 0,
            'early_departure_minutes' => 0,
            'source' => null,
            'notes' => null,
        ];
    }

    public function shifts()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        $shiftPatterns = \App\Models\ShiftPattern::where('client_id', $clientId)->get();
        
        // Calculate stats
        $totalShifts = $shiftPatterns->count();
        $activeShifts = $shiftPatterns->where('is_active', true)->count();
        $nightShifts = $shiftPatterns->where('is_night_shift', true)->count();
        $inactiveShifts = $totalShifts - $activeShifts;

        return view('attendance.shifts', [
            'currentClient' => $currentClient,
            'shiftPatterns' => $shiftPatterns,
            'stats' => [
                'total_shifts' => $totalShifts,
                'active_shifts' => $activeShifts,
                'night_shifts' => $nightShifts,
                'inactive_shifts' => $inactiveShifts,
            ],
        ]);
    }

    public function violations()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        $violations = \App\Models\AttendanceViolation::with('employee')
            ->where('client_id', $clientId)
            ->latest('violation_date')
            ->paginate(20);
        
        $employees = \App\Models\Employee::where('client_id', $clientId)->orderBy('first_name')->orderBy('last_name')->get();
        
        // Calculate stats
        $totalViolations = $violations->total();
        $openViolations = \App\Models\AttendanceViolation::where('client_id', $clientId)->where('status', 'open')->count();
        $lateArrivals = \App\Models\AttendanceViolation::where('client_id', $clientId)->where('violation_type', 'late_arrival')->count();
        $earlyDepartures = \App\Models\AttendanceViolation::where('client_id', $clientId)->where('violation_type', 'early_departure')->count();
        $absenteeism = \App\Models\AttendanceViolation::where('client_id', $clientId)->where('violation_type', 'absenteeism')->count();

        return view('attendance.violations', [
            'currentClient' => $currentClient,
            'violations' => $violations,
            'employees' => $employees,
            'stats' => [
                'total_violations' => $totalViolations,
                'open_violations' => $openViolations,
                'late_arrivals' => $lateArrivals,
                'early_departures' => $earlyDepartures,
                'absenteeism' => $absenteeism,
            ],
        ]);
    }

    public function storeShift(\Illuminate\Http\Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $validated = $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_duration' => 'required|integer|min:0',
            'allowance_rate' => 'nullable|numeric|min:0',
            'is_night_shift' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['client_id'] = $clientId;
        $validated['is_night_shift'] = $request->has('is_night_shift');
        $validated['is_active'] = $request->has('is_active');

        \App\Models\ShiftPattern::create($validated);

        return redirect()->route('attendance.shifts')->with('success', 'Shift created successfully.');
    }

    public function updateShift(\Illuminate\Http\Request $request, \App\Models\ShiftPattern $shift)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        if ($shift->client_id != $clientId) {
            abort(403);
        }

        $validated = $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_duration' => 'required|integer|min:0',
            'allowance_rate' => 'nullable|numeric|min:0',
            'is_night_shift' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_night_shift'] = $request->has('is_night_shift');
        $validated['is_active'] = $request->has('is_active');

        $shift->update($validated);

        return redirect()->route('attendance.shifts')->with('success', 'Shift updated successfully.');
    }

    public function destroyShift(\App\Models\ShiftPattern $shift)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        if ($shift->client_id != $clientId) {
            abort(403);
        }

        $shift->delete();

        return redirect()->route('attendance.shifts')->with('success', 'Shift deleted successfully.');
    }

    public function updateViolation(\Illuminate\Http\Request $request, \App\Models\AttendanceViolation $violation)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        if ($violation->client_id != $clientId) {
            abort(403);
        }

        $validated = $request->validate([
            'details' => 'nullable|string',
            'status' => 'required|in:open,closed',
        ]);

        $violation->update($validated);

        return redirect()->route('attendance.violations')->with('success', 'Violation updated successfully.');
    }

    public function closeViolation(\App\Models\AttendanceViolation $violation)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        if ($violation->client_id != $clientId) {
            abort(403);
        }

        $violation->update(['status' => 'closed']);

        return redirect()->route('attendance.violations')->with('success', 'Violation closed successfully.');
    }

    private function resolveEmployee(int $clientId, array $row): ?Employee
    {
        $employeeIdentifier = trim((string) ($row['employee_id'] ?? ''));
        if ($employeeIdentifier === '') {
            return null;
        }

        $employee = Employee::where('client_id', $clientId)
            ->where('employee_id', $employeeIdentifier)
            ->first();

        if ($employee) {
            return $employee;
        }

        if (ctype_digit($employeeIdentifier)) {
            $employee = Employee::where('client_id', $clientId)
                ->where('id', (int) $employeeIdentifier)
                ->first();
        }

        return $employee;
    }

    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeTime(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '-') return null;

        try {
            return Carbon::createFromFormat('H:i', $value)->format('H:i');
        } catch (\Throwable $e) {
            try {
                return Carbon::parse($value)->format('H:i');
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }

    private function resolveStatusCode(?string $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        if ($value === '') {
            return null;
        }

        $map = [
            'A' => 'A',
            'ABSENT' => 'A',
            'AL' => 'AL',
            'ANNUAL_LEAVE' => 'AL',
            'ANNUAL LEAVE' => 'AL',
            'SLF' => 'SLF',
            'SICK_LEAVE_FULL_PAY' => 'SLF',
            'SICK LEAVE FULL PAY' => 'SLF',
            'SLH' => 'SLH',
            'SICK_LEAVE_HALF_PAY' => 'SLH',
            'SICK LEAVE HALF PAY' => 'SLH',
            'UL' => 'UL',
            'UNPAID_LEAVE' => 'UL',
            'UNPAID LEAVE' => 'UL',
            'M' => 'M',
            'MISSION' => 'M',
            'OFFICIAL_MISSION' => 'M',
            'OFFICIAL MISSION' => 'M',
            '9' => '9',
            'PRESENT' => '9',
            'ORDINARY' => '9',
            'ORDINARY_HOURS' => '9',
            'ORDINARY HOURS' => '9',
            'LATE' => '9',
            '12' => '12',
            'OVERTIME' => '12',
            'HALF_DAY' => '9',
            'ON_LEAVE' => 'AL',
            'LEAVE' => 'AL',
            'HOLIDAY' => '9',
        ];

        return $map[$value] ?? null;
    }

    private function buildCalendar(int $clientId, Carbon $monthDate, Carbon $selectedDate): array
    {
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $monthStatsRows = Attendance::where('client_id', $clientId)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->select('attendance_date', 'status_code', DB::raw('count(*) as c'))
            ->groupBy('attendance_date', 'status_code')
            ->get();
        $violationRows = AttendanceViolation::where('client_id', $clientId)
            ->whereBetween('violation_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->select('violation_date', DB::raw('count(*) as c'))
            ->groupBy('violation_date')
            ->get()
            ->pluck('c', 'violation_date');
        $holidayRows = PublicHoliday::where(function ($query) use ($clientId) {
                $query->where('client_id', $clientId)->orWhereNull('client_id');
            })
            ->whereBetween('holiday_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(fn (PublicHoliday $holiday) => $holiday->holiday_date->toDateString());

        $monthStats = [];
        foreach ($monthStatsRows as $row) {
            $dayKey = Carbon::parse($row->attendance_date)->toDateString();
            if (!isset($monthStats[$dayKey])) {
                $monthStats[$dayKey] = [];
            }
            $monthStats[$dayKey][$row->status_code] = (int) $row->c;
        }

        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $calendarDays = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $dayKey = $cursor->toDateString();
            $inMonth = $cursor->month === $monthStart->month;
            $isWeekend = $cursor->isWeekend();
            $counts = $monthStats[$dayKey] ?? [];
            $isSelected = $dayKey === $selectedDate->toDateString();
            $holiday = $holidayRows->get($dayKey);

            $worked = (int) (($counts['9'] ?? 0) + ($counts['12'] ?? 0) + ($counts['M'] ?? 0));
            $leave = (int) (($counts['AL'] ?? 0) + ($counts['SLF'] ?? 0) + ($counts['SLH'] ?? 0) + ($counts['UL'] ?? 0));
            $absent = (int) ($counts['A'] ?? 0);

            $calendarDays[] = [
                'date' => $dayKey,
                'day' => (int) $cursor->format('j'),
                'in_month' => $inMonth,
                'is_weekend' => $isWeekend,
                'is_selected' => $isSelected,
                'holiday_name' => $holiday?->holiday_name,
                'is_public_holiday' => (bool) $holiday,
                'violations' => (int) ($violationRows[$dayKey] ?? 0),
                'counts' => [
                    'worked' => $worked,
                    'leave' => $leave,
                    'absent' => $absent,
                    'mission' => (int) ($counts['M'] ?? 0),
                    'overtime_shift' => (int) ($counts['12'] ?? 0),
                ],
            ];

            $cursor->addDay();
        }

        return [
            'label' => $monthStart->format('F Y'),
            'prev' => $monthStart->copy()->subMonth()->startOfMonth()->toDateString(),
            'next' => $monthStart->copy()->addMonth()->startOfMonth()->toDateString(),
            'days' => $calendarDays,
        ];
    }

    private function persistAttendance(int $clientId, Employee $employee, Carbon $workDate, array $input, ?Attendance $existing = null): Attendance
    {
        $statusCode = $this->resolveStatusCode($input['status_code'] ?? $input['status'] ?? null) ?? '9';
        $shift = $this->resolveShiftForEmployee($clientId, $employee, $workDate);
        $holiday = $this->getHolidayForDate($clientId, $workDate);
        $source = $this->normalizeSource($input['source'] ?? null);
        $manualEntry = filter_var($input['manual_entry'] ?? ($source === 'manual'), FILTER_VALIDATE_BOOLEAN);
        $timeMetrics = $this->calculateTimeMetrics(
            $workDate,
            $statusCode,
            $input['clock_in'] ?? null,
            $input['clock_out'] ?? null,
            $shift,
            $holiday !== null,
            isset($input['total_hours']) && is_numeric($input['total_hours']) ? (float) $input['total_hours'] : null
        );

        $workflowStatus = trim((string) ($input['workflow_status'] ?? ''));
        if ($workflowStatus === '') {
            // P0: manual and imported entries are treated as approved so they
            // flow into payroll. A dedicated approval workflow is a later phase.
            $workflowStatus = 'approved';
        }

        $attendance = Attendance::updateOrCreate(
            [
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'attendance_date' => $workDate->toDateString(),
            ],
            [
                'status' => $this->mapStatusCodeToLegacyStatus($statusCode, $timeMetrics['late_minutes'], $holiday !== null, $timeMetrics['productive_hours']),
                'status_code' => $statusCode,
                'clock_in' => $input['clock_in'] ?? null,
                'clock_out' => $input['clock_out'] ?? null,
                'total_hours' => $timeMetrics['total_hours'],
                'ordinary_hours' => $timeMetrics['ordinary_hours'],
                'overtime_hours' => $timeMetrics['overtime_hours'],
                'rest_day_hours' => $timeMetrics['rest_day_hours'],
                'ph_hours' => $timeMetrics['ph_hours'],
                'night_hours' => $timeMetrics['night_hours'],
                'source' => $source,
                'manual_entry' => $manualEntry,
                'workflow_status' => $workflowStatus,
                'approved_by' => $workflowStatus === 'approved' ? (Auth::id() ?: ($existing?->approved_by)) : null,
                'approved_at' => $workflowStatus === 'approved' ? now() : null,
                'late_minutes' => $timeMetrics['late_minutes'],
                'early_departure_minutes' => $timeMetrics['early_departure_minutes'],
                'violation_flags' => [],
                'shift_pattern_id' => $shift?->id,
                'notes' => $input['notes'] ?? null,
                'location' => $holiday?->holiday_name ?: ($existing?->location),
            ]
        );

        $flags = $this->syncViolations($attendance, $employee, $workDate);
        if (($attendance->violation_flags ?? []) !== $flags) {
            $attendance->forceFill(['violation_flags' => $flags])->save();
        }

        return $attendance;
    }

    private function calculateTimeMetrics(
        Carbon $workDate,
        string $statusCode,
        ?string $clockIn,
        ?string $clockOut,
        ?ShiftPattern $shift,
        bool $isPublicHoliday,
        ?float $providedTotalHours = null
    ): array {
        $breakHours = round(((float) ($shift?->break_duration ?? 60)) / 60, 2);
        $totalHours = 0.0;
        $productiveHours = 0.0;

        if ($clockIn && $clockOut) {
            $start = Carbon::parse($workDate->toDateString() . ' ' . $clockIn);
            $end = Carbon::parse($workDate->toDateString() . ' ' . $clockOut);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            $totalHours = round($start->diffInMinutes($end) / 60, 2);
            $productiveHours = round(max(0, $totalHours - $breakHours), 2);
        } elseif ($providedTotalHours !== null) {
            $totalHours = round($providedTotalHours, 2);
            $productiveHours = round(max(0, $totalHours - min($breakHours, $totalHours)), 2);
        } elseif (in_array($statusCode, ['9', 'M'], true)) {
            $totalHours = 9.0;
            $productiveHours = 8.0;
        } elseif ($statusCode === '12') {
            $totalHours = 12.0;
            $productiveHours = 11.0;
        }

        $ordinaryHours = 0.0;
        $overtimeHours = 0.0;
        $restDayHours = 0.0;
        $publicHolidayHours = 0.0;

        if (in_array($statusCode, ['9', '12', 'M'], true)) {
            if ($isPublicHoliday) {
                $publicHolidayHours = $productiveHours;
            } elseif ($workDate->isWeekend()) {
                $restDayHours = $productiveHours;
            } else {
                $ordinaryHours = min(8.0, $productiveHours);
                $overtimeHours = max(0, $productiveHours - 8.0);
            }
        }

        if ($statusCode === '12' && !$clockIn && !$clockOut && !$workDate->isWeekend() && !$isPublicHoliday) {
            $ordinaryHours = 8.0;
            $overtimeHours = 3.0;
        }

        $lateMinutes = 0;
        $earlyDepartureMinutes = 0;
        if ($clockIn && $shift) {
            $scheduledStart = Carbon::parse($workDate->toDateString() . ' ' . $shift->start_time);
            $actualStart = Carbon::parse($workDate->toDateString() . ' ' . $clockIn);
            if ($actualStart->greaterThan($scheduledStart)) {
                $lateMinutes = $scheduledStart->diffInMinutes($actualStart);
            }
        }
        if ($clockOut && $shift) {
            $scheduledEnd = Carbon::parse($workDate->toDateString() . ' ' . $shift->end_time);
            $actualEnd = Carbon::parse($workDate->toDateString() . ' ' . $clockOut);
            if ($scheduledEnd->lessThanOrEqualTo(Carbon::parse($workDate->toDateString() . ' ' . $shift->start_time))) {
                $scheduledEnd->addDay();
            }
            if ($actualEnd->lessThanOrEqualTo(Carbon::parse($workDate->toDateString() . ' ' . $shift->start_time))) {
                $actualEnd->addDay();
            }
            if ($actualEnd->lessThan($scheduledEnd)) {
                $earlyDepartureMinutes = $actualEnd->diffInMinutes($scheduledEnd);
            }
        }

        $nightHours = $this->calculateNightHours($workDate, $clockIn, $clockOut, $productiveHours, $shift);

        return [
            'total_hours' => round($totalHours, 2),
            'productive_hours' => round($productiveHours, 2),
            'ordinary_hours' => round($ordinaryHours, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'rest_day_hours' => round($restDayHours, 2),
            'ph_hours' => round($publicHolidayHours, 2),
            'night_hours' => round($nightHours, 2),
            'late_minutes' => $lateMinutes,
            'early_departure_minutes' => $earlyDepartureMinutes,
        ];
    }

    private function calculateNightHours(
        Carbon $workDate,
        ?string $clockIn,
        ?string $clockOut,
        float $fallbackHours,
        ?ShiftPattern $shift
    ): float {
        if ($clockIn && $clockOut) {
            $start = Carbon::parse($workDate->toDateString() . ' ' . $clockIn);
            $end = Carbon::parse($workDate->toDateString() . ' ' . $clockOut);
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

        if ($shift?->is_night_shift) {
            return round($fallbackHours, 2);
        }

        return 0.0;
    }

    private function mapStatusCodeToLegacyStatus(string $statusCode, int $lateMinutes, bool $isHoliday, float $productiveHours): string
    {
        if ($statusCode === 'A') {
            return 'absent';
        }

        if (in_array($statusCode, ['AL', 'SLF', 'SLH', 'UL'], true)) {
            return 'on_leave';
        }

        if ($isHoliday && $productiveHours > 0) {
            return 'holiday';
        }

        if ($lateMinutes > 0) {
            return 'late';
        }

        return 'present';
    }

    private function buildDailySummary(Collection $attendanceByEmployeeId, int $totalEmployees): array
    {
        $records = $attendanceByEmployeeId->values();

        return [
            'worked' => $records->whereIn('status_code', ['9', '12', 'M'])->count(),
            'late' => $records->where('late_minutes', '>', 0)->count(),
            'absent' => $records->where('status_code', 'A')->count(),
            'leave' => $records->whereIn('status_code', ['AL', 'SLF', 'SLH', 'UL'])->count(),
            'open_violations' => $records->sum(fn ($record) => count($record->violation_flags ?? [])),
            'pending_approval' => $records->where('workflow_status', 'pending_approval')->count(),
            'total' => $totalEmployees,
        ];
    }

    private function buildPayrollFeedMetrics(int $clientId, Carbon $monthStart, Carbon $monthEnd): array
    {
        $attendance = Attendance::where('client_id', $clientId)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        return [
            'ordinary_hours' => round((float) $attendance->sum('ordinary_hours'), 2),
            'overtime_hours' => round((float) $attendance->sum('overtime_hours'), 2),
            'rest_day_hours' => round((float) $attendance->sum('rest_day_hours'), 2),
            'public_holiday_hours' => round((float) $attendance->sum('ph_hours'), 2),
            'night_hours' => round((float) $attendance->sum('night_hours'), 2),
            'violations' => AttendanceViolation::where('client_id', $clientId)
                ->whereBetween('violation_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count(),
        ];
    }

    private function refreshMonthlySummaries(int $clientId, Carbon $monthStart, Carbon $monthEnd, array $employeeIds): Collection
    {
        $employees = Employee::where('client_id', $clientId)
            ->whereIn('id', $employeeIds)
            ->get()
            ->keyBy('id');

        foreach ($employees as $employee) {
            $this->refreshMonthlySummaryForEmployee($clientId, $employee, $monthStart, $monthEnd);
        }

        return AttendanceMonthlySummary::with('employee')
            ->where('client_id', $clientId)
            ->where('month', $monthStart->month)
            ->where('year', $monthStart->year)
            ->orderByDesc('worked_days')
            ->get();
    }

    private function refreshMonthlySummaryForEmployee(int $clientId, Employee $employee, Carbon $monthStart, Carbon $monthEnd): void
    {
        $attendance = Attendance::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        AttendanceMonthlySummary::updateOrCreate(
            [
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'month' => $monthStart->month,
                'year' => $monthStart->year,
            ],
            [
                'total_days' => $monthStart->daysInMonth,
                'worked_days' => $attendance->whereIn('status_code', ['9', '12', 'M'])->count(),
                'absent_days' => $attendance->where('status_code', 'A')->count(),
                'leave_days' => $attendance->whereIn('status_code', ['AL', 'SLF', 'SLH', 'UL'])->count(),
                'overtime_hours' => round((float) $attendance->sum('overtime_hours'), 2),
                'night_hours' => round((float) $attendance->sum('night_hours'), 2),
            ]
        );
    }

    private function syncViolations(Attendance $attendance, Employee $employee, Carbon $workDate): array
    {
        $types = [
            'late_arrival',
            'early_departure',
            'absenteeism',
            'daily_work_limit',
            'weekly_work_limit',
            'monthly_overtime_limit',
            'weekly_rest_violation',
        ];

        AttendanceViolation::where('client_id', $attendance->client_id)
            ->where('employee_id', $employee->id)
            ->where('violation_date', $workDate->toDateString())
            ->whereIn('violation_type', $types)
            ->delete();

        $flags = [];

        if ((int) $attendance->late_minutes > 0) {
            $flags[] = 'late_arrival';
            $this->createViolation($attendance, $employee, $workDate, 'late_arrival', 'Late arrival detected (' . $attendance->late_minutes . ' minutes).');
        }

        if ((int) $attendance->early_departure_minutes > 0) {
            $flags[] = 'early_departure';
            $this->createViolation($attendance, $employee, $workDate, 'early_departure', 'Unauthorised early departure detected (' . $attendance->early_departure_minutes . ' minutes).');
        }

        if ($attendance->status_code === 'A') {
            $flags[] = 'absenteeism';
            $this->createViolation($attendance, $employee, $workDate, 'absenteeism', 'Employee marked absent without attendance hours recorded.');
        }

        if ((float) $attendance->total_hours > 12) {
            $flags[] = 'daily_work_limit';
            $this->createViolation($attendance, $employee, $workDate, 'daily_work_limit', 'Daily work exceeded 12 hours.');
        }

        $weekStart = $workDate->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $workDate->copy()->endOfWeek(Carbon::SUNDAY);
        $weekRecords = Attendance::where('client_id', $attendance->client_id)
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();
        $weekHours = round((float) $weekRecords->sum(fn ($record) => (float) $record->ordinary_hours + (float) $record->overtime_hours + (float) $record->rest_day_hours + (float) $record->ph_hours), 2);

        if ($weekHours > 45) {
            $flags[] = 'weekly_work_limit';
            $this->createViolation($attendance, $employee, $workDate, 'weekly_work_limit', 'Weekly hours reached ' . $weekHours . ', exceeding the 45-hour maximum.');
        }

        $workedDaysInWeek = $weekRecords->filter(function ($record) {
            return ((float) $record->ordinary_hours + (float) $record->overtime_hours + (float) $record->rest_day_hours + (float) $record->ph_hours) > 0;
        })->count();
        if ($workedDaysInWeek >= 7) {
            $flags[] = 'weekly_rest_violation';
            $this->createViolation($attendance, $employee, $workDate, 'weekly_rest_violation', 'No 24-hour uninterrupted weekly rest period detected.');
        }

        $monthStart = $workDate->copy()->startOfMonth();
        $monthEnd = $workDate->copy()->endOfMonth();
        $monthlyOvertime = round((float) Attendance::where('client_id', $attendance->client_id)
            ->where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('overtime_hours'), 2);
        if ($monthlyOvertime > 50) {
            $flags[] = 'monthly_overtime_limit';
            $this->createViolation($attendance, $employee, $workDate, 'monthly_overtime_limit', 'Monthly overtime reached ' . $monthlyOvertime . ' hours, exceeding the 50-hour maximum.');
        }

        return array_values(array_unique($flags));
    }

    private function createViolation(Attendance $attendance, Employee $employee, Carbon $workDate, string $type, string $details): void
    {
        AttendanceViolation::updateOrCreate(
            [
                'client_id' => $attendance->client_id,
                'employee_id' => $employee->id,
                'attendance_id' => $attendance->id,
                'violation_date' => $workDate->toDateString(),
                'violation_type' => $type,
            ],
            [
                'details' => $details,
                'status' => 'open',
                'action_triggered' => in_array($type, ['late_arrival', 'early_departure', 'absenteeism'], true),
            ]
        );
    }

    private function resolveShiftForEmployee(int $clientId, Employee $employee, Carbon $date): ?ShiftPattern
    {
        $assignment = EmployeeShift::where('client_id', $clientId)
            ->where('employee_id', $employee->id)
            ->whereDate('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date->toDateString());
            })
            ->latest('effective_from')
            ->first();

        if ($assignment) {
            return ShiftPattern::find($assignment->shift_pattern_id);
        }

        return ShiftPattern::where('client_id', $clientId)->where('is_active', true)->orderBy('id')->first();
    }

    private function getShiftAssignmentsForDate(int $clientId, Carbon $date): Collection
    {
        $assignments = EmployeeShift::where('client_id', $clientId)
            ->whereDate('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date->toDateString());
            })
            ->get()
            ->keyBy('employee_id');

        $shiftIds = $assignments->pluck('shift_pattern_id')->filter()->unique()->all();
        $patterns = ShiftPattern::whereIn('id', $shiftIds)->get()->keyBy('id');

        return $assignments->map(function (EmployeeShift $assignment) use ($patterns) {
            $assignment->setRelation('shiftPattern', $patterns->get($assignment->shift_pattern_id));
            return $assignment;
        });
    }

    private function ensureEmployeeShiftAssignments(int $clientId, Collection $employees): void
    {
        $defaultShift = ShiftPattern::where('client_id', $clientId)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if (!$defaultShift) {
            return;
        }

        foreach ($employees as $employee) {
            $exists = EmployeeShift::where('client_id', $clientId)
                ->where('employee_id', $employee->id)
                ->exists();

            if (!$exists) {
                EmployeeShift::create([
                    'client_id' => $clientId,
                    'employee_id' => $employee->id,
                    'shift_pattern_id' => $defaultShift->id,
                    'effective_from' => $employee->hire_date?->toDateString() ?: now()->toDateString(),
                    'effective_to' => null,
                ]);
            }
        }
    }

    private function getHolidayForDate(int $clientId, Carbon $date): ?PublicHoliday
    {
        return PublicHoliday::where(function ($query) use ($clientId) {
                $query->where('client_id', $clientId)->orWhereNull('client_id');
            })
            ->whereDate('holiday_date', $date->toDateString())
            ->first();
    }

    private function ensureReferenceData(int $clientId, int $year): void
    {
        $this->ensureShiftPatterns($clientId);
        $this->ensureTanzaniaPublicHolidays($clientId, $year);
    }

    private function ensureShiftPatterns(int $clientId): void
    {
        $patterns = [
            [
                'shift_name' => 'Day Shift',
                'start_time' => '08:00',
                'end_time' => '17:00',
                'break_duration' => 60,
                'is_night_shift' => false,
                'allowance_rate' => 0,
            ],
            [
                'shift_name' => 'Early Shift',
                'start_time' => '07:00',
                'end_time' => '16:00',
                'break_duration' => 60,
                'is_night_shift' => false,
                'allowance_rate' => 0,
            ],
            [
                'shift_name' => 'Night Shift',
                'start_time' => '20:00',
                'end_time' => '06:00',
                'break_duration' => 60,
                'is_night_shift' => true,
                'allowance_rate' => 5,
            ],
        ];

        foreach ($patterns as $pattern) {
            ShiftPattern::updateOrCreate(
                [
                    'client_id' => $clientId,
                    'shift_name' => $pattern['shift_name'],
                ],
                array_merge($pattern, ['is_active' => true])
            );
        }
    }

    private function ensureTanzaniaPublicHolidays(int $clientId, int $year): void
    {
        $holidays = [
            ['date' => Carbon::create($year, 1, 1), 'name' => 'New Year Day'],
            ['date' => Carbon::create($year, 1, 12), 'name' => 'Zanzibar Revolution Day'],
            ['date' => Carbon::create($year, 4, 7), 'name' => 'Karume Day'],
            ['date' => Carbon::create($year, 4, 26), 'name' => 'Union Day'],
            ['date' => Carbon::create($year, 5, 1), 'name' => 'Workers Day'],
            ['date' => Carbon::create($year, 7, 7), 'name' => 'Saba Saba Day'],
            ['date' => Carbon::create($year, 10, 14), 'name' => 'Nyerere Day'],
            ['date' => Carbon::create($year, 12, 9), 'name' => 'Independence Day'],
            ['date' => Carbon::create($year, 12, 25), 'name' => 'Christmas Day'],
            ['date' => Carbon::create($year, 12, 26), 'name' => 'Boxing Day'],
        ];

        $easter = Carbon::instance(\DateTime::createFromFormat('U', (string) easter_date($year)));
        $holidays[] = ['date' => $easter->copy()->subDays(2), 'name' => 'Good Friday'];
        $holidays[] = ['date' => $easter->copy()->addDay(), 'name' => 'Easter Monday'];

        foreach ($holidays as $holiday) {
            PublicHoliday::updateOrCreate(
                [
                    'client_id' => $clientId,
                    'holiday_date' => $holiday['date']->toDateString(),
                ],
                [
                    'holiday_name' => $holiday['name'],
                    'is_recurring' => true,
                    'active_year' => $year,
                ]
            );
        }
    }

    private function attendanceStatusReference(): array
    {
        return [
            ['code' => 'A', 'meaning' => 'Absent', 'action' => 'Salary deduction and misconduct workflow if not authorised.'],
            ['code' => 'AL', 'meaning' => 'Annual Leave', 'action' => 'Annual leave balance decremented and paid leave processed.'],
            ['code' => 'SLF', 'meaning' => 'Sick Leave Full Pay', 'action' => 'Full pay sick leave processed.'],
            ['code' => 'SLH', 'meaning' => 'Sick Leave Half Pay', 'action' => 'Half pay sick leave processed.'],
            ['code' => 'UL', 'meaning' => 'Unpaid Leave', 'action' => 'Salary deduction applied against approved unpaid leave.'],
            ['code' => 'M', 'meaning' => 'Official Mission', 'action' => 'Full pay processed with mission allowance reference.'],
            ['code' => '9', 'meaning' => 'Ordinary Hours', 'action' => 'Standard day pay with no overtime.'],
            ['code' => '12', 'meaning' => '12-Hour Shift', 'action' => 'Standard day pay plus 3 overtime hours at 1.5x rate.'],
        ];
    }

    private function normalizeSource(?string $value): string
    {
        $value = strtolower(trim((string) $value));

        return match ($value) {
            'biometric', 'device' => 'biometric',
            'mobile', 'mobile_clock_in' => 'mobile',
            'manual' => 'manual',
            default => 'web',
        };
    }
}
