@php
    $steps = [
        ['key' => 'pending_payment', 'label' => 'Pesanan Dibuat', 'icon' => '🧾', 'time' => $order->created_at],
        ['key' => 'paid', 'label' => 'Pembayaran Diterima', 'icon' => '💳', 'time' => $order->paid_at],
        ['key' => 'processing', 'label' => 'Dikemas (Lamongan)', 'icon' => '📦', 'time' => $order->paid_at],
        ['key' => 'shipped', 'label' => 'Barang Dikirim (J&T)', 'icon' => '🚚', 'time' => $order->shipped_at],
        ['key' => 'completed', 'label' => 'Pesanan Selesai', 'icon' => '✅', 'time' => $order->completed_at ?: $order->delivered_at],
    ];

    $statusOrder = [
        'pending_payment' => 1,
        'paid' => 2,
        'processing' => 3,
        'shipped' => 4,
        'delivered' => 4,
        'completed' => 5,
        'cancelled' => 0,
    ];
    $currentStepIndex = $statusOrder[$order->status] ?? 1;
@endphp

<x-app-layout>
    <div class="space-y-6" x-data="{
        gatewayModalOpen: @js(session('open_payment_gateway', false) && $order->payment_status !== 'paid'),
        copiedResi: false,
        selectedPayChannel: @js($order->payment_method ?: 'QRIS Otomatis (All Bank & E-Wallet)'),
        copyText(txt) {
            navigator.clipboard.writeText(txt);
            this.copiedResi = true;
            setTimeout(() => this.copiedResi = false, 2000);
        }
    }">

        <!-- Top Breadcrumb & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <a href="{{ route('orders.index') }}" class="text-xs font-bold text-brand-primary hover:underline inline-flex items-center gap-1 mb-1">
                    &larr; Kembali ke Pesanan Saya
                </a>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-2xl font-serif font-bold text-brand-text">Detail Pesanan #{{ $order->order_number }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                    <button type="button"
                            @click="gatewayModalOpen = true"
                            class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primaryAlt text-white text-xs font-bold shadow-soft cursor-pointer">
                        💳 Bayar Sekarang ({{ $order->formatted_total }})
                    </button>
                @endif

                @if(in_array($order->status, ['shipped', 'delivered']))
                    <form action="{{ route('orders.received', $order) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa paket J&T sudah Anda terima dengan baik?')">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-soft cursor-pointer">
                            ✅ Konfirmasi Pesanan Diterima
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Shopee-Style Order Progress Stepper -->
        @if($order->status !== 'cancelled')
            <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    @foreach($steps as $idx => $step)
                        @php
                            $stepNum = $idx + 1;
                            $isDone = $currentStepIndex >= $stepNum;
                        @endphp
                        <div class="flex flex-col items-center text-center p-3 rounded-2xl border {{ $isDone ? 'bg-emerald-50/70 border-emerald-300' : 'bg-brand-cream/30 border-brand-border/60 opacity-60' }}">
                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl mb-2 {{ $isDone ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-brand-textAlt border border-brand-border' }}">
                                {{ $step['icon'] }}
                            </div>
                            <p class="text-xs font-bold text-brand-text">{{ $step['label'] }}</p>
                            @if($isDone && $step['time'])
                                <p class="text-[10px] text-emerald-700 font-mono mt-0.5">{{ $step['time']->format('d M H:i') }}</p>
                            @else
                                <p class="text-[10px] text-brand-textAlt mt-0.5">Menunggu</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-5 rounded-3xl bg-rose-50 border border-rose-200 text-rose-900">
                <p class="font-bold text-sm">✕ Pesanan Ini Telah Dibatalkan</p>
                <p class="text-xs text-rose-700 mt-1">Alasan: {{ $order->cancel_reason ?: 'Dibatalkan.' }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- KOLOM KIRI: INFO PENGIRIMAN J&T, TIMELINE STATUS, DAN ITEM PRODUK -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Kartu Pengiriman J&T Express & Nomor Resi -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-brand-border/60">
                        <div>
                            <h2 class="text-base sm:text-lg font-serif font-bold text-brand-text">🚚 Informasi Pengiriman ({{ $order->courier }})</h2>
                            <p class="text-xs text-brand-textAlt">
                                Dikirim dari Gudang Resmi URSKYND <strong>Lamongan, Jawa Timur</strong> • Zona: <strong>{{ $order->shipping_zone_name }}</strong> ({{ $order->shipping_etd }})
                            </p>
                        </div>

                        @if($order->tracking_number)
                            <div class="flex items-center gap-2 bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs">
                                <span class="text-white/70">No. Resi J&amp;T:</span>
                                <strong class="font-mono tracking-wider">{{ $order->tracking_number }}</strong>
                                <button type="button" @click="copyText(@js($order->tracking_number))" class="px-2 py-0.5 rounded bg-white/20 hover:bg-white/30 text-[10px] font-bold cursor-pointer">
                                    <span x-text="copiedResi ? 'Tersalin!' : 'Salin'">Salin</span>
                                </button>
                            </div>
                        @else
                            <span class="px-3 py-1 rounded-full bg-brand-cream text-brand-primary text-xs font-bold">
                                Resi J&amp;T akan muncul saat barang dikirim
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3.5 rounded-2xl bg-brand-cream/40 border border-brand-border/60">
                            <p class="text-[10px] font-bold uppercase text-brand-textAlt mb-1">Penerima &amp; Kontak</p>
                            <p class="font-bold text-brand-text text-sm">{{ $order->recipient_name }}</p>
                            <p class="text-brand-textAlt font-mono mt-0.5">{{ $order->recipient_phone }}</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-brand-cream/40 border border-brand-border/60">
                            <p class="text-[10px] font-bold uppercase text-brand-textAlt mb-1">Alamat Tujuan Pengiriman</p>
                            <p class="font-semibold text-brand-text">{{ $order->address }}</p>
                            <p class="text-brand-textAlt mt-0.5">
                                {{ $order->district ? $order->district . ', ' : '' }}{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
                            </p>
                        </div>
                    </div>

                    <!-- Lacak Riwayat Status Pengiriman & Pembayaran (Shopee Timeline) -->
                    <div class="pt-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-textAlt mb-3">📍 Riwayat Pelacakan Pesanan &amp; Kurir:</p>
                        <div class="space-y-3 border-l-2 border-brand-primary/30 ml-2 pl-4">
                            @foreach($order->statusHistories as $idx => $hist)
                                <div class="relative">
                                    <span class="w-3 h-3 rounded-full absolute -left-[23px] top-1 {{ $idx === 0 ? 'bg-brand-primary ring-4 ring-brand-primary/20' : 'bg-brand-border' }}"></span>
                                    <p class="text-xs font-bold {{ $idx === 0 ? 'text-brand-primary' : 'text-brand-text' }}">
                                        {{ $hist->title }}
                                    </p>
                                    @if($hist->note)
                                        <p class="text-xs text-brand-textAlt mt-0.5 leading-relaxed">{{ $hist->note }}</p>
                                    @endif
                                    <p class="text-[10px] font-mono text-brand-textAlt mt-0.5">{{ $hist->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Daftar Produk yang Dipesan -->
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft">
                    <h3 class="text-base font-serif font-bold text-brand-text pb-3 mb-4 border-b border-brand-border/60">
                        🧴 Rincian Produk ({{ $order->items->sum('quantity') }} pcs)
                    </h3>

                    <div class="divide-y divide-brand-border/40">
                        @foreach($order->items as $item)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}" class="w-14 h-14 rounded-xl object-cover border border-brand-border bg-brand-cream">
                                    <div>
                                        <p class="text-sm font-bold text-brand-text">{{ $item->product_name }}</p>
                                        <p class="text-xs text-brand-textAlt">{{ $item->quantity }} pcs &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-extrabold text-brand-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: RINGKASAN PEMBAYARAN & PAYMENT GATEWAY OTOMATIS -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-brand-border shadow-soft-lg space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-brand-border/60">
                        <div>
                            <h3 class="text-lg font-serif font-bold text-brand-text">💳 Status &amp; Rincian Pembayaran</h3>
                            <p class="text-xs text-brand-textAlt">Metode: <strong>{{ $order->payment_method ?: 'Payment Gateway' }}</strong></p>
                        </div>
                        @if($order->payment_status === 'paid')
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold">
                                ✅ LUNAS
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold">
                                ⏳ BELUM BAYAR
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 text-xs sm:text-sm">
                        <div class="flex justify-between text-brand-textAlt">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-brand-text">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between text-brand-textAlt">
                            <span>Ongkos Kirim ({{ $order->courier }} — {{ $order->shipping_zone_name }})</span>
                            <span class="font-semibold text-brand-text">{{ $order->formatted_shipping_cost }}</span>
                        </div>
                        <div class="pt-3 border-t border-brand-border flex justify-between items-baseline">
                            <span class="font-bold text-brand-text">Total Pembayaran</span>
                            <span class="text-2xl font-extrabold text-brand-primary">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    @if($order->payment_reference)
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                            <p class="font-bold">Referensi Payment Gateway: <span class="font-mono">{{ $order->payment_reference }}</span></p>
                            @if($order->paid_at)
                                <p class="text-[11px] text-emerald-700 mt-0.5">Dibayar pada: {{ $order->paid_at->format('d M Y, H:i') }} WIB</p>
                            @endif
                        </div>
                    @endif

                    @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                        <div class="p-4 rounded-2xl bg-brand-cream/50 border border-brand-border space-y-3">
                            <p class="text-xs font-bold text-brand-text">⚡ Selesaikan Pembayaran Otomatis:</p>
                            <p class="text-[11px] text-brand-textAlt leading-relaxed">
                                Klik tombol di bawah untuk membuka jendela Payment Gateway (QRIS / Virtual Account / E-Wallet). Begitu dibayar, status pesanan langsung berubah menjadi <strong>Pembayaran Diterima</strong> secara otomatis!
                            </p>

                            <button type="button"
                                    @click="gatewayModalOpen = true"
                                    class="w-full py-3.5 px-5 rounded-2xl bg-brand-primary hover:bg-brand-primaryAlt text-white font-bold text-xs sm:text-sm shadow-soft cursor-pointer">
                                🔒 Buka Payment Gateway &amp; Bayar ({{ $order->formatted_total }})
                            </button>

                            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="w-full py-2 text-xs text-rose-600 hover:underline font-semibold cursor-pointer">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- MODAL PAYMENT GATEWAY OTOMATIS (MIDTRANS / XENDIT / INSTANT AUTO-VERIFY) -->
        <div x-show="gatewayModalOpen"
             x-transition
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;"
             @keydown.escape.window="gatewayModalOpen = false">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-brand-border max-h-[92vh] overflow-y-auto"
                 @click.outside="gatewayModalOpen = false">

                <div class="flex items-center justify-between pb-3 mb-4 border-b border-brand-border">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold">⚡</span>
                        <div>
                            <h3 class="text-base font-serif font-bold text-brand-text">URSKYND Automatic Payment Gateway</h3>
                            <p class="text-[11px] text-brand-textAlt">Order ID: <strong class="font-mono">{{ $order->order_number }}</strong> • Verifikasi Lunas Otomatis</p>
                        </div>
                    </div>
                    <button type="button" @click="gatewayModalOpen = false" class="text-brand-textAlt hover:text-brand-text text-lg font-bold cursor-pointer">✕</button>
                </div>

                <!-- Nominal Tagihan -->
                <div class="p-4 rounded-2xl bg-gradient-to-r from-[#9A6A52] to-[#7C523E] text-white mb-4 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-white/80 block">Total Pembayaran</span>
                        <span class="text-2xl font-extrabold">{{ $order->formatted_total }}</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-white/20 text-[11px] font-bold">
                        🚚 {{ $order->courier }} ({{ $order->shipping_zone_name }})
                    </span>
                </div>

                @if($order->snap_redirect_url)
                    <div class="mb-4 p-3.5 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900 space-y-2">
                        <p class="font-bold">🔗 Link Pembayaran Midtrans Snap Tersedia:</p>
                        <a href="{{ $order->snap_redirect_url }}" target="_blank" class="inline-block px-4 py-2 rounded-xl bg-blue-600 text-white font-bold">
                            Buka Halaman Midtrans Snap &nearr;
                        </a>
                    </div>
                @endif

                <form action="{{ route('orders.pay', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-brand-text uppercase mb-1.5">Pilih Channel Pembayaran:</label>
                        <select name="payment_method" x-model="selectedPayChannel" class="w-full px-3.5 py-2.5 rounded-xl border border-brand-border text-xs font-bold bg-brand-cream/40">
                            <option value="QRIS Otomatis (All Bank & E-Wallet)">📲 QRIS Otomatis (Semua M-Banking & E-Wallet)</option>
                            <option value="BCA Virtual Account">🏦 BCA Virtual Account (8801{{ substr(preg_replace('/[^0-9]/', '', $order->recipient_phone), -8) }})</option>
                            <option value="Mandiri / BRI / BNI Virtual Account">💳 Mandiri / BRI / BNI Virtual Account</option>
                            <option value="ShopeePay / GoPay / DANA / OVO">⚡ E-Wallet Instan (ShopeePay / GoPay / DANA)</option>
                        </select>
                    </div>

                    <!-- Tampilan QRIS Resmi URSKYND -->
                    @if(!empty($qrisImg))
                        <div class="p-4 rounded-2xl bg-brand-cream/40 border border-brand-border text-center space-y-2">
                            <p class="text-xs font-extrabold text-brand-text">{{ $qrisMerchant }} (NMID: {{ $qrisNmid }})</p>
                            <img src="{{ $qrisImg }}" alt="QRIS URSKYND" class="w-44 h-auto mx-auto rounded-xl border border-brand-border bg-white p-1.5 shadow-xs">
                            <p class="text-[11px] text-brand-textAlt">
                                Scan QRIS atau selesaikan pembayaran pada channel di atas, lalu klik tombol konfirmasi otomatis di bawah.
                            </p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-bold text-brand-textAlt uppercase mb-1">Upload Bukti Pembayaran (Opsional)</label>
                        <input type="file" name="payment_proof_file" accept="image/*" class="block w-full text-xs text-brand-textAlt file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-brand-cream file:text-brand-primary file:font-bold">
                    </div>

                    <button type="submit" class="w-full py-4 px-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-soft transition-all cursor-pointer">
                        ⚡ Konfirmasi &amp; Selesaikan Pembayaran Otomatis Sekarang
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

