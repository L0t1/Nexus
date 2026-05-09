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
        'ai_extra_credits',
        'ai_messages_reset_at',
        'settings',
        'meta_credentials',
    ];

    protected $casts = [
        'settings' => 'array',
        'meta_credentials' => 'array',
        'subscription_expires_at' => 'datetime',
        'ai_messages_reset_at' => 'datetime',
        'is_active' => 'boolean',
        'onboarding_completed' => 'boolean',
        'ai_enabled' => 'boolean',
        'ai_monthly_messages_count' => 'integer',
        'ai_extra_credits' => 'integer',
        'default_tax_rate' => 'decimal:2',
        'tax_inclusive' => 'boolean',
    ];

    protected $appends = [
        'features',
        'ai_messages_remaining',
        'orders_monthly_limit',
    ];

    public function getFeaturesAttribute()
    {
        return $this->allowed_features;
    }

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
        'starter'  => 0,      // No AI
        'growth'   => 300,    // Basic AI Extraction
        'scale'    => 1500,   // Full AI Assistant + Auto Replies
        'ultimate' => null,   // Unlimited (Custom/Enterprise)
    ];

    public const FEATURES_MAP = [
        'starter'  => [
            'products', 'contacts', 'tags', 'templates', 'followups', 'returns',
            'orders', 'inbox', 'analytics'
        ],
        'growth'   => [
            'products', 'contacts', 'tags', 'templates', 'followups', 'returns',
            'orders', 'inbox', 'analytics', 'advanced_analytics',
            'invoices', 'coupons', 'team_activity', 'ai_extraction'
        ],
        'scale'    => [
            'products', 'contacts', 'tags', 'templates', 'followups', 'returns',
            'orders', 'inbox', 'analytics', 'advanced_analytics',
            'broadcasts', 'invoices', 'coupons', 'csat', 'team_activity', 
            'storefront', 'whatsapp_sync', 'forecast', 'ai_chat', 
            'ai_extraction', 'smart_order', 'priority_inbox', 'all_channels'
        ],
        'ultimate' => [
            'products', 'contacts', 'tags', 'templates', 'followups', 'returns',
            'orders', 'inbox', 'analytics', 'advanced_analytics',
            'broadcasts', 'invoices', 'coupons', 'csat', 'team_activity', 
            'storefront', 'whatsapp_sync', 'forecast', 'ai_chat', 
            'ai_extraction', 'smart_order', 'priority_inbox', 'custom_branding', 
            'dedicated_support', 'all_channels'
        ],
    ];

    public function hasFeature(string $feature): bool
    {
        $tier = $this->subscription_tier ?? 'starter';

        if ($feature === 'all_channels' && in_array($tier, ['scale', 'ultimate'])) {
            return true;
        }

        return in_array($feature, self::FEATURES_MAP[$tier] ?? []);
    }

    public function getAllowedFeaturesAttribute(): array
    {
        $tier = $this->subscription_tier ?? 'starter';

        // If subscription has expired, fall back to starter
        if (!$this->isSubscriptionActive()) {
            $tier = 'starter';
        }

        return self::FEATURES_MAP[$tier] ?? [];
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
        if (!$this->hasFeature('ai_chat') && !$this->hasFeature('ai_extraction')) return false;
        if (!$this->ai_enabled) return false;

        $this->maybeResetMonthlyCounter();

        $limit = self::AI_MONTHLY_LIMITS[$this->subscription_tier ?? 'starter'] ?? 0;

        // They can send if:
        // 1. They are under their monthly limit (and limit is not 0)
        // 2. OR they have extra credits available
        $hasMonthlyQuota = ($limit !== 0 && $limit !== null && $this->ai_monthly_messages_count < $limit);
        $hasUnlimited = ($limit === null);
        $hasExtraCredits = ($this->ai_extra_credits > 0);

        return $hasMonthlyQuota || $hasUnlimited || $hasExtraCredits;
    }

    /**
     * Increment the rolling monthly AI message counter.
     * Call AFTER a reply is successfully sent.
     */
    public function incrementAiMessageCount(): void
    {
        $this->maybeResetMonthlyCounter();

        $limit = self::AI_MONTHLY_LIMITS[$this->subscription_tier ?? 'starter'] ?? 0;

        // If we are still within the monthly limit, increment monthly count
        if ($limit === null || $this->ai_monthly_messages_count < $limit) {
            $this->increment('ai_monthly_messages_count');
            $currentCount = $this->ai_monthly_messages_count;
            
            // Notifications for monthly quota
            if ($limit) {
                $threshold80 = (int) ceil($limit * 0.8);
                if ($currentCount === $threshold80) {
                    $this->owner->notify(new \App\Notifications\AiQuotaWarning($this, 80));
                } elseif ($currentCount >= $limit) {
                    $this->owner->notify(new \App\Notifications\AiQuotaWarning($this, 100));
                }
            }
        } else {
            // Spill over to extra credits
            if ($this->ai_extra_credits > 0) {
                $this->decrement('ai_extra_credits');
            }
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

        $monthlyRemaining = max(0, $limit - $this->ai_monthly_messages_count);
        return $monthlyRemaining + $this->ai_extra_credits;
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

    public function getOrdersMonthlyLimitAttribute(): ?int
    {
        return match ($this->subscription_tier) {
            'starter' => 100,
            'growth' => 500,
            'scale' => null,
            'ultimate' => null,
            default => 500,
        };
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
