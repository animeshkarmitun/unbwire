<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'billing_period',
        'access_news',
        'access_images',
        'access_videos',
        'access_exclusive',
        'max_articles_per_day',
        'ad_free',
        'priority_support',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'access_news' => 'boolean',
        'access_images' => 'boolean',
        'access_videos' => 'boolean',
        'access_exclusive' => 'boolean',
        'ad_free' => 'boolean',
        'priority_support' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get all active subscriptions for this package
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get active subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if package has access to a specific feature
     */
    public function hasAccess(string $feature): bool
    {
        return match($feature) {
            'news' => $this->access_news,
            'images' => $this->access_images,
            'videos' => $this->access_videos,
            'exclusive' => $this->access_exclusive,
            default => false,
        };
    }

    /**
     * Get package tier level (for comparison)
     */
    public function getTierLevel(): int
    {
        return match(strtolower($this->slug)) {
            'unb-lite' => 1,
            'unb-pro' => 2,
            'unb-ultra' => 3,
            default => 0,
        };
    }
}

