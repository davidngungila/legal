<?php

namespace App\Http\Middleware;

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
        $clientId = Session::get('current_client_id');
        
        if ($clientId) {
            // Add global scope to filter models by current client
            // This will be applied to models that use the BelongsToCurrentClient trait
            app()->singleton('current_client_id', function () use ($clientId) {
                return $clientId;
            });
        } else {
            // Ensure the singleton exists even if no client is set
            app()->singleton('current_client_id', function () {
                return null;
            });
        }

        return $next($request);
    }
}
