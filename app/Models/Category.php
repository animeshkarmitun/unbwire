<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'language',
        'name',
        'slug',
        'show_at_nav',
        'status',
        'order',
        'parent_id',
    ];

    protected $casts = [
        'show_at_nav' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order', 'asc');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Check if category has children
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * Scope to get only parent categories (no parent)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only subcategories (has parent)
     */
    public function scopeSubcategories($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
