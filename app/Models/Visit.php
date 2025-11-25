<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    protected $fillable = [
        'visitor_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'referrer_type',
        'landing_page',
        'exit_page',
        'country',
        'country_code',
        'city',
        'device_type',
        'browser',
        'os',
        'is_bot',
        'user_type',
        'duration',
        'page_views_count',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
        'duration' => 'integer',
        'page_views_count' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the visitor that owns this visit
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * Get all page views for this visit
     */
    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    /**
     * Calculate and update visit duration
     */
    public function calculateDuration(): void
    {
        if ($this->ended_at && $this->started_at) {
            $this->duration = $this->ended_at->diffInSeconds($this->started_at);
            $this->save();
        }
    }

    /**
     * Check if visit is a bounce (single page view)
     */
    public function isBounce(): bool
    {
        return $this->page_views_count <= 1;
    }
}
