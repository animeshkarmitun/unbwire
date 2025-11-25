<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    protected $fillable = [
        'visitor_id',
        'first_visit_at',
        'last_visit_at',
        'total_visits',
        'total_page_views',
        'is_bot',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'country',
        'country_code',
        'city',
        'referrer',
        'referrer_type',
    ];

    protected $casts = [
        'first_visit_at' => 'datetime',
        'last_visit_at' => 'datetime',
        'is_bot' => 'boolean',
        'total_visits' => 'integer',
        'total_page_views' => 'integer',
    ];

    /**
     * Get all visits for this visitor
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get all page views for this visitor
     */
    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    /**
     * Check if visitor is returning
     */
    public function isReturning(): bool
    {
        return $this->total_visits > 1;
    }

    /**
     * Get visitor by visitor_id
     */
    public static function findByVisitorId(string $visitorId): ?self
    {
        return static::where('visitor_id', $visitorId)->first();
    }
}
