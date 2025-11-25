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
    ];

    protected $casts = [
        'show_at_nav' => 'boolean',
        'status' => 'boolean',
    ];
}
