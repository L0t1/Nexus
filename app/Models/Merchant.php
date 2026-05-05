<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_slug',
        'phone',
        'email',
        'logo_path',
        'country',
        'city',
        'address',
        'subscription_tier',
        'subscription_expires_at',
        'is_active',
        'settings',
        'meta_credentials',
    ];

    protected $casts = [
        'settings' => 'encrypted:array',
        'meta_credentials' => 'encrypted:array',
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers ──────────────────────────────────

    public function isSubscriptionActive(): bool
    {
        return $this->is_active &&
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function getOrderLimitAttribute(): ?int
    {
        return match ($this->subscription_tier) {
            'starter' => 250,
            'growth' => null, // unlimited
            'enterprise' => null,
            default => 250,
        };
    }
}
