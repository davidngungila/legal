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
            $firstClient = $this->getFirstAvailableClient();
            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
                $clientId = $firstClient->id;
            }
        }

        if ($clientId) {
            $client = Client::find($clientId);
            if (!$client) {
                // Client no longer exists, get a new default
                $firstClient = $this->getFirstAvailableClient();
                if ($firstClient) {
                    Session::put('current_client_id', $firstClient->id);
                    Session::put('current_client_name', $firstClient->name);
                    $client = $firstClient;
                }
            }
            
            if ($client) {
                return response()->json([
                    'success' => true,
                    'client' => $client
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No clients available'
        ]);
    }
    
    /**
     * Get the first available client for the current user
     */
    private function getFirstAvailableClient()
    {
        // If user is authenticated, try to get their first assigned client
        if (auth()->check()) {
            $user = auth()->user();
            $firstClient = $user->clients()->first();
            
            if ($firstClient) {
                return $firstClient;
            }
        }
        
        // Fallback to any available client
        return Client::orderBy('created_at', 'desc')->first();
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
