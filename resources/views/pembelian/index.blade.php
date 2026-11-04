@php
    use App\Models\PageContent;
    use App\Support\PurchasePageDefaults as PPD;

    $g = $content['general'];
    $pr = $content['problem'];
    $waNumber = $g['whatsapp_number'];
    $waDisplay = $g['whatsapp_display'] ?: $waNumber;
    $paymentMethods = PPD::lines($g['payment_methods']);
    $qrisImg = PageContent::imageUrl($g['qris_image']);
    $qrisMerchant = $g['qris_merchant'] ?? 'URSKYND SKINCARE';
    $qrisNmid = $g['qris_nmid'] ?? 'ID1026569298288';
    $defaultPackage = collect($packages)->firstWhere('popular', true) ?? ($packages[0] ?? null);
    $checkIcon = '<svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
    $waIcon = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>';
@endphp

<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6" x-data="{
        selectedPackage: @js($defaultPackage ? $defaultPackage['name'] . ' (' . $defaultPackage['formatted_price'] . ')' : ''),
        qrisZoomOpen: false,
        hours: 0,
        minutes: 0,
        seconds: 0,
        timerInterval: null,
        updateTimer() {
            const now = new Date();
            const endOfDay = new Date();
            endOfDay.setHours(23, 59, 59, 999);
            const diff = Math.max(0, Math.floor((endOfDay.getTime() - now.getTime()) / 1000));
            this.hours = Math.floor(diff / 3600);
            this.minutes = Math.floor((diff % 3600) / 60);
            this.seconds = diff % 60;
        },
        init() {
            this.updateTimer();
            this.timerInterval = setInterval(() => {
                this.updateTimer();
            }, 1000);
        },
        selectAndScroll(pkgName) {
            this.selectedPackage = pkgName;
            const el = document.getElementById('order-form-section');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }">

        @auth
            @if(auth()->user()->isAdmin())
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('admin.pembelian.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-soft hover:bg-black">
                        ✏️ Edit Halaman Ini (Admin)
                    </a>
                </div>
            @endif
        @endauth

        <!-- Promo Banner Image -->
        @if($bannerImg = PageContent::imageUrl($g['banner_image']))
        <div class="mb-10 max-w-2xl mx-auto rounded-3xl overflow-hidden shadow-soft-lg border border-brand-border bg-white">
            <img src="{{ $bannerImg }}" alt="{{ $g['banner_title'] ?: 'Promo Spesial URSKYND' }}" class="w-full h-auto block">
        </div>
        @endif

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            @if($g['hero_badge'])
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4 border border-brand-primary/20">
                <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                {{ $g['hero_badge'] }}
            </span>
            @endif
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-text mb-4 leading-tight">
                {{ $g['hero_title'] }} <span class="text-brand-primary">{{ $g['hero_highlight'] }}</span> {{ $g['hero_title_suffix'] }}
            </h1>
            <p class="text-brand-textAlt text-sm sm:text-base leading-relaxed mb-6">
                {{ $g['hero_description'] }}
            </p>

            @if($heroImg = PageContent::imageUrl($g['hero_image']))
                <div class="mb-8 rounded-3xl overflow-hidden border border-brand-border shadow-soft-lg">
                    <img src="{{ $heroImg }}" alt="{{ $g['hero_title'] }}" class="w-full max-h-[420px] object-cover">
                </div>
            @endif

            <!-- Quick Trust Pillars -->
            @if(count($content['trust']))
            <div class="grid grid-cols-2 sm:grid-cols-{{ min(4, count($content['trust'])) }} gap-3 max-w-4xl mx-auto text-left">
                @foreach($content['trust'] as $t)
                <div class="p-3 bg-white rounded-xl border border-brand-border/70 shadow-xs flex items-center gap-2.5">
                    <span class="text-xl">{{ $t['icon'] }}</span>
                    <div>
                        <p class="text-xs font-bold text-brand-text">{{ $t['title'] }}</p>
                        <p class="text-[10px] text-brand-textAlt">{{ $t['subtitle'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Problem & Solution Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-14">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-200">
                        Pernahkah Anda Mengalami Ini?
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text mt-3 mb-4">
                        {{ $pr['title'] }}
                    </h2>
                    <p class="text-sm text-brand-textAlt leading-relaxed mb-4">
                        {{ $pr['description'] }}
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-brand-text">
                        @foreach(PPD::lines($pr['points']) as $point)
                        <li class="flex items-start gap-2 text-rose-600 font-medium">
                            <span class="font-bold">✕</span> {{ $point }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-brand-cream/50 p-6 rounded-2xl border border-brand-border">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full border border-brand-primary/20">
                        Solusi Nyata Dari URSKYND
                    </span>
                    <h3 class="text-xl font-bold text-brand-text mt-3 mb-3">
                        {{ $pr['solution_title'] }}
                    </h3>
                    <p class="text-xs sm:text-sm text-brand-textAlt leading-relaxed mb-4">
                        {{ $pr['solution_description'] }}
                    </p>
                    <div class="space-y-2 text-xs sm:text-sm text-brand-text">
                        @foreach(PPD::lines($pr['solution_points']) as $point)
                        <div class="flex items-center gap-2 text-emerald-700 font-medium">
                            {!! $checkIcon !!}
                            <span>{{ $point }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Paket Bundling Showcase -->
        @if(count($packages))
        <div class="mb-14" id="paket-section">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Pilihan Paket Promo Spesial
                </span>
                <h2 class="text-2xl sm:text-4xl font-serif font-bold text-brand-text mt-3 mb-2">
                    {{ $g['packages_title'] }}
                </h2>
                <p class="text-sm text-brand-textAlt">
                    {{ $g['packages_subtitle'] }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ min(3, count($packages)) }} gap-6">
                @foreach($packages as $pkg)
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 transition-all flex flex-col justify-between relative {{ $pkg['popular'] ? 'border-brand-primary shadow-soft-lg ring-4 ring-brand-primary/10' : 'border-brand-border shadow-soft hover:shadow-soft-lg' }}">

                        @if($pkg['popular'])
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-brand-primary to-amber-600 text-white text-[11px] font-extrabold px-4 py-1 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1 whitespace-nowrap z-10">
                                <span>⭐ Paling Populer &amp; Terlaris ⭐</span>
                            </div>
                        @endif

                        <div>
                            @if($pkg['image_url'])
                                <div class="-mx-2 -mt-2 mb-4 h-44 rounded-2xl overflow-hidden bg-brand-cream/40 border border-brand-border/60">
                                    <img src="{{ $pkg['image_url'] }}" alt="{{ $pkg['name'] }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-2 mb-3">
                                @if($pkg['tag'])
                                <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $pkg['popular'] ? 'bg-brand-primary/15 text-brand-primary' : 'bg-brand-cream text-brand-textAlt' }}">
                                    {{ $pkg['tag'] }}
                                </span>
                                @endif
                                @if($pkg['badge'])
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                    {{ $pkg['badge'] }}
                                </span>
                                @endif
                            </div>

                            <h3 class="text-xl font-serif font-bold text-brand-text mb-2">{{ $pkg['name'] }}</h3>

                            <!-- Price Display -->
                            <div class="my-4 p-3 bg-brand-cream/60 rounded-2xl border border-brand-border/60">
                                @if($pkg['formatted_original_price'] && $pkg['original_price'] > $pkg['price'])
                                    <p class="text-xs text-brand-textAlt line-through">{{ $pkg['formatted_original_price'] }}</p>
                                @endif
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-brand-primary">{{ $pkg['formatted_price'] }}</span>
                                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">HEMAT BANGET</span>
                                </div>
                                @if($pkg['free_shipping'])
                                <p class="text-[11px] font-semibold text-brand-textAlt mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    {{ $pkg['free_shipping'] }}
                                </p>
                                @endif
                            </div>

                            <!-- Included Items -->
                            @if(count($pkg['items']))
                            <p class="text-xs font-bold text-brand-text uppercase tracking-wider mb-2">Isi Paket &amp; Bonus:</p>
                            <ul class="space-y-2 text-xs text-brand-textAlt mb-6">
                                @foreach($pkg['items'] as $item)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="{{ str_contains($item, 'Bonus') ? 'font-bold text-brand-primary' : '' }}">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-4 border-t border-brand-border/60">
                            <button type="button"
                                    @click="selectAndScroll(@js($pkg['name'] . ' (' . $pkg['formatted_price'] . ')'))"
                                    class="w-full py-3 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-soft {{ $pkg['popular'] ? 'bg-brand-primary text-white hover:bg-brand-primaryAlt' : 'bg-brand-text text-white hover:bg-black' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span>Pesan Paket Ini (Form Cepat)</span>
                            </button>

                            <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($pkg['wa_message'] ?: 'Halo Admin URSKYND, saya mau pesan ' . $pkg['name'] . ' (' . $pkg['formatted_price'] . ').') }}"
                               target="_blank"
                               rel="noopener"
                               class="w-full py-2.5 px-3 rounded-xl border border-emerald-600 text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                                {!! $waIcon !!}
                                <span>Order via WhatsApp (CS {{ $waDisplay }})</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Lineup Produk Showcase -->
        @if(count($content['products']))
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-14">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Kandungan &amp; Manfaat Produk
                </span>
                <h2 class="text-2xl font-serif font-bold text-brand-text mt-2">{{ $g['products_title'] }}</h2>
                <p class="text-xs text-brand-textAlt">{{ $g['products_subtitle'] }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ min(3, count($content['products'])) }} gap-6">
                @foreach($content['products'] as $prod)
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border flex flex-col justify-between">
                    <div>
                        <div class="h-44 w-full rounded-xl overflow-hidden bg-white mb-4 border border-brand-border flex items-center justify-center">
                            @if($img = PageContent::imageUrl($prod['image'] ?? null))
                                <img src="{{ $img }}" alt="{{ $prod['name'] }}" class="h-full w-full object-cover hover:scale-105 transition-transform duration-300">
                            @else
                                <span class="text-4xl">🧴</span>
                            @endif
                        </div>
                        @if($prod['step'])
                            <span class="text-[10px] font-bold uppercase text-brand-primary bg-brand-primary/10 px-2 py-0.5 rounded">{{ $prod['step'] }}</span>
                        @endif
                        <h4 class="font-bold text-sm text-brand-text mt-1">{{ $prod['name'] }}</h4>
                        <p class="text-xs text-brand-textAlt mt-1">{{ $prod['description'] }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between">
                        <span class="font-bold text-sm text-brand-primary">{{ !empty($prod['price']) ? PPD::rupiah($prod['price']) : '' }}</span>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-text hover:text-brand-primary">Lihat Detail &rarr;</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Form Pemesanan Cepat & Instan -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-brand-primary/30 shadow-soft-lg mb-14" id="order-form-section">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-8">
                    <span class="w-12 h-12 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mx-auto mb-3 text-2xl">
                        📝
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">{{ $g['order_title'] }}</h2>
                    <p class="text-xs sm:text-sm text-brand-textAlt mt-1">
                        {{ $g['order_description'] }} (<strong>{{ $waDisplay }}</strong>)
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                        {!! $checkIcon !!}
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
                        <p class="font-bold mb-1">Mohon lengkapi formulir dengan benar:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pembelian.order') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Pilihan Paket -->
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                            Pilih Paket Pemesanan <span class="text-red-500">*</span>
                        </label>
                        <select name="package_name" x-model="selectedPackage" class="w-full px-4 py-3 rounded-xl border-brand-border bg-brand-cream/30 text-brand-text font-medium text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg['name'] }} ({{ $pkg['formatted_price'] }})">
                                    {{ $pkg['name'] }} — {{ $pkg['formatted_price'] }}{{ $pkg['tag'] ? ' (' . $pkg['tag'] . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nama Lengkap & No WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="Contoh: Rina Melati" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                No. WhatsApp Aktif <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Contoh: 081234567890" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                        </div>
                    </div>

                    <!-- Metode Pembayaran & QRIS -->
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-{{ min(3, max(1, count($paymentMethods))) }} gap-3 mb-4">
                            @foreach($paymentMethods as $idx => $method)
                            <label class="p-3 rounded-xl border border-brand-border bg-brand-cream/30 hover:border-brand-primary cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method') ? old('payment_method') === $method : $idx === 0) class="text-brand-primary focus:ring-brand-primary">
                                <span class="text-xs font-bold text-brand-text">{{ $method }}</span>
                            </label>
                            @endforeach
                        </div>

                        @if($qrisImg)
                        <div class="p-4 sm:p-5 rounded-2xl bg-brand-cream/40 border border-brand-border flex flex-col sm:flex-row items-center gap-5">
                            <button type="button" @click="qrisZoomOpen = true" class="group relative w-44 sm:w-48 bg-white p-2.5 rounded-2xl border border-brand-border shadow-xs shrink-0 overflow-hidden cursor-pointer">
                                <img src="{{ $qrisImg }}" alt="QRIS {{ $qrisMerchant }}" class="w-full h-auto rounded-lg object-contain">
                                <span class="mt-1.5 block text-[10px] font-bold text-brand-primary group-hover:underline">🔍 Klik untuk perbesar QRIS</span>
                            </button>
                            <div class="text-center sm:text-left space-y-2">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                    <span>✅</span> Pembayaran Resmi QRIS Nasional (GPN)
                                </div>
                                <h4 class="text-sm sm:text-base font-extrabold text-brand-text">{{ $qrisMerchant }}</h4>
                                @if($qrisNmid)
                                    <p class="text-xs font-mono text-brand-textAlt">NMID: <strong>{{ $qrisNmid }}</strong></p>
                                @endif
                                <p class="text-xs text-brand-textAlt leading-relaxed">
                                    Kami <strong>tidak melayani COD</strong>. Silakan scan kode QRIS di samping menggunakan aplikasi <strong>M-Banking</strong> (BCA, Mandiri, BRI, BNI, BSI, dll) atau <strong>E-Wallet</strong> (GoPay, OVO, DANA, ShopeePay, LinkAja), lalu lampirkan bukti bayar saat konfirmasi ke WhatsApp Admin.
                                </p>
                                <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                    <button type="button" @click="qrisZoomOpen = true" class="px-3 py-1.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt transition-colors">
                                        Perbesar Kode QRIS
                                    </button>
                                    <a href="{{ $qrisImg }}" download="QRIS-URSKYND-SKINCARE.jpg" class="px-3 py-1.5 rounded-xl border border-brand-border bg-white text-brand-text text-xs font-bold hover:bg-brand-cream transition-colors">
                                        ⬇️ Simpan Gambar QRIS
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                            Alamat Lengkap Pengiriman <span class="text-red-500">*</span>
                        </label>
                        <textarea name="shipping_address" rows="3" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, dan Kode Pos..." class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>{{ old('shipping_address') }}</textarea>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                            Catatan Khusus (Opsional)
                        </label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Titip ke satpam jika saya belum pulang / warna pouch favorit..." class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-bold rounded-2xl hover:from-emerald-700 hover:to-teal-800 transition-all shadow-soft-lg flex items-center justify-center gap-2 text-sm sm:text-base cursor-pointer">
                        {!! str_replace('w-4 h-4', 'w-5 h-5', $waIcon) !!}
                        <span>PESAN SEKARANG VIA WHATSAPP ({{ $waDisplay }})</span>
                    </button>

                    <p class="text-[11px] text-center text-brand-textAlt">
                        🔒 Data pribadi Anda dijamin aman 100%. Kami tidak membagikan informasi Anda kepada pihak lain.
                    </p>
                </form>
            </div>
        </div>

        <!-- Testimoni & Social Proof -->
        @if(count($content['testimonials']))
        <div class="mb-14">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                    Kisah Nyata Pelanggan
                </span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text mt-2">{{ $g['testimonials_title'] }}</h2>
                <p class="text-xs text-brand-textAlt">Ulasan langsung dari pengguna URSKYND di seluruh Indonesia.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-{{ min(3, count($content['testimonials'])) }} gap-6">
                @foreach($content['testimonials'] as $t)
                <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
                            {{ str_repeat('★', (int) ($t['rating'] ?? 5)) }}<span class="text-brand-border">{{ str_repeat('★', 5 - (int) ($t['rating'] ?? 5)) }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-brand-text leading-relaxed italic mb-4">
                            "{{ $t['text'] }}"
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-3 border-t border-brand-border/60">
                        @if($photo = PageContent::imageUrl($t['photo'] ?? null))
                            <img src="{{ $photo }}" alt="{{ $t['name'] }}" class="w-9 h-9 rounded-full object-cover border border-brand-border">
                        @else
                            <div class="w-9 h-9 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-xs">
                                {{ collect(preg_split('/\s+/', preg_replace('/^(dr\.|drg\.)\s*/i', '', $t['name'])))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->join('') }}
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-bold text-brand-text">{{ $t['name'] }}</p>
                            <p class="text-[10px] text-brand-textAlt">{{ $t['location'] ? $t['location'] . ' • ' : '' }}Verified Buyer</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- FAQ Section -->
        @if(count($content['faqs']))
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-16">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Tanya Jawab
                </span>
                <h2 class="text-2xl font-serif font-bold text-brand-text mt-2">{{ $g['faq_title'] }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm">
                @foreach($content['faqs'] as $faq)
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border/60">
                    <p class="font-bold text-brand-text mb-1">Q: {{ $faq['question'] }}</p>
                    <p class="text-brand-textAlt leading-relaxed">
                        A: {{ $faq['answer'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Modal Perbesar QRIS -->
        @if($qrisImg)
        <div x-show="qrisZoomOpen"
             x-transition.opacity
             style="display: none;"
             @keydown.escape.window="qrisZoomOpen = false"
             class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.outside="qrisZoomOpen = false" class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-brand-border text-center">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-brand-border/60">
                    <div class="text-left">
                        <p class="text-xs font-extrabold text-brand-text">{{ $qrisMerchant }}</p>
                        @if($qrisNmid)
                            <p class="text-[11px] font-mono text-brand-textAlt">NMID: {{ $qrisNmid }}</p>
                        @endif
                    </div>
                    <button type="button" @click="qrisZoomOpen = false" class="p-1.5 rounded-xl text-brand-textAlt hover:text-brand-text hover:bg-brand-cream">
                        ✕
                    </button>
                </div>
                <img src="{{ $qrisImg }}" alt="QRIS {{ $qrisMerchant }}" class="w-full max-h-[70vh] object-contain rounded-2xl border border-brand-border/60 mx-auto mb-4">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ $qrisImg }}" download="QRIS-URSKYND-SKINCARE.jpg" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt">
                        ⬇️ Simpan Gambar QRIS
                    </a>
                    <button type="button" @click="qrisZoomOpen = false" class="px-4 py-2.5 rounded-xl border border-brand-border text-xs font-bold text-brand-text hover:bg-brand-cream">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Sticky Floating WhatsApp CTA on Mobile & Desktop -->
        <div class="fixed bottom-4 right-4 z-40 flex flex-col items-end gap-2">
            <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo Admin URSKYND, saya mau konsultasi dan pesan paket produk URSKYND. Mohon infonya ya!') }}"
               target="_blank"
               rel="noopener"
               class="inline-flex items-center gap-2.5 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-soft-lg font-bold text-xs sm:text-sm hover:scale-105 transition-all">
                {!! str_replace('w-4 h-4', 'w-5 h-5', $waIcon) !!}
                <span>Chat Admin / Order WA: {{ $waDisplay }}</span>
            </a>
        </div>

    </div>
</x-dynamic-component>
