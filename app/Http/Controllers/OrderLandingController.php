<?php

namespace App\Http\Controllers;

use App\Http\Middleware\TrackAffiliateReferral;
use App\Models\AffiliateSale;
use App\Models\PageContent;
use App\Models\User;
use App\Support\PurchasePageDefaults;
use Illuminate\Http\Request;

class OrderLandingController extends Controller
{
    /**
     * Tampilkan halaman landing detail pembelian produk (ala format sales page / Berdu).
     * Konten dapat diubah admin melalui /admin/detail-pembelian.
     */
    public function index(Request $request)
    {
        $affiliateRef = TrackAffiliateReferral::currentCode($request);

        $content = AdminPurchasePageController::content();

        $packages = collect($content['packages'])->values()->map(function ($pkg, $i) {
            $pkg['id'] = 'paket-' . ($i + 1);
            $pkg['items'] = PurchasePageDefaults::lines($pkg['items'] ?? '');
            $pkg['formatted_price'] = PurchasePageDefaults::rupiah($pkg['price'] ?? 0);
            $pkg['formatted_original_price'] = !empty($pkg['original_price']) ? PurchasePageDefaults::rupiah($pkg['original_price']) : null;
            $pkg['image_url'] = PageContent::imageUrl($pkg['image'] ?? null);
            return $pkg;
        })->all();

        return view('pembelian.index', compact('content', 'packages', 'affiliateRef'));
    }

    /**
     * Submit pemesanan cepat dari halaman detail pembelian, catat komisi afiliasi (jika ada),
     * dan teruskan ke WhatsApp resmi.
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'package_name' => 'required|string|max:150',
            'payment_method' => 'required|string|max:50',
            'shipping_address' => 'required|string|max:300',
            'affiliate_code' => 'nullable|string|max:40',
            'notes' => 'nullable|string|max:500',
        ]);

        $content = AdminPurchasePageController::content();
        $adminWhatsapp = $content['general']['whatsapp_number'];

        // Deteksi otomatis afiliator dari cookie 30 hari / session / input
        $affiliate = TrackAffiliateReferral::currentAffiliate($request, $validated['affiliate_code'] ?? null);
        $orderCode = null;

        if ($affiliate) {
                // Cari harga paket yang sesuai atau ekstrak angka dari nama paket
                $saleAmount = 0;
                foreach ($content['packages'] ?? [] as $pkg) {
                    $label = $pkg['name'] . ' (' . PurchasePageDefaults::rupiah($pkg['price'] ?? 0) . ')';
                    if ($validated['package_name'] === $label || str_contains($validated['package_name'], $pkg['name'])) {
                        $saleAmount = (int) ($pkg['price'] ?? 0);
                        break;
                    }
                }
                if ($saleAmount <= 0 && preg_match('/Rp\s*([0-9.]+)/i', $validated['package_name'], $m)) {
                    $saleAmount = (int) str_replace('.', '', $m[1]);
                }

                $rate = (float) ($affiliate->commission_rate ?: 10);
                $commissionAmount = (int) round($saleAmount * ($rate / 100));
                $orderCode = 'ORD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -5));

                AffiliateSale::create([
                    'affiliate_id' => $affiliate->id,
                    'product_id' => $request->session()->get('affiliate_last_product_id'),
                    'order_code' => $orderCode,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'package_name' => $validated['package_name'],
                    'quantity' => 1,
                    'sale_amount' => $saleAmount,
                    'commission_rate' => $rate,
                    'commission_amount' => $commissionAmount,
                    'payment_method' => $validated['payment_method'],
                    'shipping_address' => $validated['shipping_address'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                ]);
        }

        $text = "Halo Admin URSKYND, saya mau konfirmasi pemesanan produk:\n\n";
        if ($orderCode) {
            $text .= "🧾 *Kode Pesanan*: " . $orderCode . "\n";
        }
        $text .= "📦 *Pilihan Paket*: " . $validated['package_name'] . "\n"
              . "👤 *Nama Pembeli*: " . $validated['customer_name'] . "\n"
              . "📱 *No. WhatsApp*: " . $validated['customer_phone'] . "\n"
              . "💳 *Metode Pembayaran*: " . $validated['payment_method'] . "\n"
              . "🏠 *Alamat Pengiriman*: " . $validated['shipping_address'] . "\n";

        if ($affiliate) {
            $text .= "🤝 *Kode Mitra/Afiliator*: " . $affiliate->affiliate_code . " (" . $affiliate->name . ")\n";
        }

        if (!empty($validated['notes'])) {
            $text .= "📝 *Catatan*: " . $validated['notes'] . "\n";
        }

        $text .= "\nMohon segera diproses ya Admin, terima kasih!";

        $whatsappUrl = 'https://wa.me/' . $adminWhatsapp . '?text=' . rawurlencode($text);

        return redirect($whatsappUrl);
    }

    /**
     * Klik tombol cepat "Order via WhatsApp" pada kartu paket.
     * Otomatis mendeteksi afiliator dari cookie/session dan mencatat transaksi ke database sebelum redirect ke WA!
     */
    public function quickWhatsappOrder(Request $request)
    {
        $content = AdminPurchasePageController::content();
        $adminWhatsapp = $content['general']['whatsapp_number'];
        $pkgIndex = (int) $request->query('pkg', 0);
        $pkg = $content['packages'][$pkgIndex] ?? ($content['packages'][0] ?? null);

        $pkgName = $pkg['name'] ?? 'Paket Skincare URSKYND';
        $saleAmount = (int) ($pkg['price'] ?? 185000);
        $formattedPrice = PurchasePageDefaults::rupiah($saleAmount);

        $affiliate = TrackAffiliateReferral::currentAffiliate($request, $request->query('ref'));
        $orderCode = 'ORD-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -5));

        if ($affiliate) {
            $rate = (float) ($affiliate->commission_rate ?: 10);
            $commissionAmount = (int) round($saleAmount * ($rate / 100));

            AffiliateSale::create([
                'affiliate_id' => $affiliate->id,
                'product_id' => $request->session()->get('affiliate_last_product_id'),
                'order_code' => $orderCode,
                'customer_name' => auth()->user()->name ?? 'Pembeli via Link WA (' . $orderCode . ')',
                'customer_phone' => auth()->user()->phone ?? 'Konfirmasi via WhatsApp',
                'package_name' => $pkgName . ' (' . $formattedPrice . ')',
                'quantity' => 1,
                'sale_amount' => $saleAmount,
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'QRIS Resmi URSKYND',
                'notes' => 'Order cepat via tombol WhatsApp paket (Ref: ' . $affiliate->affiliate_code . ')',
                'status' => 'pending',
            ]);
        }

        $baseMsg = !empty($pkg['wa_message'])
            ? $pkg['wa_message']
            : "Halo Admin URSKYND, saya mau pesan {$pkgName} ({$formattedPrice}) dengan pembayaran QRIS.";

        if ($affiliate) {
            $baseMsg .= "\n\n🧾 *Kode Order*: {$orderCode}\n🤝 *Kode Mitra/Afiliator*: {$affiliate->affiliate_code} ({$affiliate->name})";
        }

        return redirect('https://wa.me/' . $adminWhatsapp . '?text=' . rawurlencode($baseMsg));
    }
}
