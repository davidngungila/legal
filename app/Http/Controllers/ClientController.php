<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController
{
    /**
     * Get all clients
     */
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return response()->json($clients);
    }

    /**
     * Store new client
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
        ]);

        $client = Client::create($request->all());

        return response()->json($client, 201);
    }

    /**
     * Get specific client
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return response()->json($client);
    }

    /**
     * Update client
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $id,
            'phone' => 'required|string|max:20',
            'industry' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $client->update($request->all());

        return response()->json($client);
    }

    /**
     * Delete client
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    /**
     * Bulk operations
     */
    public function bulkOperations(Request $request)
    {
        $request->validate([
            'operation' => 'required|in:delete,activate,deactivate',
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $clients = Client::whereIn('id', $request->client_ids);

        switch ($request->operation) {
            case 'delete':
                $clients->delete();
                $message = 'Clients deleted successfully';
                break;
            case 'activate':
                $clients->update(['status' => 'active']);
                $message = 'Clients activated successfully';
                break;
            case 'deactivate':
                $clients->update(['status' => 'inactive']);
                $message = 'Clients deactivated successfully';
                break;
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Export clients
     */
    public function export()
    {
        $clients = Client::orderBy('name')->get();
        
        // Export logic would go here
        return response()->json($clients);
    }

    /**
     * Get client statistics
     */
    public function statistics()
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'inactive_clients' => Client::where('status', 'inactive')->count(),
            'suspended_clients' => Client::where('status', 'suspended')->count(),
            'clients_by_plan' => [
                'basic' => Client::where('subscription_plan', 'basic')->count(),
                'premium' => Client::where('subscription_plan', 'premium')->count(),
                'enterprise' => Client::where('subscription_plan', 'enterprise')->count(),
            ],
            'clients_by_industry' => Client::select('industry', \DB::raw('count(*) as count'))
                ->groupBy('industry')
                ->get()
                ->pluck('count', 'industry')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}
