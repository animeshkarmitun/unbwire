<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'language',
        'category_id',
        'auther_id',
        'author_id',
        'image',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_breaking_news',
        'show_at_slider',
        'show_at_popular',
        'is_exclusive',
        'is_approved',
        'status',
        'order_position',
        'breaking_order',
        'slider_order',
        'popular_order',
        'views',
        'created_by',
        'created_by_type',
        'updated_by',
        'updated_by_type',
        'approve_by',
        'approve_by_type',
    ];

    protected $casts = [
        'is_breaking_news' => 'integer',
        'show_at_slider' => 'integer',
        'show_at_popular' => 'integer',
        'is_exclusive' => 'integer',
        'is_approved' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the admin who created this news (explicit relationship)
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    
    /**
     * Get the user who created this news (explicit relationship)
     */
    public function createdByRegularUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the user who created this news (dynamic)
     */
    public function createdByUser()
    {
        if ($this->created_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'created_by');
        }
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin who updated this news (explicit relationship)
     */
    public function updatedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }
    
    /**
     * Get the user who updated this news (explicit relationship)
     */
    public function updatedByRegularUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get the user who updated this news (dynamic)
     */
    public function updatedByUser()
    {
        if ($this->updated_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'updated_by');
        }
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the admin who approved this news (explicit relationship)
     */
    public function approvedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'approve_by');
    }
    
    /**
     * Get the user who approved this news (explicit relationship)
     */
    public function approvedByRegularUser()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }
    
    /**
     * Get the user who approved this news (dynamic)
     */
    public function approvedByUser()
    {
        if ($this->approve_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'approve_by');
        }
        return $this->belongsTo(User::class, 'approve_by');
    }

    /** scope for active items */
    public function scopeActiveEntries($query)
    {
        return $query->where([
            'status' => 1,
            'is_approved' => 1
        ]);
    }

    /** scope for check language */
    public function scopeWithLocalize($query)
    {
        return $query->where([
            'language' => getLangauge()
        ]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tags');
    }

    public function auther()
    {
        return $this->belongsTo(Admin::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if news requires subscription
     */
    public function requiresSubscription(): bool
    {
        return $this->subscription_required !== 'free' || $this->is_exclusive;
    }

    /**
     * Check if news has video content
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    /**
     * Scope for filtering news based on subscription tier
     */
    public function scopeForSubscriptionTier($query, $tier = 'free')
    {
        // Map tier slug to level
        $tierLevel = match(strtolower($tier)) {
            'unb-lite' => 1,
            'unb-pro' => 2,
            'unb-ultra' => 3,
            'lite' => 1,
            'pro' => 2,
            'ultra' => 3,
            default => 0,
        };

        $allowedTiers = [];
        for ($i = 0; $i <= $tierLevel; $i++) {
            $allowedTiers[] = match($i) {
                0 => 'free',
                1 => 'lite',
                2 => 'pro',
                3 => 'ultra',
                default => 'free',
            };
        }

        return $query->whereIn('subscription_required', $allowedTiers)
            ->when($tierLevel < 3, function($q) {
                $q->where('is_exclusive', false);
            });
    }
    /**
     * Scope for filtering news based on user's language permissions
     */
    public function scopeForUserLanguage($query, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return $query; // Guest users - let other scopes handle access (e.g. valid free content)
        }

        // Admins can see everything
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return $query;
        }

        $package = $user->currentPackage();
        
        if (!$package) {
            // No package? Fallback to other scopes, but techincally no language restriction applied here
            // because "Free" users might just see free content in any language? 
            // Ideally we assume free users can see everything labeled 'free'. 
            // Or maybe Free users are unrestricted by language?
            // User request implies "Subscription user" restriction.
            return $query;
        }

        $hasBangla = (bool) $package->access_bangla;
        $hasEnglish = (bool) $package->access_english;

        // Optimized filtering
        if ($hasBangla && $hasEnglish) {
            return $query; // Allow all
        }

        if ($hasBangla) {
             // Allow Bangla AND any content with no specific language set (if that's a thing), or strictly 'bn'/'bangla'
             // Usually Language is required.
             return $query->whereIn('language', ['bn', 'bangla']);
        }

        if ($hasEnglish) {
            return $query->whereIn('language', ['en', 'english']);
        }

        // If neither allowed, block all (or return empty)
        return $query->whereRaw('1 = 0');
    }
}
