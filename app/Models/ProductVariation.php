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
        'weight',
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

    /**
     * Get the weight of this variation (in kg), falling back to parent product weight or parsed weight from value
     */
    public function getActiveWeightAttribute(): float
    {
        if ($this->weight !== null && $this->weight > 0) {
            return (float) $this->weight;
        }

        // Try parsing from value (e.g. "5kg", "500g", "1.5 kg", "250 gram")
        if (!empty($this->value)) {
            $val = strtolower(trim($this->value));
            if (preg_match('/^([\d\.]+)\s*(kg|kilo|kilogram)$/i', $val, $m)) {
                return (float) $m[1];
            }
            if (preg_match('/^([\d\.]+)\s*(g|gram|grams)$/i', $val, $m)) {
                return (float) $m[1] / 1000;
            }
        }

        return (float) ($this->product->weight ?? 0.50);
    }
}
