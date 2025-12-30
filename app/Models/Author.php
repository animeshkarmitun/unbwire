<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = [
        'name',
        'language',
        'designation',
        'photo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the news articles written by this author
     */
    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }

    /**
     * Scope for active authors
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
