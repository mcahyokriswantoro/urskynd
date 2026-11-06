<x-app-layout>
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-1.5">
                    <span>📦</span> Status &amp; Lacak Pengiriman J&amp;T
                </div>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-brand-text">Pesanan Saya</h1>
                <p class="text-xs sm:text-sm text-brand-textAlt">
                    Pantau status pembayaran, pengemasan di Gudang Lamongan, hingga nomor resi pengiriman J&amp;T Express.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cart.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-brand-border text-brand-text text-xs font-bold hover:bg-brand-cream transition-colors">
                    🛒 Keranjang Saya
                </a>
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt transition-colors">
                    + Belanja Lagi
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Shopee-Style Status Filter Tabs -->
        <div class="flex gap-1.5 overflow-x-auto bg-white p-1.5 rounded-2xl border border-brand-border shadow-soft">
            @foreach([
                ['key' => 'all', 'label' => 'Semua', 'count' => $counts['all']],
                ['key' => 'pending_payment', 'label' => 'Belum Bayar', 'count' => $counts['pending_payment']],
                ['key' => 'processing_group', 'label' => 'Dikemas', 'count' => $counts['processing_group']],
                ['key' => 'shipped', 'label' => 'Dikirim (J&T)', 'count' => $counts['shipped']],
                ['key' => 'completed', 'label' => 'Selesai', 'count' => $counts['completed']],
                ['key' => 'cancelled', 'label' => 'Dibatalkan', 'count' => $counts['cancelled']],
            ] as $t)
                <a href="{{ route('orders.index', ['status' => $t['key']]) }}"
                   class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $tab === $t['key'] ? 'bg-brand-primary text-white shadow-soft' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-text' }}">
                    <span>{{ $t['label'] }}</span>
                    @if($t['count'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === $t['key'] ? 'bg-white text-brand-primary' : 'bg-brand-cream text-brand-primary' }}">
                            {{ $t['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Daftar Kartu Pesanan -->
        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-brand-border shadow-soft max-w-xl mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-brand-cream text-brand-primary flex items-center justify-center text-3xl mx-auto mb-4">
                    📦
                </div>
                <h2 class="text-xl font-serif font-bold text-brand-text mb-1">Belum Ada Pesanan pada Kategori Ini</h2>
                <p class="text-xs sm:text-sm text-brand-textAlt mb-6">
                    Yuk pesan produk perawatan kulit resmi URSKYND dan pantau proses pengirimannya secara real-time di sini!
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-brand-primary text-white text-xs sm:text-sm font-bold">
                    <span>Belanja Sekarang</span>
                    <span>&rarr;</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-3xl border border-brand-border shadow-soft overflow-hidden hover:border-brand-primary/40 transition-all">
                        <!-- Top Bar Kartu Pesanan -->
                        <div class="px-5 py-3.5 bg-brand-cream/40 border-b border-brand-border/60 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-bold text-brand-text flex items-center gap-1">
                                    <span>🏪</span> URSKYND Official Store (Lamongan)
                                </span>
                                <span class="text-brand-textAlt">•</span>
                                <span class="font-mono font-bold text-brand-primary">{{ $order->order_number }}</span>
                                <span class="text-brand-textAlt">•</span>
                                <span class="text-brand-textAlt">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($order->tracking_number)
                                    <span class="px-2.5 py-1 rounded-full bg-slate-900 text-white font-mono text-[11px] font-bold">
                                        🚚 {{ $order->courier }}: {{ $order->tracking_number }}
                                    </span>
                                @endif

                                @php
                                    $badgeStyle = match($order->status) {
                                        'pending_payment' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'paid', 'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'shipped', 'delivered' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full border text-[11px] font-extrabold {{ $badgeStyle }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Daftar Produk di Dalam Pesanan -->
                        <div class="p-5 divide-y divide-brand-border/40">
                            @foreach($order->items as $item)
                                <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-14 h-14 rounded-xl bg-brand-cream border border-brand-border overflow-hidden shrink-0">
                                            <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-brand-text truncate">{{ $item->product_name }}</p>
                                            <p class="text-xs text-brand-textAlt">
                                                {{ $item->quantity }} pcs &times; Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs sm:text-sm font-bold text-brand-text">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Kartu Pesanan: Total & Tombol Aksi -->
                        <div class="px-5 py-4 bg-brand-cream/25 border-t border-brand-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="text-xs text-brand-textAlt">
                                <span>Tujuan: <strong class="text-brand-text">{{ $order->recipient_name }}</strong> ({{ $order->city }}, {{ $order->province }})</span>
                                <span class="mx-1.5">•</span>
                                <span>Ongkir {{ $order->courier }}: <strong class="text-brand-text">{{ $order->formatted_shipping_cost }}</strong></span>
                            </div>

                            <div class="flex flex-wrap items-center justify-between sm:justify-end gap-3">
                                <div class="text-right mr-2">
                                    <span class="text-[11px] text-brand-textAlt block">Total Pesanan:</span>
                                    <span class="text-base sm:text-lg font-extrabold text-brand-primary">{{ $order->formatted_total }}</span>
                                </div>

                                @if($order->status === 'pending_payment')
                                    <a href="{{ route('orders.show', $order) }}" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primaryAlt text-white text-xs font-bold shadow-soft">
                                        💳 Bayar Sekarang
                                    </a>
                                @elseif(in_array($order->status, ['shipped', 'delivered']))
                                    <form action="{{ route('orders.received', $order) }}" method="POST" onsubmit="return confirm('Apakah paket J&T sudah Anda terima dengan baik?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-soft cursor-pointer">
                                            ✅ Pesanan Diterima
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2.5 rounded-xl bg-white border border-brand-border hover:bg-brand-cream text-brand-text text-xs font-bold">
                                    Detail &amp; Lacak J&amp;T &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>

