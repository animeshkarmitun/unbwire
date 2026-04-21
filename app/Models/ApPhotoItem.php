<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPhotoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ap_photo_id',
        'filename',
        'file_path',
        'file_url',
        'file_size',
    ];

    public function apPhoto()
    {
        return $this->belongsTo(ApPhoto::class, 'ap_photo_id');
    }
}
