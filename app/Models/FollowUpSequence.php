<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowUpSequence extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
        'message_template',
        'delay_hours',
        'is_active',
        'trigger_condition',
        'sent_count',
        'converted_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'delay_hours' => 'integer',
        'sent_count' => 'integer',
        'converted_count' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(FollowUpLog::class, 'sequence_id');
    }

    /**
     * Get the conversion rate as a percentage.
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->converted_count / $this->sent_count) * 100, 1);
    }
}
