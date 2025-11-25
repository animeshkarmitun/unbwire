# Visitor Analytics Implementation Plan

## Overview
Comprehensive visitor analytics system with real-time tracking, date-wise reports, country-wise analytics, organic traffic analysis, and repeat visitor tracking.

## Architecture

### 1. Database Schema

#### `visitors` Table
- `id` (bigint, primary)
- `visitor_id` (string, unique) - Unique identifier (fingerprint/hash)
- `first_visit_at` (timestamp)
- `last_visit_at` (timestamp)
- `total_visits` (integer, default 0)
- `total_page_views` (integer, default 0)
- `is_bot` (boolean, default false)
- `user_agent` (text, nullable)
- `device_type` (enum: desktop, mobile, tablet)
- `browser` (string, nullable)
- `os` (string, nullable)
- `country` (string, nullable)
- `country_code` (string, nullable)
- `city` (string, nullable)
- `referrer` (string, nullable)
- `referrer_type` (enum: direct, organic, social, referral, email, other)
- `created_at`, `updated_at`

#### `visits` Table
- `id` (bigint, primary)
- `visitor_id` (foreign key to visitors)
- `session_id` (string, index)
- `ip_address` (string, index)
- `user_agent` (text)
- `referrer` (string, nullable)
- `referrer_type` (enum)
- `landing_page` (string)
- `exit_page` (string, nullable)
- `country` (string, nullable)
- `country_code` (string, nullable)
- `city` (string, nullable)
- `device_type` (enum)
- `browser` (string, nullable)
- `os` (string, nullable)
- `is_bot` (boolean)
- `duration` (integer, nullable) - seconds
- `page_views_count` (integer, default 1)
- `started_at` (timestamp)
- `ended_at` (timestamp, nullable)
- `created_at`, `updated_at`

#### `page_views` Table
- `id` (bigint, primary)
- `visit_id` (foreign key to visits)
- `visitor_id` (foreign key to visitors)
- `url` (string, index)
- `path` (string, index)
- `title` (string, nullable)
- `referrer` (string, nullable)
- `load_time` (integer, nullable) - milliseconds
- `viewed_at` (timestamp, index)
- `created_at`, `updated_at`

#### `analytics_summary` Table (Aggregated Data)
- `id` (bigint, primary)
- `date` (date, unique index)
- `visitors` (integer, default 0)
- `new_visitors` (integer, default 0)
- `returning_visitors` (integer, default 0)
- `visits` (integer, default 0)
- `page_views` (integer, default 0)
- `unique_page_views` (integer, default 0)
- `bounce_rate` (decimal, nullable)
- `avg_session_duration` (integer, nullable) - seconds
- `organic_traffic` (integer, default 0)
- `direct_traffic` (integer, default 0)
- `social_traffic` (integer, default 0)
- `referral_traffic` (integer, default 0)
- `top_country` (string, nullable)
- `top_referrer` (string, nullable)
- `created_at`, `updated_at`

### 2. Models

- `App\Models\Visitor`
- `App\Models\Visit`
- `App\Models\PageView`
- `App\Models\AnalyticsSummary`

### 3. Middleware

**TrackingMiddleware**
- Captures visitor data on each request
- Identifies unique visitors (fingerprinting)
- Detects bots and filters them
- Extracts device/browser/OS information
- Gets geolocation data (IP to country/city)
- Determines referrer type (organic, direct, social, etc.)
- Tracks session start/end
- Records page views

### 4. Services

**AnalyticsService**
- `trackVisit()` - Record new visit
- `trackPageView()` - Record page view
- `identifyVisitor()` - Create/update visitor
- `getRealTimeStats()` - Real-time analytics
- `getDateWiseStats($startDate, $endDate)` - Date range analytics
- `getCountryWiseStats($date)` - Country breakdown
- `getOrganicStats($date)` - Organic traffic analysis
- `getRepeaterStats($date)` - Returning visitors
- `aggregateDailyStats($date)` - Daily aggregation job
- `detectBot($userAgent)` - Bot detection
- `getReferrerType($referrer)` - Classify referrer
- `getDeviceInfo($userAgent)` - Parse user agent

