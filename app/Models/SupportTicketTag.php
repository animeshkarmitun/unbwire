<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupportTicketTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Get all tickets with this tag
     */
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(SupportTicket::class, 'support_ticket_ticket_tag', 'tag_id', 'ticket_id');
    }
}
