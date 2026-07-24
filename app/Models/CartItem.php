<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'is_selected',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    /**
     * Get the active unit price of the item
     */
    public function getUnitPriceAttribute(): float
    {
        if ($this->product_variation_id && $this->variation) {
            return $this->variation->active_price;
        }
        return $this->product->active_price;
    }

    /**
     * Get the subtotal for this cart item line
     */
    public function getSubtotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get unit weight in kg
     */
    public function getUnitWeightAttribute(): float
    {
        if ($this->product_variation_id && $this->variation) {
            return $this->variation->active_weight;
        }
        return (float) ($this->product->weight ?? 0.50);
    }

    /**
     * Get total weight for this cart item line in kg
     */
    public function getItemWeightAttribute(): float
    {
        return $this->unit_weight * $this->quantity;
    }
}
