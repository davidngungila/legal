<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetCurrentClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $clientId = Session::get('current_client_id');
        
        // If no client is set in session, try to set a default
        if (!$clientId) {
            $this->setDefaultClient();
            $clientId = Session::get('current_client_id');
        }
        
        // Validate that the client still exists
        if ($clientId) {
            $currentClient = Client::find($clientId);
            if (!$currentClient) {
                // Client no longer exists, set a new default
                $this->setDefaultClient();
                $clientId = Session::get('current_client_id');
                $currentClient = $clientId ? Client::find($clientId) : null;
            }
            
            // Share current client with all views
            if ($currentClient) {
                view()->share('currentClient', $currentClient);
            }
        }

        return $next($request);
    }
    
    /**
     * Set a default client for the current user
     */
    private function setDefaultClient()
    {
        // If user is authenticated, try to get their first assigned client
        if (auth()->check()) {
            $user = auth()->user();
            $firstClient = $user->clients()->first();
            
            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
                return;
            }
        }
        
        // Fallback to any available client
        $firstClient = Client::orderBy('created_at', 'desc')->first();
        
        if ($firstClient) {
            Session::put('current_client_id', $firstClient->id);
            Session::put('current_client_name', $firstClient->name);
        }
    }
}
