<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'merchant_id', 'user_id', 'action', 'subject_type',
        'subject_id', 'description', 'metadata', 'ip_address',
    ];

    protected $casts = ['metadata' => 'array'];

    public function merchant(): BelongsTo { return $this->belongsTo(Merchant::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    /**
     * Quick logging helper.
     */
    public static function record(
        int $merchantId, string $action, string $description,
        ?string $subjectType = null, ?int $subjectId = null,
        array $metadata = [], ?int $userId = null
    ): self {
        return static::create([
            'merchant_id'  => $merchantId,
            'user_id'      => $userId ?? auth()->id(),
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'metadata'     => $metadata ?: null,
            'ip_address'   => request()->ip(),
        ]);
    }
}
