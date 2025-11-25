<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Handle an incoming request.
     * Track visitor, visit, and page view for analytics
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for API routes
        if ($request->is('api/*')) {
            return $next($request);
        }
        
        // Check if tracking is enabled for this type of user
        $isAdminRoute = $request->is('admin/*');
        $trackAdmin = $this->getSetting('analytics_track_admin', '0') === '1';
        $trackFrontend = $this->getSetting('analytics_track_frontend', '1') === '1';
        
        // Skip tracking if disabled for this user type
        if ($isAdminRoute && !$trackAdmin) {
            return $next($request);
        }
        
        if (!$isAdminRoute && !$trackFrontend) {
            return $next($request);
        }
        
        // Skip tracking for AJAX requests (optional - you may want to track these)
        // if ($request->ajax()) {
        //     return $next($request);
        // }
        
        try {
            // Identify or create visitor
            $visitor = $this->analyticsService->identifyVisitor($request);
            
            // Check if this is a new visit (new session or no active visit)
            $sessionId = session()->getId();
            $activeVisit = \App\Models\Visit::where('session_id', $sessionId)
                ->whereNull('ended_at')
                ->first();
            
            if (!$activeVisit) {
                // Track new visit
                $visit = $this->analyticsService->trackVisit($request, $visitor);
            } else {
                $visit = $activeVisit;
            }
            
            // Track page view
            $this->analyticsService->trackPageView($request, $visitor, $visit);
            
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Analytics tracking failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
        }
        
        $response = $next($request);
        
        // Update visit end time when response is sent (for session duration)
        if (isset($visit) && $visit instanceof \App\Models\Visit) {
            $visit->update(['ended_at' => now()]);
            $visit->calculateDuration();
        }
        
        return $response;
    }

    /**
     * Get setting value
     */
    protected function getSetting(string $key, string $default = '0'): string
    {
        return \App\Models\Setting::where('key', $key)->value('value') ?? $default;
    }
}
