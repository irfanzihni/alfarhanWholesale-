<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Customer Storefront Front-facing Catalog
Route::get('/', [ProductController::class, 'home'])->name('shop.home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('shop.show');
Route::get('/about', function () {
    return view('shop.about');
})->name('shop.about');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Customer Auth
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Admin Auth Login
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);
});

Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Logged-in Customer Features (Cart, Coupons, Checkout, History)
Route::middleware('auth')->group(function () {
    // Shopping Cart
    Route::get('/cart', [CartController::class, 'index'])->name('shop.cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Coupon Actions
    Route::post('/coupon/claim', [CartController::class, 'claimCoupon'])->name('coupon.claim');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Customer Profile Orders
    Route::get('/orders', [CheckoutController::class, 'orders'])->name('customer.orders');

    // ----------------------------------------------------
    // ADMINISTRATIVE PORTAL (Restricted to Staff Roles)
    // ----------------------------------------------------
    Route::prefix('admin')->group(function () {
        // Dashboard Home page
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Admin Role: Product & Variations CRUD
        Route::get('/products', [AdminController::class, 'productList'])->name('admin.products');
        Route::get('/products/create', [AdminController::class, 'productCreate'])->name('admin.products.create');
        Route::post('/products', [AdminController::class, 'productStore'])->name('admin.products.store');
        Route::get('/products/edit/{id}', [AdminController::class, 'productEdit'])->name('admin.products.edit');
        Route::post('/products/update/{id}', [AdminController::class, 'productUpdate'])->name('admin.products.update');
        Route::post('/products/delete/{id}', [AdminController::class, 'productDestroy'])->name('admin.products.delete');
        
        // Variations CRUD
        Route::post('/products/{productId}/variations', [AdminController::class, 'variationStore'])->name('admin.variations.store');
        Route::post('/variations/delete/{id}', [AdminController::class, 'variationDestroy'])->name('admin.variations.delete');

        // Outdoor Sales Agent: Log Sales
        Route::post('/sales/outdoor', [AdminController::class, 'logOutdoorSale'])->name('admin.sales.outdoor');

        // Purchaser Role: Inventory & Restocking
        Route::get('/inventory', [AdminController::class, 'inventoryList'])->name('admin.inventory');
        Route::post('/inventory/restock', [AdminController::class, 'restock'])->name('admin.inventory.restock');

        // Storekeeper Role: Order Fulfillment
        Route::get('/orders', [AdminController::class, 'orderList'])->name('admin.orders');
        Route::post('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');

        // Reports View
        Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    });
});
