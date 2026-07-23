<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Storefront Homepage
    public function home()
    {
        // Get a few featured products for the slider/grid
        $featuredProducts = Product::with('variations')->take(4)->get();
        return view('shop.home', compact('featuredProducts'));
    }

    // Catalog Listing (Shop)
    public function index(Request $request)
    {
        $query = Product::with('variations');

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->has('category') && $request->category != 'all' && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Price Sort filter
        if ($request->has('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy('base_price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('base_price', 'desc');
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(9);

        // Fetch categories and their counts for sidebar
        $dbCategories = Category::all();
        $categories = [];
        foreach ($dbCategories as $cat) {
            $categories[strtolower($cat->name)] = $cat->name;
        }

        return view('shop.index', compact('products', 'categories'));
    }

    // Product Detail Page
    public function show($id)
    {
        $product = Product::with('variations')->findOrFail($id);
        
        // Find other products in same category as recommendations
        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->take(3)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
