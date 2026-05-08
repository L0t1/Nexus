<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CsatSurvey extends Model
{
    protected $fillable = [
        'merchant_id', 'order_id', 'contact_id', 'conversation_id',
        'rating', 'feedback', 'status', 'token', 'sent_at', 'completed_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn (CsatSurvey $s) => $s->token = $s->token ?: Str::random(64));
    }

    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }

    public function getPublicUrlAttribute(): string
    {
        return url("/feedback/{$this->token}");
    }
}
