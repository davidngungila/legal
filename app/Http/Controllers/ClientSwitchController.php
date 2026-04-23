<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClientSwitchController extends Controller
{
    /**
     * Switch the current active client.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id'
        ]);

        $clientId = $request->client_id;
        $client = Client::findOrFail($clientId);

        // Store the current client in session
        Session::put('current_client_id', $clientId);
        Session::put('current_client_name', $client->name);
        
        // Also share with views immediately for this request
        view()->share('currentClient', $client);

        return response()->json([
            'success' => true,
            'message' => "Switched to {$client->name}",
            'client' => $client
        ]);
    }

    /**
     * Get the current active client.
     */
    public function current()
    {
        $clientId = Session::get('current_client_id');
        
        if (!$clientId) {
            // Get first available client as default
            $firstClient = Client::orderBy('created_at', 'desc')->first();
            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
                $clientId = $firstClient->id;
            }
        }

        if ($clientId) {
            $client = Client::find($clientId);
            return response()->json([
                'success' => true,
                'client' => $client
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No clients available'
        ]);
    }

    /**
     * Get all available clients for switching.
     */
    public function available()
    {
        $clients = Client::orderBy('name')->get();
        $currentClientId = Session::get('current_client_id');

        return response()->json([
            'success' => true,
            'clients' => $clients->map(function ($client) use ($currentClientId) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'status' => $client->status,
                    'is_current' => $client->id == $currentClientId
                ];
            })
        ]);
    }
}
