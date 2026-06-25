<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_amount',
        'discount_type',
        'min_spend',
        'is_active',
    ];

    public function userCoupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    /**
     * Check if this coupon is valid for a given user and spend amount
     */
    public function isValidForUser(User $user, float $spendAmount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($spendAmount < $this->min_spend) {
            return false;
        }

        // Check if user has claimed this coupon
        $userCoupon = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $this->id)
            ->first();

        if (!$userCoupon) {
            return false; // must claim it first
        }

        if ($userCoupon->used_at !== null) {
            return false; // already used
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percent') {
            return round(($subtotal * $this->discount_amount) / 100, 2);
        }
        
        return min((float) $this->discount_amount, $subtotal);
    }
}
