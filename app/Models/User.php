<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'merchant_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Relationships ────────────────────────────

    public function ownedMerchant(): HasOne
    {
        return $this->hasOne(Merchant::class, 'user_id');
    }

    public function employer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function assignedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'assigned_to');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ─── Helpers ──────────────────────────────────

    protected ?Merchant $resolvedMerchant = null;

    public function getMerchantAttribute(): ?Merchant
    {
        if ($this->resolvedMerchant !== null) {
            return $this->resolvedMerchant;
        }
        
        $this->resolvedMerchant = $this->ownedMerchant ?? $this->employer;
        return $this->resolvedMerchant;
    }
}
