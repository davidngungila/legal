<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    /**
     * Display the organization setup page.
     */
    public function setup()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $client = Client::find($currentClient['id']);
        
        if (!$client) {
            return redirect()->route('clients.index')
                ->with('error', 'Client not found.');
        }

        return view('organization.setup', compact('client'));
    }

    /**
     * Update organization information.
     */
    public function update(Request $request)
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $client = Client::find($currentClient['id']);
        
        if (!$client) {
            return response()->json(['error' => 'Client not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'tin_number' => 'nullable|string|max:255',
            'nssf_employer_number' => 'nullable|string|max:255',
            'wcf_employer_number' => 'nullable|string|max:255',
            'industry' => 'required|string|max:255',
            'company_size' => 'required|string|max:255',
            'founded_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'departments_count' => 'nullable|integer|min:1',
            'locations_count' => 'nullable|integer|min:1',
        ]);

        $client->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Organization information updated successfully!',
            'client' => $client
        ]);
    }

    /**
     * Get organization statistics.
     */
    public function stats()
    {
        $currentClient = session('current_client');
        
        if (!$currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $client = Client::find($currentClient['id']);
        
        if (!$client) {
            return response()->json(['error' => 'Client not found.'], 404);
        }

        $stats = [
            'total_employees' => $client->employees()->count(),
            'active_employees' => $client->employees()->where('status', 'active')->count(),
            'departments' => $client->departments_count ?? 8,
            'locations' => $client->locations_count ?? 3,
            'years_in_business' => $client->founded_year ? date('Y') - $client->founded_year : 0,
            'compliance_score' => $this->calculateComplianceScore($client),
        ];

        return response()->json($stats);
    }

    /**
     * Calculate compliance score for the client.
     */
    private function calculateComplianceScore($client)
    {
        $score = 100;
        
        // Deduct points for missing required information
        if (!$client->registration_number) $score -= 10;
        if (!$client->tin_number) $score -= 10;
        if (!$client->nssf_employer_number) $score -= 10;
        if (!$client->wcf_employer_number) $score -= 10;
        
        // Deduct points for missing contact information
        if (!$client->phone) $score -= 5;
        if (!$client->email) $score -= 5;
        
        return max(0, $score);
    }
}
