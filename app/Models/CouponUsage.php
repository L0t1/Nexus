<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    protected $fillable = ['coupon_id', 'order_id', 'contact_id', 'discount_applied'];
    protected $casts = ['discount_applied' => 'decimal:2'];

    public function coupon(): BelongsTo { return $this->belongsTo(Coupon::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
}
