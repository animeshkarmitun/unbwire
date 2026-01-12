<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_notifications_enabled',
        'send_full_news_email',
        'language_preference',
        'last_notified_at',
        'unsubscribe_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_notifications_enabled' => 'boolean',
        'send_full_news_email' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    /**
     * Get all subscriptions for this user
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the active subscription for this user
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('starts_at', '<=', now())
            ->latest();
    }

    /**
     * Get the pending subscription for this user
     */
    public function pendingSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'pending')
            ->latest();
    }

    /**
     * Get the current subscription package
     */
    public function currentPackage()
    {
        $subscription = $this->activeSubscription;
        return $subscription ? $subscription->package : null;
    }

    /**
     * Check if user has access to a specific feature
     */
    public function hasSubscriptionAccess(string $feature): bool
    {
        $package = $this->currentPackage();
        
        if (!$package) {
            // Free users only have access to basic news
            return $feature === 'news';
        }

        return $package->hasAccess($feature);
    }

    /**
     * Check if user can access a news article based on subscription package permissions
     */
    public function canAccessNews($news): bool
    {
        $package = $this->currentPackage();
        
        // Free users (no package) can only access free content that is not exclusive
        if (!$package) {
            return strtolower($news->subscription_required ?? 'free') === 'free' && !$news->is_exclusive;
        }

        // Check if package has access to news feature
        if (!$package->hasAccess('news')) {
            return false;
        }

        // Check language access based on package permissions
        // Logic: 
        // - If package has language permissions enabled (at least one true), only allow those languages
        // - If both are false, allow all languages (no restrictions)
        if ($news->language) {
            $newsLang = strtolower(trim($news->language));
            
            // Get package permissions - reload from database to avoid caching issues
            $packageId = $package->id;
            $freshPackage = \App\Models\SubscriptionPackage::find($packageId);
            
            if (!$freshPackage) {
                return false; // Package not found
            }
            
            // Get package permissions directly from fresh database record
            $hasBanglaAccess = (bool) $freshPackage->access_bangla;
            $hasEnglishAccess = (bool) $freshPackage->access_english;
            
            // Check restrictions - Enforce strict language access
            // If a language is disabled in the package, access to that language content is denied.
            // If both are disabled, access to any language content is denied.
            $langAllowed = match($newsLang) {
                'bn', 'bangla' => $hasBanglaAccess,
                'en', 'english' => $hasEnglishAccess,
                default => false, // Unknown language - deny
            };
            
            if (!$langAllowed) {
                return false; // Package doesn't allow this language
            }
        }

        // Check subscription_required level
        $subscriptionRequired = strtolower($news->subscription_required ?? 'free');
        
        // Free news - check if package allows it
        if ($subscriptionRequired === 'free') {
            // Free news is accessible if package has news access
            // But exclusive free news requires exclusive permission
            if ($news->is_exclusive) {
                return $package->hasAccess('exclusive');
            }
            return true; // Regular free news is accessible
        }

        // For non-free news, check if package has appropriate access
        // This is a fallback - ideally subscription_required should match package permissions
        // But we check package permissions directly
        if ($subscriptionRequired !== 'free') {
            // If news requires subscription, ensure package has news access
            if (!$package->hasAccess('news')) {
                return false;
            }
        }

        // Exclusive content requires exclusive permission
        if ($news->is_exclusive) {
            return $package->hasAccess('exclusive');
        }

        // If we get here, user has news access and content is not exclusive
        return true;
    }

    /**
     * Check if user has ad-free access
     */
    public function hasAdFreeAccess(): bool
    {
        $package = $this->currentPackage();
        return $package && $package->ad_free === true;
    }

    /**
     * Get all notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Check if user should receive email notifications
     */
    public function shouldReceiveEmail(): bool
    {
        return $this->email_notifications_enabled ?? true;
    }

    /**
     * Get subscription tier level for email content filtering
     */
    public function getSubscriptionTierLevel(): int
    {
        $package = $this->currentPackage();
        return $package ? $package->getTierLevel() : 0; // 0 = free tier
    }

    /**
     * Get subscription tier slug
     */
    public function getSubscriptionTier(): string
    {
        $package = $this->currentPackage();
        return $package ? $package->slug : 'free';
    }
}
