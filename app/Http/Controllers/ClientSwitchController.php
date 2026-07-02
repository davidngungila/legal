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
        Session::put('current_client', $client);
        
        // Persist to user record if authenticated
        if (auth()->check()) {
            $user = auth()->user();
            $user->update(['current_client_id' => $clientId]);
            
            // For super_admin users, ensure they can access any client
            // by adding the client to their relationship if not already present
            if ($user->hasRole('super_admin')) {
                if (!$user->clients()->where('clients.id', $clientId)->exists()) {
                    $user->clients()->syncWithoutDetaching([
                        $clientId => [
                            'role' => 'admin',
                            'is_active' => true,
                            'joined_at' => now(),
                        ],
                    ]);
                }
            }
        }
        
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
        
        // If not in session, try user record
        if (!$clientId && auth()->check()) {
            $clientId = auth()->user()->current_client_id;
            if ($clientId) {
                Session::put('current_client_id', $clientId);
            }
        }
        
        if (!$clientId) {
            // Get first available client as default
            $firstClient = $this->getFirstAvailableClient();
            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
                Session::put('current_client', $firstClient);
                
                // Persist to database if authenticated
                if (auth()->check()) {
                    auth()->user()->update(['current_client_id' => $firstClient->id]);
                }
                
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
                    Session::put('current_client', $firstClient);
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
            
            // If user is super admin or admin, they can see all clients
            if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                return Client::orderBy('name')->first();
            }
            
            $firstClient = $user->clients()->orderBy('name')->first();
            
            if ($firstClient) {
                return $firstClient;
            }
        }
        
        // Fallback to any available client, sorted alphabetically for stability
        return Client::orderBy('name')->first();
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
