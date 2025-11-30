<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'admin_id',
        'category_id',
        'priority',
        'status',
        'subject',
        'description',
        'email',
        'phone',
        'source',
        'sla_due_at',
        'resolved_at',
        'closed_at',
        'satisfaction_rating',
        'satisfaction_feedback',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    /**
     * Get the user who created the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned admin agent
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportTicketCategory::class, 'category_id');
    }

    /**
     * Get all replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get public replies (non-internal)
     */
    public function publicReplies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')
            ->where('is_internal', false)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get all attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    /**
     * Get all activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(SupportTicketActivity::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get all tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(SupportTicketTag::class, 'support_ticket_ticket_tag', 'ticket_id', 'tag_id');
    }

    /**
     * Get SLA logs
     */
    public function slaLogs(): HasMany
    {
        return $this->hasMany(SupportTicketSlaLog::class, 'ticket_id');
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket && preg_match('/TKT-' . $year . '-(\d+)/', $lastTicket->ticket_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'TKT-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Assign ticket to admin
     */
    public function assignTo($admin): void
    {
        $oldAdmin = $this->admin_id;
        $this->admin_id = is_object($admin) ? $admin->id : $admin;
        $this->save();

        $this->logActivity('assigned', $oldAdmin, $this->admin_id, "Ticket assigned to {$this->admin->name}");
    }

    /**
     * Change ticket status
     */
    public function changeStatus(string $status): void
    {
        $oldStatus = $this->status;
        $this->status = $status;

        if ($status === 'resolved' && !$this->resolved_at) {
            $this->resolved_at = now();
        }

        if ($status === 'closed' && !$this->closed_at) {
            $this->closed_at = now();
        }

        $this->save();
        $this->logActivity('status_changed', $oldStatus, $status);
    }

    /**
     * Change ticket priority
     */
    public function changePriority(string $priority): void
    {
        $oldPriority = $this->priority;
        $this->priority = $priority;
        $this->save();
        $this->logActivity('priority_changed', $oldPriority, $priority);
    }

    /**
     * Mark ticket as resolved
     */
    public function markAsResolved(): void
    {
        $this->changeStatus('resolved');
        if (!$this->resolved_at) {
            $this->resolved_at = now();
            $this->save();
        }
    }

    /**
     * Close ticket
     */
    public function close(): void
    {
        $this->changeStatus('closed');
        if (!$this->closed_at) {
            $this->closed_at = now();
            $this->save();
        }
    }

    /**
     * Add reply to ticket
     */
    public function addReply(string $message, $user = null, $admin = null, bool $isInternal = false): SupportTicketReply
    {
        return SupportTicketReply::create([
            'ticket_id' => $this->id,
            'user_id' => $user ? (is_object($user) ? $user->id : $user) : null,
            'admin_id' => $admin ? (is_object($admin) ? $admin->id : $admin) : null,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);
    }

    /**
     * Calculate SLA due date
     */
    public function calculateSLA(): ?\DateTime
    {
        if (!$this->category || !$this->category->sla_hours) {
            return null;
        }

        $slaHours = $this->category->sla_hours;
        return $this->created_at->copy()->addHours($slaHours);
    }

    /**
     * Check if SLA is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->sla_due_at) {
            return false;
        }

        return now()->greaterThan($this->sla_due_at) && !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if SLA is due soon (within 2 hours)
     */
    public function isDueSoon(): bool
    {
        if (!$this->sla_due_at || $this->isOverdue()) {
            return false;
        }

        return now()->addHours(2)->greaterThan($this->sla_due_at);
    }

    /**
     * Log activity
     */
    public function logActivity(string $action, $oldValue = null, $newValue = null, ?string $description = null): void
    {
        $user = auth()->guard('admin')->user() ?? auth()->user();
        
        SupportTicketActivity::create([
            'ticket_id' => $this->id,
            'user_id' => $user && get_class($user) === 'App\Models\User' ? $user->id : null,
            'admin_id' => $user && get_class($user) === 'App\Models\Admin' ? $user->id : null,
            'action' => $action,
            'old_value' => $oldValue ? (string) $oldValue : null,
            'new_value' => $newValue ? (string) $newValue : null,
            'description' => $description,
        ]);
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeAssignedTo($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeDueSoon($query)
    {
        return $query->whereNotNull('sla_due_at')
            ->whereBetween('sla_due_at', [now(), now()->addHours(2)])
            ->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('admin_id');
    }

    /**
     * Boot method to auto-generate ticket number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }

            // Calculate SLA if category has SLA hours
            if ($ticket->category && $ticket->category->sla_hours) {
                $createdAt = $ticket->created_at ?? now();
                $ticket->sla_due_at = $createdAt->copy()->addHours($ticket->category->sla_hours);
            }

            // Auto-assign if category has default assignee
            if ($ticket->category && $ticket->category->default_assignee_id && !$ticket->admin_id) {
                $ticket->admin_id = $ticket->category->default_assignee_id;
            }
        });

        static::created(function ($ticket) {
            $ticket->logActivity('created', null, null, 'Ticket created');
        });
    }
}
