<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    protected $fillable = [
        'type',
        'recipient',
        'message',
        'subject',
        'status',
        'response',
        'context',
        'user_id',
        'branch_id',
    ];

    /**
     * Get the user who triggered this communication.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch associated with this communication.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get status badge color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'green',
            'failed' => 'red',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get type icon for display.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'sms' => '📱',
            'email' => '📧',
            default => '📤',
        };
    }
}
