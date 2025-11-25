<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedNews extends Model
{
    use HasFactory;

    protected $table = 'archived_news';

    protected $fillable = [
        'original_id',
        'language',
        'category_id',
        'auther_id',
        'image',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_breaking_news',
        'show_at_slider',
        'show_at_popular',
        'is_approved',
        'status',
        'views',
        'is_exclusive',
        'video_url',
        'subscription_required',
        'deleted_by',
        'deleted_at',
        'deletion_reason',
    ];

    protected $casts = [
        'is_breaking_news' => 'boolean',
        'show_at_slider' => 'boolean',
        'show_at_popular' => 'boolean',
        'is_approved' => 'boolean',
        'status' => 'boolean',
        'is_exclusive' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the admin who deleted this news
     */
    public function deletedBy()
    {
        return $this->belongsTo(Admin::class, 'deleted_by');
    }

    /**
     * Get the original category (if still exists)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the author (if still exists)
     */
    public function auther()
    {
        return $this->belongsTo(Admin::class, 'auther_id');
    }
}
