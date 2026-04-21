<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPhotoCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'language',
        'slug',
        'parent_id',
        'status',
        'order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ApPhotoCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ApPhotoCategory::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function apPhotos()
    {
        return $this->hasMany(ApPhoto::class, 'category_id');
    }
}
