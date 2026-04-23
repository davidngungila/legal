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
        // Check if current client is set in session
        if (!Session::has('current_client_id')) {
            // Try to get first available client as default
            $firstClient = Client::orderBy('created_at', 'desc')->first();
            
            if ($firstClient) {
                Session::put('current_client_id', $firstClient->id);
                Session::put('current_client_name', $firstClient->name);
            }
        }

        // Share current client with all views - always refresh from session to ensure consistency
        $clientId = Session::get('current_client_id');
        if ($clientId) {
            $currentClient = Client::find($clientId);
            view()->share('currentClient', $currentClient);
        }

        return $next($request);
    }
}
