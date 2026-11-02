<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class OrderLandingController extends Controller
{
    /**
     * Tampilkan halaman landing detail pembelian produk (ala format sales page / Berdu).
     */
    public function index()
    {
        $products = Product::where('status', 'active')->get();

        $packages = [
            [
                'id' => 'paket-1',
                'name' => 'Paket Glowing Starter',
                'tag' => 'Cocok untuk Pemula',
                'badge' => 'Hemat 15%',
                'items' => [
                    '1x URSKYND Foaming Face Wash (100ml)',
                    '1x URSKYND UV Sunscreen (50ml)',
                    'Bonus: Pouch Cantik & Panduan Rutinitas Kulit',
                ],
                'original_price' => 217000,
                'price' => 185000,
                'formatted_original_price' => 'Rp 217.000',
                'formatted_price' => 'Rp 185.000',
                'free_shipping' => 'Gratis Ongkir Pulau Jawa',
                'popular' => false,
                'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 1: Glowing Starter (Rp 185.000). Apakah promo gratis ongkirnya masih tersedia kak?",
            ],
            [
                'id' => 'paket-2',
                'name' => 'Paket Best Seller - Complete Radiant Glow',
                'tag' => 'Paling Populer & Terlaris',
                'badge' => 'Hemat 25% + Konsultasi Gratis',
                'items' => [
                    '1x URSKYND Foaming Face Wash (100ml)',
                    '1x URSKYND Brightening & Anti-Aging Face Serum (30ml)',
                    '1x URSKYND UV Sunscreen (50ml)',
                    'Bonus: Konsultasi Dokter Kulit Online Gratis',
                    'Bonus: Pouch Eksklusif URSKYND + Meteran Skin Check',
                ],
                'original_price' => 406000,
                'price' => 299000,
                'formatted_original_price' => 'Rp 406.000',
                'formatted_price' => 'Rp 299.000',
                'free_shipping' => 'Gratis Ongkir Seluruh Indonesia',
                'popular' => true,
                'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 2: Complete Radiant Glow (Best Seller - Rp 299.000). Tolong info cara order dan klaim gratis ongkirnya ya kak!",
            ],
            [
                'id' => 'paket-3',
                'name' => 'Paket Ultimate Intensive Flawless Pack',
                'tag' => 'Hasil Maksimal & Tahan Lama',
                'badge' => 'Hemat 35% + Super Lengkap',
                'items' => [
                    '2x URSKYND Brightening Face Serum (30ml) - Double Power',
                    '1x URSKYND Foaming Face Wash (100ml)',
                    '1x URSKYND UV Sunscreen (50ml)',
                    'Bonus: VIP Konsultasi Dokter Spesialis 1 Bulan',
                    'Bonus: Merchandise Eksklusif & Free Retur Garansi 100%',
                ],
                'original_price' => 595000,
                'price' => 389000,
                'formatted_original_price' => 'Rp 595.000',
                'formatted_price' => 'Rp 389.000',
                'free_shipping' => 'Gratis Ongkir Kilat Se-Indonesia + COD',
                'popular' => false,
                'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 3: Ultimate Intensive Flawless (Rp 389.000). Apakah bisa kirim COD ke alamat saya kak?",
            ],
        ];

        return view('pembelian.index', compact('products', 'packages'));
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

        $adminWhatsapp = '6285282784356';

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
