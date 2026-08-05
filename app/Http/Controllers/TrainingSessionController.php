<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Employee;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingSessionController extends Controller
{
    public function show(TrainingSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return redirect()->route('training.index')->with('error', 'Invalid request.');
        }

        $currentClient = Client::find($clientId);
        $session->load(['program', 'plan', 'enrollments.employee']);

        $enrolledEmployeeIds = $session->enrollments->pluck('employee_id')->all();
        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->whereNotIn('id', $enrolledEmployeeIds)
            ->orderBy('first_name')
            ->get();

        return view('training.sessions.show', compact('currentClient', 'session', 'employees'));
    }

    public function store(Request $request, \App\Models\TrainingProgram $program)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $program->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'plan_id' => 'nullable|exists:training_plans,id',
            'instructor' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:scheduled,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $session = TrainingSession::create([
                'client_id' => $clientId,
                'program_id' => $program->id,
                'plan_id' => $request->plan_id ?: null,
                'title' => $request->title,
                'instructor' => $request->instructor,
                'venue' => $request->venue,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'capacity' => $request->capacity ?: 0,
                'status' => $request->status ?: 'scheduled',
                'created_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_session.created',
                $session,
                'Training Sessions',
                "Training session created: {$session->title}"
            );

            return redirect()->route('training.programs.show', $program->id)->with('success', 'Training session created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create training session: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, TrainingSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'plan_id' => 'nullable|exists:training_plans,id',
            'instructor' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'capacity' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:scheduled,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $session->toArray();
            $session->update([
                'title' => $request->title,
                'plan_id' => $request->plan_id ?: null,
                'instructor' => $request->instructor,
                'venue' => $request->venue,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'capacity' => $request->capacity ?: 0,
                'status' => $request->status ?: 'scheduled',
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_session.updated',
                $session,
                'Training Sessions',
                "Training session updated: {$session->title}",
                $old,
                $session->toArray()
            );

            return back()->with('success', 'Training session updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update training session: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(TrainingSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'training_session.deleted',
                $session,
                'Training Sessions',
                "Training session deleted: {$session->title}"
            );
            $session->delete();

            return back()->with('success', 'Training session deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete training session: ' . $e->getMessage());
        }
    }

    public function bulkEnroll(Request $request, TrainingSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $count = 0;
            foreach ($request->employee_ids as $employeeId) {
                $employee = Employee::where('client_id', $clientId)->find($employeeId);
                if (! $employee) {
                    continue;
                }

                $existing = TrainingEnrollment::where('session_id', $session->id)
                    ->where('employee_id', $employeeId)
                    ->exists();

                if (! $existing) {
                    TrainingEnrollment::create([
                        'client_id' => $clientId,
                        'session_id' => $session->id,
                        'employee_id' => $employeeId,
                        'enrollment_date' => now()->toDateString(),
                        'status' => 'enrolled',
                        'created_by' => auth()->id(),
                    ]);
                    $count++;
                }
            }

            AuditLogger::log(
                'training_enrollment.bulk',
                $session,
                'Training Sessions',
                "Enrolled {$count} employee(s) into session #{$session->id}"
            );

            return redirect()->route('training.sessions.show', $session->id)->with('success', "{$count} employee(s) enrolled successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to enroll employees: ' . $e->getMessage())->withInput();
        }
    }

    public function updateAttendance(Request $request, TrainingEnrollment $enrollment)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $enrollment->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'attendance_status' => 'nullable|string|in:present,absent,late',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $old = $enrollment->toArray();
        $enrollment->update([
            'attendance_status' => $request->attendance_status,
            'updated_by' => auth()->id(),
        ]);

        AuditLogger::log(
            'training_enrollment.attendance',
            $enrollment,
            'Training Sessions',
            "Attendance for enrollment #{$enrollment->id} set to {$request->attendance_status}",
            $old,
            $enrollment->toArray()
        );

        return back()->with('success', 'Attendance updated successfully!');
    }

    public function updateScore(Request $request, TrainingEnrollment $enrollment)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $enrollment->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'assessment_score' => 'nullable|numeric|min:0|max:100',
            'passed' => 'nullable|boolean',
            'status' => 'nullable|string|in:enrolled,in_progress,completed,dropped',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $enrollment->toArray();
            $enrollment->update([
                'assessment_score' => $request->assessment_score !== null ? $request->assessment_score : $enrollment->assessment_score,
                'passed' => $request->has('passed') ? (bool) $request->passed : $enrollment->passed,
                'status' => $request->status ?: $enrollment->status,
                'completed_at' => ($request->status === 'completed') ? now() : ($request->status === 'enrolled' ? null : $enrollment->completed_at),
                'updated_by' => auth()->id(),
            ]);

            AuditLogger::log(
                'training_enrollment.scored',
                $enrollment,
                'Training Sessions',
                "Score/status updated for enrollment #{$enrollment->id}",
                $old,
                $enrollment->toArray()
            );

            return back()->with('success', 'Assessment updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update assessment: ' . $e->getMessage())->withInput();
        }
    }

    public function unenroll(TrainingEnrollment $enrollment)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $enrollment->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'training_enrollment.deleted',
                $enrollment,
                'Training Sessions',
                "Employee #{$enrollment->employee_id} removed from session #{$enrollment->session_id}"
            );
            $enrollment->delete();

            return back()->with('success', 'Enrollment removed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove enrollment: ' . $e->getMessage());
        }
    }
}
