<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TrackAffiliateReferral;
use App\Models\AffiliateSale;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Support\ShippingRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    /**
     * Tampilkan halaman Keranjang Belanja + Checkout E-Commerce (gaya Shopee).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Jika masuk dari tombol "Beli Sekarang" (?buy_now=product_id&qty=1), otomatis masukkan ke keranjang
        if ($request->filled('buy_now')) {
            $prod = Product::where('id', $request->query('buy_now'))
                ->orWhere('slug', $request->query('buy_now'))
                ->first();
            if ($prod) {
                $qty = max(1, (int) $request->query('qty', 1));
                $existing = CartItem::where('user_id', $user->id)->where('product_id', $prod->id)->first();
                if ($existing) {
                    $existing->update(['quantity' => $qty]);
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                    ]);
                }
                return redirect()->route('cart.index')->with('success', 'Produk "' . $prod->name . '" siap di-checkout!');
            }
        }

        $cartItems = CartItem::with('product.category')
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn ($item) => $item->product !== null)
            ->values();

        ShippingRateService::syncDefaultZoneKeywords();
        $shippingZones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        $indonesiaRegions = ShippingRateService::indonesiaRegions();
        $lamonganDistricts = ShippingRateService::lamonganDistricts();
        $districtsByCity = ShippingRateService::districtsByCity();
        $popularLocations = ShippingRateService::popularLocations();
        $provinces = ShippingRateService::provinces();
        $detectedAffiliate = TrackAffiliateReferral::currentAffiliate($request);
        $gatewaySettings = ShippingRateService::gatewaySettings();

        return view('shop.cart', compact(
            'user',
            'cartItems',
            'shippingZones',
            'indonesiaRegions',
            'lamonganDistricts',
            'districtsByCity',
            'popularLocations',
            'provinces',
            'detectedAffiliate',
            'gatewaySettings'
        ));
    }

    /**
     * Tambah produk ke Keranjang Belanja (atau langsung arahkan ke halaman Checkout jika buy_now=1).
     */
    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
            'buy_now' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        $qty = (int) ($validated['quantity'] ?? 1);

        $item = CartItem::where('user_id', $user->id)->where('product_id', $product->id)->first();
        if ($item) {
            $item->update([
                'quantity' => $request->boolean('buy_now') ? $qty : ($item->quantity + $qty),
            ]);
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $qty,
            ]);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('cart.index')->with('success', 'Silakan lengkapi alamat pengiriman & pilih ongkir J&T untuk melanjutkan pembayaran.');
        }

        return back()->with('success', '🛒 "' . $product->name . '" (' . $qty . ' pcs) berhasil ditambahkan ke Keranjang Belanja!');
    }

    public function updateItem(Request $request, CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cartItem->update(['quantity' => (int) $validated['quantity']]);

        return back()->with('success', 'Jumlah produk di keranjang diperbarui.');
    }

    public function removeItem(Request $request, CartItem $cartItem)
    {
        abort_if($cartItem->user_id !== $request->user()->id, 403);
        $cartItem->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Buat Pesanan Baru dari Keranjang, hitung Ongkir J&T sesuai Zona, buat Token Payment Gateway (Midtrans/Otomatis),
     * dan catat komisi Mitra Afiliator jika ada.
     */
    public function checkout(Request $request)
    {
        $user = $request->user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn ($i) => $i->product !== null);

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->withErrors(['cart' => 'Keranjang belanja Anda masih kosong.']);
        }

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:80'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:500'],
            'shipping_zone_id' => ['nullable', 'exists:shipping_zones,id'],
            'payment_channel' => ['required', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Tentukan zona tarif J&T secara otomatis dari alamat yang dimasukkan pelanggan
        $zone = ShippingRateService::matchZone(
            $validated['city'],
            $validated['province'],
            $validated['district'] ?? null,
            $validated['address'] ?? null
        );

        if (!$zone && !empty($validated['shipping_zone_id'])) {
            $zone = ShippingZone::find($validated['shipping_zone_id']);
        }
        if (!$zone) {
            $zone = ShippingZone::where('is_active', true)->orderBy('sort_order')->firstOrFail();
        }

        $subtotal = 0;
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $price = (int) ($item->product->price ?? 129000);
            $weight = (int) ($item->product->weight ?: 200);
            $subtotal += $price * $item->quantity;
            $totalWeight += $weight * $item->quantity;
        }

        $shippingCalc = ShippingRateService::calculateByZone($zone, $totalWeight, $subtotal);
        $shippingCost = (int) $shippingCalc['cost'];
        $grandTotal = $subtotal + $shippingCost;

        $affiliate = TrackAffiliateReferral::currentAffiliate($request);
        if ($affiliate && $affiliate->id === $user->id) {
            // Opsional: tetap izinkan afiliator mengetes atau berbelanja
        }

        $orderNumber = 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $order = DB::transaction(function () use (
            $user,
            $cartItems,
            $validated,
            $zone,
            $shippingCalc,
            $subtotal,
            $totalWeight,
            $shippingCost,
            $grandTotal,
            $affiliate,
            $orderNumber
        ) {
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'payment_status' => 'unpaid',
                'payment_gateway' => 'midtrans',
                'payment_method' => $validated['payment_channel'],
                'payment_expires_at' => now()->addHours(24),
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'district' => $validated['district'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'address' => $validated['address'],
                'shipping_zone_id' => $zone->id,
                'shipping_zone_name' => $zone->name,
                'courier' => $shippingCalc['courier'],
                'courier_service' => $shippingCalc['service'],
                'shipping_etd' => $shippingCalc['etd'],
                'total_weight' => $totalWeight,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $grandTotal,
                'notes' => $validated['notes'] ?? null,
                'affiliate_id' => $affiliate?->id,
            ]);

            $summaryNames = [];

            foreach ($cartItems as $item) {
                $prod = $item->product;
                $price = (int) ($prod->price ?? 129000);
                $weight = (int) ($prod->weight ?: 200);
                $lineSubtotal = $price * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'product_name' => $prod->name,
                    'product_image' => $prod->image_url,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'weight' => $weight,
                    'subtotal' => $lineSubtotal,
                ]);

                $summaryNames[] = $prod->name . ' (' . $item->quantity . 'x)';

                if ($prod->stock >= $item->quantity) {
                    $prod->decrement('stock', $item->quantity);
                }
            }

            $order->logHistory(
                Order::STATUS_PENDING_PAYMENT,
                'Pesanan Dibuat — Menunggu Pembayaran',
                'Pesanan #' . $order->order_number . ' berhasil dibuat dengan pengiriman ' . $order->courier . ' (' . $order->shipping_zone_name . ').',
                $user->id
            );

            // Jika ada referral afiliator aktif, otomatis catat komisi per pesanan
            if ($affiliate) {
                $totalCommission = 0;
                $firstProductId = $cartItems->first()->product_id;
                $avgRate = (float) ($affiliate->commission_rate ?: 10);

                foreach ($cartItems as $item) {
                    $prod = $item->product;
                    $rate = (float) ($prod->commission_rate ?: $affiliate->commission_rate ?: 10);
                    $lineAmount = (int) ($prod->price ?? 129000) * $item->quantity;
                    $totalCommission += (int) round($lineAmount * ($rate / 100));
                    $avgRate = $rate;
                }

                AffiliateSale::create([
                    'affiliate_id' => $affiliate->id,
                    'product_id' => $firstProductId,
                    'order_id' => $order->id,
                    'order_code' => $order->order_number,
                    'customer_name' => $validated['recipient_name'],
                    'customer_phone' => $validated['recipient_phone'],
                    'package_name' => implode(', ', $summaryNames),
                    'quantity' => (int) $cartItems->sum('quantity'),
                    'sale_amount' => $subtotal,
                    'commission_rate' => $avgRate,
                    'commission_amount' => $totalCommission,
                    'payment_method' => $validated['payment_channel'],
                    'shipping_address' => $validated['address'] . ', ' . $validated['city'] . ', ' . $validated['province'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                ]);
            }

            // Kosongkan keranjang setelah pesanan dibuat
            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });

        // Jika Server Key Midtrans tersedia, buat Snap Token asli dari API Midtrans
        $this->tryCreateMidtransSnapToken($order);

        return redirect()->route('orders.show', $order)
            ->with('open_payment_gateway', true)
            ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran melalui Payment Gateway.');
    }

    protected function tryCreateMidtransSnapToken(Order $order): void
    {
        $gw = ShippingRateService::gatewaySettings();
        $serverKey = trim((string) ($gw['midtrans_server_key'] ?: config('services.midtrans.server_key')));
        if ($serverKey === '') {
            return;
        }

        $isProd = ($gw['mode'] ?? 'sandbox') === 'production';
        $endpoint = $isProd
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->acceptJson()
                ->timeout(8)
                ->post($endpoint, [
                    'transaction_details' => [
                        'order_id' => $order->order_number,
                        'gross_amount' => (int) $order->total,
                    ],
                    'customer_details' => [
                        'first_name' => $order->recipient_name,
                        'email' => $order->user->email ?? 'customer@urskynd.com',
                        'phone' => $order->recipient_phone,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $order->update([
                    'snap_token' => $data['token'] ?? null,
                    'snap_redirect_url' => $data['redirect_url'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            // Gunakan Built-in Payment Gateway Modal jika tidak terkoneksi
        }
    }
}

