<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;

class ClientSwitchController
{
    /**
     * Switch to a different client
     */
    public function switch(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $user = auth()->user();
        $client = Client::findOrFail($request->client_id);

        // Check if user has access to this client
        if (!$user->isSuperAdmin() && !$user->clients()->where('clients.id', $client->id)->exists()) {
            return response()->json(['error' => 'Unauthorized access to this client'], 403);
        }

        // Update session
        session(['current_client_id' => $client->id]);

        return response()->json([
            'success' => true,
            'message' => 'Switched to ' . $client->name,
            'client' => $client
        ]);
    }

    /**
     * Get current client
     */
    public function current()
    {
        $clientId = session('current_client_id');
        
        if (!$clientId) {
            return response()->json(['error' => 'No client selected'], 404);
        }

        $client = Client::find($clientId);
        
        if (!$client) {
            return response()->json(['error' => 'Selected client not found'], 404);
        }

        return response()->json($client);
    }

    /**
     * Get available clients for user
     */
    public function available()
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $clients = Client::orderBy('name')->get();
        } else {
            $clients = $user->clients()->orderBy('name')->get();
        }

        return response()->json($clients);
    }
}
