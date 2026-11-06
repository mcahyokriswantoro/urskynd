<?php

namespace App\Support;

use App\Models\PageContent;
use App\Models\ShippingZone;

class ShippingRateService
{
    public const GATEWAY_KEY = 'payment_gateway_settings';

    public static function gatewaySettings(): array
    {
        $defaults = [
            'provider' => 'midtrans', // midtrans | xendit | builtin
            'mode' => 'sandbox', // sandbox | production
            'midtrans_client_key' => env('MIDTRANS_CLIENT_KEY', ''),
            'midtrans_server_key' => env('MIDTRANS_SERVER_KEY', ''),
            'xendit_secret_key' => env('XENDIT_SECRET_KEY', ''),
            'enable_auto_simulator' => true,
            'origin_city' => 'Kab. Lamongan, Jawa Timur',
        ];

        $saved = PageContent::getContent(self::GATEWAY_KEY, $defaults);

        return array_replace($defaults, $saved);
    }

    public static function calculateByZone(ShippingZone $zone, int $totalWeightGrams, int $subtotal): array
    {
        $kg = max(1, (int) ceil(max(1, $totalWeightGrams) / 1000));
        $rawCost = (int) $zone->cost_first_kg + max(0, $kg - 1) * (int) $zone->cost_next_kg;

        $isFree = $zone->free_shipping_min && $subtotal >= (int) $zone->free_shipping_min;
        $finalCost = $isFree ? 0 : $rawCost;

        return [
            'zone_id' => $zone->id,
            'zone_name' => $zone->name,
            'courier' => $zone->courier ?: 'J&T Express',
            'service' => $zone->service ?: 'EZ (Reguler)',
            'etd' => $zone->etd ?: '2-3 hari',
            'kg' => $kg,
            'raw_cost' => $rawCost,
            'cost' => $finalCost,
            'is_free' => $isFree,
            'free_shipping_min' => $zone->free_shipping_min,
        ];
    }

    /**
     * Pastikan kata kunci kecamatan/kota di 6 zona default sudah lengkap untuk deteksi otomatis dari alamat.
     */
    public static function syncDefaultZoneKeywords(): void
    {
        $zone1 = ShippingZone::where('sort_order', 1)->first();
        if ($zone1 && mb_strlen((string) $zone1->match_cities) < 20) {
            $zone1->update([
                'match_cities' => 'Lamongan, Babat, Paciran, Brondong, Sukodadi, Deket, Tikung, Turi, Kalitengah, Karanggeneng, Sekaran, Maduran, Laren, Solokuro, Karangbinangun, Glagah, Sarirejo, Kembangbahu, Sugio, Kedungpring, Modo, Ngimbang, Bluluk, Sukorame, Sambeng, Mantup, Pucuk',
            ]);
            ShippingZone::where('sort_order', 3)->whereNull('match_cities')->update([
                'match_cities' => 'Malang, Batu, Kediri, Blitar, Tulungagung, Trenggalek, Nganjuk, Madiun, Magetan, Ngawi, Ponorogo, Pacitan, Pasuruan, Probolinggo, Lumajang, Jember, Bondowoso, Situbondo, Banyuwangi, Sampang, Pamekasan, Sumenep, Madura',
            ]);
            ShippingZone::where('sort_order', 4)->whereNull('match_cities')->update([
                'match_cities' => 'Jakarta, Bogor, Depok, Tangerang, Bekasi, Bandung, Cimahi, Cirebon, Tasikmalaya, Sukabumi, Karawang, Garut, Semarang, Solo, Surakarta, Magelang, Pekalongan, Tegal, Salatiga, Purwokerto, Banyumas, Cilacap, Kudus, Jepara, Pati, Klaten, Yogyakarta, Jogja, Sleman, Bantul, Serang, Cilegon, Denpasar, Badung, Gianyar, Tabanan, Singaraja',
            ]);
            ShippingZone::where('sort_order', 5)->whereNull('match_cities')->update([
                'match_cities' => 'Medan, Banda Aceh, Padang, Bukittinggi, Pekanbaru, Dumai, Batam, Tanjungpinang, Jambi, Palembang, Pangkalpinang, Bengkulu, Bandar Lampung, Metro, Pontianak, Singkawang, Palangkaraya, Banjarmasin, Banjarbaru, Balikpapan, Samarinda, Bontang, Tarakan, Manado, Bitung, Gorontalo, Palu, Mamuju, Makassar, Parepare, Palopo, Kendari, Baubau, Mataram, Bima, Lombok, Kupang',
            ]);
            ShippingZone::where('sort_order', 6)->whereNull('match_cities')->update([
                'match_cities' => 'Ambon, Tual, Ternate, Tidore, Jayapura, Merauke, Timika, Mimika, Biak, Nabire, Sorong, Manokwari, Wamena',
            ]);
        }
    }

