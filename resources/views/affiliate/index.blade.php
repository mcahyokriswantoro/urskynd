<x-app-layout>
    <div class="space-y-6" x-data="{
        copied: false,
        editBankOpen: false,
        selectedPkgPrice: @js($packages[0]['price'] ?? 185000),
        selectedPkgName: @js($packages[0]['name'] ?? 'Paket Glowing Starter'),
        copyLink(url) {
            navigator.clipboard.writeText(url);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        }
    }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-2">
                    🤝 Program Kemitraan Resmi
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">Mitra &amp; Afiliator URSKYND</h1>
                <p class="text-xs sm:text-sm text-brand-textAlt mt-0.5">
                    Promosikan produk perawatan kulit URSKYND, pantau penjualan, dan cairkan komisi Anda.
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
                        Aktifkan fitur afiliasi pada akun Anda dan dapatkan komisi <strong>10%</strong> dari setiap penjualan produk URSKYND yang dapat dicairkan ke Bank atau E-Wallet.
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
            <!-- DASHBOARD AKTIF MITRA / AFILIATOR -->
            @php
                $shareUrl = route('pembelian.index', ['ref' => $user->affiliate_code]);
                $waShareText = "Halo! Aku rekomendasikan perawatan kulit klinis resmi URSKYND (BPOM & Halal). Pesan lewat link resmi ini untuk dapat harga promo hemat s/d 35% ya:\n" . $shareUrl . "\n\n(Kode Referral: " . $user->affiliate_code . ")";
            @endphp

            <!-- Banner Link & Kode Afiliasi -->
            <div class="bg-gradient-to-r from-[#9A6A52] to-[#7C523E] rounded-3xl p-6 sm:p-7 text-white shadow-soft-lg">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-5 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-[11px] font-bold uppercase tracking-wider">
                                Komisi Aktif: {{ rtrim(rtrim(number_format($user->commission_rate, 2), '0'), '.') }}% / Penjualan
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-400/25 border border-emerald-300/40 text-emerald-100 text-[11px] font-bold">
                                ● Mitra Aktif
                            </span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold">Kode Mitra: <span class="font-mono bg-black/25 px-3 py-1 rounded-xl">{{ $user->affiliate_code }}</span></h2>
                        <p class="text-xs text-white/85 leading-relaxed">
                            Bagikan link afiliasi di samping ke WhatsApp, Instagram, atau TikTok. Setiap pembeli yang memesan melalui link atau memasukkan kode Anda akan otomatis menambah komisi Anda!
                        </p>
                    </div>

                    <div class="lg:col-span-7 bg-white/10 backdrop-blur-xs p-4 rounded-2xl border border-white/20 space-y-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-white/80">
                            🔗 Link Promosi Afiliasi Anda:
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" readonly value="{{ $shareUrl }}" class="flex-1 px-3.5 py-2.5 rounded-xl bg-white text-brand-text font-mono text-xs border-0 focus:ring-0">
                            <button type="button"
                                    @click="copyLink(@js($shareUrl))"
                                    class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold shrink-0 cursor-pointer transition-colors">
                                <span x-text="copied ? '✅ Tersalin!' : '📋 Salin Link'">📋 Salin Link</span>
                            </button>
                            <a href="https://wa.me/?text={{ rawurlencode($waShareText) }}"
                               target="_blank"
                               rel="noopener"
                               class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shrink-0 text-center transition-colors">
                                💬 Share WA
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Kartu KPI Komisi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border-2 border-emerald-500/40 shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Saldo Komisi Siap Cair</span>
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">💰</span>
                    </div>
                    <p class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($stats['balance'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">Dapat langsung dicairkan ke rekening</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Total Komisi Terkumpul</span>
                        <span class="w-9 h-9 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-lg">📈</span>
                    </div>
                    <p class="text-2xl font-extrabold text-brand-text">Rp {{ number_format($stats['total_earned'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">{{ $stats['approved_sales_count'] }} penjualan terverifikasi</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-brand-textAlt uppercase">Menunggu Verifikasi</span>
                        <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">⏳</span>
                    </div>
                    <p class="text-2xl font-extrabold text-amber-600">Rp {{ number_format($stats['pending_commission'], 0, ',', '.') }}</p>
                    <p class="text-[11px] text-brand-textAlt mt-1">{{ $stats['pending_sales_count'] }} pesanan sedang dicek Admin</p>
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
                            Transfer ke {{ $user->bank_name ?: 'Rekening Mitra' }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Estimasi Komisi Per Paket -->
            @if(count($packages))
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-brand-border shadow-soft">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-brand-text">💡 Potensi Komisi Anda Per Paket Produk</h3>
                        <p class="text-xs text-brand-textAlt">Semakin banyak paket terjual, semakin besar komisi yang bisa langsung Anda cairkan.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($packages as $pkg)
                        <div class="p-4 rounded-2xl bg-brand-cream/40 border border-brand-border flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-brand-text">{{ $pkg['name'] }}</p>
                                <p class="text-[11px] text-brand-textAlt">Harga Paket: {{ $pkg['formatted_price'] }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="block text-[10px] font-bold uppercase text-emerald-700">Komisi Anda</span>
                                <span class="text-sm font-extrabold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-lg inline-block mt-0.5">
                                    +{{ $pkg['formatted_commission'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Grid Pencairan Komisi & Klaim Penjualan Manual -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom 1: Form Pencairan Komisi (Withdraw) -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-brand-border/60">
                            <div>
                                <h3 class="text-lg font-serif font-bold text-brand-text">🏦 Cairkan Komisi (Withdraw)</h3>
                                <p class="text-xs text-brand-textAlt">Tarik saldo komisi ke rekening Bank atau E-Wallet Anda (Min. Rp 20.000).</p>
                            </div>
                            <button type="button" @click="editBankOpen = !editBankOpen" class="text-xs font-bold text-brand-primary hover:underline">
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

                <!-- Kolom 2: Form Klaim / Catat Penjualan Mitra -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                    <div class="mb-4 pb-3 border-b border-brand-border/60">
                        <h3 class="text-lg font-serif font-bold text-brand-text">🛍️ Klaim / Catat Penjualan Anda</h3>
                        <p class="text-xs text-brand-textAlt">
                            Penjualan melalui link afiliasi tercatat otomatis. Jika pembeli memesan manual lewat Anda, catat di sini untuk diverifikasi Admin.
                        </p>
                    </div>

                    <form action="{{ route('affiliate.sales.store') }}" method="POST" class="space-y-3.5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1">
                                Paket / Produk yang Terjual <span class="text-red-500">*</span>
                            </label>
                            <select name="package_name"
                                    x-model="selectedPkgName"
                                    @change="const found = @js($packages).find(p => p.name === $event.target.value); if (found) selectedPkgPrice = found.price;"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border bg-brand-cream/30 text-xs sm:text-sm font-medium">
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg['name'] }}">{{ $pkg['name'] }} ({{ $pkg['formatted_price'] }})</option>
                                @endforeach
                                <option value="Produk Satuan / Custom Order">Produk Satuan / Custom Order</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1">
                                    Nama Pembeli <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" required placeholder="Contoh: Rina Melati" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border bg-white text-xs sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1">
                                    No. WhatsApp Pembeli <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_phone" required placeholder="0812xxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border bg-white text-xs sm:text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1">
                                    Total Harga Penjualan (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="sale_amount" x-model="selectedPkgPrice" min="10000" required class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border bg-white text-xs sm:text-sm font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-1">
                                    Catatan / Bukti Order (Opsional)
                                </label>
                                <input type="text" name="notes" placeholder="Sudah bayar QRIS" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border bg-white text-xs sm:text-sm">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 px-5 rounded-2xl bg-brand-primary hover:bg-brand-primaryAlt text-white font-bold text-xs sm:text-sm shadow-soft transition-colors cursor-pointer">
                            📝 Kirim Klaim Penjualan (Komisi {{ rtrim(rtrim(number_format($user->commission_rate, 2), '0'), '.') }}%)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Riwayat Penjualan & Komisi -->
            <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                <h3 class="text-lg font-serif font-bold text-brand-text mb-4">📦 Riwayat Penjualan &amp; Komisi Afiliasi</h3>
                @if($sales->isEmpty())
                    <p class="text-xs text-brand-textAlt py-6 text-center">Belum ada transaksi penjualan afiliasi. Bagikan link promosi Anda untuk mulai meraih komisi!</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-brand-border text-brand-textAlt uppercase">
                                    <th class="py-3 px-3">Kode Order</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Pembeli</th>
                                    <th class="py-3 px-3">Paket / Produk</th>
                                    <th class="py-3 px-3">Nilai Transaksi</th>
                                    <th class="py-3 px-3">Komisi Anda</th>
                                    <th class="py-3 px-3">Status</th>
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
                                        <td class="py-3 px-3 font-medium text-brand-text">{{ $sale->package_name }}</td>
                                        <td class="py-3 px-3">{{ $sale->formatted_sale_amount }}</td>
                                        <td class="py-3 px-3 font-extrabold text-emerald-700">
                                            +{{ $sale->formatted_commission_amount }}
                                            <span class="text-[10px] font-normal text-brand-textAlt">({{ rtrim(rtrim(number_format($sale->commission_rate, 2), '0'), '.') }}%)</span>
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($sale->status === 'approved')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">✅ Disetujui (Masuk Saldo)</span>
                                            @elseif($sale->status === 'rejected')
                                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">✕ Ditolak / Batal</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">⏳ Menunggu Verifikasi</span>
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

