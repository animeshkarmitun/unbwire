<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatermarkSetting extends Model
{
    protected $fillable = [
        'enabled',
        'watermark_image',
        'watermark_size',
        'opacity',
        'offset',
        'position',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'watermark_size' => 'integer',
        'opacity' => 'integer',
        'offset' => 'integer',
    ];
}
