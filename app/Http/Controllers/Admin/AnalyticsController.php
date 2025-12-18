<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\AnalyticsSummary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
        
        // Apply permission middleware
        $this->middleware(['permission:analytics index,admin'])->only(['index']);
        $this->middleware(['permission:analytics view,admin'])->only(['realTime', 'dateWise', 'countryWise', 'organic', 'repeaters', 'mostViewedPages']);
        $this->middleware(['permission:analytics export,admin'])->only(['export']);
        $this->middleware(['permission:analytics index,admin'])->only(['settings', 'updateSettings', 'mostVisitedIps', 'blockIp', 'unblockIp']);
    }

    /**
     * Display the main analytics dashboard
     */
    public function index()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $last7Days = Carbon::today()->subDays(7);
        $last30Days = Carbon::today()->subDays(30);
        
        // Get today's stats
        $todayStats = $this->analyticsService->getRealTimeStats();
        
        // Get yesterday's summary for comparison
        $yesterdaySummary = AnalyticsSummary::forDate($yesterday);
        
        // Get last 7 days data for charts
        $last7DaysData = $this->analyticsService->getDateWiseStats($last7Days, $today);
        
        // Get top countries
        $topCountries = $this->analyticsService->getCountryWiseStats($today);
        
        // Get organic stats
        $organicStats = $this->analyticsService->getOrganicStats($today);
        
        // Get repeater stats
        $repeaterStats = $this->analyticsService->getRepeaterStats($today);
        
        // Get bot vs human stats
        $botVsHumanStats = $this->analyticsService->getBotVsHumanStats($today);
        
        return view('admin.analytics.index', compact(
            'todayStats',
            'yesterdaySummary',
            'last7DaysData',
            'topCountries',
            'organicStats',
            'repeaterStats',
            'botVsHumanStats'
        ));
    }

    /**
     * Get real-time analytics data (AJAX)
     */
    public function realTime(): JsonResponse
    {
        $stats = $this->analyticsService->getRealTimeStats();
        
        return response()->json($stats);
    }

    /**
     * Get date-wise analytics
     */
    public function dateWise(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $data = $this->analyticsService->getDateWiseStats($start, $end);
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('admin.analytics.date-wise', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Get country-wise analytics
     */
    public function countryWise(Request $request)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date'))
            : Carbon::today();
        
        $data = $this->analyticsService->getCountryWiseStats($date);
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('admin.analytics.country-wise', compact('data', 'date'));
    }

    /**
     * Get organic traffic analytics
     */
    public function organic(Request $request)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date'))
            : Carbon::today();
        
        $data = $this->analyticsService->getOrganicStats($date);
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('admin.analytics.organic', compact('data', 'date'));
    }

    /**
     * Get repeater (returning visitor) analytics
     */
    public function repeaters(Request $request)
    {
        $date = $request->input('date') 
            ? Carbon::parse($request->input('date'))
            : Carbon::today();
        
        $data = $this->analyticsService->getRepeaterStats($date);
        
        if ($request->ajax()) {
            return response()->json($data);
        }
        
        return view('admin.analytics.repeaters', compact('data', 'date'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');
        $startDate = $request->input('start_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $data = AnalyticsSummary::forDateRange($start, $end);
        
        // TODO: Implement CSV/Excel export
        // For now, return JSON
        return response()->json($data);
    }

    /**
     * Display analytics settings page
     */
    public function settings()
    {
        $settings = \App\Models\Setting::whereIn('key', [
            'analytics_whois_enabled',
            'analytics_track_admin',
            'analytics_track_frontend'
        ])->pluck('value', 'key')->toArray();

        return view('admin.analytics.settings', compact('settings'));
    }

    /**
     * Update analytics settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'analytics_whois_enabled' => ['nullable', 'boolean'],
            'analytics_track_admin' => ['nullable', 'boolean'],
            'analytics_track_frontend' => ['nullable', 'boolean'],
        ]);

        \App\Models\Setting::updateOrCreate(
            ['key' => 'analytics_whois_enabled'],
            ['value' => $request->has('analytics_whois_enabled') ? '1' : '0']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'analytics_track_admin'],
            ['value' => $request->has('analytics_track_admin') ? '1' : '0']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'analytics_track_frontend'],
            ['value' => $request->has('analytics_track_frontend') ? '1' : '0']
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.analytics.settings');
    }

    /**
     * Display bot activity
     */
    public function botActivity(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::today()->subDays(7);
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))
            : Carbon::today();

        $limit = $request->input('limit', 50);

        $botActivity = $this->analyticsService->getBotActivity($limit, $startDate, $endDate);
        $botStats = $this->analyticsService->getBotVsHumanStats(Carbon::today());

        if ($request->ajax()) {
            return response()->json($botActivity);
        }

        return view('admin.analytics.bot-activity', compact('botActivity', 'botStats', 'startDate', 'endDate', 'limit'));
    }

    /**
     * Display most visited IPs
     */
    public function mostVisitedIps(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))
            : Carbon::today()->subDays(30);
        
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))
            : Carbon::today();

        $limit = $request->input('limit', 50);

        $ips = $this->analyticsService->getMostVisitedIps($limit, $startDate, $endDate);

        if ($request->ajax()) {
            return response()->json($ips);
        }

        return view('admin.analytics.most-visited-ips', compact('ips', 'startDate', 'endDate', 'limit'));
    }

    /**
     * Block an IP address
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $ip = $request->input('ip_address');
        $reason = $request->input('reason');
        $blockedBy = auth()->guard('admin')->id();

        \App\Models\BlockedIp::block($ip, $reason, $blockedBy);

        return response()->json([
            'status' => 'success',
            'message' => __('admin.IP address blocked successfully'),
        ]);
    }

    /**
     * Unblock an IP address
     */
    public function unblockIp(Request $request)
    {
        $request->validate([
            'ip_address' => ['required', 'ip'],
        ]);

        $ip = $request->input('ip_address');
        \App\Models\BlockedIp::unblock($ip);

        return response()->json([
            'status' => 'success',
            'message' => __('admin.IP address unblocked successfully'),
        ]);
    }

    /**
     * Display most viewed pages
     */
    public function mostViewedPages(Request $request)
    {
        $period = $request->input('period', 'all'); // all, year, month, today
        $limit = $request->input('limit', 50);

        $pages = $this->analyticsService->getMostViewedPages($period, $limit);

        if ($request->ajax()) {
            return response()->json($pages);
        }

        return view('admin.analytics.most-viewed-pages', compact('pages', 'period', 'limit'));
    }
}
