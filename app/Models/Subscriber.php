<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'user_id',
        'email_notifications_enabled',
        'language_preference',
        'last_notified_at',
        'unsubscribe_token',
    ];

    protected $casts = [
        'email_notifications_enabled' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = \Str::random(64);
            }
        });
    }

    /**
     * Get the user account linked to this subscriber
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all notifications for this subscriber
     */
    public function notifications()
    {
        return $this->hasMany(SubscriberNotification::class);
    }

    /**
     * Get unread notifications for this subscriber
     */
    public function unreadNotifications()
    {
        return $this->hasMany(SubscriberNotification::class)->where('is_read', false);
    }

    /**
     * Check if subscriber should receive email notifications
     */
    public function shouldReceiveEmail(): bool
    {
        return $this->email_notifications_enabled === true;
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(): int
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * Get subscription tier slug
     */
    public function getSubscriptionTier(): string
    {
        if ($this->user_id && $this->user) {
            $package = $this->user->currentPackage();
            return $package ? $package->slug : 'free';
        }
        return 'free';
    }

    /**
     * Get subscription tier level (0=free, 1=lite, 2=pro, 3=ultra)
     */
    public function getSubscriptionTierLevel(): int
    {
        if ($this->user_id && $this->user) {
            $package = $this->user->currentPackage();
            return $package ? $package->getTierLevel() : 0;
        }
        return 0; // Free tier
    }

    /**
     * Check if subscriber has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->user_id && $this->user) {
            return $this->user->activeSubscription !== null;
        }
        return false;
    }

    /**
     * Get current subscription package
     */
    public function currentPackage()
    {
        if ($this->user_id && $this->user) {
            return $this->user->currentPackage();
        }
        return null;
    }

    /**
     * Check if subscriber can access a specific news article
     * Matches User::canAccessNews() logic
     */
    public function canAccessNews(News $news): bool
    {
        // If subscriber has linked user account
        if ($this->user_id && $this->user) {
            $user = $this->user;
            $package = $user->currentPackage();
            
            // No active subscription = free tier
            if (!$package) {
                return $news->subscription_required === 'free' && !$news->is_exclusive;
            }
            
            // Use same logic as User::canAccessNews()
            $packageTier = $package->getTierLevel(); // Returns: 0=free, 1=lite, 2=pro, 3=ultra
            $requiredTier = match($news->subscription_required) {
                'free' => 0,
                'lite' => 1,
                'pro' => 2,
                'ultra' => 3,
                default => 0,
            };
            
            // User's package tier must be >= required tier
            $tierAccess = $packageTier >= $requiredTier;
            
            // Exclusive content requires ultra tier (level 3)
            // This ensures consistency with scopeForSubscriptionTier()
            if ($news->is_exclusive && $packageTier < 3) {
                return false;
            }
            
            return $tierAccess;
        }
        
        // Subscriber without user account = free tier
        // Can only access free, non-exclusive news
        return $news->subscription_required === 'free' && !$news->is_exclusive;
    }
}


