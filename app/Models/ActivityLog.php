<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'created_by',
        'created_by_type',
        'updated_by',
        'updated_by_type',
        'approve_by',
        'approve_by_type',
        'model_type',
        'model_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     * This is a dynamic relationship that returns the correct model based on user_type
     */
    public function user()
    {
        // This will be set manually in the controller based on user_type
        // For eager loading, we use adminUser() and regularUser() separately
        if ($this->user_type === 'admin') {
            return $this->belongsTo(Admin::class, 'user_id');
        }
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get admin user (explicit relationship for eager loading)
     */
    public function adminUser()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
    
    /**
     * Get regular user (explicit relationship for eager loading)
     */
    public function regularUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created the model
     */
    public function createdBy()
    {
        if ($this->created_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'created_by');
        }
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the model
     */
    public function updatedBy()
    {
        if ($this->updated_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'updated_by');
        }
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who approved the model
     */
    public function approvedBy()
    {
        if ($this->approve_by_type === 'admin') {
            return $this->belongsTo(Admin::class, 'approve_by');
        }
        return $this->belongsTo(User::class, 'approve_by');
    }

    /**
     * Get the model that was affected
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Scope for filtering by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by model type
     */
    public function scopeModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeForUser($query, int $userId, string $userType = 'admin')
    {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    /**
     * Scope for deleted items
     */
    public function scopeDeletedItems($query)
    {
        return $query->where('action', 'deleted');
    }

    /**
     * Scope for recent activity
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get human-readable action description
     */
    public function getActionDescriptionAttribute(): string
    {
        $modelName = class_basename($this->model_type);
        
        return match($this->action) {
            'created' => "Created {$modelName}",
            'updated' => "Updated {$modelName}",
            'deleted' => "Deleted {$modelName}",
            'restored' => "Restored {$modelName}",
            'viewed' => "Viewed {$modelName}",
            default => ucfirst($this->action) . " {$modelName}",
        };
    }

    /**
     * Check if this log can be restored
     */
    public function canBeRestored(): bool
    {
        return $this->action === 'deleted' && !empty($this->old_values);
    }
}
