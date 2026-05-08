<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoTagRule extends Model
{
    protected $fillable = [
        'merchant_id',
        'tag_id',
        'field',
        'operator',
        'value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Evaluate whether a contact matches this rule.
     */
    public function matches(Contact $contact): bool
    {
        $contactValue = $contact->{$this->field} ?? null;

        if ($contactValue === null) return false;

        return match ($this->operator) {
            '>'  => $contactValue > $this->value,
            '>=' => $contactValue >= $this->value,
            '<'  => $contactValue < $this->value,
            '<=' => $contactValue <= $this->value,
            '='  => (string) $contactValue === (string) $this->value,
            '!=' => (string) $contactValue !== (string) $this->value,
            default => false,
        };
    }
}
