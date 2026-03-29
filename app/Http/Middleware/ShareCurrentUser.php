<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\AuthController;

class ShareCurrentUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get current authenticated user
        $user = AuthController::getCurrentUser();
        
        // Share user data with all views
        view()->share('currentUser', $user);
        
        return $next($request);
    }
}
