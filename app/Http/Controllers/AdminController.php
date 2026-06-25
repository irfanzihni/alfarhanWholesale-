<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Check role access
    private function checkAccess($roles)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, (array)$roles)) {
            abort(403, 'Unauthorized access.');
        }
    }

    // Main Administrative Dashboard redirecting or rendering based on role
    public function dashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Stats for Admin Overview
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('final_amount');
        $onlineRevenue = Order::where('order_type', 'online')->where('status', '!=', 'cancelled')->sum('final_amount');
        $outdoorRevenue = Order::where('order_type', 'outdoor')->where('status', '!=', 'cancelled')->sum('final_amount');
        $orderCount = Order::count();
        $pendingOrders = Order::where('status', 'pending')->orderBy('created_at', 'desc')->take(5)->get();

        // Stock alerts
        $lowStockProducts = Product::where('stock', '<', 10)->whereDoesntHave('variations')->get();
        $lowStockVariations = ProductVariation::with('product')->where('stock', '<', 10)->get();

        // Outdoor sales list
        $outdoorOrders = Order::where('order_type', 'outdoor')->orderBy('created_at', 'desc')->take(5)->get();

        // Feed products for dropdowns
        $productsDropdown = Product::with('variations')->get();

        return view('admin.dashboard', compact(
            'user',
            'totalRevenue',
            'onlineRevenue',
            'outdoorRevenue',
            'orderCount',
            'pendingOrders',
            'lowStockProducts',
            'lowStockVariations',
            'outdoorOrders',
            'productsDropdown'
        ));
    }

    // ----------------------------------------------------
    // ADMIN ONLY: PRODUCT MANAGEMENT (CRUD)
    // ----------------------------------------------------

    public function productList()
    {
        $this->checkAccess('admin');
        $products = Product::with('variations')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function productCreate()
    {
        $this->checkAccess('admin');
        return view('admin.products.create');
    }

    public function productStore(Request $request)
    {
        $this->checkAccess('admin');
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:dates,honey,perfume,bakhoor,others',
            'base_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = '/images/products/placeholder.jpg';
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $imageUrl = '/images/products/' . $imageName;
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function productEdit($id)
    {
        $this->checkAccess('admin');
        $product = Product::with('variations')->findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function productUpdate(Request $request, $id)
    {
        $this->checkAccess('admin');
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:dates,honey,perfume,bakhoor,others',
            'base_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:base_price',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = $product->image_url;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/products'), $imageName);
            $imageUrl = '/images/products/' . $imageName;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function productDestroy($id)
    {
        $this->checkAccess('admin');
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    // Add variation to product
    public function variationStore(Request $request, $productId)
    {
        $this->checkAccess('admin');
        $request->validate([
            'variation_name' => 'required|string|max:255',
            'variation_value' => 'required|string|max:255',
            'variation_price' => 'nullable|numeric|min:0',
            'variation_stock' => 'required|integer|min:0',
        ]);

        ProductVariation::create([
            'product_id' => $productId,
            'name' => $request->variation_name,
            'value' => $request->variation_value,
            'price' => $request->variation_price,
            'stock' => $request->variation_stock,
        ]);

        return back()->with('success', 'Variation added successfully.');
    }

    // Delete variation
    public function variationDestroy($id)
    {
        $this->checkAccess('admin');
        $variation = ProductVariation::findOrFail($id);
        $variation->delete();

        return back()->with('success', 'Variation deleted successfully.');
    }

    // ----------------------------------------------------
    // OUTDOOR SALES AGENT: OFF-LINE ORDER LOGGING
    // ----------------------------------------------------

    public function logOutdoorSale(Request $request)
    {
        $this->checkAccess(['admin', 'outdoor_sales']);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'delivery_address' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variation = null;
        $unitPrice = $product->active_price;

        if ($request->product_variation_id) {
            $variation = ProductVariation::findOrFail($request->product_variation_id);
            $unitPrice = $variation->active_price;

            if ($variation->stock < $request->quantity) {
                return back()->with('error', 'Insufficient stock! Only ' . $variation->stock . ' units available for ' . $product->name . ' (' . $variation->value . ').');
            }
        } else {
            if ($product->stock < $request->quantity) {
                return back()->with('error', 'Insufficient stock! Only ' . $product->stock . ' units available.');
            }
        }

        $totalAmount = $unitPrice * $request->quantity;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => null,
                'order_type' => 'outdoor',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'delivery_address' => $request->delivery_address ?: 'Outdoor Sales Event',
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'final_amount' => $totalAmount,
                'status' => 'completed', // Outdoor sales are completed immediately
                'created_by' => Auth::id(),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variation_id' => $request->product_variation_id,
                'price' => $unitPrice,
                'quantity' => $request->quantity,
            ]);

            // Deduct stock
            if ($variation) {
                $variation->stock -= $request->quantity;
                $variation->save();
            } else {
                $product->stock -= $request->quantity;
                $product->save();
            }

            DB::commit();
            return back()->with('success', 'Outdoor sale logged successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error logging outdoor sale: ' . $e->getMessage());
        }
    }

    // ----------------------------------------------------
    // PURCHASER: INVENTORY MONITOR & RESTOCKING
    // ----------------------------------------------------

    public function inventoryList()
    {
        $this->checkAccess(['admin', 'purchaser', 'storekeeper']);
        $products = Product::with('variations')->orderBy('name', 'asc')->paginate(15);
        return view('admin.inventory.index', compact('products'));
    }

    public function restock(Request $request)
    {
        $this->checkAccess(['admin', 'purchaser']);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($request->product_variation_id) {
            $variation = ProductVariation::findOrFail($request->product_variation_id);
            $variation->stock += $request->quantity;
            $variation->save();
            $name = $variation->product->name . ' (' . $variation->value . ')';
        } else {
            $product = Product::findOrFail($request->product_id);
            $product->stock += $request->quantity;
            $product->save();
            $name = $product->name;
        }

        return back()->with('success', 'Restocked ' . $request->quantity . ' units of ' . $name . ' successfully.');
    }

    // ----------------------------------------------------
    // STOREKEEPER: FULFILLMENT MANAGEMENT
    // ----------------------------------------------------

    public function orderList()
    {
        $this->checkAccess(['admin', 'storekeeper']);
        $orders = Order::with('items.product')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $this->checkAccess(['admin', 'storekeeper']);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        
        // If order status is changing from cancelled back to active or vice-versa, handle stock corrections?
        // For simplicity, we just change the status
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status) . '.');
    }

    // ----------------------------------------------------
    // REPORTS GENERATION
    // ----------------------------------------------------

    public function reports(Request $request)
    {
        $this->checkAccess(['admin', 'outdoor_sales']); // Let outdoor sales view reports if needed, or only admin.

        // Sales by type
        $onlineRevenue = Order::where('order_type', 'online')->where('status', '!=', 'cancelled')->sum('final_amount');
        $outdoorRevenue = Order::where('order_type', 'outdoor')->where('status', '!=', 'cancelled')->sum('final_amount');
        $totalRevenue = $onlineRevenue + $outdoorRevenue;

        // Daily sales data (last 30 days)
        $dailySales = Order::select(
            DB::raw('date(created_at) as sales_date'),
            DB::raw('sum(final_amount) as daily_revenue'),
            DB::raw('count(*) as order_count')
        )
        ->where('status', '!=', 'cancelled')
        ->groupBy('sales_date')
        ->orderBy('sales_date', 'desc')
        ->take(15)
        ->get();

        // Product performance (best sellers)
        $bestSellers = OrderItem::select(
            'product_id',
            DB::raw('sum(quantity) as units_sold'),
            DB::raw('sum(quantity * price) as revenue')
        )
        ->with('product')
        ->groupBy('product_id')
        ->orderBy('units_sold', 'desc')
        ->take(10)
        ->get();

        // Outdoor sales breakdown by staff agent
        $staffSales = Order::select(
            'created_by',
            DB::raw('sum(final_amount) as total_sales'),
            DB::raw('count(*) as order_count')
        )
        ->where('order_type', 'outdoor')
        ->where('status', '!=', 'cancelled')
        ->with('creator')
        ->groupBy('created_by')
        ->get();

        // Low stock overview
        $lowStockProducts = Product::where('stock', '<', 10)->whereDoesntHave('variations')->get();
        $lowStockVariations = ProductVariation::with('product')->where('stock', '<', 10)->get();

        return view('admin.reports.index', compact(
            'onlineRevenue',
            'outdoorRevenue',
            'totalRevenue',
            'dailySales',
            'bestSellers',
            'staffSales',
            'lowStockProducts',
            'lowStockVariations'
        ));
    }
}
