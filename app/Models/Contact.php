<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'external_id',
        'platform',
        'name',
        'phone',
        'email',
        'gender',
        'city',
        'neighborhood',
        'street_address',
        'notes',
        'total_orders',
        'total_spent',
        'last_contacted_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'total_spent' => 'decimal:2',
        'last_contacted_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ─── Helpers ──────────────────────────────────

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->street_address,
            $this->neighborhood,
            $this->city,
        ])->filter()->implode(', ');
    }

    public function incrementOrderStats(float $amount): void
    {
        $this->increment('total_orders');
        $this->increment('total_spent', $amount);
    }
}
