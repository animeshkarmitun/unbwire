<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'sub_category_id',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ApPhotoCategory::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(ApPhotoCategory::class, 'sub_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ApPhotoTag::class, 'ap_photo_tag_relations', 'ap_photo_id', 'tag_id');
    }

    public function items()
    {
        return $this->hasMany(ApPhotoItem::class, 'ap_photo_id');
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
