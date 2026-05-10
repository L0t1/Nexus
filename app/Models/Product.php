<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'merchant_id',
        'name',
        'sku',
        'description',
        'image_path',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $merchantId = $product->merchant_id ?? auth()->user()?->merchant_id;
                
                do {
                    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name), 0, 3));
                    if (empty($prefix)) $prefix = 'PRD';
                    $sku = $prefix . '-' . strtoupper(substr(uniqid(), -6));
                } while (static::where('merchant_id', $merchantId)->where('sku', $sku)->exists());
                
                $product->sku = $sku;
            }
        });
    }
}
