<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReplyTemplate extends Model
{
    protected $fillable = [
        'merchant_id',
        'shortcut',
        'name',
        'body',
        'category',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Render the template body with variable substitution.
     */
    public function render(array $variables = []): string
    {
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $body = str_replace("{{$key}}", $value ?? '', $body);
        }

        return $body;
    }
}
