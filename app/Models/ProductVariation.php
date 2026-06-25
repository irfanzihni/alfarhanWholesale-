<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'value',
        'price',
        'stock',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the active price of this variation, falling back to the parent product's active price
     */
    public function getActivePriceAttribute(): float
    {
        if ($this->price !== null) {
            return (float) $this->price;
        }
        return $this->product->active_price;
    }
}
