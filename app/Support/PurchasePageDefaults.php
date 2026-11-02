<?php

namespace App\Support;

/**
 * Konten default halaman /detail-pembelian (dipakai jika admin belum mengubah apa pun).
 */
class PurchasePageDefaults
{
    public const KEY = 'detail_pembelian';

    public static function get(): array
    {
        return [
            'general' => [
                'whatsapp_number' => '6285282784356',
                'whatsapp_display' => '085282784356',
                'banner_title' => 'PROMO SPESIAL HARI INI: GRATIS ONGKIR SE-INDONESIA',
                'banner_subtitle' => 'Dapatkan diskon paket hemat s/d 35% + Free Konsultasi Dokter Kulit resmi URSKYND!',
                'show_countdown' => true,
                'hero_badge' => 'Official Store • Detail Pembelian Produk Asli',
                'hero_title' => 'Paket Skincare Klinis',
                'hero_highlight' => 'URSKYND',
                'hero_title_suffix' => 'Asli Bergaransi',
                'hero_description' => 'Rawat kulit wajah kusam, flek hitam, jerawat, dan perbaiki skin barrier dengan produk formulasi dermatologis berteknologi AI. 100% Terdaftar BPOM, Halal, dan didampingi konsultasi dokter spesialis.',
                'hero_image' => null,
                'packages_title' => 'Pilih Paket Perawatan Terbaik Anda',
                'packages_subtitle' => 'Pesan hari ini untuk mendapatkan harga promo bundling, gratis ongkos kirim, dan bonus spesial.',
                'products_title' => 'Katalog Produk Resmi URSKYND',
                'products_subtitle' => 'Diformulasikan secara sinergis untuk hasil perawatan kulit yang komprehensif.',
                'order_title' => 'Formulir Pemesanan Cepat',
                'order_description' => 'Isi form di bawah ini, scan QRIS resmi URSKYND, lalu konfirmasi pesanan Anda langsung ke WhatsApp Admin Resmi untuk proses pengiriman.',
                'payment_methods' => "QRIS Resmi URSKYND (Semua M-Banking & E-Wallet)",
                'qris_image' => 'images/qris-urskynd.jpg',
                'qris_merchant' => 'URSKYND SKINCARE',
                'qris_nmid' => 'ID1026569298288',
                'testimonials_title' => 'Paket Sampai, Ini Kata Mereka',
                'faq_title' => 'Pertanyaan Seputar Pemesanan',
            ],
            'problem' => [
                'title' => 'Sudah Coba Berbagai Skincare Tapi Hasilnya Belum Kelihatan?',
                'description' => 'Seringkali skincare di pasaran tidak memberikan hasil karena kandungannya tidak sesuai tipe kulit atau bahan aktifnya tidak stabil. Hati-hati juga terhadap maraknya skincare non-BPOM tanpa legalitas resmi!',
                'points' => "Kulit wajah kusam dan belang akibat paparan sinar UV matahari.\nSkin barrier rusak, kulit mudah merah, perih, dan mengelupas.\nBekas jerawat PIE/PIH dan noda hitam membandel yang sulit pudar.",
                'solution_title' => 'Formulasi Klinis + Teknologi Cerdas AI Skin Companion',
                'solution_description' => 'URSKYND dirancang khusus untuk kulit tropis Indonesia dengan bahan aktif berkualitas tinggi seperti Pure Niacinamide, Hyaluronic Acid Multi-Molecular, Ceramide Complex, dan UV Filter spektrum luas.',
                'solution_points' => "Tekstur ringan, cepat meresap, dan non-comedogenic (tidak menyumbat pori).\nDermatologically Tested untuk kulit sensitif sekalipun.\nAman digunakan pria dan wanita usia 15 tahun ke atas.",
            ],
            'trust' => [
                ['icon' => '🛡️', 'title' => '100% Original', 'subtitle' => 'Barcode Resmi BPOM'],
                ['icon' => '📲', 'title' => 'Pembayaran QRIS', 'subtitle' => 'Semua Bank & E-Wallet'],
                ['icon' => '👩‍⚕️', 'title' => 'Free Dokter Chat', 'subtitle' => 'Konsultasi Terpandu'],
                ['icon' => '✨', 'title' => 'Garansi Tukar', 'subtitle' => 'Jika Rusak / Cacat'],
            ],
            'packages' => [
                [
                    'name' => 'Paket Glowing Starter',
                    'tag' => 'Cocok untuk Pemula',
                    'badge' => 'Hemat 15%',
                    'items' => "1x URSKYND Foaming Face Wash (100ml)\n1x URSKYND UV Sunscreen (50ml)\nBonus: Pouch Cantik & Panduan Rutinitas Kulit",
                    'original_price' => 217000,
                    'price' => 185000,
                    'free_shipping' => 'Gratis Ongkir Pulau Jawa',
                    'popular' => false,
                    'image' => null,
                    'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 1: Glowing Starter (Rp 185.000) via pembayaran QRIS. Apakah promo gratis ongkirnya masih tersedia kak?",
                ],
                [
                    'name' => 'Paket Best Seller - Complete Radiant Glow',
                    'tag' => 'Paling Populer & Terlaris',
                    'badge' => 'Hemat 25% + Konsultasi Gratis',
                    'items' => "1x URSKYND Foaming Face Wash (100ml)\n1x URSKYND Brightening & Anti-Aging Face Serum (30ml)\n1x URSKYND UV Sunscreen (50ml)\nBonus: Konsultasi Dokter Kulit Online Gratis\nBonus: Pouch Eksklusif URSKYND + Meteran Skin Check",
                    'original_price' => 406000,
                    'price' => 299000,
                    'free_shipping' => 'Gratis Ongkir Seluruh Indonesia',
                    'popular' => true,
                    'image' => null,
                    'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 2: Complete Radiant Glow (Best Seller - Rp 299.000) via pembayaran QRIS. Tolong info proses order dan klaim gratis ongkirnya ya kak!",
                ],
                [
                    'name' => 'Paket Ultimate Intensive Flawless Pack',
                    'tag' => 'Hasil Maksimal & Tahan Lama',
                    'badge' => 'Hemat 35% + Super Lengkap',
                    'items' => "2x URSKYND Brightening Face Serum (30ml) - Double Power\n1x URSKYND Foaming Face Wash (100ml)\n1x URSKYND UV Sunscreen (50ml)\nBonus: VIP Konsultasi Dokter Spesialis 1 Bulan\nBonus: Merchandise Eksklusif & Free Retur Garansi 100%",
                    'original_price' => 595000,
                    'price' => 389000,
                    'free_shipping' => 'Gratis Ongkir Kilat Se-Indonesia',
                    'popular' => false,
                    'image' => null,
                    'wa_message' => "Halo Admin URSKYND,\n\nSaya mau pesan Paket 3: Ultimate Intensive Flawless (Rp 389.000) dengan pembayaran QRIS. Mohon dibantu proses pengirimannya ya kak!",
                ],
            ],
            'products' => [
                ['image' => 'images/products/lineup.jpeg', 'step' => 'Langkah 1: Cleanser', 'name' => 'URSKYND Foaming Face Wash (100ml)', 'description' => 'Membersihkan debu, sebum, dan polusi hingga ke dalam pori tanpa membuat kulit kesat atau tertarik.', 'price' => 98000],
                ['image' => 'images/products/serum.jpeg', 'step' => 'Langkah 2: Treatment', 'name' => 'URSKYND Brightening Face Serum (30ml)', 'description' => 'Serum konsentrat tinggi untuk memudarkan noda hitam, mencerahkan warna kulit kusam, dan merangsang kolagen.', 'price' => 189000],
                ['image' => 'images/products/sunscreen.jpeg', 'step' => 'Langkah 3: Protection', 'name' => 'URSKYND UV Sunscreen SPF 50+ PA++++ (50ml)', 'description' => 'Proteksi maksimal dari bahaya UV A/B & blue light. Formula tidak berminyak, no whitecast, dan nyaman sepanjang hari.', 'price' => 119000],
            ],
            'testimonials' => [
                ['name' => 'dr. Dinda R.', 'location' => 'Surabaya', 'rating' => 5, 'photo' => null, 'text' => 'Awalnya ragu karena sering gonta-ganti skincare jerawat ga sembuh. Pas coba Paket 2 Complete Radiance, seminggu pertama kemerahan mereda banget, skin barrier makin kuat. Pelayanannya lewat WA juga ramah dan fast respon!'],
                ['name' => 'Siti Nurjanah', 'location' => 'Bandung', 'rating' => 5, 'photo' => null, 'text' => 'Paket tiba cepat cuma 2 hari, bayar pakai QRIS praktis banget. Packing rapi, dapat pouch eksklusif dan gratis konsultasi dokter. Serumnya ringan ga bikin minyakan sama sekali. Recommended banget buat usia 30-an ke atas!'],
                ['name' => 'Anisa Wardani', 'location' => 'Jakarta Selatan', 'rating' => 5, 'photo' => null, 'text' => 'Sunscreen-nya juara, ga whitecast dan ga perih di mata. Saya pesan Paket Ultimate Flawless bareng teman sekantor, dapat gratis ongkir dan bonus konsultasi dokter. Bener-bener puas!'],
            ],
            'faqs' => [
                ['question' => 'Bagaimana metode pembayarannya? Apakah ada COD?', 'answer' => 'Kami tidak melayani pembayaran COD (Bayar di Tempat). Seluruh transaksi menggunakan QRIS Resmi URSKYND SKINCARE (NMID: ID1026569298288) yang dapat di-scan dari semua aplikasi Mobile Banking (BCA, Mandiri, BRI, BNI, BSI, dll) maupun E-Wallet (GoPay, OVO, DANA, ShopeePay, LinkAja).'],
                ['question' => 'Apakah produk URSKYND Asli & Resmi BPOM?', 'answer' => '100% Asli dan Terdaftar Resmi di BPOM RI. Setiap kemasan dilengkapi barcode dan segel keamanan eksklusif dari pabrik resmi.'],
                ['question' => 'Berapa lama waktu pengiriman paket?', 'answer' => 'Untuk wilayah Pulau Jawa biasanya 1-2 hari kerja. Di luar Pulau Jawa berkisar antara 2-4 hari kerja tergantung kota tujuan dan ekspedisi pilihan.'],
                ['question' => 'Bagaimana cara klaim konsultasi dokter gratis?', 'answer' => 'Setelah pemesanan paket terkonfirmasi via WhatsApp, Customer Service kami akan memberikan voucher akses telekonsultasi dokter gratis di menu Konsultasi website.'],
            ],
        ];
    }

    /**
     * Pecah teks multi-baris menjadi array baris yang tidak kosong.
     */
    public static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text))));
    }

    public static function rupiah($value): string
    {
        return 'Rp ' . number_format((int) $value, 0, ',', '.');
    }
}
