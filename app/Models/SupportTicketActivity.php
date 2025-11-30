<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'admin_id',
        'action',
        'old_value',
        'new_value',
        'description',
    ];

    /**
     * Get the ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who performed the action
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get human-readable activity description
     */
    public function getActivityDescriptionAttribute(): string
    {
        $actor = $this->admin ? $this->admin->name : ($this->user ? $this->user->name : 'System');
        
        $descriptions = [
            'created' => "Ticket created by {$actor}",
            'assigned' => "Ticket assigned to {$this->new_value} by {$actor}",
            'status_changed' => "Status changed from {$this->old_value} to {$this->new_value} by {$actor}",
            'priority_changed' => "Priority changed from {$this->old_value} to {$this->new_value} by {$actor}",
            'reply_added' => "Reply added by {$actor}",
        ];

        return $descriptions[$this->action] ?? $this->description ?? "Action: {$this->action}";
    }
}
