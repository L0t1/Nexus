<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OrderReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'merchant_id', 'order_id', 'contact_id', 'handled_by',
        'return_number', 'reason', 'description', 'status',
        'refund_method', 'refund_amount', 'items', 'admin_notes',
        'approved_at', 'received_at', 'refunded_at',
    ];

    protected $casts = [
        'items' => 'array',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (OrderReturn $r) {
            if (empty($r->return_number)) {
                $r->return_number = 'RET-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function handler(): BelongsTo { return $this->belongsTo(User::class, 'handled_by'); }
}
