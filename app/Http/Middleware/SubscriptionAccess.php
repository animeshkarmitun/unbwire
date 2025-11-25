<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this content.');
        }

        $user = auth()->user();

        if (!$user->hasSubscriptionAccess($feature)) {
            $package = $user->currentPackage();
            $packageName = $package ? $package->name : 'Free';
            
            return redirect()
                ->route('subscription.plans')
                ->with('error', "This content requires a higher subscription tier. Your current plan: {$packageName}");
        }

        return $next($request);
    }
}

