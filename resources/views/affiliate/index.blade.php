<x-app-layout>
    <div class="space-y-6" x-data="{
        copiedKey: null,
        editBankOpen: false,
        copyLink(url, key = 'main') {
            navigator.clipboard.writeText(url);
            this.copiedKey = key;
            setTimeout(() => {
                if (this.copiedKey === key) this.copiedKey = null;
            }, 2200);
        }
    }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-2">
                    🤝 URSKYND Affiliate Center
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">Mitra &amp; Afiliator Produk</h1>
                <p class="text-xs sm:text-sm text-brand-textAlt mt-0.5">
                    Pilih produk dari katalog, bagikan link afiliasi produk Anda, dan dapatkan komisi otomatis setiap ada pembelian.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Mohon periksa kembali data yang Anda masukkan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$user->isAffiliate())
            <!-- Jika User Belum Terdaftar Sebagai Mitra / Afiliator -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft max-w-2xl mx-auto">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-3xl mx-auto mb-3">
                        🤝
                    </div>
                    <h2 class="text-xl sm:text-2xl font-serif font-bold text-brand-text">Gabung Menjadi Mitra / Afiliator Resmi</h2>
                    <p class="text-xs sm:text-sm text-brand-textAlt mt-1">
                        Aktifkan fitur afiliasi pada akun Anda dan dapatkan komisi otomatis dari setiap produk URSKYND yang terjual melalui link produk Anda.
                    </p>
                </div>

                <form action="{{ route('affiliate.join') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1.5">
                            Kode Referral / Afiliasi Pilihan (Opsional)
                        </label>
                        <input type="text" name="affiliate_code" value="{{ old('affiliate_code') }}" placeholder="Contoh: SARAHGLOW (Kosongkan agar dibuat otomatis)" class="w-full px-4 py-2.5 rounded-xl border border-brand-border bg-brand-cream/30 text-sm uppercase font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1.5">
                            Bank / E-Wallet Pencairan Komisi <span class="text-red-500">*</span>
                        </label>
                        <select name="bank_name" required class="w-full px-4 py-2.5 rounded-xl border border-brand-border bg-white text-sm">
                            <option value="">-- Pilih Bank / E-Wallet --</option>
                            @foreach(['BCA', 'Mandiri', 'BRI', 'BNI', 'BSI (Bank Syariah Indonesia)', 'CIMB Niaga', 'Bank Jago', 'SeaBank', 'DANA', 'OVO', 'GoPay', 'ShopeePay'] as $bank)
                                <option value="{{ $bank }}" @selected(old('bank_name') === $bank)>{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1.5">
                                Nomor Rekening / E-Wallet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" required placeholder="Contoh: 1234567890" class="w-full px-4 py-2.5 rounded-xl border border-brand-border bg-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1.5">
                                Atas Nama Pemilik <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-brand-border bg-white text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-brand-primary hover:bg-brand-primaryAlt text-white font-bold text-sm shadow-soft transition-colors cursor-pointer">
                        🚀 Aktifkan Akun Mitra / Afiliator Sekarang
                    </button>
                </form>
            </div>
        @else
            <!-- DASHBOARD AKTIF MITRA / AFILIATOR (GAYA SHOPEE AFFILIATE) -->
            @php
                $bundleShareUrl = route('pembelian.index', ['ref' => $user->affiliate_code]);
            @endphp

            <!-- 4 Kartu KPI Utama (Saldo, Komisi, Klik & Konversi, Withdraw) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border-2 border-emerald-500/40 shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Saldo Komisi Siap Cair</span>
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">💰</span>
                    </div>
                    <p class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">Bisa langsung ditarik ke rekening</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Total Komisi Terkumpul</span>
                        <span class="w-9 h-9 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-lg">📈</span>
                    </div>
                    <p class="text-2xl font-extrabold text-brand-text">Rp {{ number_format($stats['total_earned'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">
                        {{ $stats['approved_sales_count'] }} pesanan selesai • {{ $stats['pending_sales_count'] }} diproses
                    </p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Performa Klik Link</span>
                        <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">🔗</span>
                    </div>
                    <p class="text-2xl font-extrabold text-brand-text">{{ number_format($stats['total_clicks'], 0, ',', '.') }} <span class="text-xs font-semibold text-brand-textAlt">Klik</span></p>
                    <p class="text-[11px] text-emerald-700 font-semibold mt-1">Rasio Konversi: {{ $stats['conversion_rate'] }}%</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Total Sudah Dicairkan</span>
                        <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">🏦</span>
                    </div>
                    <p class="text-2xl font-extrabold text-brand-text">Rp {{ number_format($stats['total_withdrawn'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">
                        @if($stats['pending_withdrawal'] > 0)
                            Diproses: Rp {{ number_format($stats['pending_withdrawal'], 0, ',', '.') }}
                        @else
                            Tujuan: {{ $user->bank_name ?: 'Rekening Mitra' }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- KATALOG LINK AFILIASI PER PRODUK (SEPERTI SHOPEE AFFILIATE) -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-brand-border shadow-soft">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6 pb-4 border-b border-brand-border/60">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold mb-1.5">
                            <span>⚡</span> Pelacakan Klik &amp; Konversi Otomatis (Cookie 30 Hari)
                        </div>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-brand-text">🧴 Pilih Produk &amp; Salin Link Afiliasi</h2>
                        <p class="text-xs text-brand-textAlt">
                            Setiap produk memiliki link afiliasi khusus dengan kode Anda (<strong class="font-mono text-brand-primary">{{ $user->affiliate_code }}</strong>). Saat pembeli klik link produk dan memesan, komisi langsung masuk otomatis!
                        </p>
                    </div>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-brand-cream hover:bg-brand-primary hover:text-white text-brand-text text-xs font-bold transition-colors shrink-0 self-start md:self-center">
                        Lihat Katalog Lengkap &rarr;
                    </a>
                </div>

                <!-- Grid Produk Afiliasi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($affiliateProducts as $prod)
                        @php
                            $prodKey = 'prod_' . $prod['id'];
                            $waProdText = "Rekomendasi skincare resmi " . $prod['name'] . " (" . $prod['formatted_price'] . ") sudah BPOM & Halal! Cek detail & pesan di sini ya:\n" . $prod['affiliate_url'];
                        @endphp
                        <div class="rounded-2xl border border-brand-border bg-brand-cream/25 p-4 flex flex-col justify-between hover:border-brand-primary/50 hover:shadow-soft transition-all">
                            <div>
                                <!-- Gambar & Badge Komisi -->
                                <div class="relative h-44 w-full rounded-xl overflow-hidden bg-white border border-brand-border mb-3.5 flex items-center justify-center">
                                    @if($prod['image_url'])
                                        <img src="{{ $prod['image_url'] }}" alt="{{ $prod['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl">🧴</span>
                                    @endif
                                    <span class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-lg bg-white/95 backdrop-blur-xs text-[10px] font-bold text-brand-text border border-brand-border/50">
                                        {{ $prod['category'] }}
                                    </span>
                                    <span class="absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-extrabold shadow-xs">
                                        Komisi +{{ $prod['formatted_commission'] }} ({{ rtrim(rtrim(number_format($prod['commission_rate'], 2), '0'), '.') }}%)
                                    </span>
                                </div>

                                <h3 class="font-bold text-sm text-brand-text leading-snug mb-1">{{ $prod['name'] }}</h3>
                                <div class="flex items-baseline justify-between mb-3">
                                    <span class="text-xs text-brand-textAlt">Harga Jual: <strong class="text-brand-text">{{ $prod['formatted_price'] }}</strong></span>
                                    <span class="text-xs font-bold text-emerald-700">+{{ $prod['formatted_commission'] }} / pcs</span>
                                </div>

                                <!-- Statistik Performa Produk Ini -->
                                <div class="grid grid-cols-3 gap-1.5 p-2.5 rounded-xl bg-white border border-brand-border/70 text-center mb-3.5">
                                    <div>
                                        <span class="block text-[10px] text-brand-textAlt">Klik Link</span>
                                        <span class="text-xs font-extrabold text-brand-text">{{ $prod['clicks_count'] }}</span>
                                    </div>
                                    <div class="border-x border-brand-border/50">
                                        <span class="block text-[10px] text-brand-textAlt">Terjual</span>
                                        <span class="text-xs font-extrabold text-brand-primary">{{ $prod['orders_count'] }} pcs</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-brand-textAlt">Komisi Cair</span>
                                        <span class="text-[11px] font-extrabold text-emerald-700">{{ $prod['earned_commission'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Link Produk & Tombol Copy/Share -->
                            <div class="space-y-2 pt-2 border-t border-brand-border/50">
                                <input type="text" readonly value="{{ $prod['affiliate_url'] }}" class="w-full px-3 py-1.5 rounded-lg bg-white border border-brand-border text-[11px] font-mono text-brand-textAlt">
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button"
                                            @click="copyLink(@js($prod['affiliate_url']), @js($prodKey))"
                                            class="py-2 px-3 rounded-xl bg-brand-primary hover:bg-brand-primaryAlt text-white text-xs font-bold transition-colors cursor-pointer">
                                        <span x-text="copiedKey === @js($prodKey) ? '✅ Tersalin!' : '📋 Salin Link'">📋 Salin Link</span>
                                    </button>
                                    <a href="https://wa.me/?text={{ rawurlencode($waProdText) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold text-center transition-colors">
                                        💬 Share WA
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Link Paket Bundling Promo (Tambahan) -->
                <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-[#9A6A52] to-[#7C523E] text-white flex flex-col lg:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-center lg:text-left">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-[10px] font-bold uppercase tracking-wider">
                            🎁 Link Halaman Paket Bundling Hemat (Komisi s/d Rp 38.900 / Paket)
                        </span>
                        <p class="text-xs text-white/90">
                            Selain link per produk di atas, Anda juga bisa membagikan link halaman promo bundling semua paket:
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                        <input type="text" readonly value="{{ $bundleShareUrl }}" class="sm:w-80 px-3 py-2 rounded-xl bg-white text-brand-text font-mono text-xs border-0">
                        <button type="button"
                                @click="copyLink(@js($bundleShareUrl), 'bundle')"
                                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shrink-0 cursor-pointer">
                            <span x-text="copiedKey === 'bundle' ? '✅ Tersalin!' : '📋 Salin Link Paket'">📋 Salin Link Paket</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid Pencairan Komisi & Info Sistem Pelacakan Otomatis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom 1: Form Pencairan Komisi (Withdraw) -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-brand-border/60">
                            <div>
                                <h3 class="text-lg font-serif font-bold text-brand-text">🏦 Cairkan Komisi (Withdraw)</h3>
                                <p class="text-xs text-brand-textAlt">Tarik saldo komisi ke rekening Bank atau E-Wallet Anda (Min. Rp 20.000).</p>
                            </div>
                            <button type="button" @click="editBankOpen = !editBankOpen" class="text-xs font-bold text-brand-primary hover:underline cursor-pointer">
                                ⚙️ Ubah Rekening
                            </button>
                        </div>

                        <!-- Form Edit Rekening & Kode (Collapsible) -->
                        <div x-show="editBankOpen" x-transition class="mb-5 p-4 rounded-2xl bg-brand-cream/50 border border-brand-border">
                            <p class="text-xs font-bold text-brand-text mb-3">Perbarui Kode Mitra &amp; Rekening Pencairan</p>
                            <form action="{{ route('affiliate.bank.update') }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-brand-textAlt uppercase mb-1">Kode Afiliasi</label>
                                        <input type="text" name="affiliate_code" value="{{ $user->affiliate_code }}" required class="w-full px-3 py-2 rounded-xl border border-brand-border text-xs uppercase font-mono bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-brand-textAlt uppercase mb-1">Bank / E-Wallet</label>
                                        <input type="text" name="bank_name" value="{{ $user->bank_name }}" required class="w-full px-3 py-2 rounded-xl border border-brand-border text-xs bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-brand-textAlt uppercase mb-1">Nomor Rekening</label>
                                        <input type="text" name="bank_account_number" value="{{ $user->bank_account_number }}" required class="w-full px-3 py-2 rounded-xl border border-brand-border text-xs bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-brand-textAlt uppercase mb-1">Atas Nama</label>
                                        <input type="text" name="bank_account_holder" value="{{ $user->bank_account_holder }}" required class="w-full px-3 py-2 rounded-xl border border-brand-border text-xs bg-white">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editBankOpen = false" class="px-3 py-1.5 rounded-lg text-xs text-brand-textAlt">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-brand-primary text-white text-xs font-bold">Simpan Rekening</button>
                                </div>
                            </form>
                        </div>

                        <form action="{{ route('affiliate.withdraw') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="p-3.5 rounded-2xl bg-brand-cream/40 border border-brand-border text-xs flex items-center justify-between">
                                <div>
                                    <span class="text-brand-textAlt block">Rekening Tujuan Pencairan:</span>
                                    <strong class="text-brand-text">{{ $user->bank_name ?: '-' }} • {{ $user->bank_account_number ?: '-' }}</strong>
                                    <span class="block text-[11px] text-brand-textAlt">a.n. {{ $user->bank_account_holder ?: $user->name }}</span>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold">
                                    Saldo: Rp {{ number_format($stats['balance'], 0, ',', '.') }}
                                </span>
                            </div>

                            <input type="hidden" name="bank_name" value="{{ $user->bank_name }}">
                            <input type="hidden" name="bank_account_number" value="{{ $user->bank_account_number }}">
                            <input type="hidden" name="bank_account_holder" value="{{ $user->bank_account_holder }}">

                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1.5">
                                    Nominal Pencairan Komisi (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       name="amount"
                                       min="20000"
                                       max="{{ max(20000, $stats['balance']) }}"
                                       value="{{ old('amount', $stats['balance'] >= 20000 ? $stats['balance'] : '') }}"
                                       placeholder="Minimal 20000"
                                       class="w-full px-4 py-3 rounded-xl border border-brand-border bg-white text-sm font-bold text-brand-text"
                                       required>
                            </div>

                            <button type="submit"
                                    @disabled($stats['balance'] < 20000)
                                    class="w-full py-3.5 px-5 rounded-2xl font-bold text-xs sm:text-sm transition-all shadow-soft {{ $stats['balance'] >= 20000 ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}">
                                @if($stats['balance'] >= 20000)
                                    💸 Ajukan Pencairan Komisi Sekarang
                                @else
                                    Saldo Belum Mencukupi Minimal Pencairan (Rp 20.000)
                                @endif
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Kolom 2: Cara Kerja Pelacakan Otomatis (Ala Shopee Affiliate) -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-brand-border/60 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-serif font-bold text-brand-text">⚡ Cara Kerja Komisi Otomatis</h3>
                                <p class="text-xs text-brand-textAlt">Tanpa input manual — semua penjualan terlacak otomatis dari link produk Anda.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">
                                Auto-Track Aktif
                            </span>
                        </div>

                        <div class="space-y-3.5 text-xs">
                            <div class="flex items-start gap-3 p-3 rounded-2xl bg-brand-cream/40 border border-brand-border/60">
                                <span class="w-7 h-7 rounded-xl bg-brand-primary text-white font-bold flex items-center justify-center shrink-0">1</span>
                                <div>
                                    <p class="font-bold text-brand-text">Salin &amp; Sebarkan Link Produk</p>
                                    <p class="text-brand-textAlt mt-0.5 leading-relaxed">
                                        Pilih produk di atas (misal <em>Face Serum</em> atau <em>UV Sunscreen</em>) lalu klik <strong>Salin Link</strong> atau bagikan langsung dari halaman detail produk.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 rounded-2xl bg-brand-cream/40 border border-brand-border/60">
                                <span class="w-7 h-7 rounded-xl bg-brand-primary text-white font-bold flex items-center justify-center shrink-0">2</span>
                                <div>
                                    <p class="font-bold text-brand-text">Otomatis Rekam Klik &amp; Cookie 30 Hari</p>
                                    <p class="text-brand-textAlt mt-0.5 leading-relaxed">
                                        Saat calon pembeli membuka link Anda, sistem langsung mencatat jumlah klik produk dan mengunci kode afiliasi Anda (<strong class="font-mono">{{ $user->affiliate_code }}</strong>) di perangkat pembeli selama 30 hari.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 p-3 rounded-2xl bg-brand-cream/40 border border-brand-border/60">
                                <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-bold flex items-center justify-center shrink-0">3</span>
                                <div>
                                    <p class="font-bold text-brand-text">Pembeli Checkout = Komisi Masuk Otomatis</p>
                                    <p class="text-brand-textAlt mt-0.5 leading-relaxed">
                                        Begitu pembeli melakukan pemesanan produk (baik langsung di halaman produk maupun paket bundling), pesanan otomatis muncul di tabel <strong>Riwayat Penjualan &amp; Komisi</strong> di bawah ini!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Penjualan & Komisi Otomatis -->
            <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-serif font-bold text-brand-text">📦 Riwayat Konversi Penjualan &amp; Komisi Otomatis</h3>
                        <p class="text-xs text-brand-textAlt">Setiap pembelian yang berasal dari link produk Anda otomatis tercatat di sini.</p>
                    </div>
                </div>
                @if($sales->isEmpty())
                    <p class="text-xs text-brand-textAlt py-6 text-center">Belum ada konversi penjualan dari link afiliasi Anda. Bagikan link produk di atas untuk mulai meraih komisi!</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-brand-border text-brand-textAlt uppercase">
                                    <th class="py-3 px-3">Kode Order</th>
                                    <th class="py-3 px-3">Waktu Order</th>
                                    <th class="py-3 px-3">Pembeli</th>
                                    <th class="py-3 px-3">Produk / Paket Terjual</th>
                                    <th class="py-3 px-3">Nilai Transaksi</th>
                                    <th class="py-3 px-3">Komisi Anda</th>
                                    <th class="py-3 px-3">Status Komisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-border/50">
                                @foreach($sales as $sale)
                                    <tr>
                                        <td class="py-3 px-3 font-mono font-bold text-brand-text">{{ $sale->order_code }}</td>
                                        <td class="py-3 px-3 text-brand-textAlt">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-3 px-3">
                                            <p class="font-bold text-brand-text">{{ $sale->customer_name }}</p>
                                            <p class="text-[11px] text-brand-textAlt">{{ $sale->customer_phone }}</p>
                                        </td>
                                        <td class="py-3 px-3 font-medium text-brand-text">
                                            {{ $sale->package_name }}
                                            @if(($sale->quantity ?? 1) > 1)
                                                <span class="px-1.5 py-0.5 rounded bg-brand-cream text-[10px] font-bold">{{ $sale->quantity }} pcs</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3">{{ $sale->formatted_sale_amount }}</td>
                                        <td class="py-3 px-3 font-extrabold text-emerald-700">
                                            +{{ $sale->formatted_commission_amount }}
                                            <span class="text-[10px] font-normal text-brand-textAlt">({{ rtrim(rtrim(number_format($sale->commission_rate, 2), '0'), '.') }}%)</span>
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($sale->status === 'approved')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">✅ Selesai (Masuk Saldo)</span>
                                            @elseif($sale->status === 'rejected')
                                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">✕ Dibatalkan</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">⏳ Menunggu Bayar QRIS</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tabel Riwayat Pencairan Komisi -->
            <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                <h3 class="text-lg font-serif font-bold text-brand-text mb-4">💸 Riwayat Pencairan Komisi (Withdraw)</h3>
                @if($withdrawals->isEmpty())
                    <p class="text-xs text-brand-textAlt py-6 text-center">Belum ada riwayat pengajuan pencairan komisi.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-brand-border text-brand-textAlt uppercase">
                                    <th class="py-3 px-3">Kode Withdraw</th>
                                    <th class="py-3 px-3">Tanggal Pengajuan</th>
                                    <th class="py-3 px-3">Rekening Tujuan</th>
                                    <th class="py-3 px-3">Nominal Cair</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Catatan Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-brand-border/50">
                                @foreach($withdrawals as $wd)
                                    <tr>
                                        <td class="py-3 px-3 font-mono font-bold text-brand-text">{{ $wd->withdrawal_code }}</td>
                                        <td class="py-3 px-3 text-brand-textAlt">{{ $wd->created_at->format('d M Y H:i') }}</td>
                                        <td class="py-3 px-3">
                                            <p class="font-bold text-brand-text">{{ $wd->bank_name }} - {{ $wd->bank_account_number }}</p>
                                            <p class="text-[11px] text-brand-textAlt">a.n. {{ $wd->bank_account_holder }}</p>
                                        </td>
                                        <td class="py-3 px-3 font-extrabold text-brand-primary">{{ $wd->formatted_amount }}</td>
                                        <td class="py-3 px-3">
                                            @if($wd->status === 'approved')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">✅ Sudah Ditransfer</span>
                                            @elseif($wd->status === 'rejected')
                                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">✕ Ditolak (Saldo Kembali)</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">⏳ Sedang Diproses</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-brand-textAlt">{{ $wd->admin_notes ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-app-layout>
