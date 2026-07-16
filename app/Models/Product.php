<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'base_price',
        'discount_price',
        'stock',
        'image_url',
        'weight',
    ];

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the active price of the product (handling discount if active)
     */
    public function getActivePriceAttribute(): float
    {
        return $this->discount_price !== null && $this->discount_price < $this->base_price
            ? (float) $this->discount_price
            : (float) $this->base_price;
    }
}
