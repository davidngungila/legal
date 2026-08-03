<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LifeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BenefitsController extends Controller
{
    public function lifeEvents()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = \App\Models\Client::find($clientId);

        $lifeEvents = LifeEvent::with('employee')
            ->where('client_id', $clientId)
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name']);

        $stats = [
            'total' => $lifeEvents->count(),
            'pending' => $lifeEvents->where('status', 'pending')->count(),
            'processing' => $lifeEvents->where('status', 'processing')->count(),
            'completed' => $lifeEvents->where('status', 'completed')->count(),
        ];

        return view('benefits.life-events', compact('currentClient', 'lifeEvents', 'employees', 'stats'));
    }

    public function storeLifeEvent(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'event_type' => 'required|string|max:50',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            LifeEvent::create([
                'client_id' => $clientId,
                'employee_id' => $employee->id,
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'description' => $request->description,
                'status' => $request->status ?: 'pending',
            ]);

            return redirect()->route('benefits.life-events')->with('success', 'Life event recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to record life event: ' . $e->getMessage())->withInput();
        }
    }

    public function updateLifeEvent(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $event = LifeEvent::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'event_type' => 'required|string|max:50',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $employee = Employee::where('client_id', $clientId)->findOrFail($request->employee_id);

            $event->update([
                'employee_id' => $employee->id,
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'description' => $request->description,
                'status' => $request->status ?: 'pending',
            ]);

            return redirect()->route('benefits.life-events')->with('success', 'Life event updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update life event: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyLifeEvent($id)
    {
        $clientId = session('current_client_id');
        $event = LifeEvent::where('client_id', $clientId)->findOrFail($id);
        $event->delete();

        return redirect()->route('benefits.life-events')->with('success', 'Life event deleted successfully!');
    }
}
