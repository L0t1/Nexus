<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'contact_id',
        'conversation_id',
        'created_by',
        'order_number',
        'customer_name',
        'customer_phone',
        'shipping_city',
        'shipping_neighborhood',
        'shipping_street',
        'shipping_notes',
        'items',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'total',
        'currency',
        'tax_rate',
        'tax_amount',
        'coupon_id',
        'payment_method',
        'payment_status',
        'courier_provider',
        'tracking_number',
        'waybill_path',
        'fulfillment_status',
        'dispatched_at',
        'delivered_at',
        'is_ai_generated',
        'ai_confidence',
        'internal_notes',
    ];

    protected $casts = [
        'items' => 'array',
        'ai_confidence' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_ai_generated' => 'boolean',
    ];

    // ─── Boot ─────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                // Example: NX-20260505-ABC12D
                $order->order_number = 'NX-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // ─── Relationships ────────────────────────────

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->latest();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function csatSurvey()
    {
        return $this->hasOne(CsatSurvey::class);
    }

    // ─── Scopes ───────────────────────────────────

    public function scopePending($query)
    {
        return $query->whereIn('fulfillment_status', ['draft', 'confirmed', 'processing']);
    }

    public function scopeInTransit($query)
    {
        return $query->whereIn('fulfillment_status', ['dispatched', 'in_transit', 'out_for_delivery']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('fulfillment_status', 'delivered');
    }

    public function scopeCodPending($query)
    {
        return $query->where('payment_method', 'cod')
                     ->where('payment_status', 'pending')
                     ->where('fulfillment_status', 'delivered');
    }

    // ─── Helpers ──────────────────────────────────

    public function getFullShippingAddressAttribute(): string
    {
        return collect([
            $this->shipping_street,
            $this->shipping_neighborhood,
            $this->shipping_city,
        ])->filter()->implode(', ');
    }

    public function calculateTotal(): void
    {
        $subtotal = collect($this->items ?? [])->sum(fn ($item) => ($item['qty'] ?? 1) * ($item['price'] ?? 0));
        $this->subtotal = $subtotal;

        // Apply discount
        $discounted = $subtotal - ($this->discount_amount ?? 0);

        // Apply tax
        $taxRate = $this->tax_rate ?? 0;
        $this->tax_amount = round($discounted * ($taxRate / 100), 2);

        $this->total = $discounted + $this->tax_amount + ($this->shipping_cost ?? 0);
    }

    public function canBeDispatched(): bool
    {
        return in_array($this->fulfillment_status, ['confirmed', 'processing']);
    }
}
