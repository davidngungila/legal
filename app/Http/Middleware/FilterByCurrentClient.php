<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FilterByCurrentClient
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
        // Get current client from session
        $clientId = Session::get('current_client_id');
        
        if ($clientId) {
            $currentClient = Client::find($clientId);
            
            // If client exists, share it with views and potentially filter queries
            if ($currentClient) {
                view()->share('currentClient', $currentClient);
                
                // You can add query filtering logic here if needed
                // For example, you could set a global scope or modify request parameters
                $request->attributes->set('current_client_id', $clientId);
                $request->attributes->set('current_client', $currentClient);
            }
        }

        return $next($request);
    }
}
