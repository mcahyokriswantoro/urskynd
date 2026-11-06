<x-admin-layout>
<div class="space-y-6" x-data="{ activeTab: '{{ $tab }}' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-mono uppercase tracking-widest text-[#9A6A52] font-bold mb-1">E-Commerce &amp; Ekspedisi J&amp;T Lamongan</p>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Pesanan, Resi J&amp;T &amp; Tarif Ongkir</h1>
            <p class="text-xs text-slate-500">Pantau pembayaran masuk otomatis, kemas barang, input nomor resi J&amp;T Express dari Lamongan, dan atur zona ongkir.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <p class="font-bold">⚠️ Terdapat kesalahan input:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Navigasi Tab Utama -->
    <div class="bg-white rounded-3xl p-2 border border-slate-200 shadow-sm flex flex-wrap gap-2">
        <button type="button" @click="activeTab = 'orders'"
            :class="activeTab === 'orders' ? 'bg-brand-primary text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
            <span>🛍️</span>
            <span>Daftar Pesanan & Pengiriman</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                :class="activeTab === 'orders' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                {{ $stats['total_orders'] }}
            </span>
        </button>

        <button type="button" @click="activeTab = 'shipping'"
            :class="activeTab === 'shipping' ? 'bg-brand-primary text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
            <span>🚚</span>
            <span>Tarif Ongkir J&T (Asal Lamongan)</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                :class="activeTab === 'shipping' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                {{ $shippingZones->count() }} Zona
            </span>
        </button>

        <button type="button" @click="activeTab = 'gateway'"
            :class="activeTab === 'gateway' ? 'bg-brand-primary text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50'"
            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer">
            <span>⚡</span>
            <span>Payment Gateway Otomatis</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: KELOLA PESANAN & PENGIRIMAN J&T     -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'orders'" x-transition class="space-y-6">

        <!-- KPI Ringkasan E-Commerce -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pesanan</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total_orders'] }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Semua transaksi</p>
            </div>
            <div class="bg-amber-50/70 rounded-3xl p-4 border border-amber-200 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Belum Bayar</p>
                <p class="text-2xl font-extrabold text-amber-800 mt-1">{{ $stats['pending_payment'] }}</p>
                <p class="text-[11px] text-amber-700 mt-1">Menunggu pembayaran</p>
            </div>
            <div class="bg-indigo-50/70 rounded-3xl p-4 border border-indigo-200 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Perlu Dikirim</p>
                <p class="text-2xl font-extrabold text-indigo-900 mt-1">{{ $stats['paid_ready'] }}</p>
                <p class="text-[11px] text-indigo-700 mt-1">Lunas / Sedang dikemas</p>
            </div>
            <div class="bg-sky-50/70 rounded-3xl p-4 border border-sky-200 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-sky-700">Dikirim J&T</p>
                <p class="text-2xl font-extrabold text-sky-900 mt-1">{{ $stats['shipped'] }}</p>
                <p class="text-[11px] text-sky-700 mt-1">Dalam perjalanan kurir</p>
            </div>
            <div class="bg-emerald-50/70 rounded-3xl p-4 border border-emerald-200 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Selesai</p>
                <p class="text-2xl font-extrabold text-emerald-900 mt-1">{{ $stats['completed'] }}</p>
                <p class="text-[11px] text-emerald-700 mt-1">Diterima pembeli</p>
            </div>
            <div class="bg-slate-900 text-white rounded-3xl p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Omzet Lunas</p>
                <p class="text-lg font-extrabold text-emerald-400 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                <p class="text-[10px] text-slate-400 mt-1">Ongkir J&T: Rp {{ number_format($stats['total_shipping_collected'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Filter Status ala Shopee Seller Center & Pencarian -->
        <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                @php
                    $filterTabs = [
                        'all' => ['label' => 'Semua', 'count' => $stats['total_orders']],
                        'pending_payment' => ['label' => 'Belum Bayar', 'count' => $stats['pending_payment']],
                        'need_process' => ['label' => 'Perlu Dikemas / Dikirim', 'count' => $stats['paid_ready']],
                        'shipped' => ['label' => 'Dikirim J&T', 'count' => $stats['shipped']],
                        'completed' => ['label' => 'Selesai', 'count' => $stats['completed']],
                        'cancelled' => ['label' => 'Dibatalkan', 'count' => $stats['cancelled']],
                    ];
                @endphp
                @foreach($filterTabs as $key => $ft)
                    <a href="{{ route('admin.orders.index', ['tab' => 'orders', 'status' => $key, 'search' => $search]) }}"
                       class="px-3.5 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-1.5 {{ $statusFilter === $key ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span>{{ $ft['label'] }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === $key ? 'bg-white/20 text-white' : 'bg-white text-slate-700' }}">{{ $ft['count'] }}</span>
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="orders">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <div class="relative flex-1 sm:w-72">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Pesanan, Nama, Kota, Resi J&T..."
                        class="w-full pl-9 pr-4 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:border-brand-primary focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button type="submit" class="px-4 py-2 rounded-2xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt transition-all cursor-pointer">
                    Cari
                </button>
                @if($search !== '')
                    <a href="{{ route('admin.orders.index', ['tab' => 'orders', 'status' => $statusFilter]) }}" class="px-3 py-2 rounded-2xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Daftar Kartu Pesanan -->
        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl p-12 border border-slate-200 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto text-2xl">📦</div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Pesanan pada Filter Ini</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Pesanan pelanggan yang masuk dari halaman produk & keranjang belanja akan tampil secara real-time di sini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    @php
                        $badgeColors = [
                            'amber' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'indigo' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'sky' => 'bg-sky-100 text-sky-800 border-sky-200',
                            'teal' => 'bg-teal-100 text-teal-800 border-teal-200',
                            'rose' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
                        ];
                        $badgeClass = $badgeColors[$order->status_badge_color] ?? $badgeColors['slate'];
                    @endphp

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ openDetail: false, openShipModal: false }">
                        <!-- Header Kartu Pesanan -->
                        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="font-mono text-xs font-extrabold text-slate-900 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                                    {{ $order->order_number }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold border {{ $badgeClass }}">
                                    {{ $order->status_label }}
                                </span>
                                @if($order->payment_status === 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        💳 LUNAS ({{ $order->payment_method ?: 'Payment Gateway' }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ BELUM DIBAYAR ({{ $order->payment_method ?: 'Payment Gateway' }})
                                    </span>
                                @endif

                                @if($order->affiliate)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        🤝 Afiliator: {{ $order->affiliate->name }} ({{ $order->referral_code }})
                                    </span>
                                @endif
                            </div>

                            <div class="text-right text-xs text-slate-500">
                                <span>{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>

                        <!-- Body Kartu Pesanan -->
                        <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            <!-- Kolom 1: Daftar Produk (5 cols) -->
                            <div class="lg:col-span-5 space-y-3">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Item Pesanan ({{ $order->items->sum('quantity') }} pcs • {{ number_format($order->total_weight_grams / 1000, 1, ',', '.') }} kg)</p>
                                <div class="space-y-2.5">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100">
                                            @if($item->product_image)
                                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 bg-white shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-brand-secondary text-brand-primary flex items-center justify-center font-bold text-xs shrink-0">IMG</div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-900 truncate">{{ $item->product_name }}</p>
                                                <p class="text-[11px] text-slate-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-extrabold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Subtotal Produk: <strong>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</strong></span>
                                    <span class="text-slate-500">Ongkir J&T: <strong>{{ $order->shipping_cost == 0 ? 'GRATIS' : 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') }}</strong></span>
                                </div>
                                <div class="flex items-center justify-between bg-slate-900 text-white px-4 py-2.5 rounded-2xl">
                                    <span class="text-xs font-semibold">Total Pembayaran</span>
                                    <span class="text-sm font-extrabold text-emerald-400">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Kolom 2: Info Pengiriman J&T & Penerima (4 cols) -->
                            <div class="lg:col-span-4 space-y-2.5 bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tujuan Pengiriman J&T</span>
                                    <span class="px-2 py-0.5 rounded-lg bg-red-50 text-red-700 font-extrabold text-[10px] border border-red-200">
                                        {{ $order->courier }} - {{ $order->courier_service }}
                                    </span>
                                </div>

                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $order->recipient_name }}</p>
                                    <p class="text-slate-600 font-medium">{{ $order->recipient_phone }}</p>
                                </div>

                                <div class="text-slate-600 leading-relaxed">
                                    <p>{{ $order->full_address }}</p>
                                    <p class="font-bold text-slate-800 mt-0.5">
                                        {{ $order->district ? 'Kec. ' . $order->district . ', ' : '' }}{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
                                    </p>
                                </div>

                                <div class="pt-2 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Zona Tarif Lamongan:</span>
                                        <span class="font-bold text-slate-800">{{ $order->shipping_zone_name ?: 'J&T Reguler' }} ({{ $order->shipping_etd }})</span>
                                    </div>
                                    @if($order->tracking_number)
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-400 block">No. Resi J&T:</span>
                                            <span class="font-mono font-extrabold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200">{{ $order->tracking_number }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if($order->notes)
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px]">
                                        <strong>Catatan Pembeli:</strong> {{ $order->notes }}
                                    </div>
                                @endif
                            </div>

                            <!-- Kolom 3: Aksi Cepat Admin (Pembayaran Diterima, Dikemas, Kirim + Input Resi) (3 cols) -->
                            <div class="lg:col-span-3 space-y-3">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tindakan Cepat Admin</p>

                                <!-- 1. Tombol Pembayaran Diterima -->
                                @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                                            <span>✅</span>
                                            <span>Terima Pembayaran (Lunas)</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- 2. Tombol Sedang Dikemas -->
                                @if(in_array($order->status, ['paid', 'pending_payment']))
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                                            <span>📦</span>
                                            <span>Proses & Kemas Barang</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- 3. Form Langsung Kirim Barang & Input Resi J&T -->
                                @if(!in_array($order->status, ['completed', 'cancelled']))
                                    <div class="p-3.5 rounded-2xl bg-sky-50/80 border border-sky-200 space-y-2.5">
                                        <p class="text-[11px] font-extrabold text-sky-900 flex items-center gap-1.5">
                                            <span>🚚</span>
                                            <span>Kirim via J&T Express</span>
                                        </p>
                                        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="shipped">
                                            <input type="hidden" name="courier" value="{{ $order->courier ?: 'J&T Express' }}">
                                            <div>
                                                <input type="text" name="tracking_number" value="{{ $order->tracking_number }}"
                                                    placeholder="No. Resi J&T (Kosongkan = Auto)"
                                                    class="w-full px-3 py-2 rounded-xl bg-white border border-sky-200 text-xs font-mono focus:border-sky-500 focus:outline-none">
                                            </div>
                                            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition-all cursor-pointer">
                                                {{ $order->status === 'shipped' ? 'Update No. Resi J&T' : 'Kirim Barang Sekarang' }}
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- 4. Paket Sampai / Selesai -->
                                @if($order->status === 'shipped')
                                    <div class="grid grid-cols-2 gap-2">
                                        <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] transition-all cursor-pointer">
                                                📍 Paket Sampai
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-all cursor-pointer">
                                                🎉 Selesai
                                            </button>
                                        </form>
                                    </div>
                                @elseif($order->status === 'delivered')
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all cursor-pointer">
                                            🎉 Tandai Pesanan Selesai
                                        </button>
                                    </form>
                                @endif

                                <!-- Dropdown Ubah Status Manual / Batalkan -->
                                <details class="group text-xs">
                                    <summary class="cursor-pointer text-slate-500 hover:text-slate-800 font-semibold flex items-center justify-between px-2 py-1">
                                        <span>Opsi Status Lainnya / Catatan</span>
                                        <span class="group-open:rotate-180 transition-transform">▼</span>
                                    </summary>
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-2 p-3 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Pilih Status</label>
                                            <select name="status" class="w-full px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs">
                                                <option value="pending_payment" @selected($order->status === 'pending_payment')>Menunggu Pembayaran</option>
                                                <option value="paid" @selected($order->status === 'paid')>Pembayaran Diterima</option>
                                                <option value="processing" @selected($order->status === 'processing')>Sedang Dikemas</option>
                                                <option value="shipped" @selected($order->status === 'shipped')>Dikirim Kurir J&T</option>
                                                <option value="delivered" @selected($order->status === 'delivered')>Paket Telah Sampai</option>
                                                <option value="completed" @selected($order->status === 'completed')>Pesanan Selesai</option>
                                                <option value="cancelled" @selected($order->status === 'cancelled')>Batalkan Pesanan</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Catatan Admin / Kurir</label>
                                            <input type="text" name="admin_notes" value="{{ $order->admin_notes }}" placeholder="Cth: Paket dijemput J&T Lamongan"
                                                class="w-full px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs">
                                        </div>
                                        <button type="submit" class="w-full py-1.5 rounded-xl bg-slate-900 text-white font-bold text-xs cursor-pointer">
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                </details>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ========================================================== -->
    <!-- TAB 2: TARIF ONGKIR J&T EXPRESS (ASAL LAMONGAN & SEKITAR)  -->
    <!-- ========================================================== -->
    <div x-show="activeTab === 'shipping'" x-transition class="space-y-6" style="display: none;">
        <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-3xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="px-3 py-1 rounded-full bg-white/20 text-[11px] font-extrabold uppercase tracking-wider">Gudang Asal: Kab. Lamongan, Jawa Timur</span>
                <h2 class="text-xl font-extrabold mt-1">Pengaturan Zona & Tarif Ongkir J&T Express</h2>
                <p class="text-xs text-rose-100 max-w-2xl">
                    Sesuaikan tarif ongkos kirim per kilogram (kg) mulai dari <strong>Sesama Lamongan</strong>, <strong>Sekitar Lamongan (Gresik, Surabaya, Tuban, Bojonegoro)</strong>, <strong>Jawa Timur Lainnya</strong>, hingga <strong>Seluruh Indonesia</strong>.
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-xs px-4 py-3 rounded-2xl border border-white/20 text-xs">
                <p class="font-bold">💡 Cara Kerja Pencocokan Kota:</p>
                <p class="text-rose-100 text-[11px] mt-0.5">Sistem mencocokkan nama Kota/Kabupaten atau Provinsi yang dipilih pelanggan saat checkout secara otomatis.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Daftar Zona Ongkir yang Ada (8 Cols) -->
            <div class="lg:col-span-8 space-y-4">
                @foreach($shippingZones as $zone)
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5" x-data="{ editing: false }">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-2xl bg-red-50 text-red-600 border border-red-200 flex items-center justify-center font-extrabold text-xs">
                                    J&T
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-extrabold text-slate-900">{{ $zone->name }}</h3>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-bold text-[10px]">{{ $zone->courier }} {{ $zone->service }}</span>
                                        @if($zone->is_default)
                                            <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-800 font-bold text-[10px]">Default Fallback</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Estimasi Tiba: <strong>{{ $zone->etd }}</strong> • Urutan Prioritas: #{{ $zone->sort_order }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-base font-extrabold text-brand-primary">Rp {{ number_format($zone->cost_first_kg, 0, ',', '.') }}<span class="text-xs font-normal text-slate-500">/kg</span></p>
                                    @if($zone->free_shipping_min)
                                        <p class="text-[10px] font-bold text-emerald-600">Gratis Ongkir min. Rp {{ number_format($zone->free_shipping_min, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <button type="button" @click="editing = !editing" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all cursor-pointer">
                                    <span x-text="editing ? 'Tutup' : '✏️ Edit Tarif'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100 text-xs text-slate-600 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Cakupan Kota / Kabupaten:</span>
                                <span class="font-medium text-slate-700">{{ $zone->match_cities ?: '(Semua kota di provinsi terkait)' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Cakupan Provinsi:</span>
                                <span class="font-medium text-slate-700">{{ $zone->match_provinces ?: '(Sesuai daftar kota / seluruh Indonesia)' }}</span>
                            </div>
                        </div>

                        <!-- Form Edit Zona -->
                        <div x-show="editing" x-transition class="mt-4 pt-4 border-t border-slate-200">
                            <form action="{{ route('admin.shipping-zones.update', $zone) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Zona Area</label>
                                        <input type="text" name="name" value="{{ $zone->name }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Estimasi Hari (ETD)</label>
                                        <input type="text" name="etd" value="{{ $zone->etd }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kurir</label>
                                        <input type="text" name="courier" value="{{ $zone->courier }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Layanan</label>
                                        <input type="text" name="service" value="{{ $zone->service }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tarif 1 Kg Pertama (Rp)</label>
                                        <input type="number" name="cost_first_kg" value="{{ $zone->cost_first_kg }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Tarif Per Kg Berikutnya (Rp)</label>
                                        <input type="number" name="cost_next_kg" value="{{ $zone->cost_next_kg }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kata Kunci Kota/Kecamatan (Pisahkan koma)</label>
                                        <textarea name="match_cities" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $zone->match_cities }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kata Kunci Provinsi (Pisahkan koma)</label>
                                        <textarea name="match_provinces" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">{{ $zone->match_provinces }}</textarea>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Min. Belanja Gratis Ongkir (Rp)</label>
                                        <input type="number" name="free_shipping_min" value="{{ $zone->free_shipping_min }}" placeholder="Kosongkan jika tidak ada" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Urutan Pencocokan</label>
                                        <input type="number" name="sort_order" value="{{ $zone->sort_order }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                                    </div>
                                    <div class="flex items-center gap-4 pb-2">
                                        <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                            <input type="checkbox" name="is_active" value="1" @checked($zone->is_active) class="rounded border-slate-300">
                                            <span>Aktif</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                            <input type="checkbox" name="is_default" value="1" @checked($zone->is_default) class="rounded border-slate-300">
                                            <span>Default</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-primaryAlt transition-all cursor-pointer">
                                        💾 Simpan Tarif Zona
                                    </button>
                                </div>
                            </form>

                            <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" class="mt-2 text-right" onsubmit="return confirm('Hapus zona ongkir ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">
                                    Hapus Zona Ini
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Form Tambah Zona Baru (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">➕ Tambah Zona Tarif Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Buat tarif khusus kecamatan/kota tertentu dari Lamongan.</p>
                </div>

                <form action="{{ route('admin.shipping-zones.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Zona Pengiriman</label>
                        <input type="text" name="name" placeholder="Cth: Sekitar Babat & Paciran" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Kurir</label>
                            <input type="text" name="courier" value="J&T Express" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Kode Layanan</label>
                            <input type="text" name="service" value="EZ (Reguler)" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Tarif 1 Kg Pertama (Rp)</label>
                            <input type="number" name="cost_first_kg" value="8000" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Tarif Kg Berikutnya (Rp)</label>
                            <input type="number" name="cost_next_kg" value="8000" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Estimasi Hari (ETD)</label>
                            <input type="text" name="etd" value="1-2 Hari" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Min. Gratis Ongkir (Rp)</label>
                            <input type="number" name="free_shipping_min" placeholder="Opsional" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Daftar Kota / Kecamatan (Pisahkan koma)</label>
                        <textarea name="match_cities" rows="2" placeholder="lamongan, babat, sukodadi, paciran" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Daftar Provinsi (Pisahkan koma)</label>
                        <input type="text" name="match_provinces" placeholder="jawa timur" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>

                    <input type="hidden" name="is_active" value="1">
                    <button type="submit" class="w-full py-2.5 rounded-2xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-primaryAlt transition-all shadow-sm cursor-pointer">
                        ➕ Tambah Zona Ongkir J&T
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- TAB 3: PENGATURAN PAYMENT GATEWAY OTOMATIS                 -->
    <!-- ========================================================== -->
    <div x-show="activeTab === 'gateway'" x-transition class="space-y-6" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 lg:p-8 max-w-4xl">
            <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-extrabold">
                        ⚡ Pembayaran Otomatis Terintegrasi
                    </span>
                    <h2 class="text-lg font-extrabold text-slate-900 mt-2">Konfigurasi Payment Gateway (QRIS, Virtual Account, E-Wallet)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Saat pelanggan selesai membayar, status pesanan otomatis berubah menjadi <strong>Pembayaran Diterima (Lunas)</strong> dan siap dikemas.
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.orders.gateway.update') }}" method="POST" class="space-y-5 pt-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Provider Payment Gateway</label>
                        <select name="provider" class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold bg-slate-50">
                            <option value="builtin" @selected(($gatewaySettings['provider'] ?? 'builtin') === 'builtin')>⚡ URSKYND Instant Gateway (QRIS/VA Otomatis)</option>
                            <option value="midtrans" @selected(($gatewaySettings['provider'] ?? '') === 'midtrans')>Midtrans Snap (QRIS, BCA/BRI/Mandiri VA, GoPay)</option>
                            <option value="xendit" @selected(($gatewaySettings['provider'] ?? '') === 'xendit')>Xendit Checkout (QRIS, VA, OVO, Dana, ShopeePay)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Environment Mode</label>
                        <select name="mode" class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold bg-slate-50">
                            <option value="sandbox" @selected(($gatewaySettings['mode'] ?? 'sandbox') === 'sandbox')>Sandbox (Testing / Simulasi)</option>
                            <option value="production" @selected(($gatewaySettings['mode'] ?? '') === 'production')>Production (Transaksi Live)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Kota Gudang Pengiriman Asal</label>
                        <input type="text" name="origin_city" value="{{ $gatewaySettings['origin_city'] ?? 'Kab. Lamongan, Jawa Timur' }}" required
                            class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold">
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Kredensial API Midtrans / Xendit (Opsional jika menggunakan Instant Gateway)</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Midtrans Client Key</label>
                            <input type="text" name="midtrans_client_key" value="{{ $gatewaySettings['midtrans_client_key'] ?? '' }}"
                                placeholder="SB-Mid-client-xxxxxxxx" class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Midtrans Server Key</label>
                            <input type="password" name="midtrans_server_key" value="{{ $gatewaySettings['midtrans_server_key'] ?? '' }}"
                                placeholder="SB-Mid-server-xxxxxxxx" class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Xendit Secret API Key</label>
                        <input type="password" name="xendit_secret_key" value="{{ $gatewaySettings['xendit_secret_key'] ?? '' }}"
                            placeholder="xnd_development_xxxxxxxx" class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-mono">
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                    <input type="checkbox" name="enable_auto_simulator" id="enable_auto_simulator" value="1"
                        @checked($gatewaySettings['enable_auto_simulator'] ?? true) class="mt-1 rounded border-emerald-400">
                    <label for="enable_auto_simulator" class="text-xs text-emerald-900">
                        <span class="font-extrabold block">Aktifkan Konfirmasi Pembayaran Otomatis Instan di Halaman Tagihan Pelanggan</span>
                        <span class="text-emerald-700 text-[11px]">Memungkinkan pelanggan menyelesaikan pembayaran QRIS / Virtual Account secara langsung dan status pesanan otomatis berubah menjadi <strong>Pembayaran Diterima</strong> tanpa perlu verifikasi manual.</span>
                    </label>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 text-white text-xs space-y-1">
                    <p class="font-bold text-slate-300">Webhook Notification URL (Untuk Dashboard Midtrans / Xendit):</p>
                    <p class="font-mono text-emerald-400 select-all">{{ route('payment.webhook') }}</p>
                </div>

                <button type="submit" class="px-6 py-3 rounded-2xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-primaryAlt transition-all shadow-sm cursor-pointer">
                    💾 Simpan Pengaturan Payment Gateway
                </button>
            </form>
        </div>
    </div>

</div>
</x-admin-layout>

