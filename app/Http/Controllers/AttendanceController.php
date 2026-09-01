<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceMonthlySummary;
use App\Models\AttendanceViolation;
use App\Models\Client;
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
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);
        
        // Get selected month/year from request, default to current
        $monthDate = $request->filled('month') 
            ? \Carbon\Carbon::parse($request->get('month'))->startOfMonth()
            : now()->startOfMonth();
        
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();
        
        // Get employees with their positions/departments
        $employees = \App\Models\Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        // Get departments for filter
        $departments = \App\Models\Department::where('client_id', $clientId)
            ->orderBy('name')
            ->get();
            
        // Get monthly summaries
        $monthlySummaries = \App\Models\AttendanceMonthlySummary::with(['employee'])
            ->where('client_id', $clientId)
            ->where('month', $monthStart->month)
            ->where('year', $monthStart->year)
            ->get()
            ->keyBy('employee_id');
            
        // Prepare final data - include all employees even if no summary
        $timesheetData = $employees->map(function ($employee) use ($monthlySummaries, $clientId, $monthStart, $monthEnd) {
            $summary = $monthlySummaries->get($employee->id);
            
            // If no summary exists, calculate basic stats
            if (!$summary) {
                $attendance = \App\Models\Attendance::where('client_id', $clientId)
                    ->where('employee_id', $employee->id)
                    ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                    ->get();
                    
                $summary = (object)[
                    'total_days' => $monthStart->daysInMonth,
                    'worked_days' => $attendance->whereIn('status_code', ['9', '12', 'M'])->count(),
                    'absent_days' => $attendance->where('status_code', 'A')->count(),
                    'leave_days' => $attendance->whereIn('status_code', ['AL', 'SLF', 'SLH', 'UL'])->count(),
                    'overtime_hours' => round((float) $attendance->sum('overtime_hours'), 2),
                    'night_hours' => round((float) $attendance->sum('night_hours'), 2),
                ];
            }
            
            return [
                'employee' => $employee,
                'summary' => $summary,
            ];
        });
        
        // Calculate overall stats
        $totalEmployees = $employees->count();
        $totalWorkedDays = $timesheetData->sum(fn($d) => $d['summary']->worked_days);
        $totalAbsentDays = $timesheetData->sum(fn($d) => $d['summary']->absent_days);
        $totalLeaveDays = $timesheetData->sum(fn($d) => $d['summary']->leave_days);
        $totalOvertimeHours = $timesheetData->sum(fn($d) => $d['summary']->overtime_hours);

        return view('attendance.timesheets', [
            'currentClient' => $currentClient,
            'timesheetData' => $timesheetData,
            'employees' => $employees,
            'departments' => $departments,
            'monthDate' => $monthDate,
            'stats' => [
                'total_employees' => $totalEmployees,
                'total_worked_days' => $totalWorkedDays,
                'total_absent_days' => $totalAbsentDays,
                'total_leave_days' => $totalLeaveDays,
                'total_overtime_hours' => $totalOvertimeHours,
            ],
        ]);
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
            $workflowStatus = $manualEntry ? 'pending_approval' : 'approved';
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
