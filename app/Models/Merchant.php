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
        'default_currency',
        'default_tax_rate',
        'tax_label',
        'tax_inclusive',
        'subscription_tier',
        'subscription_expires_at',
        'is_active',
        'onboarding_completed',
        'ai_enabled',
        'ai_monthly_messages_count',
        'ai_messages_reset_at',
        'settings',
        'meta_credentials',
    ];

    protected $casts = [
        'settings' => 'encrypted:array',
        'meta_credentials' => 'encrypted:array',
        'subscription_expires_at' => 'datetime',
        'ai_messages_reset_at' => 'datetime',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
        'ai_enabled' => 'boolean',
        'ai_monthly_messages_count' => 'integer',
        'default_tax_rate' => 'decimal:2',
        'tax_inclusive' => 'boolean',
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

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function replyTemplates(): HasMany
    {
        return $this->hasMany(ReplyTemplate::class);
    }

    public function scheduledMessages(): HasMany
    {
        return $this->hasMany(ScheduledMessage::class);
    }

    public function followUpSequences(): HasMany
    {
        return $this->hasMany(FollowUpSequence::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function csatSurveys(): HasMany
    {
        return $this->hasMany(CsatSurvey::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(DemandForecast::class);
    }

    // ─── Helpers ──────────────────────────────────

    // Messages per month each tier may auto-send via AI (null = unlimited)
    public const AI_MONTHLY_LIMITS = [
        'starter'  => 0,    // no AI
        'pro'      => 0,    // no AI
        'ultimate' => null, // unlimited
    ];

    public function hasFeature(string $feature): bool
    {
        $tier = $this->subscription_tier ?? 'starter';

        $features = [
            'starter'  => ['basic_analytics', '1_channel'],
            'pro'      => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders'],
            'ultimate' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders', 'ai_chat', 'ai_extraction', 'priority_inbox', 'broadcasts'],
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
            'starter'  => ['basic_analytics', '1_channel'],
            'pro'      => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders'],
            'ultimate' => ['basic_analytics', 'all_channels', 'products', 'advanced_analytics', 'unlimited_orders', 'ai_chat', 'ai_extraction', 'priority_inbox', 'broadcasts'],
        ];

        return $features[$tier] ?? [];
    }

    public function isSubscriptionActive(): bool
    {
        return $this->is_active &&
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    /**
     * Whether the merchant can fire an AI auto-reply right now.
     * Checks: feature access, toggle, and monthly quota.
     */
    public function canSendAiReply(): bool
    {
        if (!$this->hasFeature('ai_chat')) return false;
        if (!$this->ai_enabled) return false;

        $limit = self::AI_MONTHLY_LIMITS[$this->subscription_tier ?? 'starter'] ?? 0;

        if ($limit === 0) return false;
        if ($limit === null) return true; // unlimited

        // Reset counter if we've rolled into a new calendar month
        $this->maybeResetMonthlyCounter();

        return $this->ai_monthly_messages_count < $limit;
    }

    /**
     * Increment the rolling monthly AI message counter.
     * Call AFTER a reply is successfully sent.
     */
    public function incrementAiMessageCount(): void
    {
        $this->maybeResetMonthlyCounter();

        $newCount = $this->ai_monthly_messages_count + 1;
        $this->increment('ai_monthly_messages_count');

        // Fire quota warnings at 80% and 100%
        $limit = self::AI_MONTHLY_LIMITS[$this->subscription_tier ?? 'starter'] ?? 0;
        if (!$limit) return; // unlimited or disabled

        $threshold80 = (int) ceil($limit * 0.8);

        if ($newCount === $threshold80) {
            $this->owner->notify(new \App\Notifications\AiQuotaWarning($this, 80));
        } elseif ($newCount >= $limit) {
            $this->owner->notify(new \App\Notifications\AiQuotaWarning($this, 100));
        }
    }

    /**
     * Returns how many AI messages remain this month (null = unlimited).
     */
    public function getAiMessagesRemainingAttribute(): ?int
    {
        $limit = self::AI_MONTHLY_LIMITS[$this->subscription_tier ?? 'starter'] ?? 0;

        if ($limit === null) return null;

        $this->maybeResetMonthlyCounter();

        return max(0, $limit - $this->ai_monthly_messages_count);
    }

    private function maybeResetMonthlyCounter(): void
    {
        $resetAt = $this->ai_messages_reset_at;

        if ($resetAt === null || $resetAt->month !== now()->month || $resetAt->year !== now()->year) {
            $this->update([
                'ai_monthly_messages_count' => 0,
                'ai_messages_reset_at' => now()->startOfMonth(),
            ]);
        }
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
