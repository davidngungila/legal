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
        
        // Always set the singleton to prevent errors in models
        app()->singleton('current_client_id', function () use ($clientId) {
            return $clientId;
        });
        
        // If no client is available, add a flash message to inform the user
        if (!$clientId && auth()->check()) {
            // Only add the message once to avoid repetition
            if (!session()->has('client_missing_warning')) {
                session()->flash('warning', 'No client context available. Please select a client from the dropdown.');
                session()->flash('client_missing_warning', true);
            }
        }

        return $next($request);
    }
}
