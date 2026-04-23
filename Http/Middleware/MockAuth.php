<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MockAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Create a mock user for demonstration purposes
        $mockUser = (object) [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@legalhr.com',
            'role' => 'super_admin'
        ];

        // Mock the auth facade
        Auth::shouldReceive('user')->andReturn($mockUser);
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('id')->andReturn(1);

        // Share the mock user with all views
        view()->share('auth', (object) [
            'user' => $mockUser,
            'check' => true,
            'id' => 1
        ]);

        return $next($request);
    }
}
