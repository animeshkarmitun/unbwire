<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicketReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'admin_id',
        'message',
        'is_internal',
        'is_automated',
        'attachments',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_automated' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who replied
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who replied
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get attachments for this reply
     */
    public function replyAttachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'reply_id');
    }

    /**
     * Check if reply is from user
     */
    public function isFromUser(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Check if reply is from admin
     */
    public function isFromAdmin(): bool
    {
        return !is_null($this->admin_id);
    }

    /**
     * Check if reply is internal
     */
    public function isInternal(): bool
    {
        return $this->is_internal;
    }

    /**
     * Boot method to log activity
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->ticket->logActivity('reply_added', null, null, 
                $reply->is_internal ? 'Internal note added' : 'Reply added');
        });
    }
}
