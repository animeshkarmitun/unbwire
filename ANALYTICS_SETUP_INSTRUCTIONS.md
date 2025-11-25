# Visitor Analytics Setup Instructions

## ✅ Implementation Complete

A comprehensive visitor analytics system has been implemented with the following features:

### Features Implemented

1. **Real-Time Analytics**
   - Active visitors tracking (last 5 minutes)
   - Live visit and page view counts
   - Real-time dashboard updates (every 30 seconds)

2. **Date-Wise Analytics**
   - Date range filtering
   - Daily/weekly/monthly reports
   - Trends and comparisons

3. **Country-Wise Analytics**
   - Geographic visitor distribution
   - Top countries list
   - Country-specific metrics

4. **Organic Traffic Analytics**
   - Search engine breakdown
   - Organic vs paid traffic
   - Top organic referrers

5. **Repeater (Returning Visitor) Analytics**
   - New vs returning visitors
   - Visitor retention metrics
   - Loyalty statistics

## 📋 Setup Steps

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `visitors` - Unique visitor tracking
- `visits` - Individual visit sessions
- `page_views` - Detailed page view data
- `analytics_summary` - Aggregated daily statistics

### 2. Verify Middleware Registration

The `TrackVisitor` middleware has been registered in `bootstrap/app.php`. It will automatically track:
- All frontend routes (excluding admin and API routes)
- Visitor information (IP, user agent, device, browser, OS)
- Geolocation data (country, city)
- Referrer information and classification
- Session tracking

### 3. Access Analytics Dashboard

Navigate to: `http://your-domain.com/admin/analytics`

**Routes Available:**
- `/admin/analytics` - Main dashboard
- `/admin/analytics/real-time` - Real-time stats API
- `/admin/analytics/date-wise` - Date range analytics
- `/admin/analytics/country-wise` - Country breakdown
- `/admin/analytics/organic` - Organic traffic report
- `/admin/analytics/repeaters` - Returning visitors report
- `/admin/analytics/export` - Export analytics data

### 4. Schedule Daily Aggregation

The daily aggregation job is scheduled to run at 1:00 AM UTC every day. Make sure your cron is set up:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or manually run aggregation for a specific date:
```php
php artisan tinker
>>> \App\Jobs\AggregateDailyAnalytics::dispatch(\Carbon\Carbon::yesterday());
```

## 🔧 Configuration

### Geolocation Service

The system uses `ip-api.com` (free, no API key required) for IP geolocation. For production, consider:
- MaxMind GeoIP2 (more accurate, requires license)
- ipapi.co (paid plans available)
- Custom geolocation service

To change the geolocation service, modify `app/Services/GeolocationService.php`.

### Bot Detection

Bots are automatically detected and filtered. The detection looks for common bot user agents:
- bot, crawler, spider, scraper
- facebookexternalhit, twitterbot, googlebot

### Caching

Real-time stats are cached for 30 seconds to reduce database load. Adjust in `AnalyticsService::getRealTimeStats()`.

## 📊 Database Structure

### Visitors Table
- Tracks unique visitors with fingerprinting
- Stores device, browser, OS, location info
- Tracks first/last visit and total stats

### Visits Table
- Individual visit sessions
- Session duration tracking
- Landing/exit pages
- Referrer classification

### Page Views Table
- Detailed page view data
- URL, path, title tracking
- Load time (if available)
- Linked to visits and visitors

### Analytics Summary Table
- Pre-aggregated daily statistics
- Reduces query load for historical data
- Includes bounce rate, avg duration, traffic sources

## 🎨 Customization

### Adding New Metrics

1. Add fields to `analytics_summary` migration
2. Update `AnalyticsService::aggregateDailyStats()`
3. Add to `AnalyticsController` methods
4. Update dashboard views

### Custom Tracking

To track custom events, use the AnalyticsService:

```php
use App\Services\AnalyticsService;

// In your controller
$analyticsService = app(AnalyticsService::class);
$visitor = $analyticsService->identifyVisitor($request);
$visit = $analyticsService->trackVisit($request, $visitor);
$pageView = $analyticsService->trackPageView($request, $visitor, $visit);
```

### Excluding Routes from Tracking

Modify `app/Http/Middleware/TrackVisitor.php`:

```php
// Skip tracking for specific routes
if ($request->is('excluded-route/*')) {
    return $next($request);
}
```

## 🚀 Performance Optimization

1. **Indexes**: All frequently queried columns are indexed
2. **Caching**: Real-time stats cached for 30 seconds
3. **Aggregation**: Daily summaries reduce query complexity
4. **Queue Jobs**: Heavy operations run in background

## 📈 Future Enhancements

Consider adding:
- Custom event tracking
- A/B testing integration
- Funnel analysis
- Heatmaps
- User journey tracking
- Export to CSV/Excel
- Email reports
- API for external integrations

## 🔒 Privacy & GDPR

The system tracks:
- IP addresses (can be anonymized)
- User agents
- Referrer URLs
- Geolocation data

For GDPR compliance:
- Add IP anonymization option
- Implement data retention policies
- Add user consent tracking
- Provide data export/deletion features

## 📝 Notes

- The system automatically excludes admin routes from tracking
- Bot traffic is detected and can be filtered
- Session-based visit tracking prevents duplicate counts
- Daily aggregation runs automatically via scheduler

## 🐛 Troubleshooting

**No data showing?**
- Check if middleware is registered: `php artisan route:list | grep analytics`
- Verify migrations ran: `php artisan migrate:status`
- Check logs: `storage/logs/laravel.log`

**Geolocation not working?**
- Check internet connection (uses external API)
- Verify IP is not private/local
- Check API rate limits

**Performance issues?**
- Enable query caching
- Run daily aggregation manually
- Check database indexes
- Consider using Redis for sessions


