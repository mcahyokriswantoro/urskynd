<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6" x-data="{
        selectedPackage: 'Paket Best Seller - Complete Radiant Glow (Rp 299.000)',
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

        <!-- Urgent Flash Sale Banner -->
        <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-brand-primary text-white shadow-soft-lg flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 text-center sm:text-left">
                <span class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl shrink-0 animate-pulse">🔥</span>
                <div>
                    <p class="font-extrabold text-sm sm:text-base tracking-wide uppercase">
                        PROMO SPESIAL HARI INI ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}): GRATIS ONGKIR SE-INDONESIA
                    </p>
                    <p class="text-xs text-white/90">Dapatkan diskon paket hemat s/d 35% + Free Konsultasi Dokter Kulit resmi URSKYND!</p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-black/25 px-4 py-2 rounded-xl backdrop-blur-xs border border-white/20 shrink-0">
                <span class="text-xs font-medium uppercase tracking-wider text-rose-200">Sisa Promo Hari Ini:</span>
                <div class="font-mono font-bold text-sm sm:text-base flex items-center gap-1">
                    <span class="bg-black/40 px-2 py-1 rounded" x-text="String(hours).padStart(2, '0')">00</span>:
                    <span class="bg-black/40 px-2 py-1 rounded" x-text="String(minutes).padStart(2, '0')">00</span>:
                    <span class="bg-black/40 px-2 py-1 rounded" x-text="String(seconds).padStart(2, '0')">00</span>
                </div>
            </div>
        </div>

        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4 border border-brand-primary/20">
                <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                Official Store &bull; Detail Pembelian Produk Asli
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-text mb-4 leading-tight">
                Paket Skincare Klinis <span class="text-brand-primary">URSKYND</span> Asli Bergaransi
            </h1>
            <p class="text-brand-textAlt text-sm sm:text-base leading-relaxed mb-6">
                Rawat kulit wajah kusam, flek hitam, jerawat, dan perbaiki skin barrier dengan produk formulasi dermatologis berteknologi AI. 100% Terdaftar BPOM, Halal, dan didampingi konsultasi dokter spesialis.
            </p>
            
            <!-- Quick Trust Pillars -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-4xl mx-auto text-left">
                <div class="p-3 bg-white rounded-xl border border-brand-border/70 shadow-xs flex items-center gap-2.5">
                    <span class="text-xl">🛡️</span>
                    <div>
                        <p class="text-xs font-bold text-brand-text">100% Original</p>
                        <p class="text-[10px] text-brand-textAlt">Barcode Resmi BPOM</p>
                    </div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-brand-border/70 shadow-xs flex items-center gap-2.5">
                    <span class="text-xl">🚚</span>
                    <div>
                        <p class="text-xs font-bold text-brand-text">Bisa Bayar COD</p>
                        <p class="text-[10px] text-brand-textAlt">Bayar Saat Paket Tiba</p>
                    </div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-brand-border/70 shadow-xs flex items-center gap-2.5">
                    <span class="text-xl">👩‍⚕️</span>
                    <div>
                        <p class="text-xs font-bold text-brand-text">Free Dokter Chat</p>
                        <p class="text-[10px] text-brand-textAlt">Konsultasi Terpandu</p>
                    </div>
                </div>
                <div class="p-3 bg-white rounded-xl border border-brand-border/70 shadow-xs flex items-center gap-2.5">
                    <span class="text-xl">✨</span>
                    <div>
                        <p class="text-xs font-bold text-brand-text">Garansi Tukar</p>
                        <p class="text-[10px] text-brand-textAlt">Jika Rusak / Cacat</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problem & Solution Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-14">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-200">
                        Pernahkah Anda Mengalami Ini?
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text mt-3 mb-4">
                        Sudah Coba Berbagai Skincare Tapi Hasilnya Belum Kelihatan?
                    </h2>
                    <p class="text-sm text-brand-textAlt leading-relaxed mb-4">
                        Seringkali skincare di pasaran tidak memberikan hasil karena kandungannya tidak sesuai tipe kulit atau bahan aktifnya tidak stabil. Hati-hati juga terhadap maraknya skincare non-BPOM tanpa legalitas resmi!
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-brand-text">
                        <li class="flex items-start gap-2 text-rose-600 font-medium">
                            <span class="font-bold">✕</span> Kulit wajah kusam dan belang akibat paparan sinar UV matahari.
                        </li>
                        <li class="flex items-start gap-2 text-rose-600 font-medium">
                            <span class="font-bold">✕</span> Skin barrier rusak, kulit mudah merah, perih, dan mengelupas.
                        </li>
                        <li class="flex items-start gap-2 text-rose-600 font-medium">
                            <span class="font-bold">✕</span> Bekas jerawat PIE/PIH dan noda hitam membandel yang sulit pudar.
                        </li>
                    </ul>
                </div>

                <div class="bg-brand-cream/50 p-6 rounded-2xl border border-brand-border">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full border border-brand-primary/20">
                        Solusi Nyata Dari URSKYND
                    </span>
                    <h3 class="text-xl font-bold text-brand-text mt-3 mb-3">
                        Formulasi Klinis + Teknologi Cerdas AI Skin Companion
                    </h3>
                    <p class="text-xs sm:text-sm text-brand-textAlt leading-relaxed mb-4">
                        URSKYND dirancang khusus untuk kulit tropis Indonesia dengan bahan aktif berkualitas tinggi seperti <em>Pure Niacinamide, Hyaluronic Acid Multi-Molecular, Ceramide Complex</em>, dan UV Filter spektrum luas.
                    </p>
                    <div class="space-y-2 text-xs sm:text-sm text-brand-text">
                        <div class="flex items-center gap-2 text-emerald-700 font-medium">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Tekstur ringan, cepat meresap, dan non-comedogenic (tidak menyumbat pori).</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-700 font-medium">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Dermatologically Tested untuk kulit sensitif sekalipun.</span>
                        </div>
                        <div class="flex items-center gap-2 text-emerald-700 font-medium">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Aman digunakan pria dan wanita usia 15 tahun ke atas.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paket Bundling Showcase -->
        <div class="mb-14" id="paket-section">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Pilihan Paket Promo Spesial
                </span>
                <h2 class="text-2xl sm:text-4xl font-serif font-bold text-brand-text mt-3 mb-2">
                    Pilih Paket Perawatan Terbaik Anda
                </h2>
                <p class="text-sm text-brand-textAlt">
                    Pesan hari ini untuk mendapatkan harga promo bundling, gratis ongkos kirim, dan bonus spesial.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($packages as $pkg)
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 transition-all flex flex-col justify-between relative {{ $pkg['popular'] ? 'border-brand-primary shadow-soft-lg ring-4 ring-brand-primary/10' : 'border-brand-border shadow-soft hover:shadow-soft-lg' }}">
                        
                        @if($pkg['popular'])
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-brand-primary to-amber-600 text-white text-[11px] font-extrabold px-4 py-1 rounded-full shadow-sm uppercase tracking-wider flex items-center gap-1">
                                <span>⭐ Paling Populer &amp; Terlaris ⭐</span>
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $pkg['popular'] ? 'bg-brand-primary/15 text-brand-primary' : 'bg-brand-cream text-brand-textAlt' }}">
                                    {{ $pkg['tag'] }}
                                </span>
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                    {{ $pkg['badge'] }}
                                </span>
                            </div>

                            <h3 class="text-xl font-serif font-bold text-brand-text mb-2">{{ $pkg['name'] }}</h3>

                            <!-- Price Display -->
                            <div class="my-4 p-3 bg-brand-cream/60 rounded-2xl border border-brand-border/60">
                                <p class="text-xs text-brand-textAlt line-through">{{ $pkg['formatted_original_price'] }}</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-brand-primary">{{ $pkg['formatted_price'] }}</span>
                                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">HEMAT BANGET</span>
                                </div>
                                <p class="text-[11px] font-semibold text-brand-textAlt mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    {{ $pkg['free_shipping'] }}
                                </p>
                            </div>

                            <!-- Included Items -->
                            <p class="text-xs font-bold text-brand-text uppercase tracking-wider mb-2">Isi Paket &amp; Bonus:</p>
                            <ul class="space-y-2 text-xs text-brand-textAlt mb-6">
                                @foreach($pkg['items'] as $item)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="{{ str_contains($item, 'Bonus') ? 'font-bold text-brand-primary' : '' }}">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-4 border-t border-brand-border/60">
                            <button type="button" 
                                    @click="selectAndScroll('{{ $pkg['name'] }} ({{ $pkg['formatted_price'] }})')"
                                    class="w-full py-3 px-4 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-soft {{ $pkg['popular'] ? 'bg-brand-primary text-white hover:bg-brand-primaryAlt' : 'bg-brand-text text-white hover:bg-black' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span>Pesan Paket Ini (Form Cepat)</span>
                            </button>

                            <a href="https://wa.me/6285282784356?text={{ rawurlencode($pkg['wa_message']) }}" 
                               target="_blank" 
                               rel="noopener"
                               class="w-full py-2.5 px-3 rounded-xl border border-emerald-600 text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                                <span>Order via WhatsApp (CS 085282784356)</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- Lineup Produk Showcase -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-14">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Kandungan &amp; Manfaat Produk
                </span>
                <h2 class="text-2xl font-serif font-bold text-brand-text mt-2">Katalog Produk Resmi URSKYND</h2>
                <p class="text-xs text-brand-textAlt">Diformulasikan secara sinergis untuk hasil perawatan kulit yang komprehensif.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Product 1 -->
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border flex flex-col justify-between">
                    <div>
                        <div class="h-44 w-full rounded-xl overflow-hidden bg-white mb-4 border border-brand-border flex items-center justify-center">
                            <img src="{{ asset('images/products/lineup.jpeg') }}" alt="URSKYND Face Wash" class="h-full w-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-brand-primary bg-brand-primary/10 px-2 py-0.5 rounded">Langkah 1: Cleanser</span>
                        <h4 class="font-bold text-sm text-brand-text mt-1">URSKYND Foaming Face Wash (100ml)</h4>
                        <p class="text-xs text-brand-textAlt mt-1">Membersihkan debu, sebum, dan polusi hingga ke dalam pori tanpa membuat kulit kesat atau tertarik.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between">
                        <span class="font-bold text-sm text-brand-primary">Rp 98.000</span>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-text hover:text-brand-primary">Lihat Detail &rarr;</a>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border flex flex-col justify-between">
                    <div>
                        <div class="h-44 w-full rounded-xl overflow-hidden bg-white mb-4 border border-brand-border flex items-center justify-center">
                            <img src="{{ asset('images/products/serum.jpeg') }}" alt="URSKYND Face Serum" class="h-full w-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-brand-primary bg-brand-primary/10 px-2 py-0.5 rounded">Langkah 2: Treatment</span>
                        <h4 class="font-bold text-sm text-brand-text mt-1">URSKYND Brightening Face Serum (30ml)</h4>
                        <p class="text-xs text-brand-textAlt mt-1">Serum konsentrat tinggi untuk memudarkan noda hitam, mencerahkan warna kulit kusam, dan merangsang kolagen.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between">
                        <span class="font-bold text-sm text-brand-primary">Rp 189.000</span>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-text hover:text-brand-primary">Lihat Detail &rarr;</a>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border flex flex-col justify-between">
                    <div>
                        <div class="h-44 w-full rounded-xl overflow-hidden bg-white mb-4 border border-brand-border flex items-center justify-center">
                            <img src="{{ asset('images/products/sunscreen.jpeg') }}" alt="URSKYND Sunscreen" class="h-full w-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        <span class="text-[10px] font-bold uppercase text-brand-primary bg-brand-primary/10 px-2 py-0.5 rounded">Langkah 3: Protection</span>
                        <h4 class="font-bold text-sm text-brand-text mt-1">URSKYND UV Sunscreen SPF 50+ PA++++ (50ml)</h4>
                        <p class="text-xs text-brand-textAlt mt-1">Proteksi maksimal dari bahaya UV A/B &amp; blue light. Formula tidak berminyak, no whitecast, dan nyaman sepanjang hari.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between">
                        <span class="font-bold text-sm text-brand-primary">Rp 119.000</span>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-text hover:text-brand-primary">Lihat Detail &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pemesanan Cepat & Instan -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-brand-primary/30 shadow-soft-lg mb-14" id="order-form-section">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-8">
                    <span class="w-12 h-12 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mx-auto mb-3 text-2xl">
                        📝
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">Formulir Pemesanan Cepat</h2>
                    <p class="text-xs sm:text-sm text-brand-textAlt mt-1">
                        Isi form di bawah ini untuk mengonfirmasi order Anda. Pesanan akan otomatis diteruskan ke WhatsApp Admin Resmi URSKYND (<strong>085282784356</strong>) untuk proses pengiriman.
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
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
                                    {{ $pkg['name'] }} — {{ $pkg['formatted_price'] }} ({{ $pkg['tag'] }})
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

                    <!-- Metode Pembayaran -->
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                            Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="p-3 rounded-xl border border-brand-border bg-brand-cream/30 hover:border-brand-primary cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="COD (Bayar di Tempat)" checked class="text-brand-primary focus:ring-brand-primary">
                                <span class="text-xs font-bold text-brand-text">COD (Bayar di Tempat)</span>
                            </label>
                            <label class="p-3 rounded-xl border border-brand-border bg-brand-cream/30 hover:border-brand-primary cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="Transfer Bank (BCA/Mandiri/BRI)" class="text-brand-primary focus:ring-brand-primary">
                                <span class="text-xs font-bold text-brand-text">Transfer Bank</span>
                            </label>
                            <label class="p-3 rounded-xl border border-brand-border bg-brand-cream/30 hover:border-brand-primary cursor-pointer flex items-center gap-2.5">
                                <input type="radio" name="payment_method" value="E-Wallet / QRIS Instan" class="text-brand-primary focus:ring-brand-primary">
                                <span class="text-xs font-bold text-brand-text">QRIS / E-Wallet</span>
                            </label>
                        </div>
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
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                        <span>PESAN SEKARANG VIA WHATSAPP (085282784356)</span>
                    </button>

                    <p class="text-[11px] text-center text-brand-textAlt">
                        🔒 Data pribadi Anda dijamin aman 100%. Kami tidak membagikan informasi Anda kepada pihak lain.
                    </p>
                </form>
            </div>
        </div>

        <!-- Testimoni & Social Proof -->
        <div class="mb-14">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">
                    Kisah Nyata Pelanggan
                </span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text mt-2">Paket Sampai, Ini Kata Mereka</h2>
                <p class="text-xs text-brand-textAlt">Ulasan langsung dari pengguna URSKYND di seluruh Indonesia.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-xs sm:text-sm text-brand-text leading-relaxed italic mb-4">
                            "Awalnya ragu karena sering gonta-ganti skincare jerawat ga sembuh. Pas coba Paket 2 Complete Radiance, seminggu pertama kemerahan mereda banget, skin barrier makin kuat. Pelayanannya lewat WA 085282784356 juga ramah dan fast respon!"
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-3 border-t border-brand-border/60">
                        <div class="w-9 h-9 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-xs">
                            DR
                        </div>
                        <div>
                            <p class="text-xs font-bold text-brand-text">dr. Dinda R.</p>
                            <p class="text-[10px] text-brand-textAlt">Surabaya &bull; Verified Buyer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-xs sm:text-sm text-brand-text leading-relaxed italic mb-4">
                            "Paket tiba cepat cuma 2 hari via J&amp;T COD. Packing rapi, dapat pouch eksklusif dan gratis konsultasi dokter. Serumnya ringan ga bikin minyakan sama sekali. Recommended banget buat usia 30-an ke atas!"
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-3 border-t border-brand-border/60">
                        <div class="w-9 h-9 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-xs">
                            SN
                        </div>
                        <div>
                            <p class="text-xs font-bold text-brand-text">Siti Nurjanah</p>
                            <p class="text-[10px] text-brand-textAlt">Bandung &bull; Verified Buyer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 text-amber-400 mb-3 text-sm">
                            ★★★★★
                        </div>
                        <p class="text-xs sm:text-sm text-brand-text leading-relaxed italic mb-4">
                            "Sunscreen-nya juara, ga whitecast dan ga perih di mata. Saya pesan Paket Ultimate Flawless bareng teman sekantor, dapat gratis ongkir dan bonus konsultasi dokter. Bener-bener puas!"
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-3 border-t border-brand-border/60">
                        <div class="w-9 h-9 rounded-full bg-brand-primary/10 text-brand-primary font-bold flex items-center justify-center text-xs">
                            AW
                        </div>
                        <div>
                            <p class="text-xs font-bold text-brand-text">Anisa Wardani</p>
                            <p class="text-[10px] text-brand-textAlt">Jakarta Selatan &bull; Verified Buyer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft mb-16">
            <div class="text-center max-w-xl mx-auto mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                    Tanya Jawab
                </span>
                <h2 class="text-2xl font-serif font-bold text-brand-text mt-2">Pertanyaan Seputar Pemesanan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm" x-data="{ activeFaq: null }">
                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border/60">
                    <p class="font-bold text-brand-text mb-1">Q: Apakah bisa Bayar di Tempat (COD)?</p>
                    <p class="text-brand-textAlt leading-relaxed">
                        A: Ya, kami melayani sistem pembayaran COD ke hampir seluruh wilayah kecamatan di Indonesia. Anda cukup membayar saat kurir mengantarkan paket ke rumah Anda.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border/60">
                    <p class="font-bold text-brand-text mb-1">Q: Apakah produk URSKYND Asli &amp; Resmi BPOM?</p>
                    <p class="text-brand-textAlt leading-relaxed">
                        A: 100% Asli dan Terdaftar Resmi di BPOM RI. Setiap kemasan dilengkapi barcode dan segel keamanan eksklusif dari pabrik resmi.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border/60">
                    <p class="font-bold text-brand-text mb-1">Q: Berapa lama waktu pengiriman paket?</p>
                    <p class="text-brand-textAlt leading-relaxed">
                        A: Untuk wilayah Pulau Jawa biasanya 1-2 hari kerja. Di luar Pulau Jawa berkisar antara 2-4 hari kerja tergantung kota tujuan dan ekspedisi pilihan.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-brand-cream/30 border border-brand-border/60">
                    <p class="font-bold text-brand-text mb-1">Q: Bagaimana cara klaim konsultasi dokter gratis?</p>
                    <p class="text-brand-textAlt leading-relaxed">
                        A: Setelah pemesanan paket terkonfirmasi via WhatsApp <strong>085282784356</strong>, Customer Service kami akan memberikan voucher akses telekonsultasi dokter gratis di menu Konsultasi website.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sticky Floating WhatsApp CTA on Mobile & Desktop -->
        <div class="fixed bottom-4 right-4 z-40 flex flex-col items-end gap-2">
            <a href="https://wa.me/6285282784356?text={{ rawurlencode('Halo Admin URSKYND, saya mau konsultasi dan pesan paket produk URSKYND. Mohon infonya ya!') }}" 
               target="_blank" 
               rel="noopener"
               class="inline-flex items-center gap-2.5 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full shadow-soft-lg font-bold text-xs sm:text-sm hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                <span>Chat Admin / Order WA: 085282784356</span>
            </a>
        </div>

    </div>
</x-dynamic-component>
