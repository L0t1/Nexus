<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'channel_id',
        'contact_id',
        'assigned_to',
        'external_thread_id',
        'platform',
        'status',
        'subject',
        'last_message_preview',
        'last_message_at',
        'is_unread',
        'unread_count',
        'labels',
    ];

    protected $casts = [
        'labels' => 'array',
        'last_message_at' => 'datetime',
        'is_unread' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Scopes ───────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_unread', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    // ─── Helpers ──────────────────────────────────

    public function markAsRead(): void
    {
        $this->update([
            'is_unread' => false,
            'unread_count' => 0,
        ]);
    }

    public function updateLastMessage(Message $message): void
    {
        $this->update([
            'last_message_preview' => str($message->body)->limit(100),
            'last_message_at' => $message->created_at,
            'is_unread' => $message->direction === 'inbound',
            'unread_count' => $message->direction === 'inbound'
                ? $this->unread_count + 1
                : 0,
        ]);
    }
}
