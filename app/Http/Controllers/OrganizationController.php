<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class OrganizationController
{
    /**
     * Show organization setup form
     */
    public function setup()
    {
        return view('organization.setup');
    }

    /**
     * Update organization information
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'nullable|string',
            'website' => 'nullable|url',
            'registration_number' => 'nullable|string',
            'tin_number' => 'nullable|string',
            'business_license_number' => 'nullable|string',
            'license_expiry_date' => 'nullable|date',
            'vat_registration_number' => 'nullable|string',
            'trade_license_number' => 'nullable|string',
            'nssf_employer_number' => 'nullable|string',
            'wcf_policy_number' => 'nullable|string',
            'heslb_registration_number' => 'nullable|string',
            'workmans_compensation_policy' => 'nullable|string',
        ]);

        // Update current client
        $client = Client::find(session('current_client_id'));
        if ($client) {
            $client->update($validated);
            return redirect()->back()->with('success', 'Organization information updated successfully!');
        }

        return redirect()->back()->with('error', 'Unable to update organization information.');
    }

    /**
     * Get organization statistics
     */
    public function stats()
    {
        $client = Client::find(session('current_client_id'));
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $stats = [
            'total_employees' => $client->employees()->count(),
            'active_employees' => $client->employees()->where('status', 'active')->count(),
            'departments' => $client->departments()->count(),
            'total_payroll' => $client->payrolls()->sum('net_pay'),
            'compliance_score' => 85, // Placeholder calculation
        ];

        return response()->json($stats);
    }
}
