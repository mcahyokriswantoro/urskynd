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
     * Sinkronisasi kata kunci pada 6 zona default agar seluruh 38 Provinsi & 514 Kab/Kota di Indonesia
     * terpetakan secara akurat dari Gudang Lamongan.
     */
    public static function syncDefaultZoneKeywords(): void
    {
        $zone1 = ShippingZone::where('sort_order', 1)->first();
        if ($zone1 && mb_strlen((string) $zone1->match_cities) < 20) {
            $zone1->update([
                'match_cities' => 'Kab. Lamongan, Lamongan',
                'match_provinces' => null,
            ]);
        }

        $zone2 = ShippingZone::where('sort_order', 2)->first();
        if ($zone2 && !str_contains(mb_strtolower((string) $zone2->match_cities), 'kab. gresik')) {
            $zone2->update([
                'match_cities' => 'Kab. Gresik, Gresik, Kota Surabaya, Surabaya, Kab. Tuban, Tuban, Kab. Bojonegoro, Bojonegoro, Kab. Jombang, Jombang, Kab. Mojokerto, Kota Mojokerto, Mojokerto, Kab. Sidoarjo, Sidoarjo, Kab. Bangkalan, Bangkalan',
                'match_provinces' => null,
            ]);
        }
    }

    /**
     * Deteksi otomatis zona ongkir J&T dari Kota/Kabupaten, Provinsi, Kecamatan, dan Alamat Lengkap.
     */
    public static function matchZone(?string $city, ?string $province, ?string $district = null, ?string $address = null): ?ShippingZone
    {
        self::syncDefaultZoneKeywords();
        $zones = ShippingZone::where('is_active', true)->orderBy('sort_order')->get();
        if ($zones->isEmpty()) {
            return null;
        }

        $normCity = self::normalizeRegionName((string) $city);
        $normProv = mb_strtolower(trim((string) $province));
        $normDist = mb_strtolower(trim((string) $district));

        // Jika Provinsi belum tepat tapi Kota dipilih dari daftar 514 Kab/Kota, cari provinsinya otomatis
        if ($normCity !== '') {
            $detectedProv = self::findProvinceByCity((string) $city);
            if ($detectedProv !== null) {
                $normProv = mb_strtolower($detectedProv);
            }
        }

        // 1. Cek kecocokan Kota/Kabupaten secara spesifik pada daftar kota tiap zona (Urutan 1: Lamongan, Urutan 2: Sekitar Lamongan, dst)
        if ($normCity !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->city_list as $matchCity) {
                    $cleanMatch = self::normalizeRegionName($matchCity);
                    if ($cleanMatch !== '' && ($normCity === $cleanMatch || str_contains($normCity, $cleanMatch))) {
                        return $zone;
                    }
                }
            }
        }

        // 2. Cek apakah Kecamatan adalah salah satu dari 27 Kecamatan di Kab. Lamongan (ketika provinsi Jawa Timur)
        if ($normDist !== '' && ($normProv === '' || str_contains($normProv, 'jawa timur'))) {
            foreach (self::lamonganDistricts() as $lamDist) {
                if (mb_strtolower($lamDist) === $normDist || str_contains($normDist, mb_strtolower($lamDist))) {
                    return $zones->first();
                }
            }
        }

        // 3. Cek kecocokan berdasarkan Provinsi (Jawa Timur Lainnya, Pulau Jawa & Bali, Sumatera/Kalimantan/Sulawesi/NTB/NTT, Maluku & Papua)
        if ($normProv !== '') {
            foreach ($zones as $zone) {
                foreach ($zone->province_list as $matchProv) {
                    $cleanProv = mb_strtolower(trim($matchProv));
                    if ($cleanProv !== '' && ($normProv === $cleanProv || str_contains($normProv, $cleanProv))) {
                        return $zone;
                    }
                }
            }
        }

        return $zones->firstWhere('is_default', true) ?? $zones->last();
    }

    public static function normalizeRegionName(string $name): string
    {
        $n = mb_strtolower(trim($name));
        $n = preg_replace('/^(kabupaten|kab\.|kota adm\.|kota)\s+/i', '', $n) ?? $n;
        return trim($n);
    }

    public static function findProvinceByCity(string $city): ?string
    {
        $target = self::normalizeRegionName($city);
        if ($target === '') {
            return null;
        }

        foreach (self::indonesiaRegions() as $prov => $cities) {
            foreach ($cities as $c) {
                if (self::normalizeRegionName($c) === $target || mb_strtolower(trim($c)) === mb_strtolower(trim($city))) {
                    return $prov;
                }
            }
        }

        return null;
    }

    /**
     * Daftar 27 Kecamatan resmi di Kabupaten Lamongan.
     */
    public static function lamonganDistricts(): array
    {
        return [
            'Babat', 'Bluluk', 'Brondong', 'Deket', 'Glagah', 'Kalitengah', 'Karangbinangun',
            'Karanggeneng', 'Kembangbahu', 'Kedungpring', 'Lamongan', 'Laren', 'Maduran',
            'Mantup', 'Modo', 'Ngimbang', 'Paciran', 'Pucuk', 'Sambeng', 'Sarirejo',
            'Sekaran', 'Solokuro', 'Sugio', 'Sukodadi', 'Sukorame', 'Tikung', 'Turi',
        ];
    }

    /**
     * Daftar seluruh 38 Provinsi di Indonesia.
     */
    public static function provinces(): array
    {
        return array_keys(self::indonesiaRegions());
    }

    /**
     * Data Lengkap 38 Provinsi beserta seluruh Kabupaten & Kota di Indonesia (514 Kab/Kota).
     */
    public static function indonesiaRegions(): array
    {
        return [
            'Jawa Timur' => [
                'Kab. Lamongan', 'Kab. Gresik', 'Kota Surabaya', 'Kab. Sidoarjo', 'Kab. Tuban',
                'Kab. Bojonegoro', 'Kab. Jombang', 'Kab. Mojokerto', 'Kota Mojokerto', 'Kab. Bangkalan',
                'Kab. Banyuwangi', 'Kab. Blitar', 'Kota Blitar', 'Kab. Bondowoso', 'Kab. Jember',
                'Kab. Kediri', 'Kota Kediri', 'Kab. Lumajang', 'Kab. Madiun', 'Kota Madiun',
                'Kab. Magetan', 'Kab. Malang', 'Kota Malang', 'Kota Batu', 'Kab. Nganjuk',
                'Kab. Ngawi', 'Kab. Pacitan', 'Kab. Pamekasan', 'Kab. Pasuruan', 'Kota Pasuruan',
                'Kab. Ponorogo', 'Kab. Probolinggo', 'Kota Probolinggo', 'Kab. Sampang', 'Kab. Situbondo',
                'Kab. Sumenep', 'Kab. Trenggalek', 'Kab. Tulungagung',
            ],
            'Jawa Tengah' => [
                'Kota Semarang', 'Kab. Semarang', 'Kota Surakarta', 'Kab. Banjarnegara', 'Kab. Banyumas',
                'Kab. Batang', 'Kab. Blora', 'Kab. Boyolali', 'Kab. Brebes', 'Kab. Cilacap',
                'Kab. Demak', 'Kab. Grobogan', 'Kab. Jepara', 'Kab. Karanganyar', 'Kab. Kebumen',
                'Kab. Kendal', 'Kab. Klaten', 'Kab. Kudus', 'Kab. Magelang', 'Kota Magelang',
                'Kab. Pati', 'Kab. Pekalongan', 'Kota Pekalongan', 'Kab. Pemalang', 'Kab. Purbalingga',
                'Kab. Purworejo', 'Kab. Rembang', 'Kota Salatiga', 'Kab. Sragen', 'Kab. Sukoharjo',
                'Kab. Tegal', 'Kota Tegal', 'Kab. Temanggung', 'Kab. Wonogiri', 'Kab. Wonosobo',
            ],
            'DI Yogyakarta' => [
                'Kota Yogyakarta', 'Kab. Sleman', 'Kab. Bantul', 'Kab. Kulon Progo', 'Kab. Gunungkidul',
            ],
            'Jawa Barat' => [
                'Kota Bandung', 'Kab. Bandung', 'Kab. Bandung Barat', 'Kota Cimahi', 'Kota Bekasi',
                'Kab. Bekasi', 'Kota Bogor', 'Kab. Bogor', 'Kota Depok', 'Kota Cirebon',
                'Kab. Cirebon', 'Kab. Ciamis', 'Kab. Cianjur', 'Kab. Garut', 'Kab. Indramayu',
                'Kab. Karawang', 'Kab. Kuningan', 'Kab. Majalengka', 'Kab. Pangandaran', 'Kab. Purwakarta',
                'Kab. Subang', 'Kota Sukabumi', 'Kab. Sukabumi', 'Kab. Sumedang', 'Kota Tasikmalaya',
                'Kab. Tasikmalaya', 'Kota Banjar',
            ],
            'DKI Jakarta' => [
                'Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara',
                'Kab. Kepulauan Seribu',
            ],
            'Banten' => [
                'Kota Tangerang', 'Kota Tangerang Selatan', 'Kab. Tangerang', 'Kota Serang', 'Kab. Serang',
                'Kota Cilegon', 'Kab. Lebak', 'Kab. Pandeglang',
            ],
            'Bali' => [
                'Kota Denpasar', 'Kab. Badung', 'Kab. Bangli', 'Kab. Buleleng', 'Kab. Gianyar',
                'Kab. Jembrana', 'Kab. Karangasem', 'Kab. Klungkung', 'Kab. Tabanan',
            ],
            'Aceh' => [
                'Kota Banda Aceh', 'Kota Sabang', 'Kota Lhokseumawe', 'Kota Langsa', 'Kota Subulussalam',
                'Kab. Aceh Besar', 'Kab. Aceh Barat', 'Kab. Aceh Barat Daya', 'Kab. Aceh Jaya', 'Kab. Aceh Selatan',
                'Kab. Aceh Singkil', 'Kab. Aceh Tamiang', 'Kab. Aceh Tengah', 'Kab. Aceh Tenggara', 'Kab. Aceh Timur',
                'Kab. Aceh Utara', 'Kab. Bener Meriah', 'Kab. Bireuen', 'Kab. Gayo Lues', 'Kab. Nagan Raya',
                'Kab. Pidie', 'Kab. Pidie Jaya', 'Kab. Simeulue',
            ],
            'Sumatera Utara' => [
                'Kota Medan', 'Kota Binjai', 'Kota Pematangsiantar', 'Kota Tebing Tinggi', 'Kota Tanjungbalai',
                'Kota Sibolga', 'Kota Padangsidimpuan', 'Kota Gunungsitoli', 'Kab. Deli Serdang', 'Kab. Langkat',
                'Kab. Karo', 'Kab. Simalungun', 'Kab. Asahan', 'Kab. Labuhanbatu', 'Kab. Labuhanbatu Selatan',
                'Kab. Labuhanbatu Utara', 'Kab. Tapanuli Selatan', 'Kab. Tapanuli Tengah', 'Kab. Tapanuli Utara', 'Kab. Toba',
                'Kab. Samosir', 'Kab. Humbang Hasundutan', 'Kab. Pakpak Bharat', 'Kab. Dairi', 'Kab. Batubara',
                'Kab. Serdang Bedagai', 'Kab. Mandailing Natal', 'Kab. Padang Lawas', 'Kab. Padang Lawas Utara', 'Kab. Nias',
                'Kab. Nias Barat', 'Kab. Nias Selatan', 'Kab. Nias Utara',
            ],
            'Sumatera Barat' => [
                'Kota Padang', 'Kota Bukittinggi', 'Kota Payakumbuh', 'Kota Pariaman', 'Kota Padang Panjang',
                'Kota Sawahlunto', 'Kota Solok', 'Kab. Agam', 'Kab. Dharmasraya', 'Kab. Kepulauan Mentawai',
                'Kab. Lima Puluh Kota', 'Kab. Padang Pariaman', 'Kab. Pasaman', 'Kab. Pasaman Barat', 'Kab. Pesisir Selatan',
                'Kab. Sijunjung', 'Kab. Solok', 'Kab. Solok Selatan', 'Kab. Tanah Datar',
            ],
            'Riau' => [
                'Kota Pekanbaru', 'Kota Dumai', 'Kab. Bengkalis', 'Kab. Indragiri Hilir', 'Kab. Indragiri Hulu',
                'Kab. Kampar', 'Kab. Kepulauan Meranti', 'Kab. Kuantan Singingi', 'Kab. Pelalawan', 'Kab. Rokan Hilir',
                'Kab. Rokan Hulu', 'Kab. Siak',
            ],
            'Kepulauan Riau' => [
                'Kota Batam', 'Kota Tanjungpinang', 'Kab. Bintan', 'Kab. Karimun', 'Kab. Kepulauan Anambas',
                'Kab. Lingga', 'Kab. Natuna',
            ],
            'Jambi' => [
                'Kota Jambi', 'Kota Sungai Penuh', 'Kab. Batanghari', 'Kab. Bungo', 'Kab. Kerinci',
                'Kab. Merangin', 'Kab. Muaro Jambi', 'Kab. Sarolangun', 'Kab. Tanjung Jabung Barat', 'Kab. Tanjung Jabung Timur',
                'Kab. Tebo',
            ],
            'Sumatera Selatan' => [
                'Kota Palembang', 'Kota Prabumulih', 'Kota Lubuklinggau', 'Kota Pagar Alam', 'Kab. Banyuasin',
                'Kab. Empat Lawang', 'Kab. Lahat', 'Kab. Muara Enim', 'Kab. Musi Banyuasin', 'Kab. Musi Rawas',
                'Kab. Musi Rawas Utara', 'Kab. Ogan Ilir', 'Kab. Ogan Komering Ilir', 'Kab. Ogan Komering Ulu', 'Kab. Ogan Komering Ulu Selatan',
                'Kab. Ogan Komering Ulu Timur', 'Kab. Penukal Abab Lematang Ilir',
            ],
            'Bangka Belitung' => [
                'Kota Pangkalpinang', 'Kab. Bangka', 'Kab. Bangka Barat', 'Kab. Bangka Selatan', 'Kab. Bangka Tengah',
                'Kab. Belitung', 'Kab. Belitung Timur',
            ],
            'Bengkulu' => [
                'Kota Bengkulu', 'Kab. Bengkulu Selatan', 'Kab. Bengkulu Tengah', 'Kab. Bengkulu Utara', 'Kab. Kaur',
                'Kab. Kepahiang', 'Kab. Lebong', 'Kab. Mukomuko', 'Kab. Rejang Lebong', 'Kab. Seluma',
            ],
            'Lampung' => [
                'Kota Bandar Lampung', 'Kota Metro', 'Kab. Lampung Selatan', 'Kab. Lampung Tengah', 'Kab. Lampung Timur',
                'Kab. Lampung Utara', 'Kab. Lampung Barat', 'Kab. Pesawaran', 'Kab. Pesisir Barat', 'Kab. Pringsewu',
                'Kab. Tanggamus', 'Kab. Tulang Bawang', 'Kab. Tulang Bawang Barat', 'Kab. Way Kanan', 'Kab. Mesuji',
            ],
            'Kalimantan Barat' => [
                'Kota Pontianak', 'Kota Singkawang', 'Kab. Bengkayang', 'Kab. Kapuas Hulu', 'Kab. Kayong Utara',
                'Kab. Ketapang', 'Kab. Kubu Raya', 'Kab. Landak', 'Kab. Melawi', 'Kab. Mempawah',
                'Kab. Sambas', 'Kab. Sanggau', 'Kab. Sekadau', 'Kab. Sintang',
            ],
            'Kalimantan Tengah' => [
                'Kota Palangkaraya', 'Kab. Barito Selatan', 'Kab. Barito Timur', 'Kab. Barito Utara', 'Kab. Gunung Mas',
                'Kab. Kapuas', 'Kab. Katingan', 'Kab. Kotawaringin Barat', 'Kab. Kotawaringin Timur', 'Kab. Lamandau',
                'Kab. Murung Raya', 'Kab. Pulang Pisau', 'Kab. Seruyan', 'Kab. Sukamara',
            ],
            'Kalimantan Selatan' => [
                'Kota Banjarmasin', 'Kota Banjarbaru', 'Kab. Balangan', 'Kab. Banjar', 'Kab. Barito Kuala',
                'Kab. Hulu Sungai Selatan', 'Kab. Hulu Sungai Tengah', 'Kab. Hulu Sungai Utara', 'Kab. Kotabaru', 'Kab. Tabalong',
                'Kab. Tanah Bumbu', 'Kab. Tanah Laut', 'Kab. Tapin',
            ],
            'Kalimantan Timur' => [
                'Kota Balikpapan', 'Kota Samarinda', 'Kota Bontang', 'Kab. Berau', 'Kab. Kutai Barat',
                'Kab. Kutai Kartanegara', 'Kab. Kutai Timur', 'Kab. Mahakam Ulu', 'Kab. Paser', 'Kab. Penajam Paser Utara',
            ],
            'Kalimantan Utara' => [
                'Kota Tarakan', 'Kab. Bulungan', 'Kab. Malinau', 'Kab. Nunukan', 'Kab. Tana Tidung',
            ],
            'Sulawesi Utara' => [
                'Kota Manado', 'Kota Bitung', 'Kota Tomohon', 'Kota Kotamobagu', 'Kab. Bolaang Mongondow',
                'Kab. Bolaang Mongondow Selatan', 'Kab. Bolaang Mongondow Timur', 'Kab. Bolaang Mongondow Utara', 'Kab. Kepulauan Sangihe', 'Kab. Kepulauan Siau Tagulandang Biaro',
                'Kab. Kepulauan Talaud', 'Kab. Minahasa', 'Kab. Minahasa Selatan', 'Kab. Minahasa Tenggara', 'Kab. Minahasa Utara',
            ],
            'Gorontalo' => [
                'Kota Gorontalo', 'Kab. Boalemo', 'Kab. Bone Bolango', 'Kab. Gorontalo', 'Kab. Gorontalo Utara',
                'Kab. Pohuwato',
            ],
            'Sulawesi Tengah' => [
                'Kota Palu', 'Kab. Banggai', 'Kab. Banggai Kepulauan', 'Kab. Banggai Laut', 'Kab. Buol',
                'Kab. Donggala', 'Kab. Morowali', 'Kab. Morowali Utara', 'Kab. Parigi Moutong', 'Kab. Poso',
                'Kab. Sigi', 'Kab. Tojo Una-Una', 'Kab. Tolitoli',
            ],
            'Sulawesi Barat' => [
                'Kab. Mamuju', 'Kab. Majene', 'Kab. Mamasa', 'Kab. Mamuju Tengah', 'Kab. Pasangkayu',
                'Kab. Polewali Mandar',
            ],
            'Sulawesi Selatan' => [
                'Kota Makassar', 'Kota Parepare', 'Kota Palopo', 'Kab. Bantaeng', 'Kab. Barru',
                'Kab. Bone', 'Kab. Bulukumba', 'Kab. Enrekang', 'Kab. Gowa', 'Kab. Jeneponto',
                'Kab. Kepulauan Selayar', 'Kab. Luwu', 'Kab. Luwu Timur', 'Kab. Luwu Utara', 'Kab. Maros',
                'Kab. Pangkajene dan Kepulauan', 'Kab. Pinrang', 'Kab. Sidenreng Rappang', 'Kab. Sinjai', 'Kab. Soppeng',
                'Kab. Takalar', 'Kab. Tana Toraja', 'Kab. Toraja Utara', 'Kab. Wajo',
            ],
            'Sulawesi Tenggara' => [
                'Kota Kendari', 'Kota Baubau', 'Kab. Bombana', 'Kab. Buton', 'Kab. Buton Selatan',
                'Kab. Buton Tengah', 'Kab. Buton Utara', 'Kab. Kolaka', 'Kab. Kolaka Timur', 'Kab. Kolaka Utara',
                'Kab. Konawe', 'Kab. Konawe Kepulauan', 'Kab. Konawe Selatan', 'Kab. Konawe Utara', 'Kab. Muna',
                'Kab. Muna Barat', 'Kab. Wakatobi',
            ],
            'Nusa Tenggara Barat' => [
                'Kota Mataram', 'Kota Bima', 'Kab. Bima', 'Kab. Dompu', 'Kab. Lombok Barat',
                'Kab. Lombok Tengah', 'Kab. Lombok Timur', 'Kab. Lombok Utara', 'Kab. Sumbawa', 'Kab. Sumbawa Barat',
            ],
            'Nusa Tenggara Timur' => [
                'Kota Kupang', 'Kab. Kupang', 'Kab. Alor', 'Kab. Belu', 'Kab. Ende',
                'Kab. Flores Timur', 'Kab. Lembata', 'Kab. Malaka', 'Kab. Manggarai', 'Kab. Manggarai Barat',
                'Kab. Manggarai Timur', 'Kab. Nagekeo', 'Kab. Ngada', 'Kab. Rote Ndao', 'Kab. Sabu Raijua',
                'Kab. Sikka', 'Kab. Sumba Barat', 'Kab. Sumba Barat Daya', 'Kab. Sumba Tengah', 'Kab. Sumba Timur',
                'Kab. Timor Tengah Selatan', 'Kab. Timor Tengah Utara',
            ],
            'Maluku' => [
                'Kota Ambon', 'Kota Tual', 'Kab. Buru', 'Kab. Buru Selatan', 'Kab. Kepulauan Aru',
                'Kab. Kepulauan Tanimbar', 'Kab. Maluku Barat Daya', 'Kab. Maluku Tengah', 'Kab. Maluku Tenggara', 'Kab. Seram Bagian Barat',
                'Kab. Seram Bagian Timur',
            ],
            'Maluku Utara' => [
                'Kota Ternate', 'Kota Tidore Kepulauan', 'Kab. Halmahera Barat', 'Kab. Halmahera Tengah', 'Kab. Halmahera Timur',
                'Kab. Halmahera Selatan', 'Kab. Halmahera Utara', 'Kab. Kepulauan Sula', 'Kab. Pulau Morotai', 'Kab. Pulau Taliabu',
            ],
            'Papua' => [
                'Kota Jayapura', 'Kab. Jayapura', 'Kab. Biak Numfor', 'Kab. Keerom', 'Kab. Kepulauan Yapen',
                'Kab. Mamberamo Raya', 'Kab. Sarmi', 'Kab. Supiori', 'Kab. Waropen',
            ],
            'Papua Barat' => [
                'Kab. Manokwari', 'Kab. Manokwari Selatan', 'Kab. Fakfak', 'Kab. Kaimana', 'Kab. Pegunungan Arfak',
                'Kab. Teluk Bintuni', 'Kab. Teluk Wondama',
            ],
            'Papua Barat Daya' => [
                'Kota Sorong', 'Kab. Sorong', 'Kab. Sorong Selatan', 'Kab. Maybrat', 'Kab. Raja Ampat',
                'Kab. Tambrauw',
            ],
            'Papua Tengah' => [
                'Kab. Nabire', 'Kab. Mimika', 'Kab. Paniai', 'Kab. Deiyai', 'Kab. Dogiyai',
                'Kab. Intan Jaya', 'Kab. Puncak', 'Kab. Puncak Jaya',
            ],
            'Papua Pegunungan' => [
                'Kab. Jayawijaya', 'Kab. Lanny Jaya', 'Kab. Mamberamo Tengah', 'Kab. Nduga', 'Kab. Pegunungan Bintang',
                'Kab. Tolikara', 'Kab. Yahukimo', 'Kab. Yalimo',
            ],
            'Papua Selatan' => [
                'Kab. Merauke', 'Kab. Asmat', 'Kab. Boven Digoel', 'Kab. Mappi',
            ],
        ];
    }

    /**
     * Daftar datar seluruh 514 Kab/Kota beserta Provinsinya untuk pencarian cepat / datalist.
     */
    public static function popularLocations(): array
    {
        $list = [];
        foreach (self::indonesiaRegions() as $province => $cities) {
            foreach ($cities as $city) {
                $list[] = [
                    'city' => $city,
                    'province' => $province,
                ];
            }
        }
        return $list;
    }

    /**
     * Peta Kecamatan resmi berdasarkan Kabupaten / Kota yang dipilih pelanggan.
     */
    public static function districtsByCity(): array
    {
        return [
            // JAWA TIMUR (Lengkap 38 Kab/Kota)
            'Kab. Lamongan' => self::lamonganDistricts(),
            'Kab. Gresik' => [
                'Balongpanggang', 'Benjeng', 'Bungah', 'Cerme', 'Driyorejo', 'Duduksampeyan', 'Dukun', 'Gresik',
                'Kebomas', 'Kedamean', 'Manyar', 'Menganti', 'Panceng', 'Sangkapura', 'Sidayu', 'Tambak', 'Ujungpangkah', 'Wringinanom',
            ],
            'Kota Surabaya' => [
                'Asemrowo', 'Benowo', 'Bubutan', 'Bulak', 'Dukuh Pakis', 'Gayungan', 'Genteng', 'Gubeng',
                'Gunung Anyar', 'Jambangan', 'Karang Pilang', 'Kenjeran', 'Krembangan', 'Lakarsantri', 'Mulyorejo', 'Pabean Cantian',
                'Pakal', 'Rungkut', 'Sambikerep', 'Sawahan', 'Semampir', 'Simokerto', 'Sukolilo', 'Sukomanunggal',
                'Tambaksari', 'Tandes', 'Tegalsari', 'Tenggilis Mejoyo', 'Wiyung', 'Wonocolo', 'Wonokromo',
            ],
            'Kab. Sidoarjo' => [
                'Balongbendo', 'Buduran', 'Candi', 'Gedangan', 'Jabon', 'Krembung', 'Krian', 'Porong',
                'Prambon', 'Sedati', 'Sidoarjo', 'Sukodono', 'Taman', 'Tanggulangin', 'Tarik', 'Tulangan', 'Waru', 'Wonoayu',
            ],
            'Kab. Tuban' => [
                'Bancar', 'Bangilan', 'Grabagan', 'Jatirogo', 'Jenu', 'Kenduruan', 'Kerek', 'Merakurak',
                'Montong', 'Palang', 'Parengan', 'Plumpang', 'Rengel', 'Semanding', 'Senori', 'Singgahan', 'Soko', 'Tambakboyo', 'Tuban', 'Widang',
            ],
            'Kab. Bojonegoro' => [
                'Balen', 'Baureno', 'Bojonegoro', 'Bubulan', 'Dander', 'Gayam', 'Gondang', 'Kalitidu',
                'Kanor', 'Kapas', 'Kasiman', 'Kedewan', 'Kedungadem', 'Kepohbaru', 'Malo', 'Margomulyo',
                'Ngambon', 'Ngasem', 'Ngraho', 'Padangan', 'Purwosari', 'Sekar', 'Sugihwaras', 'Sukosewu', 'Sumberejo', 'Tambakrejo', 'Temayang', 'Trucuk',
            ],
            'Kab. Jombang' => [
                'Bandarkedungmulyo', 'Bareng', 'Diwek', 'Gudo', 'Jogoroto', 'Jombang', 'Kabuh', 'Kesamben',
                'Kudu', 'Megaluh', 'Mojoagung', 'Mojowarno', 'Ngoro', 'Ngusikan', 'Perak', 'Peterongan', 'Plandaan', 'Ploso', 'Sumobito', 'Tembelang', 'Wonosalam',
            ],
            'Kab. Mojokerto' => [
                'Bangsal', 'Dawarblandong', 'Dlanggu', 'Gedeg', 'Gondang', 'Jatirejo', 'Jetis', 'Kemlagi',
                'Kutorejo', 'Mojoanyar', 'Mojosari', 'Ngoro', 'Pacet', 'Pungging', 'Puri', 'Sooko', 'Trawas', 'Trowulan',
            ],
            'Kota Mojokerto' => ['Kranggan', 'Magersari', 'Prajurit Kulon'],
            'Kab. Bangkalan' => [
                'Arosbaya', 'Bangkalan', 'Blega', 'Burneh', 'Galis', 'Geger', 'Kamal', 'Klampis',
                'Kokop', 'Konang', 'Kwanyar', 'Labang', 'Modung', 'Sepulu', 'Socah', 'Tanah Merah', 'Tanjung Bumi', 'Tragah',
            ],
            'Kab. Sampang' => [
                'Banyuates', 'Camplong', 'Jrengik', 'Karangpenang', 'Kedungdung', 'Ketapang', 'Omben',
                'Pangarengan', 'Robatal', 'Sampang', 'Sokobanah', 'Sreseh', 'Tambelangan', 'Torjun',
            ],
            'Kab. Pamekasan' => [
                'Batu Marmar', 'Galis', 'Kadur', 'Larangan', 'Pademawu', 'Pakong', 'Palengaan',
                'Pamekasan', 'Pasean', 'Pegantenan', 'Proppo', 'Tlanakan', 'Waru',
            ],
            'Kab. Sumenep' => [
                'Ambunten', 'Arjasa', 'Batang Batang', 'Batuan', 'Batuputih', 'Bluto', 'Dasuk', 'Dungkek',
                'Ganding', 'Gapura', 'Gayam', 'Giligenting', 'Guluk-Guluk', 'Kalianget', 'Kangayan', 'Kota Sumenep',
                'Lenteng', 'Manding', 'Masalembu', 'Nonggunong', 'Pasongsongan', 'Pragaan', 'Raas', 'Rubaru', 'Sapeken', 'Saronggi', 'Talango',
            ],
            'Kota Malang' => ['Blimbing', 'Kedungkandang', 'Klojen', 'Lowokwaru', 'Sukun'],
            'Kab. Malang' => [
                'Ampelgading', 'Bantur', 'Bululawang', 'Dampit', 'Dau', 'Donomulyo', 'Gedangan', 'Gondanglegi',
                'Jabung', 'Kalipare', 'Karangploso', 'Kasembon', 'Kepanjen', 'Kromengan', 'Lawang', 'Ngajum',
                'Ngantang', 'Pagak', 'Pagelaran', 'Pakis', 'Pakisaji', 'Poncokusumo', 'Pujon', 'Singosari',
                'Sumbermanjing Wetan', 'Sumberpucung', 'Tajinan', 'Tirtoyudo', 'Tumpang', 'Turen', 'Wagir', 'Wajak', 'Wonosari',
            ],
            'Kota Batu' => ['Batu', 'Bumiaji', 'Junrejo'],
            'Kota Kediri' => ['Kota Kediri', 'Mojoroto', 'Pesantren'],
            'Kab. Kediri' => [
                'Badas', 'Banyakan', 'Gampengrejo', 'Grogol', 'Gurah', 'Kandangan', 'Kandat', 'Kayen Kidul',
                'Kepung', 'Kras', 'Kunjang', 'Mojo', 'Ngadiluwih', 'Ngancar', 'Ngasem', 'Pagu',
                'Papar', 'Pare', 'Plemahan', 'Plosoklaten', 'Puncu', 'Purwoasri', 'Ringinrejo', 'Semen', 'Tarokan', 'Wates',
            ],
            'Kab. Nganjuk' => [
                'Bagor', 'Baron', 'Berbek', 'Gondang', 'Jatikalen', 'Kertosono', 'Lengkong', 'Loceret',
                'Nganjuk', 'Ngetos', 'Ngluyu', 'Ngronggot', 'Pace', 'Patianrowo', 'Prambon', 'Rejoso', 'Sawahan', 'Sukomoro', 'Tanjunganom', 'Wilangan',
            ],
            'Kota Blitar' => ['Kepanjenkidul', 'Sananwetan', 'Sukorejo'],
            'Kab. Blitar' => [
                'Bakung', 'Binangun', 'Doko', 'Gandusari', 'Garum', 'Kademangan', 'Kanigoro', 'Kesamben',
                'Nglegok', 'Panggungrejo', 'Ponggok', 'Sanankulon', 'Selopuro', 'Selorejo', 'Srengat', 'Sutojayan', 'Talun', 'Udanawu', 'Wates', 'Wlingi', 'Wonodadi', 'Wonotirto',
            ],
            'Kab. Tulungagung' => [
                'Bandung', 'Besuki', 'Boyolangu', 'Campurdarat', 'Gondang', 'Kalidawir', 'Karangrejo', 'Kauman',
                'Kedungwaru', 'Ngantru', 'Ngunut', 'Pagerwojo', 'Pakel', 'Pucanglaban', 'Rejotangan', 'Sendang', 'Sumbergempol', 'Tanggunggunung', 'Tulungagung',
            ],
            'Kab. Trenggalek' => [
                'Bendungan', 'Dongko', 'Durenan', 'Gandusari', 'Kampak', 'Karangan', 'Munjungan',
                'Panggul', 'Pogalan', 'Pule', 'Suruh', 'Trenggalek', 'Tugu', 'Watulimo',
            ],
            'Kota Madiun' => ['Kartoharjo', 'Manguharjo', 'Taman'],
            'Kab. Madiun' => [
                'Balerejo', 'Dagangan', 'Dolopo', 'Geger', 'Gemarang', 'Jiwan', 'Kare', 'Kebonsari',
                'Madiun', 'Mejayan', 'Pilangkenceng', 'Saradan', 'Sawahan', 'Wonoasri', 'Wungu',
            ],
            'Kab. Magetan' => [
                'Barat', 'Bendo', 'Karangrejo', 'Karas', 'Kartoharjo', 'Kawedanan', 'Lembeyan', 'Magetan',
                'Maospati', 'Ngariboyo', 'Nguntoronadi', 'Panekan', 'Parang', 'Plaosan', 'Poncol', 'Sidorejo', 'Sukomoro', 'Takeran',
            ],
            'Kab. Ngawi' => [
                'Bringin', 'Geneng', 'Gerih', 'Jogorogo', 'Karanganyar', 'Karangjati', 'Kasreman', 'Kedunggalar',
                'Kendal', 'Kwadungan', 'Mantingan', 'Ngawi', 'Ngrambe', 'Padas', 'Pangkur', 'Paron', 'Pitu', 'Sine', 'Widodaren',
            ],
            'Kab. Ponorogo' => [
                'Babadan', 'Badegan', 'Balong', 'Bungkal', 'Jambon', 'Jenangan', 'Jetis', 'Kauman',
                'Mlarak', 'Ngebel', 'Ngrayun', 'Ponorogo', 'Pudak', 'Pulung', 'Sambit', 'Sampung', 'Sawoo', 'Siman', 'Slahung', 'Sooko', 'Sukorejo',
            ],
            'Kab. Pacitan' => [
                'Arjosari', 'Bandar', 'Donorojo', 'Kebonagung', 'Nawangan', 'Ngadirojo', 'Pacitan', 'Pringkuku', 'Punung', 'Sudimoro', 'Tegalombo', 'Tulakan',
            ],
            'Kota Pasuruan' => ['Bugul Kidul', 'Gadingrejo', 'Panggungrejo', 'Purworejo'],
            'Kab. Pasuruan' => [
                'Bangil', 'Beji', 'Gempol', 'Gondangwetan', 'Grati', 'Kejayan', 'Kraton', 'Lekok',
                'Lumbang', 'Nguling', 'Pandaan', 'Pasrepan', 'Pohjentrek', 'Prigen', 'Purwodadi', 'Purwosari', 'Puspo', 'Rejoso', 'Rembang', 'Sukorejo', 'Tosari', 'Tutur', 'Winongan', 'Wonorejo',
            ],
            'Kota Probolinggo' => ['Kademangan', 'Kanigaran', 'Kedopok', 'Mayangan', 'Wonoasih'],
            'Kab. Probolinggo' => [
                'Bantaran', 'Banyuanyar', 'Besuk', 'Dringu', 'Gading', 'Gending', 'Kotaanyar', 'Kraksaan',
                'Krejengan', 'Krucil', 'Kuripan', 'Leces', 'Lumbang', 'Maron', 'Paiton', 'Pajarakan', 'Pakuniran', 'Sukapura', 'Sumber', 'Sumberasih', 'Tegalsiwalan', 'Tiris', 'Tongas', 'Wonomerto',
            ],
            'Kab. Lumajang' => [
                'Candipuro', 'Gucialit', 'Jatiroto', 'Kedungjajang', 'Klakah', 'Kunir', 'Lumajang', 'Padang',
                'Pasirian', 'Pasrujambe', 'Pronojiwo', 'Randuagung', 'Ranuyoso', 'Rowokangkung', 'Senduro', 'Sukodono', 'Sumbersuko', 'Tekung', 'Tempeh', 'Tempursari', 'Yosowilangun',
            ],
            'Kab. Jember' => [
                'Ajung', 'Ambulu', 'Arjasa', 'Balung', 'Bangsalsari', 'Gumukmas', 'Jelbuk', 'Jenggawah',
                'Jombang', 'Kalisat', 'Kaliwates', 'Kencong', 'Ledokombo', 'Mayang', 'Mumbulsari', 'Pakusari',
                'Panti', 'Patrang', 'Puger', 'Rambipuji', 'Semboro', 'Silo', 'Sukorambi', 'Sukowono', 'Sumberbaru', 'Sumberjambe', 'Sumbersari', 'Tanggul', 'Tempurejo', 'Umbulsari', 'Wuluhan',
            ],
            'Kab. Bondowoso' => [
                'Binakal', 'Bondowoso', 'Botolinggo', 'Cermee', 'Curahdami', 'Grujugan', 'Jambesari Darus Sholah',
                'Klabang', 'Maesan', 'Pakem', 'Prajekan', 'Pujer', 'Sempol', 'Sukosari', 'Sumberwringin', 'Taman Krocok', 'Tamanan', 'Tapen', 'Tegalampel', 'Tenggarang', 'Tlogosari', 'Wringin', 'Wonosari',
            ],
            'Kab. Situbondo' => [
                'Arjasa', 'Asembagus', 'Banyuglugur', 'Banyuputih', 'Besuki', 'Bungatan', 'Jangkar', 'Jatibanteng',
                'Kapongan', 'Kendit', 'Mangaran', 'Mlandingan', 'Panarukan', 'Panji', 'Situbondo', 'Suboh', 'Sumbermalang',
            ],
            'Kab. Banyuwangi' => [
                'Bangorejo', 'Banyuwangi', 'Blimbingsari', 'Cluring', 'Gambiran', 'Genteng', 'Giri', 'Glagah',
                'Glenmore', 'Kabat', 'Kalibaru', 'Kalipuro', 'Licin', 'Muncar', 'Pesanggaran', 'Purwoharjo',
                'Rogojampi', 'Sempu', 'Siliragung', 'Singojuruh', 'Songgon', 'Srono', 'Tegaldlimo', 'Tegalsari', 'Wongsorejo',
            ],

            // DKI JAKARTA, JAWA BARAT, BANTEN, JAWA TENGAH, DIY, BALI & KOTA UTAMA INDONESIA
            'Jakarta Selatan' => ['Cilandak', 'Jagakarsa', 'Kebayoran Baru', 'Kebayoran Lama', 'Mampang Prapatan', 'Pancoran', 'Pasar Minggu', 'Pesanggrahan', 'Setiabudi', 'Tebet'],
            'Jakarta Pusat' => ['Cempaka Putih', 'Gambir', 'Johar Baru', 'Kemayoran', 'Menteng', 'Sawah Besar', 'Senen', 'Tanah Abang'],
            'Jakarta Barat' => ['Cengkareng', 'Grogol Petamburan', 'Kalideres', 'Kebon Jeruk', 'Kembangan', 'Palmerah', 'Taman Sari', 'Tambora'],
            'Jakarta Timur' => ['Cakung', 'Cipayung', 'Ciracas', 'Duren Sawit', 'Jatinegara', 'Kramat Jati', 'Makasar', 'Matraman', 'Pasar Rebo', 'Pulo Gadung'],
            'Jakarta Utara' => ['Cilincing', 'Kelapa Gading', 'Koja', 'Pademangan', 'Penjaringan', 'Tanjung Priok'],
            'Kota Bandung' => ['Andir', 'Antapani', 'Arcamanik', 'Astanaanyar', 'Babakan Ciparay', 'Bandung Kidul', 'Bandung Kulon', 'Bandung Wetan', 'Batununggal', 'Bojongloa Kaler', 'Bojongloa Kidul', 'Buahbatu', 'Cibeunying Kaler', 'Cibeunying Kidul', 'Cibiru', 'Cicendo', 'Cidadap', 'Cinambo', 'Coblong', 'Gedebage', 'Kiaracondong', 'Lengkong', 'Mandalajati', 'Panyileukan', 'Rancasari', 'Regol', 'Sukajadi', 'Sukasari', 'Sumur Bandung', 'Ujungberung'],
            'Kota Bekasi' => ['Bantar Gebang', 'Bekasi Barat', 'Bekasi Selatan', 'Bekasi Timur', 'Bekasi Utara', 'Jatiasih', 'Jatisampurna', 'Medan Satria', 'Mustika Jaya', 'Pondok Gede', 'Pondok Melati', 'Rawalumbu'],
            'Kota Depok' => ['Beji', 'Bojongsari', 'Cilodong', 'Cimanggis', 'Cinere', 'Cipayung', 'Limo', 'Pancoran Mas', 'Sawangan', 'Sukmajaya', 'Tapos'],
            'Kota Bogor' => ['Bogor Barat', 'Bogor Selatan', 'Bogor Tengah', 'Bogor Timur', 'Bogor Utara', 'Tanah Sareal'],
            'Kota Tangerang' => ['Batuceper', 'Benda', 'Cibodas', 'Ciledug', 'Cipondoh', 'Jatiuwung', 'Karangtengah', 'Karawaci', 'Larangan', 'Neglasari', 'Periuk', 'Pinang', 'Tangerang'],
            'Kota Tangerang Selatan' => ['Ciputat', 'Ciputat Timur', 'Pamulang', 'Pondok Aren', 'Serpong', 'Serpong Utara', 'Setu'],
            'Kota Semarang' => ['Banyumanik', 'Candisari', 'Gajahmungkur', 'Gayamsari', 'Genuk', 'Gunungpati', 'Mijen', 'Ngaliyan', 'Pedurungan', 'Semarang Barat', 'Semarang Selatan', 'Semarang Tengah', 'Semarang Timur', 'Semarang Utara', 'Tembalang', 'Tugu'],
            'Kota Surakarta' => ['Banjarsari', 'Jebres', 'Laweyan', 'Pasar Kliwon', 'Serengan'],
            'Kota Yogyakarta' => ['Danurejan', 'Gedongtengen', 'Gondokusuman', 'Gondomanan', 'Jetis', 'Kotagede', 'Kraton', 'Mantrijeron', 'Mergangsan', 'Ngampilan', 'Pakualaman', 'Tegalrejo', 'Umbulharjo', 'Wirobrajan'],
            'Kab. Sleman' => ['Berbah', 'Cangkringan', 'Depok', 'Gamping', 'Godean', 'Kalasan', 'Minggir', 'Mlati', 'Moyudan', 'Ngaglik', 'Ngemplak', 'Pakem', 'Prambanan', 'Seyegan', 'Sleman', 'Tempel', 'Turi'],
            'Kab. Bantul' => ['Bambanglipuro', 'Banguntapan', 'Bantul', 'Dlingo', 'Imogiri', 'Jetis', 'Kasihan', 'Kretek', 'Pajangan', 'Pandak', 'Piyungan', 'Pleret', 'Pundong', 'Sanden', 'Sedayu', 'Sewon', 'Srandakan'],
            'Kota Denpasar' => ['Denpasar Barat', 'Denpasar Selatan', 'Denpasar Timur', 'Denpasar Utara'],
            'Kab. Badung' => ['Abiansemal', 'Kuta', 'Kuta Selatan', 'Kuta Utara', 'Mengwi', 'Petang'],
            'Kota Medan' => ['Medan Amplas', 'Medan Area', 'Medan Barat', 'Medan Baru', 'Medan Belawan', 'Medan Deli', 'Medan Denai', 'Medan Helvetia', 'Medan Johor', 'Medan Kota', 'Medan Labuhan', 'Medan Maimun', 'Medan Marelan', 'Medan Perjuangan', 'Medan Petisah', 'Medan Polonia', 'Medan Selayang', 'Medan Sunggal', 'Medan Tembung', 'Medan Timur', 'Medan Tuntungan'],
            'Kota Palembang' => ['Alang-Alang Lebar', 'Bukit Kecil', 'Gandus', 'Ilir Barat I', 'Ilir Barat II', 'Ilir Timur I', 'Ilir Timur II', 'Ilir Timur Tiga', 'Jakabaring', 'Kalidoni', 'Kemuning', 'Kertapati', 'Plaju', 'Sako', 'Seberang Ulu I', 'Seberang Ulu II', 'Sematang Borang', 'Sukarami'],
            'Kota Balikpapan' => ['Balikpapan Barat', 'Balikpapan Kota', 'Balikpapan Selatan', 'Balikpapan Tengah', 'Balikpapan Timur', 'Balikpapan Utara'],
            'Kota Samarinda' => ['Loa Janan Ilir', 'Palaran', 'Samarinda Ilir', 'Samarinda Kota', 'Samarinda Seberang', 'Samarinda Ulu', 'Samarinda Utara', 'Sambutan', 'Sungai Kunjang', 'Sungai Pinang'],
            'Kota Banjarmasin' => ['Banjarmasin Barat', 'Banjarmasin Selatan', 'Banjarmasin Tengah', 'Banjarmasin Timur', 'Banjarmasin Utara'],
            'Kota Pontianak' => ['Pontianak Barat', 'Pontianak Kota', 'Pontianak Selatan', 'Pontianak Tenggara', 'Pontianak Timur', 'Pontianak Utara'],
            'Kota Makassar' => ['Biringkanaya', 'Bontoala', 'Kepulauan Sangkarrang', 'Makassar', 'Mamajang', 'Manggala', 'Mariso', 'Panakkukang', 'Rappocini', 'Tallo', 'Tamalanrea', 'Tamalate', 'Ujung Pandang', 'Ujung Tanah', 'Wajo'],
            'Kota Manado' => ['Bunaken', 'Bunaken Kepulauan', 'Malalayang', 'Mapanget', 'Paal Dua', 'Sario', 'Singkil', 'Tikala', 'Tuminting', 'Wanea', 'Wenang'],
            'Kota Mataram' => ['Ampenan', 'Cakranegara', 'Mataram', 'Sandubaya', 'Sekarbela', 'Selaparang'],
            'Kota Kupang' => ['Alak', 'Kelapa Lima', 'Kota Lama', 'Kota Raja', 'Maulafa', 'Oebobo'],
            'Kota Ambon' => ['Baguala', 'Leitimur Selatan', 'Nusaniwe', 'Sirimau', 'Teluk Ambon'],
            'Kota Jayapura' => ['Abepura', 'Heram', 'Jayapura Selatan', 'Jayapura Utara', 'Muara Tami'],
        ];
    }
}
