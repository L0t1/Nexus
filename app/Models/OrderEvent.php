<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEvent extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'description',
        'metadata',
        'is_system',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_system' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Factory Helpers ──────────────────────────

    public static function log(Order $order, string $type, string $description, array $metadata = [], ?int $userId = null, bool $isSystem = false): self
    {
        return static::create([
            'order_id'    => $order->id,
            'user_id'     => $userId ?? auth()->id(),
            'type'        => $type,
            'description' => $description,
            'metadata'    => $metadata ?: null,
            'is_system'   => $isSystem,
        ]);
    }
}
