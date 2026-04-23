<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Client;

class PayrollController
{
    /**
     * Display payroll index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $payrolls = Payroll::where('client_id', $clientId)
            ->orderBy('payroll_period', 'desc')
            ->paginate(20);
        
        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show payroll upload form
     */
    public function showUploadForm()
    {
        return view('payroll.upload');
    }

    /**
     * Upload payroll CSV
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'payroll_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'payroll_period' => 'required|string',
        ]);

        // Handle file upload and processing
        // This would normally process the uploaded file
        
        return redirect()->back()->with('success', 'Payroll data uploaded successfully!');
    }

    /**
     * Download payroll template
     */
    public function downloadTemplate()
    {
        // Return template file download
        return response()->download(storage_path('app/templates/payroll_template.csv'));
    }

    /**
     * Show payroll details
     */
    public function show($id)
    {
        $payroll = Payroll::findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    /**
     * Update payroll status
     */
    public function updateStatus(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete payroll record
     */
    public function destroy($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->delete();
        
        return redirect()->route('payroll.index')->with('success', 'Payroll record deleted successfully!');
    }
}
