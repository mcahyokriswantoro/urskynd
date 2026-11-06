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
     * Deteksi otomatis zona ongkir J&T dari nama Kota/Kabupaten atau Provinsi.
     */
    public static function matchZone(?string $city, ?string $province): ?ShippingZone
    {
        $zones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        if ($zones->isEmpty()) {
            return null;
        }

        $normCity = mb_strtolower(trim((string) $city));
        $normProv = mb_strtolower(trim((string) $province));

        if ($normCity !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->city_list as $matchCity) {
                    if ($matchCity !== '' && (str_contains($normCity, $matchCity) || str_contains($matchCity, $normCity))) {
                        return $zone;
                    }
                }
            }
        }

        if ($normProv !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->province_list as $matchProv) {
                    if ($matchProv !== '' && (str_contains($normProv, $matchProv) || str_contains($matchProv, $normProv))) {
                        return $zone;
                    }
                }
            }
        }

        return $zones->firstWhere('is_default', true) ?? $zones->last();
    }

    /**
     * Daftar Provinsi & Kota populer untuk mempermudah pengisian alamat dan simulasi ongkir J&T dari Lamongan.
     */
    public static function popularLocations(): array
    {
        return [
            ['city' => 'Kab. Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Lamongan Kota', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Babat, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kec. Paciran, Lamongan', 'province' => 'Jawa Timur', 'hint' => 'Sesama Lamongan (Rp 8.000/kg)'],
            ['city' => 'Kab. Gresik', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Tuban', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Bojonegoro', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Sidoarjo', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kab. Jombang', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Mojokerto', 'province' => 'Jawa Timur', 'hint' => 'Sekitar Lamongan (Rp 10.000/kg)'],
            ['city' => 'Kota Malang', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kota Kediri', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Jember', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kab. Banyuwangi', 'province' => 'Jawa Timur', 'hint' => 'Jawa Timur Lainnya (Rp 12.000/kg)'],
            ['city' => 'Kota Semarang', 'province' => 'Jawa Tengah', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Surakarta (Solo)', 'province' => 'Jawa Tengah', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Yogyakarta', 'province' => 'DI Yogyakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Bandung', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Bekasi', 'province' => 'Jawa Barat', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Jakarta Selatan', 'province' => 'DKI Jakarta', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Tangerang', 'province' => 'Banten', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Denpasar', 'province' => 'Bali', 'hint' => 'Pulau Jawa & Bali (Rp 18.000/kg)'],
            ['city' => 'Kota Medan', 'province' => 'Sumatera Utara', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Palembang', 'province' => 'Sumatera Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Balikpapan', 'province' => 'Kalimantan Timur', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Banjarmasin', 'province' => 'Kalimantan Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Makassar', 'province' => 'Sulawesi Selatan', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Mataram', 'province' => 'Nusa Tenggara Barat', 'hint' => 'Luar Jawa (Rp 35.000/kg)'],
            ['city' => 'Kota Jayapura', 'province' => 'Papua', 'hint' => 'Maluku & Papua (Rp 60.000/kg)'],
        ];
    }
}

