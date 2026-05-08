<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpLog extends Model
{
    protected $fillable = [
        'sequence_id',
        'conversation_id',
        'contact_id',
        'status',
        'sent_at',
        'converted_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(FollowUpSequence::class, 'sequence_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
