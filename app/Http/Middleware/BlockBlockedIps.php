<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedIp;

class BlockBlockedIps
{
    /**
     * Handle an incoming request.
     * Block requests from blocked IP addresses
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Check if IP is blocked
        if (BlockedIp::isBlocked($ip)) {
            abort(403, 'Access denied. Your IP address has been blocked.');
        }

        return $next($request);
    }
}
