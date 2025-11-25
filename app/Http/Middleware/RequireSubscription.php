<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSubscription
{
    /**
     * Handle an incoming request.
     * Requires user to be authenticated and have an active subscription.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login and subscribe to access news content.');
        }

        $user = auth()->user();

        // Check if user has an active subscription
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need to subscribe to access news content. Please choose a subscription plan.');
        }

        return $next($request);
    }
}

