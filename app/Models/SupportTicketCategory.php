<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'default_assignee_id',
        'sla_hours',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the default assignee admin
     */
    public function defaultAssignee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'default_assignee_id');
    }

    /**
     * Get all tickets in this category
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get SLA hours for this category
     */
    public function getSLAHours(): ?int
    {
        return $this->sla_hours;
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug(string $name): string
    {
        return \Str::slug($name);
    }
}
