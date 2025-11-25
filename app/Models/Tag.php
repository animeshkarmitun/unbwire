<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'language',
    ];

    /**
     * Get the news items associated with the tag.
     */
    public function news()
    {
        return $this->belongsToMany(News::class, 'news_tags');
    }
}



