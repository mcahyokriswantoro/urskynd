<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('product_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:1|max:50',
            'size' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string',
            'usage_instruction' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        unset($validated['image_file']);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $request->file('image_file')->store('products', 'public');
        }

        $validated['commission_rate'] = (float) ($validated['commission_rate'] ?? 10);
        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$originalSlug}-{$counter}";
            $counter++;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan ke katalog!');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:1|max:50',
            'size' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string',
            'usage_instruction' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_image' => 'nullable|boolean',
        ]);

        unset($validated['image_file'], $validated['remove_image']);

        if ($request->boolean('remove_image')) {
            if ($product->image && !str_starts_with($product->image, 'images/') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        }

        if ($request->hasFile('image_file')) {
            if ($product->image && !str_starts_with($product->image, 'images/') && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image_file')->store('products', 'public');
        }

        if (isset($validated['commission_rate'])) {
            $validated['commission_rate'] = (float) $validated['commission_rate'];
        }

        if ($product->name !== $validated['name']) {
            $newSlug = Str::slug($validated['name']);
            $originalSlug = $newSlug;
            $counter = 1;
            while (Product::where('slug', $newSlug)->where('id', '!=', $product->id)->exists()) {
                $newSlug = "{$originalSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $newSlug;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image && !str_starts_with($product->image, 'images/') && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus dari katalog.');
    }
}
