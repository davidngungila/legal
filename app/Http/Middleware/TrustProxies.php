<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TrustProxies
{
    /**
     * Trust reverse-proxy headers so HTTPS, host, and client IP are detected correctly in production.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->setTrustedProxies(
            ['*'],
            SymfonyRequest::HEADER_X_FORWARDED_FOR
                | SymfonyRequest::HEADER_X_FORWARDED_HOST
                | SymfonyRequest::HEADER_X_FORWARDED_PORT
                | SymfonyRequest::HEADER_X_FORWARDED_PROTO
                | SymfonyRequest::HEADER_X_FORWARDED_AWS_ELB
        );

        return $next($request);
    }
}
