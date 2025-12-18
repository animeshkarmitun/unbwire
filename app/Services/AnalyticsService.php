<?php

namespace App\Services;

use App\Models\Visitor;
use App\Models\Visit;
use App\Models\PageView;
use App\Models\AnalyticsSummary;
use App\Services\BotDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    protected GeolocationService $geolocationService;
    protected BotDetectionService $botDetectionService;

    public function __construct(GeolocationService $geolocationService, BotDetectionService $botDetectionService)
    {
        $this->geolocationService = $geolocationService;
        $this->botDetectionService = $botDetectionService;
    }

    /**
     * Generate unique visitor ID based on fingerprint
     */
    public function generateVisitorId(Request $request): string
    {
        $fingerprint = $request->ip() . 
                      $request->userAgent() . 
                      $request->header('Accept-Language', '');
        
        return hash('sha256', $fingerprint);
    }

    /**
     * Identify or create visitor
     */
    public function identifyVisitor(Request $request): Visitor
    {
        $visitorId = $this->generateVisitorId($request);
        
        $visitor = Visitor::findByVisitorId($visitorId);
        
        if (!$visitor) {
            $location = $this->geolocationService->getLocationFromIp($request->ip());
            $deviceInfo = $this->getDeviceInfo($request->userAgent());
            $referrerInfo = $this->getReferrerInfo($request->header('referer'));
            
            $visitor = Visitor::create([
                'visitor_id' => $visitorId,
                'first_visit_at' => now(),
                'last_visit_at' => now(),
                'total_visits' => 1,
                'total_page_views' => 0,
                'is_bot' => $this->detectBot($request->userAgent()),
                'user_agent' => $request->userAgent(),
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'os' => $deviceInfo['os'],
                'country' => $location['country'] ?? null,
                'country_code' => $location['country_code'] ?? null,
                'city' => $location['city'] ?? null,
                'referrer' => $referrerInfo['referrer'],
                'referrer_type' => $referrerInfo['type'],
            ]);
        } else {
            $visitor->increment('total_visits');
            $visitor->update(['last_visit_at' => now()]);
        }
        
        return $visitor;
    }

    /**
     * Track a new visit
     */
    public function trackVisit(Request $request, Visitor $visitor): Visit
    {
        $sessionId = session()->getId();
        $location = $this->geolocationService->getLocationFromIp($request->ip());
        $deviceInfo = $this->getDeviceInfo($request->userAgent());
        $referrerInfo = $this->getReferrerInfo($request->header('referer'));
        
        // Enhanced bot detection
        $userType = $this->botDetectionService->detectUserType($request);
        $isBot = in_array($userType, ['bot', 'scraper']);
        
        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $referrerInfo['referrer'],
            'referrer_type' => $referrerInfo['type'],
            'landing_page' => $request->fullUrl(),
            'country' => $location['country'] ?? null,
            'country_code' => $location['country_code'] ?? null,
            'city' => $location['city'] ?? null,
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'os' => $deviceInfo['os'],
            'is_bot' => $isBot,
            'user_type' => $userType,
            'page_views_count' => 1,
            'started_at' => now(),
        ]);
        
        // Re-analyze after visit is created (for behavioral analysis)
        $updatedUserType = $this->botDetectionService->detectUserType($request, $visit);
        if ($updatedUserType !== $userType) {
            $visit->update([
                'user_type' => $updatedUserType,
                'is_bot' => in_array($updatedUserType, ['bot', 'scraper']),
            ]);
        }
        
        return $visit;
    }

    /**
     * Track a page view
     */
    public function trackPageView(Request $request, Visitor $visitor, Visit $visit): PageView
    {
        $pageView = PageView::create([
            'visit_id' => $visit->id,
            'visitor_id' => $visitor->id,
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'title' => $this->getPageTitle($request),
            'referrer' => $request->header('referer'),
            'viewed_at' => now(),
        ]);
        
        $visit->increment('page_views_count');
        $visitor->increment('total_page_views');
        
        return $pageView;
    }

    /**
     * Get real-time statistics
     */
    public function getRealTimeStats(): array
    {
        $cacheKey = 'analytics:realtime';
        
        return Cache::remember($cacheKey, 30, function () {
            $now = now();
            $last5Minutes = $now->copy()->subMinutes(5);
            
            return [
                'active_visitors' => Visit::where('started_at', '>=', $last5Minutes)
                    ->whereNull('ended_at')
                    ->count(),
                'visits_today' => Visit::whereDate('started_at', $now)
                    ->count(),
                'page_views_today' => PageView::whereDate('viewed_at', $now)
                    ->count(),
                'unique_visitors_today' => Visitor::whereDate('last_visit_at', $now)
                    ->count(),
                'recent_visitors' => Visit::with('visitor')
                    ->where('started_at', '>=', $last5Minutes)
                    ->orderBy('started_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($visit) {
                        return [
                            'country' => $visit->country,
                            'city' => $visit->city,
                            'device' => $visit->device_type,
                            'referrer' => $visit->referrer_type,
                            'started_at' => $visit->started_at->diffForHumans(),
                        ];
                    }),
            ];
        });
    }

    /**
     * Get date-wise statistics
     */
    public function getDateWiseStats(Carbon $startDate, Carbon $endDate): array
    {
        $visits = Visit::whereBetween('started_at', [$startDate, $endDate])
            ->selectRaw('DATE(started_at) as date, COUNT(*) as visits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $visitors = Visitor::whereBetween('last_visit_at', [$startDate, $endDate])
            ->selectRaw('DATE(last_visit_at) as date, COUNT(*) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $pageViews = PageView::whereBetween('viewed_at', [$startDate, $endDate])
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as page_views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'visits' => $visits,
            'visitors' => $visitors,
            'page_views' => $pageViews,
        ];
    }

    /**
     * Get country-wise statistics
     */
    public function getCountryWiseStats(?Carbon $date = null): array
    {
        $query = Visit::select('country', 'country_code', DB::raw('COUNT(*) as visits'))
            ->whereNotNull('country_code')
            ->groupBy('country', 'country_code')
            ->orderBy('visits', 'desc');
            
        if ($date) {
            $query->whereDate('started_at', $date);
        }
        
        return $query->get()->toArray();
    }

    /**
     * Get organic traffic statistics
     */
    public function getOrganicStats(?Carbon $date = null): array
    {
        $query = Visit::where('referrer_type', 'organic')
            ->select('referrer', DB::raw('COUNT(*) as visits'))
            ->groupBy('referrer')
            ->orderBy('visits', 'desc');
            
        if ($date) {
            $query->whereDate('started_at', $date);
        }
        
        $organicVisits = $query->get();
        
        $totalOrganic = $organicVisits->sum('visits');
        $totalVisits = $date 
            ? Visit::whereDate('started_at', $date)->count()
            : Visit::count();
        
        return [
            'total_organic' => $totalOrganic,
            'total_visits' => $totalVisits,
            'organic_percentage' => $totalVisits > 0 ? ($totalOrganic / $totalVisits) * 100 : 0,
            'by_referrer' => $organicVisits,
        ];
    }

    /**
     * Get repeater (returning visitor) statistics
     */
    public function getRepeaterStats(?Carbon $date = null): array
    {
        $query = Visitor::select(
            DB::raw('COUNT(*) as total_visitors'),
            DB::raw('SUM(CASE WHEN total_visits = 1 THEN 1 ELSE 0 END) as new_visitors'),
            DB::raw('SUM(CASE WHEN total_visits > 1 THEN 1 ELSE 0 END) as returning_visitors')
        );
        
        if ($date) {
            $query->whereDate('last_visit_at', $date);
        }
        
        $stats = $query->first();
        
        return [
            'total_visitors' => $stats->total_visitors ?? 0,
            'new_visitors' => $stats->new_visitors ?? 0,
            'returning_visitors' => $stats->returning_visitors ?? 0,
            'returning_percentage' => $stats->total_visitors > 0 
                ? (($stats->returning_visitors / $stats->total_visitors) * 100) 
                : 0,
        ];
    }

    /**
     * Aggregate daily statistics
     */
    public function aggregateDailyStats(Carbon $date): void
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        
        $visits = Visit::whereBetween('started_at', [$startOfDay, $endOfDay])->get();
        $visitors = Visitor::whereBetween('last_visit_at', [$startOfDay, $endOfDay])->get();
        
        $newVisitors = $visitors->where('total_visits', 1)->count();
        $returningVisitors = $visitors->where('total_visits', '>', 1)->count();
        
        $pageViews = PageView::whereBetween('viewed_at', [$startOfDay, $endOfDay])->count();
        $uniquePageViews = PageView::whereBetween('viewed_at', [$startOfDay, $endOfDay])
            ->distinct('path')
            ->count('path');
        
        $bounces = $visits->where('page_views_count', 1)->count();
        $bounceRate = $visits->count() > 0 ? ($bounces / $visits->count()) * 100 : 0;
        
        $avgDuration = $visits->whereNotNull('duration')->avg('duration');
        
        $organicTraffic = $visits->where('referrer_type', 'organic')->count();
        $directTraffic = $visits->where('referrer_type', 'direct')->count();
        $socialTraffic = $visits->where('referrer_type', 'social')->count();
        $referralTraffic = $visits->where('referrer_type', 'referral')->count();
        
        $topCountry = $visits->groupBy('country')->map->count()->sortDesc()->keys()->first();
        $topReferrer = $visits->whereNotNull('referrer')
            ->groupBy('referrer')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();
        
        AnalyticsSummary::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'visitors' => $visitors->count(),
                'new_visitors' => $newVisitors,
                'returning_visitors' => $returningVisitors,
                'visits' => $visits->count(),
                'page_views' => $pageViews,
                'unique_page_views' => $uniquePageViews,
                'bounce_rate' => round($bounceRate, 2),
                'avg_session_duration' => $avgDuration ? (int) $avgDuration : null,
                'organic_traffic' => $organicTraffic,
                'direct_traffic' => $directTraffic,
                'social_traffic' => $socialTraffic,
                'referral_traffic' => $referralTraffic,
                'top_country' => $topCountry,
                'top_referrer' => $topReferrer,
            ]
        );
    }

    /**
     * Detect if user agent is a bot
     */
    protected function detectBot(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }
        
        $bots = ['bot', 'crawler', 'spider', 'scraper', 'facebookexternalhit', 'twitterbot', 'googlebot'];
        
        $userAgentLower = strtolower($userAgent);
        
        foreach ($bots as $bot) {
            if (str_contains($userAgentLower, $bot)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get referrer information
     */
    protected function getReferrerInfo(?string $referrer): array
    {
        if (!$referrer) {
            return ['referrer' => null, 'type' => 'direct'];
        }
        
        $referrerLower = strtolower($referrer);
        
        // Search engines
        $searchEngines = ['google', 'bing', 'yahoo', 'duckduckgo', 'baidu', 'yandex'];
        foreach ($searchEngines as $engine) {
            if (str_contains($referrerLower, $engine)) {
                return ['referrer' => $referrer, 'type' => 'organic'];
            }
        }
        
        // Social media
        $social = ['facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'reddit', 'youtube'];
        foreach ($social as $platform) {
            if (str_contains($referrerLower, $platform)) {
                return ['referrer' => $referrer, 'type' => 'social'];
            }
        }
        
        // Email
        if (str_contains($referrerLower, 'mail') || str_contains($referrerLower, 'email')) {
            return ['referrer' => $referrer, 'type' => 'email'];
        }
        
        // Other external referrer
        return ['referrer' => $referrer, 'type' => 'referral'];
    }

    /**
     * Get device information from user agent
     */
    protected function getDeviceInfo(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'desktop',
                'browser' => null,
                'os' => null,
            ];
        }
        
        $userAgentLower = strtolower($userAgent);
        
        // Device type
        $deviceType = 'desktop';
        if (str_contains($userAgentLower, 'mobile') || str_contains($userAgentLower, 'android')) {
            $deviceType = 'mobile';
        } elseif (str_contains($userAgentLower, 'tablet') || str_contains($userAgentLower, 'ipad')) {
            $deviceType = 'tablet';
        }
        
        // Browser
        $browser = null;
        if (str_contains($userAgentLower, 'chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgentLower, 'firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgentLower, 'safari')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgentLower, 'edge')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgentLower, 'opera')) {
            $browser = 'Opera';
        }
        
        // OS
        $os = null;
        if (str_contains($userAgentLower, 'windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgentLower, 'mac')) {
            $os = 'macOS';
        } elseif (str_contains($userAgentLower, 'linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgentLower, 'android')) {
            $os = 'Android';
        } elseif (str_contains($userAgentLower, 'ios') || str_contains($userAgentLower, 'iphone') || str_contains($userAgentLower, 'ipad')) {
            $os = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Get page title from request
     */
    protected function getPageTitle(Request $request): ?string
    {
        // This would ideally be extracted from the response
        // For now, return the path
        return $request->path();
    }

    /**
     * Get bot vs human statistics
     */
    public function getBotVsHumanStats(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        
        $visits = Visit::whereBetween('started_at', [$startOfDay, $endOfDay])->get();
        
        $total = $visits->count();
        $humans = $visits->where('user_type', 'human')->count();
        $bots = $visits->where('user_type', 'bot')->count();
        $scrapers = $visits->where('user_type', 'scraper')->count();
        
        return [
            'total' => $total,
            'humans' => $humans,
            'bots' => $bots,
            'scrapers' => $scrapers,
            'human_percentage' => $total > 0 ? round(($humans / $total) * 100, 2) : 0,
            'bot_percentage' => $total > 0 ? round(($bots / $total) * 100, 2) : 0,
            'scraper_percentage' => $total > 0 ? round(($scrapers / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get bot activity list
     */
    public function getBotActivity(int $limit = 50, ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = Visit::whereIn('user_type', ['bot', 'scraper'])
            ->with('visitor')
            ->orderBy('started_at', 'desc')
            ->limit($limit);
        
        if ($startDate) {
            $query->where('started_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('started_at', '<=', $endDate);
        }
        
        return $query->get();
    }

    /**
     * Get most visited IPs with visit counts
     */
    public function getMostVisitedIps(int $limit = 50, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Visit::select('ip_address', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('ip_address')
            ->orderBy('visit_count', 'desc')
            ->limit($limit);

        if ($startDate) {
            $query->where('started_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('started_at', '<=', $endDate);
        }

        $ips = $query->get();

        // Get blocked IPs
        $blockedIps = \App\Models\BlockedIp::pluck('ip_address')->toArray();

        // Get additional info for each IP
        return $ips->map(function ($ip) use ($blockedIps) {
            $firstVisit = Visit::where('ip_address', $ip->ip_address)
                ->orderBy('started_at', 'asc')
                ->first();
            
            $lastVisit = Visit::where('ip_address', $ip->ip_address)
                ->orderBy('started_at', 'desc')
                ->first();

            $pageViews = PageView::whereHas('visit', function ($query) use ($ip) {
                $query->where('ip_address', $ip->ip_address);
            })->count();

            return [
                'ip_address' => $ip->ip_address,
                'visit_count' => $ip->visit_count,
                'page_views' => $pageViews,
                'first_visit' => $firstVisit?->started_at,
                'last_visit' => $lastVisit?->started_at,
                'country' => $firstVisit?->country,
                'country_code' => $firstVisit?->country_code,
                'city' => $firstVisit?->city,
                'is_blocked' => in_array($ip->ip_address, $blockedIps),
            ];
        })->toArray();
    }

    /**
     * Get most viewed pages with time filters
     */
    public function getMostViewedPages(string $period = 'all', int $limit = 50): array
    {
        $query = PageView::select('page_views.path', 'page_views.title', 'page_views.url', DB::raw('COUNT(*) as view_count'))
            ->join('visits', 'page_views.visit_id', '=', 'visits.id')
            ->where('visits.is_bot', false)
            ->groupBy('page_views.path', 'page_views.title', 'page_views.url')
            ->orderBy('view_count', 'desc')
            ->limit($limit);

        // Apply time filters
        switch ($period) {
            case 'today':
                $query->whereDate('page_views.viewed_at', Carbon::today());
                break;
            case 'month':
                $query->whereMonth('page_views.viewed_at', Carbon::now()->month)
                      ->whereYear('page_views.viewed_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('page_views.viewed_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // No date filter for 'all time'
                break;
        }

        $pages = $query->get();

        return $pages->map(function ($page) {
            return [
                'path' => $page->path,
                'title' => $page->title ?: $page->path,
                'url' => $page->url,
                'view_count' => $page->view_count,
            ];
        })->toArray();
    }
}

