<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TrackAffiliateReferral;
use App\Models\AffiliateSale;
use App\Models\PageContent;
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
        $categories = ProductCategory::whereHas('products', function ($q) {
            $q->where('status', 'active');
        })->get();
        $currentCategory = $request->category ?? 'all';

        return view('products.index', compact('products', 'categories', 'currentCategory'));
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, Product $product)
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
        $detectedAffiliate = TrackAffiliateReferral::currentAffiliate($request);
        $purchaseContent = AdminPurchasePageController::content();
        $qrisImg = PageContent::imageUrl($purchaseContent['general']['qris_image'] ?? 'images/qris-urskynd.jpg');
        $qrisMerchant = $purchaseContent['general']['qris_merchant'] ?? 'URSKYND SKINCARE';
        $qrisNmid = $purchaseContent['general']['qris_nmid'] ?? 'ID1026569298288';

        return view('products.show', compact(
            'product',
            'similarProducts',
            'isInCollection',
            'detectedAffiliate',
            'qrisImg',
            'qrisMerchant',
            'qrisNmid'
        ));
    }

    /**
     * Checkout & pesan produk langsung dari halaman produk.
     * Otomatis mendeteksi afiliator dari link/cookie (?ref=...) seperti Shopee Affiliate.
     */
    public function orderProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:30',
            'quantity' => 'required|integer|min:1|max:50',
            'shipping_address' => 'required|string|max:300',
            'notes' => 'nullable|string|max:500',
        ]);

        $qty = (int) $validated['quantity'];
        $unitPrice = (int) ($product->price ?? 129000);
        $saleAmount = $unitPrice * $qty;

        $purchaseContent = AdminPurchasePageController::content();
        $adminWhatsapp = $purchaseContent['general']['whatsapp_number'] ?? '6285282784356';

        // Deteksi otomatis afiliator dari session / cookie 30 hari
        $affiliate = TrackAffiliateReferral::currentAffiliate($request);
        $orderCode = 'ORD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -5));

        if ($affiliate) {
            $rate = (float) ($product->commission_rate ?: $affiliate->commission_rate ?: 10);
            $commissionAmount = (int) round($saleAmount * ($rate / 100));

            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'product_id' => $product->id,
                'order_code' => $orderCode,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'package_name' => $product->name . ($qty > 1 ? " ({$qty}x)" : ''),
                'quantity' => $qty,
                'sale_amount' => $saleAmount,
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'QRIS Resmi URSKYND',
                'shipping_address' => $validated['shipping_address'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
        }

        $formattedTotal = 'Rp ' . number_format($saleAmount, 0, ',', '.');
        $text = "Halo Admin URSKYND, saya mau konfirmasi pesanan produk:\n\n"
              . "🧾 *Kode Pesanan*: {$orderCode}\n"
              . "🧴 *Produk*: {$product->name} ({$qty} pcs)\n"
              . "💰 *Total Bayar*: {$formattedTotal}\n"
              . "👤 *Nama Pembeli*: {$validated['customer_name']}\n"
              . "📱 *No. WhatsApp*: {$validated['customer_phone']}\n"
              . "💳 *Metode Pembayaran*: QRIS Resmi URSKYND\n"
              . "🏠 *Alamat Pengiriman*: {$validated['shipping_address']}\n";

        if ($affiliate) {
            $text .= "🤝 *Ref Afiliator*: {$affiliate->affiliate_code} ({$affiliate->name})\n";
        }

        if (!empty($validated['notes'])) {
            $text .= "📝 *Catatan*: {$validated['notes']}\n";
        }

        $text .= "\nSaya sudah/siap melakukan pembayaran via QRIS, mohon diproses ya Admin!";

        return redirect('https://wa.me/' . $adminWhatsapp . '?text=' . rawurlencode($text));
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
