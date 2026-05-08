<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'merchant_id', 'order_id', 'invoice_number', 'file_path',
        'subtotal', 'tax_amount', 'discount_amount', 'total', 'currency',
        'merchant_details', 'customer_details', 'notes', 'issued_at', 'due_at',
    ];

    protected $casts = [
        'merchant_details' => 'array',
        'customer_details' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Invoice $inv) {
            if (empty($inv->invoice_number)) {
                $inv->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
