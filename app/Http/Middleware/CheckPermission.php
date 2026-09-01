<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = auth()->user();
        
        // Only Super Admin bypasses all permission checks
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }
        
        // Check if user has any of the required permissions
        foreach ($permissions as $permission) {
            if ($user && $user->hasPermission($permission)) {
                return $next($request);
            }
        }
        
        // If no permissions match, redirect or abort
        abort(403, 'Unauthorized action.');
    }
}
