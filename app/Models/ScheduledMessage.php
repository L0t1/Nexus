<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledMessage extends Model
{
    protected $fillable = [
        'merchant_id',
        'conversation_id',
        'created_by',
        'body',
        'scheduled_at',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeDue($query)
    {
        return $query->where('status', 'pending')
                     ->where('scheduled_at', '<=', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
