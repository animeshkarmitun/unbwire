<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    protected $fillable = [
        'type',
        'title',
        'description',
        'alt_text',
        'caption',
        'media_id',
        'video_url',
        'video_platform',
        'video_id',
        'gallery_slug',
        'sort_order',
        'is_exclusive',
        'status',
        'language',
        'created_by',
        'created_by_type',
    ];

    protected $casts = [
        'is_exclusive' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the media item (if from media library)
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Get the creator (Admin)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Check if gallery item is from media library
     */
    public function isFromMediaLibrary(): bool
    {
        return !is_null($this->media_id);
    }

    /**
     * Check if gallery item is external video
     */
    public function isExternalVideo(): bool
    {
        return $this->type === 'video' && !is_null($this->video_url);
    }

    /**
     * Get the display URL (media library or external)
     */
    public function getDisplayUrlAttribute(): ?string
    {
        if ($this->isFromMediaLibrary() && $this->media) {
            return $this->media->file_url;
        }
        
        if ($this->isExternalVideo()) {
            return $this->video_url;
        }
        
        return null;
    }

    /**
     * Get embedded video URL for YouTube, Vimeo, etc.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->isExternalVideo()) {
            return null;
        }

        return match($this->video_platform) {
            'youtube' => $this->video_id ? "https://www.youtube.com/embed/{$this->video_id}" : null,
            'vimeo' => $this->video_id ? "https://player.vimeo.com/video/{$this->video_id}" : null,
            'facebook' => $this->video_url,
            default => $this->video_url,
        };
    }

    /**
     * Extract video ID and platform from URL
     */
    public static function extractVideoInfo(string $url): array
    {
        $platform = null;
        $videoId = null;

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            $platform = 'youtube';
            $videoId = $matches[1];
        }
        // Vimeo
        elseif (preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $url, $matches)) {
            $platform = 'vimeo';
            $videoId = $matches[1];
        }
        // Facebook
        elseif (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
            $platform = 'facebook';
        }

        return [
            'platform' => $platform,
            'video_id' => $videoId,
        ];
    }

    /**
     * Scope for images
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    /**
     * Scope for videos
     */
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    /**
     * Scope for active galleries
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for gallery slug
     */
    public function scopeForGallery($query, string $slug)
    {
        return $query->where('gallery_slug', $slug);
    }
}
