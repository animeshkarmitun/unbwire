<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketSlaLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'sla_type',
        'target_hours',
        'actual_hours',
        'status',
        'breached_at',
    ];

    protected $casts = [
        'target_hours' => 'integer',
        'actual_hours' => 'decimal:2',
        'breached_at' => 'datetime',
    ];

    /**
     * Get the ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Mark SLA as breached
     */
    public function markAsBreached(): void
    {
        $this->status = 'breached';
        $this->breached_at = now();
        $this->save();
    }

    /**
     * Mark SLA as met
     */
    public function markAsMet(): void
    {
        $this->status = 'met';
        $this->save();
    }
}
