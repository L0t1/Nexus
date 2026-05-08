<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandForecast extends Model
{
    protected $fillable = [
        'merchant_id', 'product_name', 'product_id', 'forecast_date',
        'predicted_demand', 'actual_demand', 'confidence', 'trend', 'factors',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'factors' => 'array',
        'confidence' => 'decimal:2',
    ];

    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
}
