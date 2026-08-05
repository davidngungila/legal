<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Models\CalibrationSession;
use App\Models\Client;
use App\Models\PerformanceCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalibrationSessionController extends Controller
{
    public function index(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $query = CalibrationSession::with(['cycle', 'facilitatedBy'])
            ->where('client_id', $clientId);

        if ($request->filled('cycle_id')) {
            $query->where('cycle_id', $request->get('cycle_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $sessions = $query->orderBy('session_date', 'desc')->paginate(10);

        $cycles = PerformanceCycle::where('client_id', $clientId)->orderBy('period_start', 'desc')->get();

        $stats = [
            'total' => CalibrationSession::where('client_id', $clientId)->count(),
            'planned' => CalibrationSession::where('client_id', $clientId)->where('status', 'planned')->count(),
            'completed' => CalibrationSession::where('client_id', $clientId)->where('status', 'completed')->count(),
            'pending' => CalibrationSession::where('client_id', $clientId)->where('status', 'pending')->count(),
        ];

        return view('performance.calibration.index', compact('currentClient', 'sessions', 'cycles', 'stats'));
    }

    public function store(Request $request)
    {
        $clientId = session('current_client_id');
        if (! $clientId) {
            return back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'cycle_id' => 'required|exists:performance_cycles,id',
            'session_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:planned,pending,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            PerformanceCycle::where('client_id', $clientId)->findOrFail($request->cycle_id);

            $session = CalibrationSession::create([
                'client_id' => $clientId,
                'cycle_id' => $request->cycle_id,
                'facilitated_by' => auth()->id(),
                'session_date' => $request->session_date,
                'notes' => $request->notes,
                'status' => $request->status ?: 'planned',
            ]);

            AuditLogger::log(
                'calibration_session.created',
                $session,
                'Performance Calibration',
                "Calibration session #{$session->id} created"
            );

            return redirect()->route('performance.calibration.index')->with('success', 'Calibration session created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create calibration session: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, CalibrationSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        $validator = Validator::make($request->all(), [
            'cycle_id' => 'required|exists:performance_cycles,id',
            'session_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:planned,pending,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $old = $session->toArray();
            $session->update([
                'cycle_id' => $request->cycle_id,
                'session_date' => $request->session_date,
                'notes' => $request->notes,
                'status' => $request->status ?: 'planned',
            ]);

            AuditLogger::log(
                'calibration_session.updated',
                $session,
                'Performance Calibration',
                "Calibration session #{$session->id} updated",
                $old,
                $session->toArray()
            );

            return redirect()->route('performance.calibration.index')->with('success', 'Calibration session updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update calibration session: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(CalibrationSession $session)
    {
        $clientId = session('current_client_id');
        if (! $clientId || $session->client_id != $clientId) {
            return back()->with('error', 'Invalid request.');
        }

        try {
            AuditLogger::log(
                'calibration_session.deleted',
                $session,
                'Performance Calibration',
                "Calibration session #{$session->id} deleted"
            );
            $session->delete();

            return redirect()->route('performance.calibration.index')->with('success', 'Calibration session deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete calibration session: ' . $e->getMessage());
        }
    }
}
