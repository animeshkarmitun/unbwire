<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecivedMail extends Model
{
    use HasFactory;

    protected $table = 'recived_mails';

    protected $fillable = [
        'email',
        'subject',
        'message',
        'seen',
        'replied',
    ];

    protected $casts = [
        'seen' => 'boolean',
        'replied' => 'boolean',
    ];
}