    /**
     * Deteksi otomatis zona ongkir J&T dari Kota/Kabupaten, Kecamatan, Alamat Lengkap, dan Provinsi yang dimasukkan pelanggan.
     */
    public static function matchZone(?string $city, ?string $province, ?string $district = null, ?string $address = null): ?ShippingZone
    {
        self::syncDefaultZoneKeywords();
        $zones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        if ($zones->isEmpty()) {
            return null;
        }

        $normCity = mb_strtolower(trim((string) $city));
        $normDist = mb_strtolower(trim((string) $district));
        $normAddr = mb_strtolower(trim((string) $address));
        $normProv = mb_strtolower(trim((string) $province));

        $combinedCityDistrict = trim($normCity . ' ' . $normDist);
        $combinedFullText = trim($normCity . ' ' . $normDist . ' ' . $normAddr);

        // 1. Cek kecocokan nama Kota / Kecamatan terlebih dahulu (paling spesifik)
        if ($combinedCityDistrict !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->city_list as $matchCity) {
                    if ($matchCity !== '' && (str_contains($combinedCityDistrict, $matchCity) || ($normCity !== '' && str_contains($matchCity, $normCity)))) {
                        return $zone;
                    }
                }
            }
        }

        // 2. Cek kecocokan kata kunci kota/kecamatan di dalam teks alamat lengkap
        if ($combinedFullText !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->city_list as $matchCity) {
                    if ($matchCity !== '' && mb_strlen($matchCity) >= 4 && str_contains($combinedFullText, $matchCity)) {
                        return $zone;
                    }
                }
            }
        }

        // 3. Cek kecocokan Provinsi
        if ($normProv !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->province_list as $matchProv) {
                    if ($matchProv !== '' && (str_contains($normProv, $matchProv) || str_contains($matchProv, $normProv))) {
                        return $zone;
                    }
                }
            }
        }

        // 4. Cek kecocokan Provinsi dari teks alamat lengkap jika field provinsi belum sesuai
        if ($combinedFullText !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->province_list as $matchProv) {
                    if ($matchProv !== '' && mb_strlen($matchProv) >= 4 && str_contains($combinedFullText, $matchProv)) {
                        return $zone;
                    }
                }
            }
        }

        return $zones->firstWhere('is_default', true) ?? $zones->first();
    }

    /**
     * Daftar Provinsi di Indonesia untuk pilihan alamat.
     */
    public static function provinces(): array
    {
        return [
            'Jawa Timur',
            'Jawa Tengah',
            'DI Yogyakarta',
            'Jawa Barat',
            'DKI Jakarta',
            'Banten',
            'Bali',
            'Aceh',
            'Sumatera Utara',
            'Sumatera Barat',
            'Riau',
            'Kepulauan Riau',
            'Jambi',
            'Sumatera Selatan',
            'Bangka Belitung',
            'Bengkulu',
            'Lampung',
            'Kalimantan Barat',
            'Kalimantan Tengah',
            'Kalimantan Selatan',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Sulawesi Utara',
            'Gorontalo',
            'Sulawesi Tengah',
            'Sulawesi Barat',
            'Sulawesi Selatan',
            'Sulawesi Tenggara',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Maluku',
            'Maluku Utara',
            'Papua',
            'Papua Barat',
            'Papua Barat Daya',
            'Papua Tengah',
            'Papua Pegunungan',
            'Papua Selatan',
        ];
    }

    /**
     * Daftar Provinsi & Kota populer untuk mempermudah pengisian alamat dan deteksi otomatis ongkir J&T dari Lamongan.
     */
    public static function popularLocations(): array
    {
        return [
            ['city' => 'Kab. Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Lamongan Kota', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Babat, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Paciran, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Sukodadi, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Brondong, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kab. Gresik', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Tuban', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Bojonegoro', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Sidoarjo', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Jombang', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Mojokerto', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Bangkalan', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Malang', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kota Kediri', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kota Madiun', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Pasuruan', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Probolinggo', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Jember', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Banyuwangi', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kota Semarang', 'province' => 'Jawa Tengah', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Surakarta (Solo)', 'province' => 'Jawa Tengah', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Yogyakarta', 'province' => 'DI Yogyakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Bogor', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Depok', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Bekasi', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Selatan', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Pusat', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Barat', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Timur', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Utara', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Tangerang', 'province' => 'Banten', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Tangerang Selatan', 'province' => 'Banten', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Denpasar', 'province' => 'Bali', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Medan', 'province' => 'Sumatera Utara', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Padang', 'province' => 'Sumatera Barat', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Pekanbaru', 'province' => 'Riau', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Batam', 'province' => 'Kepulauan Riau', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Palembang', 'province' => 'Sumatera Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Bandar Lampung', 'province' => 'Lampung', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Pontianak', 'province' => 'Kalimantan Barat', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Banjarmasin', 'province' => 'Kalimantan Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Balikpapan', 'province' => 'Kalimantan Timur', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Samarinda', 'province' => 'Kalimantan Timur', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Makassar', 'province' => 'Sulawesi Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Manado', 'province' => 'Sulawesi Utara', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Mataram', 'province' => 'Nusa Tenggara Barat', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Kupang', 'province' => 'Nusa Tenggara Timur', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Ambon', 'province' => 'Maluku', 'hint' => 'Maluku & Papua (Rp 60.000/kg)'],
            ['city' => 'Kota Jayapura', 'province' => 'Papua', 'hint' => 'Maluku & Papua (Rp 60.000/kg)'],
            ['city' => 'Kota Sorong', 'province' => 'Papua Barat Daya', 'hint' => 'Maluku & Papua (Rp 60.000/kg)'],
        ];
    }
}
