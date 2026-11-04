<?php

namespace App\Support;

use App\Models\PageContent;

class AffiliateSettingsDefaults
{
    public const KEY = 'affiliate_settings';

    public static function get(): array
    {
        return [
            'default_commission_rate' => 10,
            'min_withdrawal_amount' => 20000,
            'cookie_days' => 30,
            'register_badge' => 'Komisi 10%+',
            'register_title' => 'Daftar Mitra & Afiliator',
            'register_subtitle' => 'Jual produk resmi URSKYND & raih komisi yang bisa dicairkan kapan saja',
            'register_benefits_title' => 'Keuntungan Menjadi Mitra / Afiliator Resmi URSKYND:',
            'register_benefits' => "Dapatkan Link & Kode Referral Unik otomatis setelah daftar.\nKomisi langsung 10% dari setiap paket/produk yang berhasil terjual.\nSaldo komisi transparan & bisa dicairkan (withdraw) ke Bank atau E-Wallet.",
            'dashboard_badge' => 'URSKYND Affiliate Center',
            'dashboard_title' => 'Mitra & Afiliator Produk',
            'dashboard_subtitle' => 'Pilih produk dari katalog, bagikan link afiliasi produk Anda, dan dapatkan komisi otomatis setiap ada pembelian.',
            'catalog_title' => 'Pilih Produk & Salin Link Afiliasi',
            'catalog_subtitle' => 'Setiap produk memiliki link afiliasi khusus dengan kode Anda. Saat pembeli klik link produk dan memesan, komisi langsung masuk otomatis!',
            'bundle_banner_badge' => 'Link Halaman Paket Bundling Hemat (Komisi s/d Rp 38.900 / Paket)',
            'bundle_banner_text' => 'Selain link per produk di atas, Anda juga bisa membagikan link halaman promo bundling semua paket:',
            'how_it_works_title' => 'Cara Kerja Komisi Otomatis',
            'how_it_works_subtitle' => 'Tanpa input manual — semua penjualan terlacak otomatis dari link produk Anda.',
            'step_1_title' => 'Salin & Sebarkan Link Produk',
            'step_1_desc' => 'Pilih produk di atas (misal Face Serum atau UV Sunscreen) lalu klik Salin Link atau bagikan langsung dari halaman detail produk.',
            'step_2_title' => 'Otomatis Rekam Klik & Cookie 30 Hari',
            'step_2_desc' => 'Saat calon pembeli membuka link Anda, sistem langsung mencatat jumlah klik produk dan mengunci kode afiliasi Anda di perangkat pembeli selama 30 hari.',
            'step_3_title' => 'Pembeli Checkout = Komisi Masuk Otomatis',
            'step_3_desc' => 'Begitu pembeli melakukan pemesanan produk (baik langsung di halaman produk maupun paket bundling), pesanan otomatis muncul di tabel Riwayat Penjualan & Komisi!',
        ];
    }

    public static function current(): array
    {
        $defaults = self::get();
        $saved = PageContent::getContent(self::KEY, $defaults);

        return array_replace($defaults, $saved);
    }

    public static function benefitsList(?array $settings = null): array
    {
        $settings = $settings ?? self::current();
        $lines = preg_split('/\r\n|\r|\n/', (string) ($settings['register_benefits'] ?? ''));

        return array_values(array_filter(array_map('trim', $lines)));
    }
}

