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
        'onboarding_completed',
        'settings',
        'meta_credentials',
    ];

    protected $casts = [
        'settings' => 'encrypted:array',
        'meta_credentials' => 'encrypted:array',
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ─── Helpers ──────────────────────────────────

    public function hasFeature(string $feature): bool
    {
        $tier = $this->subscription_tier ?? 'starter';

        $features = [
            'starter' => ['basic_analytics', '1_channel'],
            'pro' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders'],
            'ultimate' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders', 'ai_extraction', 'priority_inbox', 'broadcasts'],
        ];

        return in_array($feature, $features[$tier] ?? []);
    }

    public function getAllowedFeaturesAttribute(): array
    {
        $tier = $this->subscription_tier ?? 'starter';

        // If subscription has expired, fall back to starter
        if (!$this->isSubscriptionActive()) {
            $tier = 'starter';
        }

        $features = [
            'starter' => ['basic_analytics', '1_channel'],
            'pro' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders'],
            'ultimate' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders', 'ai_extraction', 'priority_inbox', 'broadcasts'],
        ];

        return $features[$tier] ?? [];
    }

    public function isSubscriptionActive(): bool
    {
        return $this->is_active &&
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function getOrderLimitAttribute(): ?int
    {
        return match ($this->subscription_tier) {
            'starter' => 500,
            'pro' => null,
            'ultimate' => null,
            default => 500,
        };
    }
}
