<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    protected $fillable = [
        'visit_id',
        'visitor_id',
        'url',
        'path',
        'title',
        'referrer',
        'load_time',
        'viewed_at',
    ];

    protected $casts = [
        'load_time' => 'integer',
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the visit that owns this page view
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Get the visitor that owns this page view
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }
}
