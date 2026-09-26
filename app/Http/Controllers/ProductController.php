<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('status', 'active');

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = ProductCategory::all();
        $currentCategory = $request->category ?? 'all';

        return view('products.index', compact('products', 'categories', 'currentCategory'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        abort_if($product->status !== 'active', 404);
        
        $product->load(['category']);
        
        // Find similar products
        $similarProducts = Product::where('product_category_id', $product->product_category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        $isInCollection = auth()->check() && auth()->user()->userProducts()->where('product_id', $product->id)->exists();

        return view('products.show', compact('product', 'similarProducts', 'isInCollection'));
    }

    /**
     * Add product to user's collection (My Shelf).
     */
    public function addToCollection(Request $request, Product $product)
    {
        $user = $request->user();

        if (!$user->products()->where('product_id', $product->id)->exists()) {
            \App\Models\UserProduct::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'status' => 'active',
                'opened_at' => now(),
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke Rak Skincare-mu! Kamu bisa mengaturnya di Rutinitas.');
    }
}
