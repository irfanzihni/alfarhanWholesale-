<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Checkout Page
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        $cartItems = CartItem::with(['product', 'variation'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->subtotal;
            $totalWeight += ($item->product->weight ?? 0.50) * $item->quantity;
        }

        // Apply coupon if valid
        $discount = 0.00;
        $couponCode = session('applied_coupon_code');
        $coupon = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValidForUser($user, $subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon_code');
            }
        }

        $total = max(0.00, $subtotal - $discount);

        return view('shop.checkout', compact('cartItems', 'subtotal', 'discount', 'total', 'couponCode', 'totalWeight'));
    }

    // AJAX Endpoint to get shipping rates
    public function getShippingRates(Request $request)
    {
        $postcode = $request->get('postcode');
        $state = $request->get('state');
        $weight = $request->get('weight', 0.50);

        if (!$postcode || strlen($postcode) < 5) {
            return response()->json(['success' => false, 'message' => 'Poskod tidak sah']);
        }

        $easyParcel = new \App\Services\EasyParcelService();
        $rates = $easyParcel->getRates($postcode, $weight, $state);

        return response()->json([
            'success' => true,
            'rates' => $rates
        ]);
    }

    // Place Order
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to place an order.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'street_address'   => 'required|string',
            'postcode'         => 'required|string|max:10',
            'city'             => 'required|string|max:255',
            'state'            => 'required|string|max:255',
            'payment_method'   => 'required|in:cod,online',
            'shipping_courier' => 'nullable|string',
            'shipping_service' => 'nullable|string',
            'shipping_cost'    => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cartItems = CartItem::with(['product', 'variation'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        // Re-verify stock before proceeding
        foreach ($cartItems as $item) {
            if ($item->product_variation_id) {
                if ($item->variation->stock < $item->quantity) {
                    return redirect()->route('shop.cart')->with('error', 'Stock conflict! Only ' . $item->variation->stock . ' units of ' . $item->product->name . ' (' . $item->variation->value . ') are available.');
                }
            } else {
                if ($item->product->stock < $item->quantity) {
                    return redirect()->route('shop.cart')->with('error', 'Stock conflict! Only ' . $item->product->stock . ' units of ' . $item->product->name . ' are available.');
                }
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->subtotal;
        }

        // Coupon calculations
        $discount = 0.00;
        $couponCode = session('applied_coupon_code');
        $coupon = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValidForUser($user, $subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $shippingCost = (float)$request->input('shipping_cost', 0.00);
        $total = max(0.00, $subtotal - $discount + $shippingCost);

        $deliveryAddress = $request->input('street_address') . ', ' .
                           $request->input('postcode') . ' ' .
                           $request->input('city') . ', ' .
                           $request->input('state');

        // Run DB Transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // 1. Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_type' => 'online',
                'customer_name' => $request->customer_name,
                'customer_email' => $user->email,
                'customer_phone' => $request->customer_phone,
                'delivery_address' => $deliveryAddress,
                'total_amount' => $subtotal,
                'discount_amount' => $discount,
                'shipping_cost' => $shippingCost,
                'final_amount' => $total,
                'coupon_code' => $couponCode,
                'status' => 'pending',
                'shipping_courier' => $request->input('shipping_courier'),
                'shipping_service' => $request->input('shipping_service'),
                'shipping_postcode' => $request->input('postcode'),
                'shipping_city' => $request->input('city'),
                'shipping_state' => $request->input('state'),
            ]);

            // 2. Create order items and decrement stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variation_id' => $item->product_variation_id,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                ]);

                // Decrement stock
                if ($item->product_variation_id) {
                    $variation = ProductVariation::lockForUpdate()->find($item->product_variation_id);
                    $variation->stock -= $item->quantity;
                    $variation->save();
                } else {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            // 3. Mark coupon as used
            if ($coupon) {
                $userCoupon = UserCoupon::where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->first();
                if ($userCoupon) {
                    $userCoupon->used_at = now();
                    $userCoupon->save();
                }
            }

            // 4. Clear Cart
            CartItem::where('user_id', $user->id)->delete();
            session()->forget('applied_coupon_code');

            DB::commit();

            // ─── Routing berdasarkan kaedah bayaran ─────────────────────────
            if ($request->payment_method === 'online') {
                // Redirect ke ToyyibPay sandbox payment page
                return redirect()
                    ->route('checkout.payment', ['order_id' => $order->id])
                    ->with('info', 'Sila lengkapkan pembayaran anda.');
            }

            // COD — terus ke halaman success
            return redirect()
                ->route('checkout.success', $order->id)
                ->with('success', 'Terima kasih! Pesanan anda telah diterima.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // Success confirmation
    public function success($id)
    {
        $user = Auth::user();
        $order = Order::with('items.product')->where('user_id', $user->id)->findOrFail($id);
        return view('shop.success', compact('order'));
    }

    // Customer order history
    public function orders()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shop.orders', compact('orders'));
    }
}
