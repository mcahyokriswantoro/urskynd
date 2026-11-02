<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use App\Support\PurchasePageDefaults;
use Illuminate\Http\Request;

class OrderLandingController extends Controller
{
    /**
     * Tampilkan halaman landing detail pembelian produk (ala format sales page / Berdu).
     * Konten dapat diubah admin melalui /admin/detail-pembelian.
     */
    public function index()
    {
        $content = AdminPurchasePageController::content();

        $packages = collect($content['packages'])->values()->map(function ($pkg, $i) {
            $pkg['id'] = 'paket-' . ($i + 1);
            $pkg['items'] = PurchasePageDefaults::lines($pkg['items'] ?? '');
            $pkg['formatted_price'] = PurchasePageDefaults::rupiah($pkg['price'] ?? 0);
            $pkg['formatted_original_price'] = !empty($pkg['original_price']) ? PurchasePageDefaults::rupiah($pkg['original_price']) : null;
            $pkg['image_url'] = PageContent::imageUrl($pkg['image'] ?? null);
            return $pkg;
        })->all();

        return view('pembelian.index', compact('content', 'packages'));
    }

    /**
     * Submit pemesanan cepat dari halaman detail pembelian dan teruskan ke WhatsApp resmi.
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:25',
            'package_name' => 'required|string|max:150',
            'payment_method' => 'required|string|max:50',
            'shipping_address' => 'required|string|max:300',
            'notes' => 'nullable|string|max:500',
        ]);

        $adminWhatsapp = AdminPurchasePageController::content()['general']['whatsapp_number'];

        $text = "Halo Admin URSKYND, saya mau konfirmasi pemesanan produk:\n\n"
              . "📦 *Pilihan Paket*: " . $validated['package_name'] . "\n"
              . "👤 *Nama Pembeli*: " . $validated['customer_name'] . "\n"
              . "📱 *No. WhatsApp*: " . $validated['customer_phone'] . "\n"
              . "💳 *Metode Pembayaran*: " . $validated['payment_method'] . "\n"
              . "🏠 *Alamat Pengiriman*: " . $validated['shipping_address'] . "\n";

        if (!empty($validated['notes'])) {
            $text .= "📝 *Catatan*: " . $validated['notes'] . "\n";
        }

        $text .= "\nMohon segera diproses ya Admin, terima kasih!";

        $whatsappUrl = 'https://wa.me/' . $adminWhatsapp . '?text=' . rawurlencode($text);

        return redirect($whatsappUrl);
    }
}
