<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'file_path',
        'file_url',
        'file_type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'title',
        'alt_text',
        'caption',
        'description',
        'uploaded_by',
        'uploaded_by_type',
        'folder_id',
        'is_featured',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the uploader (Admin)
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    /**
     * Scope for filtering by file type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    /**
     * Scope for videos only
     */
    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    /**
     * Scope for documents only
     */
    public function scopeDocuments($query)
    {
        return $query->where('file_type', 'document');
    }

    /**
     * Scope for recent media
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for featured media
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('original_filename', 'like', "%{$search}%")
              ->orWhere('alt_text', 'like', "%{$search}%")
              ->orWhere('caption', 'like', "%{$search}%");
        });
    }

    /**
     * Get human-readable file size
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get thumbnail URL (for images)
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->file_type !== 'image') {
            return null;
        }

        // For now, return the original URL
        // In future, we can generate thumbnails
        return $this->file_url;
    }

    /**
     * Check if file exists in storage
     */
    public function fileExists(): bool
    {
        $path = str_replace('storage/', '', $this->file_path);
        return Storage::disk('public')->exists($path);
    }

    /**
     * Delete the physical file from storage
     */
    public function deleteFile(): bool
    {
        if (!$this->fileExists()) {
            return false;
        }

        try {
            $path = str_replace('storage/', '', $this->file_path);
            return Storage::disk('public')->delete($path);
        } catch (\Exception $e) {
            \Log::error('Error deleting media file: ' . $this->file_path . ' - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    /**
     * Check if media is an image
     */
    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    /**
     * Check if media is a video
     */
    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    /**
     * Check if media is a document
     */
    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }

    /**
     * Get dimensions string (for images/videos)
     */
    public function getDimensionsAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return "{$this->width} × {$this->height}";
        }
        return null;
    }
}
