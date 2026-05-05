<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'contact_id',
        'user_id',
        'external_message_id',
        'direction',
        'type',
        'body',
        'media',
        'ai_extraction',
        'is_ai_processed',
        'status',
    ];

    protected $casts = [
        'media' => 'array',
        'ai_extraction' => 'array',
        'is_ai_processed' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Helpers ──────────────────────────────────

    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }

    public function hasAiData(): bool
    {
        return $this->is_ai_processed && !empty($this->ai_extraction);
    }

    public function getAiField(string $field, mixed $default = null): mixed
    {
        return data_get($this->ai_extraction, $field, $default);
    }
}
