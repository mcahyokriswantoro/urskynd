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
}
