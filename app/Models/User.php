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
     * Check if user can access a news article based on subscription
     */
    public function canAccessNews($news): bool
    {
        $package = $this->currentPackage();
        
        // Free users can only access free content
        if (!$package) {
            return $news->subscription_required === 'free' && !$news->is_exclusive;
        }

        $packageTier = $package->getTierLevel();
        $requiredTier = match($news->subscription_required) {
            'free' => 0,
            'lite' => 1,
            'pro' => 2,
            'ultra' => 3,
            default => 0,
        };

        // User's package tier must be >= required tier
        return $packageTier >= $requiredTier;
    }
}
