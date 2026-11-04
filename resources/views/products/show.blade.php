<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    @php
        $unitPrice = (int) ($product->price ?? 129000);
        $affShareUrl = auth()->check() && auth()->user()->isAffiliate()
            ? route('products.show', ['product' => $product->slug, 'ref' => auth()->user()->affiliate_code])
            : url()->current();
    @endphp
    <div class="max-w-5xl mx-auto px-4 sm:px-6" x-data="{
        checkoutModalOpen: false,
        copyToast: false,
        affCopyToast: false,
        qty: 1,
        unitPrice: @js($unitPrice),
        formatRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    }">
        
        <div class="mb-6 flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-primaryAlt transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Semua Produk
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-12">
            <!-- Product Gallery / Image -->
            <div>
                <div class="bg-gradient-to-br from-brand-cream/80 to-brand-border/40 rounded-3xl border border-brand-border p-6 flex items-center justify-center aspect-square relative shadow-soft overflow-hidden">
                    @if($product->image)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl shadow-xs">
                    @else
                        <div class="w-full h-full overflow-hidden flex items-center justify-center rounded-2xl bg-brand-cream">
                            <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover" style="object-position: 8% 85%; transform: scale(3.5);">
                        </div>
                    @endif

                    <!-- Official Badge -->
                    <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-md text-brand-primary text-xs font-bold px-3 py-1.5 rounded-full shadow-sm border border-brand-border/40">
                        {{ $product->badge }}
                    </span>
                </div>

                <!-- Lineup thumbnail strip -->
                <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
                    <div class="w-16 h-16 rounded-2xl border-2 border-brand-primary overflow-hidden p-0.5 bg-white shadow-xs shrink-0 cursor-pointer">
                        <img src="{{ $product->image_url ?? asset('images/brand-assets.jpg') }}" alt="Thumbnail 1" class="w-full h-full object-cover rounded-xl">
                    </div>
                    @if(file_exists(public_path('images/products/lineup.jpeg')))
                        <div class="w-16 h-16 rounded-2xl border border-brand-border overflow-hidden p-0.5 bg-white hover:border-brand-primary transition-colors shrink-0 opacity-80 hover:opacity-100 cursor-pointer">
                            <img src="{{ asset('images/products/lineup.jpeg') }}" alt="URSKYND Lineup" class="w-full h-full object-cover rounded-xl">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="flex flex-col justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-1">{{ $product->category->name ?? 'Skincare' }}</p>
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text mb-2 leading-tight">{{ $product->name }}</h1>
                    
                    <!-- Rating and BPOM -->
                    <div class="flex flex-wrap items-center gap-3 text-sm text-brand-textAlt mb-4">
                        <span class="inline-flex items-center gap-1 font-semibold text-brand-text">
                            <span class="text-amber-500">★</span>
                            <span>{{ $product->rating }}</span>
                        </span>
                        <span>•</span>
                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-0.5 text-xs font-bold text-emerald-700">
                            Terdaftar BPOM
                        </span>
                    </div>

                    <!-- Price Tag -->
                    <div class="mb-5">
                        <p class="text-3xl font-bold text-brand-primary">{{ $product->formatted_price }}</p>
                    </div>

                    <!-- Description -->
                    <div class="prose prose-sm text-brand-textAlt mb-5 leading-relaxed">
                        <p>{{ $product->description }}</p>
                    </div>

                    <p class="text-xs text-brand-textAlt mb-6 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span>{{ $product->size ?? 'Berat pengiriman: 250 gram' }}</span>
                    </p>

                    @if($product->usage_instruction)
                        <div class="mb-6 p-4 bg-brand-cream/50 rounded-2xl border border-brand-border/60">
                            <h4 class="font-bold text-xs uppercase tracking-wider text-brand-text mb-1">Cara Pemakaian:</h4>
                            <p class="text-xs text-brand-textAlt leading-relaxed">{{ $product->usage_instruction }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-6 border-t border-brand-border/50 space-y-3">
                    @if(session('success'))
                        <div class="text-xs font-bold text-brand-success bg-brand-success/10 p-3 rounded-xl flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(isset($detectedAffiliate) && $detectedAffiliate)
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
                            <span>🤝 Direkomendasikan oleh Mitra Resmi: <strong>{{ $detectedAffiliate->name }}</strong></span>
                            <span class="font-mono font-bold px-2 py-0.5 rounded bg-emerald-100">{{ $detectedAffiliate->affiliate_code }}</span>
                        </div>
                    @endif

                    @auth
                        @if(auth()->user()->isAffiliate())
                            <!-- Box Khusus Mitra Afiliator untuk Produk Ini -->
                            <div class="p-4 rounded-2xl bg-gradient-to-r from-[#9A6A52] to-[#7C523E] text-white space-y-2.5 shadow-soft">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-white/90">🤝 Link Afiliasi Produk Ini</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-400/25 border border-emerald-300/40 text-emerald-100 text-[11px] font-extrabold">
                                        Komisi +{{ $product->formatted_commission }} / pcs ({{ rtrim(rtrim(number_format($product->commission_rate ?? 10, 2), '0'), '.') }}%)
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ $affShareUrl }}" class="flex-1 px-3 py-2 rounded-xl bg-white text-brand-text font-mono text-[11px] border-0">
                                    <button type="button"
                                            @click="navigator.clipboard.writeText(@js($affShareUrl)); affCopyToast = true; setTimeout(() => affCopyToast = false, 2000);"
                                            class="px-3 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shrink-0 cursor-pointer">
                                        <span x-text="affCopyToast ? '✅ Tersalin!' : '📋 Salin'">📋 Salin</span>
                                    </button>
                                </div>
                                <p class="text-[10px] text-white/80">Setiap pembeli yang klik link produk ini &amp; memesan akan otomatis tercatat ke komisi Anda (seperti Shopee Affiliate).</p>
                            </div>
                        @endif
                    @endauth

                    <!-- Primary Buy / Order Button (Tersedia untuk Tamu & Member) -->
                    <button type="button" @click="checkoutModalOpen = true" class="w-full py-4 px-6 bg-brand-primary text-white font-bold rounded-2xl hover:bg-brand-primaryAlt transition-all shadow-soft flex items-center justify-center gap-2 text-sm cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>Beli Sekarang ({{ $product->formatted_price }})</span>
                    </button>

                    <!-- Share & Routine Actions -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                                @click="
                                    navigator.clipboard.writeText(@js($affShareUrl));
                                    copyToast = true;
                                    setTimeout(() => copyToast = false, 2000);
                                "
                                class="py-3 px-4 rounded-xl border border-brand-border bg-white text-xs font-bold text-brand-text hover:bg-brand-cream transition-colors text-center flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-brand-textAlt" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span x-text="copyToast ? 'Link Disalin!' : 'Salin Link Produk'">Salin Link Produk</span>
                        </button>

                        <a href="https://wa.me/?text={{ rawurlencode('Cek produk resmi ' . $product->name . ' (' . $product->formatted_price . ') di URSKYND: ' . $affShareUrl) }}"
                           target="_blank" rel="noopener"
                           class="py-3 px-4 rounded-xl border border-brand-border bg-white text-xs font-bold text-brand-text hover:bg-brand-cream transition-colors text-center flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                            <span>Bagikan ke WA</span>
                        </a>
                    </div>

                    <!-- Shelf / Routine Option for authenticated users -->
                    @auth
                        @if($isInCollection)
                            <a href="{{ route('routines.index') }}" class="w-full py-3 bg-brand-cream border border-brand-border text-brand-text font-bold rounded-xl hover:bg-brand-border/40 transition-all text-xs flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-brand-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Sudah Ada di Rak Rutinitas Kulit
                            </a>
                        @else
                            <form action="{{ route('products.add-to-collection', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-brand-cream border border-brand-border text-brand-text font-bold rounded-xl hover:bg-brand-border/40 transition-all text-xs flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Tambah ke Rak Rutinitas
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Similar Products -->
        @if($similarProducts->isNotEmpty())
            <div class="border-t border-brand-border/50 pt-12 mt-12 mb-12">
                <h2 class="text-xl font-serif font-bold text-brand-text mb-6">Produk URSKYND Lainnya</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($similarProducts as $similar)
                        <a href="{{ route('products.show', $similar) }}" class="group block bg-white rounded-3xl border border-brand-border overflow-hidden hover:shadow-soft-lg hover:border-brand-primary/40 transition-all">
                            <div class="aspect-square bg-gradient-to-br from-brand-cream/80 to-brand-border/40 p-4 flex items-center justify-center relative">
                                @if($similar->image)
                                    <img src="{{ $similar->image_url }}" alt="{{ $similar->name }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full overflow-hidden flex items-center justify-center rounded-2xl bg-brand-cream">
                                        <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $similar->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="object-position: 8% 85%; transform: scale(3.5);">
                                    </div>
                                @endif
                                <span class="absolute top-3 left-3 bg-white/95 text-brand-primary text-[10px] font-bold px-2.5 py-1 rounded-full shadow-xs border border-brand-border/40">
                                    {{ $similar->badge }}
                                </span>
                            </div>
                            <div class="p-5 border-t border-brand-border/40">
                                <p class="text-[10px] uppercase tracking-wider font-bold text-brand-primary mb-1">{{ $similar->category->name ?? 'Skincare' }}</p>
                                <h3 class="font-bold text-brand-text text-sm mb-1 leading-snug line-clamp-1 group-hover:text-brand-primary transition-colors">{{ $similar->name }}</h3>
                                <p class="text-sm font-bold text-brand-primary">{{ $similar->formatted_price }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Direct Product Purchase Modal (Otomatis Lacak Afiliasi + QRIS) -->
        <div x-show="checkoutModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="checkoutModalOpen = false">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-brand-border max-h-[92vh] overflow-y-auto"
                 @click.outside="checkoutModalOpen = false">
                
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-brand-border/50">
                    <div>
                        <h3 class="font-serif font-bold text-lg text-brand-text">Checkout Pemesanan Resmi</h3>
                        <p class="text-[11px] text-brand-textAlt">Pembayaran via QRIS Resmi URSKYND (Tanpa COD)</p>
                    </div>
                    <button type="button" @click="checkoutModalOpen = false" class="text-brand-textAlt hover:text-brand-text">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex items-center justify-between gap-3 p-3 bg-brand-cream/50 rounded-2xl border border-brand-border/60 mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-white overflow-hidden flex items-center justify-center shrink-0 border border-brand-border">
                            <img src="{{ $product->image_url ?? asset('images/brand-assets.jpg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-brand-text truncate">{{ $product->name }}</p>
                            <p class="text-xs font-bold text-brand-primary" x-text="formatRp(unitPrice * qty)">{{ $product->formatted_price }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="w-7 h-7 rounded-lg bg-white border border-brand-border font-bold text-xs">-</button>
                        <span class="w-7 text-center text-xs font-bold" x-text="qty">1</span>
                        <button type="button" @click="qty++" class="w-7 h-7 rounded-lg bg-white border border-brand-border font-bold text-xs">+</button>
                    </div>
                </div>

                @if(isset($detectedAffiliate) && $detectedAffiliate)
                    <div class="mb-4 px-3 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] flex items-center justify-between">
                        <span>🤝 Mitra Referensi Otomatis: <strong>{{ $detectedAffiliate->name }}</strong></span>
                        <span class="font-mono font-bold">{{ $detectedAffiliate->affiliate_code }}</span>
                    </div>
                @endif

                <form action="{{ route('products.order', $product) }}" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="quantity" :value="qty">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-brand-text uppercase mb-1">Nama Lengkap *</label>
                            <input type="text" name="customer_name" value="{{ auth()->user()->name ?? '' }}" required placeholder="Contoh: Rina Melati" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-brand-text uppercase mb-1">No. WhatsApp Aktif *</label>
                            <input type="text" name="customer_phone" value="{{ auth()->user()->phone ?? '' }}" required placeholder="0812xxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-brand-text uppercase mb-1">Alamat Lengkap Pengiriman *</label>
                        <textarea name="shipping_address" rows="2" required placeholder="Nama jalan, nomor rumah, RT/RW, Kecamatan, Kota, Kode Pos..." class="w-full px-3.5 py-2 rounded-xl border border-brand-border text-xs">{{ auth()->user()->address ?? '' }}</textarea>
                    </div>

                    @if(!empty($qrisImg))
                        <div class="p-3 rounded-2xl bg-brand-cream/40 border border-brand-border flex items-center gap-3">
                            <img src="{{ $qrisImg }}" alt="QRIS {{ $qrisMerchant ?? 'URSKYND' }}" class="w-24 h-auto rounded-lg border border-brand-border bg-white p-1 shrink-0">
                            <div class="text-[11px] space-y-1">
                                <p class="font-extrabold text-brand-text">{{ $qrisMerchant ?? 'URSKYND SKINCARE' }}</p>
                                <p class="font-mono text-[10px] text-brand-textAlt">NMID: {{ $qrisNmid ?? 'ID1026569298288' }}</p>
                                <p class="text-brand-textAlt leading-snug">Scan QRIS ini dengan M-Banking / E-Wallet sebesar <strong class="text-brand-primary" x-text="formatRp(unitPrice * qty)"></strong>.</p>
                                <a href="{{ $qrisImg }}" download="QRIS-URSKYND.jpg" class="inline-block text-brand-primary font-bold hover:underline">⬇️ Unduh Gambar QRIS</a>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-3.5 px-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-soft flex items-center justify-center gap-2 cursor-pointer">
                        <span>💬 Konfirmasi Pesanan via WhatsApp</span>
                    </button>
                </form>

                <div class="mt-4 pt-3 border-t border-brand-border/50 flex items-center justify-between text-xs">
                    <a href="{{ route('pembelian.index') }}" class="font-bold text-brand-primary hover:underline">
                        🛍️ Lihat Paket Bundling Hemat s/d 35% &rarr;
                    </a>
                    <button type="button" @click="checkoutModalOpen = false" class="text-brand-textAlt hover:text-brand-text">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-dynamic-component>
