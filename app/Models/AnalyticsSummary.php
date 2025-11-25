<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AnalyticsSummary extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analytics_summary';

    protected $fillable = [
        'date',
        'visitors',
        'new_visitors',
        'returning_visitors',
        'visits',
        'page_views',
        'unique_page_views',
        'bounce_rate',
        'avg_session_duration',
        'organic_traffic',
        'direct_traffic',
        'social_traffic',
        'referral_traffic',
        'top_country',
        'top_referrer',
    ];

    protected $casts = [
        'date' => 'date',
        'visitors' => 'integer',
        'new_visitors' => 'integer',
        'returning_visitors' => 'integer',
        'visits' => 'integer',
        'page_views' => 'integer',
        'unique_page_views' => 'integer',
        'bounce_rate' => 'decimal:2',
        'avg_session_duration' => 'integer',
        'organic_traffic' => 'integer',
        'direct_traffic' => 'integer',
        'social_traffic' => 'integer',
        'referral_traffic' => 'integer',
    ];

    /**
     * Get summary for a specific date
     */
    public static function forDate(Carbon $date): ?self
    {
        return static::whereDate('date', $date->format('Y-m-d'))->first();
    }

    /**
     * Get summary for date range
     */
    public static function forDateRange(Carbon $startDate, Carbon $endDate)
    {
        return static::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->get();
    }
}
