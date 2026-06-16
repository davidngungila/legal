<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $attendanceByEmployeeId = Attendance::where('client_id', $clientId)
            ->whereDate('attendance_date', $date->toDateString())
            ->get()
            ->keyBy('employee_id');

        $rows = $employees->map(function ($employee) use ($attendanceByEmployeeId) {
            $record = $attendanceByEmployeeId->get($employee->id);

            return [
                'employee' => $employee,
                'attendance' => $record,
            ];
        });

        $stats = Attendance::where('client_id', $clientId)
            ->whereDate('attendance_date', $date->toDateString())
            ->select('status', DB::raw('count(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $present = (int) ($stats['present'] ?? 0);
        $late = (int) ($stats['late'] ?? 0);
        $absent = (int) ($stats['absent'] ?? 0);
        $onLeave = (int) ($stats['on_leave'] ?? 0);

        $summary = [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'on_leave' => $onLeave,
            'total' => $employees->count(),
        ];

        $calendar = $this->buildCalendar($clientId, $date, $date);

        return view('attendance.index', [
            'rows' => $rows,
            'date' => $date->toDateString(),
            'summary' => $summary,
            'calendar' => $calendar,
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
            'status' => 'required|string|in:present,absent,late,half_day,on_leave,holiday',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'total_hours' => 'nullable|numeric|min:0|max:24',
            'overtime_hours' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        if ((int) $employee->client_id !== (int) $clientId) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $totalHours = $validated['total_hours'] ?? null;
        if ($totalHours === null && ($validated['clock_in'] ?? null) && ($validated['clock_out'] ?? null)) {
            $in = Carbon::createFromFormat('H:i', $validated['clock_in']);
            $out = Carbon::createFromFormat('H:i', $validated['clock_out']);
            if ($out->lessThan($in)) {
                $out->addDay();
            }
            $minutes = $in->diffInMinutes($out);
            $totalHours = round($minutes / 60, 2);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'attendance_date' => Carbon::parse($validated['attendance_date'])->toDateString(),
            ],
            [
                'status' => $validated['status'],
                'clock_in' => $validated['clock_in'] ?? null,
                'clock_out' => $validated['clock_out'] ?? null,
                'total_hours' => $totalHours ?? 0,
                'overtime_hours' => $validated['overtime_hours'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'data' => [
                'id' => $attendance->id,
                'employee_id' => $attendance->employee_id,
                'attendance_date' => $attendance->attendance_date?->format('Y-m-d'),
                'status' => $attendance->status,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
                'total_hours' => (float) $attendance->total_hours,
                'overtime_hours' => (float) $attendance->overtime_hours,
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

        $required = ['employee_id', 'date', 'status'];
        foreach ($required as $req) {
            if (!in_array($req, $columns, true)) {
                fclose($handle);
                Storage::delete($path);
                return redirect()->route('attendance.index')->with('error', "Missing required column: {$req}. Expected: employee_id, date, status.");
            }
        }

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

            $status = $this->normalizeStatus($assoc['status'] ?? null);
            if (!$status) {
                $skipped++;
                $errors[] = 'Invalid status for employee_id=' . ($assoc['employee_id'] ?? '') . ' (status=' . ($assoc['status'] ?? '') . ')';
                if (count($errors) > 10) break;
                continue;
            }

            $clockIn = $this->normalizeTime($assoc['clock_in'] ?? ($assoc['check_in'] ?? null));
            $clockOut = $this->normalizeTime($assoc['clock_out'] ?? ($assoc['check_out'] ?? null));
            $overtimeHours = is_numeric($assoc['overtime_hours'] ?? null) ? (float) $assoc['overtime_hours'] : 0;
            $notes = $assoc['notes'] ?? null;

            $totalHours = null;
            if (isset($assoc['total_hours']) && is_numeric($assoc['total_hours'])) {
                $totalHours = (float) $assoc['total_hours'];
            }

            $existing = Attendance::where('client_id', $clientId)
                ->where('employee_id', $employee->id)
                ->whereDate('attendance_date', $date)
                ->first();

            $attendance = Attendance::updateOrCreate(
                [
                    'client_id' => $clientId,
                    'employee_id' => $employee->id,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $status,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'total_hours' => $totalHours ?? 0,
                    'overtime_hours' => $overtimeHours,
                    'notes' => $notes,
                ]
            );

            if ($existing) {
                $updated++;
            } else {
                $imported++;
            }
        }

        fclose($handle);
        Storage::delete($path);

        $message = "Timesheet import completed. New: {$imported}, Updated: {$updated}, Skipped: {$skipped}.";
        if (!empty($errors)) {
            $message .= ' Issues: ' . implode(' | ', array_slice($errors, 0, 5));
        }

        return redirect()->route('attendance.index')->with('success', $message);
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

    private function normalizeStatus(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') return null;

        $value = str_replace([' ', '-'], '_', $value);

        $map = [
            'present' => 'present',
            'late' => 'late',
            'absent' => 'absent',
            'half_day' => 'half_day',
            'halfday' => 'half_day',
            'on_leave' => 'on_leave',
            'leave' => 'on_leave',
            'holiday' => 'holiday',
        ];

        return $map[$value] ?? null;
    }

    private function buildCalendar(int $clientId, Carbon $monthDate, Carbon $selectedDate): array
    {
        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $monthStatsRows = Attendance::where('client_id', $clientId)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->select('attendance_date', 'status', DB::raw('count(*) as c'))
            ->groupBy('attendance_date', 'status')
            ->get();

        $monthStats = [];
        foreach ($monthStatsRows as $row) {
            $dayKey = Carbon::parse($row->attendance_date)->toDateString();
            if (!isset($monthStats[$dayKey])) {
                $monthStats[$dayKey] = [];
            }
            $monthStats[$dayKey][$row->status] = (int) $row->c;
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

            $calendarDays[] = [
                'date' => $dayKey,
                'day' => (int) $cursor->format('j'),
                'in_month' => $inMonth,
                'is_weekend' => $isWeekend,
                'is_selected' => $isSelected,
                'counts' => [
                    'present' => (int) ($counts['present'] ?? 0),
                    'late' => (int) ($counts['late'] ?? 0),
                    'absent' => (int) ($counts['absent'] ?? 0),
                    'on_leave' => (int) ($counts['on_leave'] ?? 0),
                    'holiday' => (int) ($counts['holiday'] ?? 0),
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
}
