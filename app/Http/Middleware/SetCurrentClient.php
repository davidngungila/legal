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
        
        // If authenticated and session is empty, try to get from user record
        if (!$clientId && auth()->check()) {
            $clientId = auth()->user()->current_client_id;
            if ($clientId) {
                Session::put('current_client_id', $clientId);
            }
        }
        
        // Only set a default client for authenticated users (never for guests on login page)
        if (!$clientId && auth()->check()) {
            $this->setDefaultClient();
            $clientId = Session::get('current_client_id');
        }
        
        $currentClient = null;
        
        // Validate that the client still exists
        if ($clientId) {
            $currentClient = Client::find($clientId);
            if (!$currentClient) {
                // Client no longer exists, set a new default
                $this->setDefaultClient();
                $clientId = Session::get('current_client_id');
                $currentClient = $clientId ? Client::find($clientId) : null;
            } else {
                // Super admins attached to an employee-less placeholder client
                // ("Orvion") should be moved to a real tenant client so listing
                // pages such as /employees are not silently empty. Deliberate
                // switches to other clients are left untouched.
                if ($currentClient->isSystemPlaceholder()
                    && auth()->check()
                    && auth()->user()->hasRole('super_admin')) {
                    $better = auth()->user()->resolveDefaultClient();
                    if ($better && $better->id !== $currentClient->id) {
                        $currentClient = $better;
                        $clientId = $better->id;
                        Session::put('current_client_id', $clientId);
                        Session::put('current_client_name', $currentClient->name);
                        Session::put('current_client', $currentClient);
                        auth()->user()->update(['current_client_id' => $clientId]);
                    }
                }
            }
            
            // Only super_admin is auto-synced into clients they visit
            if ($currentClient && auth()->check() && auth()->user()->hasRole('super_admin')) {
                if (!auth()->user()->clients()->where('clients.id', $clientId)->exists()) {
                    auth()->user()->clients()->syncWithoutDetaching([
                        $clientId => [
                            'role' => 'admin',
                            'is_active' => true,
                            'joined_at' => now(),
                        ],
                    ]);
                }
            }
            
            // Share current client with all views and session
            if ($currentClient) {
                Session::put('current_client', $currentClient);
            } else {
                Session::forget('current_client');
            }
        } else {
            Session::forget('current_client');
        }
        
        // Always share currentClient with views (even as null)
        view()->share('currentClient', $currentClient);

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

            // Skip setting default client for Orvion users (super_admin with no clients)
            if ($user->hasRole('super_admin') && $user->clients()->count() === 0) {
                return;
            }

            // Only super_admin can access all clients; admin/lead_hr_admin restricted to assigned clients
            $firstClient = $user->resolveDefaultClient();

            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
                Session::put('current_client', $firstClient);

                // Persist to database as well
                $user->update(['current_client_id' => $firstClient->id]);
                return;
            }
        }

        // Fallback to any available client, sorted alphabetically for stability
        $firstClient = Client::orderBy('name')->first();

        if ($firstClient) {
            Session::put('current_client_id', $firstClient->id);
            Session::put('current_client_name', $firstClient->name);
            Session::put('current_client', $firstClient);
        }
    }
}
