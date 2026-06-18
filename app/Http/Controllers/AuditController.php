<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['user', 'client'])->latest();
        
        // Filters
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by client if not super admin
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('client_id', session('current_client_id'));
        }
        
        $audits = $query->paginate(20);
        
        // Get all available filters data
        $users = \App\Models\User::with('clients')->get();
        $modules = Audit::distinct()->pluck('module')->filter()->values();
        $events = Audit::distinct()->pluck('event')->filter()->values();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'audits' => $audits,
                'filters' => compact('users', 'modules', 'events'),
            ]);
        }
        
        return view('audit-trail.index', compact('audits', 'users', 'modules', 'events'));
    }
    
    public function export(Request $request)
    {
        $query = Audit::with(['user', 'client'])->latest();
        
        // Apply same filters as index
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('client_id', session('current_client_id'));
        }
        
        $audits = $query->get();
        
        $csvContent = "Timestamp,User,Event,Module,Description,IP Address,Client\n";
        
        foreach ($audits as $audit) {
            $csvContent .= '"' . $audit->created_at . '",';
            $csvContent .= '"' . ($audit->user ? $audit->user->first_name . ' ' . $audit->user->last_name : 'System') . '",';
            $csvContent .= '"' . $audit->event . '",';
            $csvContent .= '"' . $audit->module . '",';
            $csvContent .= '"' . ($audit->description ?: '-') . '",';
            $csvContent .= '"' . $audit->ip_address . '",';
            $csvContent .= '"' . ($audit->client ? $audit->client->name : '-') . '"';
            $csvContent .= "\n";
        }
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit-trail-' . date('Y-m-d') . '.csv"',
        ];
        
        return Response::make($csvContent, 200, $headers);
    }
    
    public function show($id)
    {
        $audit = Audit::with(['user', 'client', 'auditable'])->findOrFail($id);
        
        // Check permissions
        if (!auth()->user()->hasRole('super_admin')) {
            if ($audit->client_id != session('current_client_id')) {
                abort(403);
            }
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'audit' => $audit,
            ]);
        }
        
        return view('audit-trail.show', compact('audit'));
    }
}
