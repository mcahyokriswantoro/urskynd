@php
    use App\Support\ShippingRateService;

    $subtotal = (int) $cartItems->sum(fn ($i) => (int) ($i->product->price ?? 129000) * $i->quantity);
    $totalWeight = (int) $cartItems->sum(fn ($i) => (int) ($i->product->weight ?: 200) * $i->quantity);
    $totalKg = max(1, (int) ceil(max(1, $totalWeight) / 1000));

    $zonesPayload = $shippingZones->map(fn ($z) => [
        'id' => $z->id,
        'name' => $z->name,
        'courier' => $z->courier,
        'service' => $z->service,
        'etd' => $z->etd,
        'cost_first_kg' => (int) $z->cost_first_kg,
        'cost_next_kg' => (int) $z->cost_next_kg,
        'free_shipping_min' => $z->free_shipping_min ? (int) $z->free_shipping_min : null,
        'cities' => $z->city_list,
        'provinces' => $z->province_list,
        'is_default' => (bool) $z->is_default,
    ])->values();

    $defaultZone = $shippingZones->first();
@endphp

<x-app-layout>
    <div class="space-y-6" x-data="{
        subtotal: @js($subtotal),
        totalWeight: @js($totalWeight),
        totalKg: @js($totalKg),
        zones: @js($zonesPayload),
        selectedZoneId: @js(old('shipping_zone_id', $defaultZone?->id)),
        province: @js(old('province', 'Jawa Timur')),
        city: @js(old('city', 'Kab. Lamongan')),
        paymentChannel: @js(old('payment_channel', 'QRIS Otomatis (All Bank & E-Wallet)')),

        get activeZone() {
            return this.zones.find(z => Number(z.id) === Number(this.selectedZoneId)) || this.zones[0] || null;
        },

        get rawShippingCost() {
            const z = this.activeZone;
            if (!z) return 0;
            return z.cost_first_kg + Math.max(0, this.totalKg - 1) * z.cost_next_kg;
        },

        get isFreeShipping() {
            const z = this.activeZone;
            if (!z || !z.free_shipping_min) return false;
            return this.subtotal >= z.free_shipping_min;
        },

        get shippingCost() {
            return this.isFreeShipping ? 0 : this.rawShippingCost;
        },

        get grandTotal() {
            return this.subtotal + this.shippingCost;
        },

        formatRp(n) {
            return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
        },

        autoDetectZone() {
            const c = (this.city || '').toLowerCase().trim();
            const p = (this.province || '').toLowerCase().trim();

            if (c) {
                for (const z of this.zones) {
                    for (const mc of (z.cities || [])) {
                        if (mc && (c.includes(mc) || mc.includes(c))) {
                            this.selectedZoneId = z.id;
                            return;
                        }
                    }
                }
            }

            if (p) {
                for (const z of this.zones) {
                    for (const mp of (z.provinces || [])) {
                        if (mp && (p.includes(mp) || mp.includes(p))) {
                            this.selectedZoneId = z.id;
                            return;
                        }
                    }
                }
            }

            const def = this.zones.find(z => z.is_default) || this.zones[this.zones.length - 1];
            if (def) this.selectedZoneId = def.id;
        },

        pickQuickLocation(loc) {
            this.city = loc.city;
            this.province = loc.province;
            this.autoDetectZone();
        }
    }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-1.5">
                    <span>🛒</span> URSKYND Official E-Commerce Checkout
                </div>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">Keranjang Belanja &amp; Pengiriman J&amp;T</h1>
                <p class="text-xs sm:text-sm text-brand-textAlt">
                    Pengiriman langsung dari <strong>{{ $gatewaySettings['origin_city'] ?? 'Kab. Lamongan, Jawa Timur' }}</strong> via J&amp;T Express + Pembayaran Otomatis.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('orders.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-brand-border text-brand-text text-xs font-bold hover:bg-brand-cream transition-colors">
                    📦 Lihat Pesanan Saya
                </a>
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt transition-colors">
                    + Tambah Produk Lain
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Mohon lengkapi data berikut:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($cartItems->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-brand-border shadow-soft max-w-xl mx-auto my-8">
                <div class="w-16 h-16 rounded-2xl bg-brand-cream text-brand-primary flex items-center justify-center text-3xl mx-auto mb-4">
                    🛒
                </div>
                <h2 class="text-xl font-serif font-bold text-brand-text mb-2">Keranjang Belanja Anda Masih Kosong</h2>
                <p class="text-xs sm:text-sm text-brand-textAlt mb-6">
                    Pilih produk skincare URSKYND favorit Anda, masukkan ke keranjang, dan nikmati pengiriman cepat J&amp;T Express dari Lamongan.
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-brand-primary hover:bg-brand-primaryAlt text-white text-xs sm:text-sm font-bold shadow-soft">
                    <span>🧴 Jelajahi Katalog Produk</span>
                    <span>&rarr;</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- KOLOM KIRI: DAFTAR ITEM KERANJANG & FORM ALAMAT + ONGKIR J&T -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- 1. Daftar Produk di Keranjang -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-brand-border shadow-soft">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-brand-border/60">
                            <h2 class="text-base sm:text-lg font-serif font-bold text-brand-text">
                                1. Produk yang Dibeli ({{ $cartItems->sum('quantity') }} pcs • {{ number_format($totalWeight, 0, ',', '.') }} gr)
                            </h2>
                            <span class="text-xs font-bold text-brand-primary">{{ $totalKg }} kg terhitung</span>
                        </div>

                        <div class="divide-y divide-brand-border/50">
                            @foreach($cartItems as $item)
                                @php
                                    $prod = $item->product;
                                    $unitPrice = (int) ($prod->price ?? 129000);
                                @endphp
                                <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <a href="{{ route('products.show', $prod) }}" class="w-16 h-16 rounded-2xl bg-brand-cream border border-brand-border overflow-hidden shrink-0">
                                            <img src="{{ $prod->image_url ?? asset('images/brand-assets.jpg') }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                        </a>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-brand-primary">{{ $prod->category->name ?? 'Skincare' }}</p>
                                            <a href="{{ route('products.show', $prod) }}" class="text-sm font-bold text-brand-text hover:text-brand-primary truncate block">
                                                {{ $prod->name }}
                                            </a>
                                            <p class="text-xs text-brand-textAlt mt-0.5">
                                                Rp {{ number_format($unitPrice, 0, ',', '.') }} / pcs • {{ $prod->weight ?: 200 }} gr
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                        <!-- Update Quantity Form -->
                                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-1 bg-brand-cream/60 p-1 rounded-xl border border-brand-border">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="w-7 h-7 rounded-lg bg-white text-brand-text font-bold text-xs hover:bg-brand-primary hover:text-white transition-colors cursor-pointer">-</button>
                                            <span class="w-8 text-center text-xs font-extrabold text-brand-text">{{ $item->quantity }}</span>
                                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="w-7 h-7 rounded-lg bg-white text-brand-text font-bold text-xs hover:bg-brand-primary hover:text-white transition-colors cursor-pointer">+</button>
                                        </form>

                                        <div class="text-right min-w-[95px]">
                                            <p class="text-sm font-extrabold text-brand-primary">
                                                Rp {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus dari keranjang" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition-colors cursor-pointer">
                                                ✕
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Formulir Alamat Pengiriman & Kalkulator Ongkir J&T Lamongan -->
                    <form id="ecommerce-checkout-form" action="{{ route('checkout.store') }}" method="POST" class="bg-white rounded-3xl p-5 sm:p-6 border border-brand-border shadow-soft space-y-5">
                        @csrf

                        <div class="pb-3 border-b border-brand-border/60 flex items-center justify-between">
                            <div>
                                <h2 class="text-base sm:text-lg font-serif font-bold text-brand-text">📍 2. Alamat Pengiriman &amp; Ongkir J&amp;T Express</h2>
                                <p class="text-xs text-brand-textAlt">Pilih kota tujuan untuk menghitung tarif ongkir J&amp;T otomatis dari Lamongan.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-[11px] font-extrabold">
                                🚚 J&amp;T Express
                            </span>
                        </div>

                        <!-- Pintasan Kota Populer (Sesama Lamongan, Gresik, Surabaya, Malang, Jakarta, Luar Jawa) -->
                        <div>
                            <label class="block text-[11px] font-bold text-brand-textAlt uppercase tracking-wider mb-2">
                                ⚡ Pilih Cepat Area Pengiriman (Otomatis Sesuaikan Zona Ongkir J&amp;T):
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach([
                                    ['city' => 'Kab. Lamongan', 'province' => 'Jawa Timur', 'label' => '📍 Sesama Lamongan (Rp 8.000)'],
                                    ['city' => 'Kec. Babat, Lamongan', 'province' => 'Jawa Timur', 'label' => '📍 Babat / Paciran Lamongan'],
                                    ['city' => 'Kab. Gresik', 'province' => 'Jawa Timur', 'label' => 'Gresik (Rp 10.000)'],
                                    ['city' => 'Kota Surabaya', 'province' => 'Jawa Timur', 'label' => 'Surabaya (Rp 10.000)'],
                                    ['city' => 'Kab. Tuban', 'province' => 'Jawa Timur', 'label' => 'Tuban / Bojonegoro'],
                                    ['city' => 'Kota Malang', 'province' => 'Jawa Timur', 'label' => 'Malang / Kediri (Jatim)'],
                                    ['city' => 'Jakarta Selatan', 'province' => 'DKI Jakarta', 'label' => 'Jakarta / Bandung / Jateng'],
                                    ['city' => 'Kota Balikpapan', 'province' => 'Kalimantan Timur', 'label' => 'Luar Jawa'],
                                ] as $preset)
                                    <button type="button"
                                            @click="pickQuickLocation(@js($preset))"
                                            :class="city === @js($preset['city']) ? 'bg-brand-primary text-white border-brand-primary' : 'bg-brand-cream/60 text-brand-text border-brand-border hover:border-brand-primary'"
                                            class="px-3 py-1.5 rounded-xl border text-[11px] font-semibold transition-all cursor-pointer">
                                        {{ $preset['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">Nama Penerima Paket *</label>
                                <input type="text" name="recipient_name" value="{{ old('recipient_name', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">No. HP / WhatsApp Penerima *</label>
                                <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $user->phone) }}" required placeholder="0812xxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">Kota / Kabupaten Tujuan *</label>
                                <input type="text"
                                       name="city"
                                       list="popular-cities-list"
                                       x-model="city"
                                       @input="autoDetectZone()"
                                       required
                                       placeholder="Contoh: Kab. Lamongan, Surabaya..."
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white font-semibold">
                                <datalist id="popular-cities-list">
                                    @foreach($popularLocations as $loc)
                                        <option value="{{ $loc['city'] }}">{{ $loc['province'] }} — {{ $loc['hint'] }}</option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">Provinsi *</label>
                                <input type="text"
                                       name="province"
                                       x-model="province"
                                       @input="autoDetectZone()"
                                       required
                                       placeholder="Contoh: Jawa Timur"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">Kecamatan</label>
                                <input type="text" name="district" value="{{ old('district') }}" placeholder="Contoh: Kec. Lamongan / Sukodadi / Babat" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="Contoh: 62211" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase mb-1">Alamat Lengkap (Jalan, RT/RW, Patokan Rumah) *</label>
                            <textarea name="address" rows="2" required placeholder="Contoh: Jl. Veteran No. 15, RT 02 RW 01, Dekat Alun-Alun Lamongan..." class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs sm:text-sm bg-white">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <!-- Pilihan Zona Tarif J&T Express -->
                        <div class="p-4 rounded-2xl bg-brand-cream/50 border border-brand-border space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider">
                                    🚚 Zona Tarif Ongkir J&amp;T Express (Dari Gudang Lamongan) *
                                </label>
                                <span class="text-[11px] font-bold text-emerald-700" x-show="isFreeShipping">🎉 Gratis Ongkir Aktif!</span>
                            </div>

                            <div class="space-y-2">
                                @foreach($shippingZones as $z)
                                    @php
                                        $calc = ShippingRateService::calculateByZone($z, $totalWeight, $subtotal);
                                    @endphp
                                    <label class="flex items-center justify-between p-3 rounded-xl border bg-white cursor-pointer transition-all"
                                           :class="Number(selectedZoneId) === {{ $z->id }} ? 'border-brand-primary ring-2 ring-brand-primary/20' : 'border-brand-border hover:border-brand-primary/40'">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" name="shipping_zone_id" value="{{ $z->id }}" x-model="selectedZoneId" class="text-brand-primary focus:ring-brand-primary">
                                            <div>
                                                <p class="text-xs font-bold text-brand-text">
                                                    {{ $z->name }}
                                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-brand-cream text-brand-primary ml-1">{{ $z->courier }} • {{ $z->service }}</span>
                                                </p>
                                                <p class="text-[11px] text-brand-textAlt">
                                                    Estimasi tiba: {{ $z->etd }} • Tarif: Rp {{ number_format($z->cost_first_kg, 0, ',', '.') }}/kg pertama
                                                    @if($z->free_shipping_min)
                                                        <span class="text-emerald-700 font-semibold">(Gratis Ongkir min. belanja Rp {{ number_format($z->free_shipping_min, 0, ',', '.') }})</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 ml-2">
                                            @if($calc['is_free'])
                                                <span class="text-xs line-through text-brand-textAlt block">Rp {{ number_format($calc['raw_cost'], 0, ',', '.') }}</span>
                                                <span class="text-xs font-extrabold text-emerald-700">GRATIS</span>
                                            @else
                                                <span class="text-xs font-extrabold text-brand-text">Rp {{ number_format($calc['cost'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pilihan Metode Pembayaran Otomatis (Payment Gateway) -->
                        <div class="space-y-2.5">
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider">
                                💳 3. Pilih Metode Pembayaran Otomatis (Payment Gateway) *
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach([
                                    ['id' => 'QRIS Otomatis (All Bank & E-Wallet)', 'icon' => '📲', 'desc' => 'Verifikasi otomatis instan 24 jam'],
                                    ['id' => 'BCA Virtual Account', 'icon' => '🏦', 'desc' => 'Cek otomatis m-BCA / KlikBCA'],
                                    ['id' => 'Mandiri / BRI / BNI Virtual Account', 'icon' => '💳', 'desc' => 'Livin, BRImo, BNI Mobile'],
                                    ['id' => 'ShopeePay / GoPay / DANA / OVO', 'icon' => '⚡', 'desc' => 'Potong saldo E-Wallet otomatis'],
                                ] as $pm)
                                    <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all"
                                           :class="paymentChannel === @js($pm['id']) ? 'border-brand-primary bg-brand-primary/5 font-bold' : 'border-brand-border bg-white'">
                                        <input type="radio" name="payment_channel" value="{{ $pm['id'] }}" x-model="paymentChannel" class="mt-0.5 text-brand-primary focus:ring-brand-primary">
                                        <div class="text-xs">
                                            <p class="font-bold text-brand-text">{{ $pm['icon'] }} {{ $pm['id'] }}</p>
                                            <p class="text-[11px] font-normal text-brand-textAlt">{{ $pm['desc'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase mb-1">Catatan untuk Penjual / Kurir J&amp;T (Opsional)</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Tolong packing bubble wrap tebal ya kak..." class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs bg-white">
                        </div>
                    </form>
                </div>

                <!-- KOLOM KANAN: RINGKASAN BELANJA & TOMBOL BUAT PESANAN (STICKY) -->
                <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-4">
                    <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft-lg space-y-4">
                        <div class="pb-3 border-b border-brand-border/60">
                            <h3 class="text-lg font-serif font-bold text-brand-text">🧾 Ringkasan Pembayaran</h3>
                            <p class="text-xs text-brand-textAlt">Status lunas otomatis &amp; resi J&amp;T dapat dilacak di menu Pesanan Saya.</p>
                        </div>

                        @if($detectedAffiliate)
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                                <span>🤝 Mitra Referensi Aktif: <strong>{{ $detectedAffiliate->name }}</strong></span>
                                <span class="font-mono font-bold px-2 py-0.5 rounded bg-emerald-100">{{ $detectedAffiliate->affiliate_code }}</span>
                            </div>
                        @endif

                        <div class="space-y-2.5 text-xs sm:text-sm">
                            <div class="flex justify-between text-brand-textAlt">
                                <span>Subtotal Produk ({{ $cartItems->sum('quantity') }} item)</span>
                                <span class="font-bold text-brand-text" x-text="formatRp(subtotal)"></span>
                            </div>

                            <div class="flex justify-between text-brand-textAlt">
                                <span>Total Berat Paket</span>
                                <span class="font-semibold text-brand-text">{{ number_format($totalWeight, 0, ',', '.') }} gr ({{ $totalKg }} kg)</span>
                            </div>

                            <div class="flex justify-between items-start text-brand-textAlt">
                                <div>
                                    <span>Ongkos Kirim J&amp;T Express</span>
                                    <span class="block text-[11px] text-brand-primary font-semibold" x-text="activeZone ? (activeZone.name + ' • ' + activeZone.etd) : ''"></span>
                                </div>
                                <div class="text-right">
                                    <template x-if="isFreeShipping">
                                        <div>
                                            <span class="text-xs line-through text-brand-textAlt mr-1" x-text="formatRp(rawShippingCost)"></span>
                                            <span class="font-extrabold text-emerald-700">GRATIS</span>
                                        </div>
                                    </template>
                                    <template x-if="!isFreeShipping">
                                        <span class="font-bold text-brand-text" x-text="formatRp(shippingCost)"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-brand-border flex justify-between items-baseline">
                                <span class="text-sm font-bold text-brand-text">Total Tagihan</span>
                                <span class="text-2xl font-extrabold text-brand-primary" x-text="formatRp(grandTotal)"></span>
                            </div>
                        </div>

                        <button type="submit"
                                form="ecommerce-checkout-form"
                                class="w-full py-4 px-6 rounded-2xl bg-brand-primary hover:bg-brand-primaryAlt text-white font-bold text-sm shadow-soft transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span>🔒 Buat Pesanan &amp; Bayar Sekarang</span>
                            <span>&rarr;</span>
                        </button>

                        <div class="grid grid-cols-3 gap-2 pt-2 text-center text-[10px] text-brand-textAlt">
                            <div class="p-2 rounded-xl bg-brand-cream/50 border border-brand-border/60">
                                <span class="block text-sm">⚡</span>
                                <span>Lunas Otomatis</span>
                            </div>
                            <div class="p-2 rounded-xl bg-brand-cream/50 border border-brand-border/60">
                                <span class="block text-sm">🚚</span>
                                <span>J&amp;T Lamongan</span>
                            </div>
                            <div class="p-2 rounded-xl bg-brand-cream/50 border border-brand-border/60">
                                <span class="block text-sm">🛡️</span>
                                <span>100% Original BPOM</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </div>
</x-app-layout>

