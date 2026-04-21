<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPhotoTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function apPhotos()
    {
        return $this->belongsToMany(ApPhoto::class, 'ap_photo_tag_relations', 'tag_id', 'ap_photo_id');
    }
}
