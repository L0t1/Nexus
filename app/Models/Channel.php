<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'merchant_id',
        'platform',
        'external_id',
        'label',
        'credentials',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'is_active' => 'boolean',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
