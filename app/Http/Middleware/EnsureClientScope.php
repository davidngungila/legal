<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClientScope
{
    /**
     * Reject state-changing requests that try to operate on a client other
     * than the active (switched-to) client.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $next($request);
        }

        // Client switching intentionally changes the active context.
        if ($request->is('client-switch/*')) {
            return $next($request);
        }

        $sessionClientId = session('current_client_id');

        if ($request->filled('client_id') && $sessionClientId
            && (int) $request->input('client_id') !== (int) $sessionClientId) {
            abort(403, 'Request client does not match the active client context.');
        }

        return $next($request);
    }
}