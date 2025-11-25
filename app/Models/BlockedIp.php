<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_by',
        'blocked_at',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];

    /**
     * Get the admin who blocked this IP
     */
    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'blocked_by');
    }

    /**
     * Check if an IP is blocked
     */
    public static function isBlocked(string $ip): bool
    {
        return static::where('ip_address', $ip)->exists();
    }

    /**
     * Block an IP address
     */
    public static function block(string $ip, ?string $reason = null, ?int $blockedBy = null): self
    {
        return static::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_by' => $blockedBy,
                'blocked_at' => now(),
            ]
        );
    }

    /**
     * Unblock an IP address
     */
    public static function unblock(string $ip): bool
    {
        return static::where('ip_address', $ip)->delete();
    }
}