**GeolocationService**
- `getLocationFromIp($ip)` - IP to location (using free API or MaxMind)

### 5. Controllers

**AnalyticsController** (Admin)
- `index()` - Main analytics dashboard
- `realTime()` - Real-time stats API
- `dateWise()` - Date-wise analytics
- `countryWise()` - Country breakdown
- `organic()` - Organic traffic report
- `repeaters()` - Returning visitors report
- `export()` - Export analytics data

### 6. Jobs

**AggregateDailyAnalyticsJob**
- Runs daily (via scheduler)
- Aggregates previous day's data
- Calculates metrics (bounce rate, avg duration, etc.)
- Stores in `analytics_summary` table

### 7. Routes

```php
// Admin Routes
Route::prefix('admin/analytics')->name('admin.analytics.')->group(function() {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/real-time', [AnalyticsController::class, 'realTime'])->name('real-time');
    Route::get('/date-wise', [AnalyticsController::class, 'dateWise'])->name('date-wise');
    Route::get('/country-wise', [AnalyticsController::class, 'countryWise'])->name('country-wise');
    Route::get('/organic', [AnalyticsController::class, 'organic'])->name('organic');
    Route::get('/repeaters', [AnalyticsController::class, 'repeaters'])->name('repeaters');
    Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
});
```

### 8. Views

- `admin/analytics/index.blade.php` - Main dashboard
- `admin/analytics/partials/real-time.blade.php` - Real-time widget
- `admin/analytics/partials/date-wise.blade.php` - Date range charts
- `admin/analytics/partials/country-wise.blade.php` - Country map/chart
- `admin/analytics/partials/organic.blade.php` - Organic traffic
- `admin/analytics/partials/repeaters.blade.php` - Returning visitors

### 9. Features

#### Real-Time Analytics
- Active visitors count
- Current page views
- Live visitor map
- Recent visitors list
- Top pages being viewed

#### Date-Wise Analytics
- Line charts for visitors/visits/page views
- Date range picker
- Comparison (this week vs last week)
- Trends and growth percentages

#### Country-Wise Analytics
- World map visualization
- Country list with visitor counts
- Top 10 countries
- Country-specific metrics

#### Organic Traffic
- Search engine breakdown (Google, Bing, etc.)
- Top keywords (if available)
- Organic vs paid traffic
- Search engine trends

#### Repeater Analytics
- New vs returning visitors
- Returning visitor frequency
- Loyalty metrics
- Visitor retention rate

### 10. Performance Optimizations

- **Caching**: Cache aggregated data (Redis/File)
- **Indexing**: Proper database indexes on frequently queried columns
- **Queue Jobs**: Background processing for heavy operations
- **Pagination**: Limit data retrieval
- **Aggregation**: Pre-calculate daily summaries

### 11. Privacy & GDPR Compliance

- IP anonymization option
- Cookie consent tracking
- Data retention policies
- User data deletion

### 12. Dependencies

- **User Agent Parser**: `jenssegers/agent` or `whichbrowser/parser`
- **IP Geolocation**: Free API (ipapi.co, ip-api.com) or MaxMind GeoIP2
- **Charts**: Chart.js or ApexCharts
- **Maps**: Google Maps API or Leaflet.js

## Implementation Steps

1. ✅ Create database migrations
2. ✅ Create models with relationships
3. ✅ Create TrackingMiddleware
4. ✅ Create AnalyticsService
5. ✅ Create GeolocationService
6. ✅ Create AnalyticsController
7. ✅ Create background jobs
8. ✅ Create admin views
9. ✅ Add routes
10. ✅ Register middleware
11. ✅ Add scheduler for daily aggregation
12. ✅ Test and optimize


