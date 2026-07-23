<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // View Cart
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $cartItems = CartItem::with(['product', 'variation'])
            ->where('user_id', $user->id)
            ->get();

        // If no items are currently selected, default all items to selected
        if ($cartItems->isNotEmpty() && !$cartItems->contains('is_selected', true)) {
            CartItem::where('user_id', $user->id)->update(['is_selected' => true]);
            $cartItems = CartItem::with(['product', 'variation'])
                ->where('user_id', $user->id)
                ->get();
        }

        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            if ($item->is_selected) {
                $subtotal += $item->subtotal;
                $totalWeight += ($item->product->weight ?? 0.50) * $item->quantity;
            }
        }

        // Fetch available coupons the user can claim
        $allCoupons = Coupon::where('is_active', true)->get();
        
        $claimedCouponIds = UserCoupon::where('user_id', $user->id)
            ->pluck('coupon_id')
            ->toArray();

        $usedCouponIds = UserCoupon::where('user_id', $user->id)
            ->whereNotNull('used_at')
            ->pluck('coupon_id')
            ->toArray();

        // Active coupon applied in the session
        $appliedCoupon = null;
        $discount = 0.00;
        $couponCode = session('applied_coupon_code');

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValidForUser($user, $subtotal)) {
                $appliedCoupon = $coupon;
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon_code');
            }
        }

        $total = max(0.00, $subtotal - $discount);

        return view('shop.cart', compact('cartItems', 'subtotal', 'totalWeight', 'discount', 'total', 'allCoupons', 'claimedCouponIds', 'usedCouponIds', 'appliedCoupon'));
    }

    // Add Item to Cart
    public function add(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to add items to cart.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $variationId = $request->product_variation_id;
        $quantity = $request->quantity;

        // Verify stock
        if ($variationId) {
            $variation = ProductVariation::findOrFail($variationId);
            if ($variation->stock < $quantity) {
                return back()->with('error', 'Sorry, only ' . $variation->stock . ' units of this variation are available.');
            }
        } else {
            $product = Product::findOrFail($productId);
            if ($product->stock < $quantity) {
                return back()->with('error', 'Sorry, only ' . $product->stock . ' units of this product are available.');
            }
        }

        // Check if item already exists in cart
        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('product_variation_id', $variationId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            // Verify stock for updated quantity
            if ($variationId) {
                if ($variation->stock < $newQuantity) {
                    return back()->with('error', 'Cannot add more. Total stock is ' . $variation->stock . '.');
                }
            } else {
                if ($product->stock < $newQuantity) {
                    return back()->with('error', 'Cannot add more. Total stock is ' . $product->stock . '.');
                }
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'product_variation_id' => $variationId,
                'quantity' => $quantity,
                'is_selected' => true,
            ]);
        }

        return redirect()->route('shop.cart')->with('success', 'Product added to cart successfully!');
    }

    // Update Quantity
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->quantity;

        // Validate stock
        if ($cartItem->product_variation_id) {
            if ($cartItem->variation->stock < $quantity) {
                return back()->with('error', 'Sorry, only ' . $cartItem->variation->stock . ' units are available.');
            }
        } else {
            if ($cartItem->product->stock < $quantity) {
                return back()->with('error', 'Sorry, only ' . $cartItem->product->stock . ' units are available.');
            }
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return redirect()->route('shop.cart')->with('success', 'Cart updated successfully.');
    }

    // Remove Item
    public function remove($id)
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)->findOrFail($id);
        $cartItem->delete();

        return redirect()->route('shop.cart')->with('success', 'Item removed from cart.');
    }

    // Update item selection state (is_selected)
    public function updateSelection(Request $request)
    {
        $user = Auth::user();
        $checkedIds = $request->checked_ids ?? [];

        // Mark items as selected or unselected
        CartItem::where('user_id', $user->id)->update(['is_selected' => false]);
        if (!empty($checkedIds)) {
            CartItem::where('user_id', $user->id)
                ->whereIn('id', $checkedIds)
                ->update(['is_selected' => true]);
        }

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            if ($item->is_selected) {
                $subtotal += $item->subtotal;
                $totalWeight += ($item->product->weight ?? 0.50) * $item->quantity;
            }
        }

        $discount = 0.00;
        $couponCode = session('applied_coupon_code');
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValidForUser($user, $subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon_code');
            }
        }

        $total = max(0.00, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal, 2),
            'total_weight' => (float)$totalWeight,
            'total_weight_formatted' => number_format($totalWeight, 2) . ' kg',
            'discount' => number_format($discount, 2),
            'total' => number_format($total, 2),
            'coupon_removed' => $couponCode && !session('applied_coupon_code'),
        ]);
    }

    // Claim Coupon
    public function claimCoupon(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to claim coupons.');
        }

        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
        ]);

        $couponId = $request->coupon_id;

        // Verify it hasn't been claimed yet
        $alreadyClaimed = UserCoupon::where('user_id', $user->id)
            ->where('coupon_id', $couponId)
            ->exists();

        if ($alreadyClaimed) {
            return back()->with('error', 'You have already claimed this coupon.');
        }

        UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $couponId,
        ]);

        return back()->with('success', 'Coupon claimed successfully! You can now apply it to your cart.');
    }

    // Apply Coupon to Cart
    public function applyCoupon(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to apply coupons.');
        }

        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->where('is_active', true)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        // Calculate current subtotal from selected items only
        $cartItems = CartItem::where('user_id', $user->id)->get();
        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->is_selected) {
                $subtotal += $item->subtotal;
            }
        }

        if (!$coupon->isValidForUser($user, $subtotal)) {
            if ($subtotal < $coupon->min_spend) {
                return back()->with('error', 'Minimum pembelian sebanyak RM' . number_format($coupon->min_spend, 2) . ' diperlukan untuk menggunakan kupon ini.');
            }
            return back()->with('error', 'This coupon is not valid for you (perhaps you need to claim it first or you have already used it).');
        }

        session(['applied_coupon_code' => $coupon->code]);

        return back()->with('success', 'Coupon code applied successfully!');
    }

    // Remove Applied Coupon
    public function removeCoupon()
    {
        session()->forget('applied_coupon_code');
        return back()->with('success', 'Coupon removed.');
    }
}
