@php
    $defaultTab = request('tab', $stats['pending_withdrawals_count'] > 0 ? 'withdrawals' : 'sales');
@endphp

<x-admin-layout>
    <div class="space-y-6" x-data="{
        tab: @js($defaultTab),
        selectedPkgPrice: @js((int) ($packages[0]['price'] ?? 185000)),
        selectedPkgName: @js($packages[0]['name'] ?? 'Paket Glowing Starter')
    }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-mono uppercase tracking-widest text-[#9A6A52] font-bold mb-1">Program Kemitraan &amp; Komisi</p>
                <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Mitra Afiliator &amp; Pencairan Komisi</h1>
                <p class="text-xs text-slate-500">Verifikasi penjualan afiliasi, kelola persentase komisi per mitra, dan proses pencairan saldo (withdraw).</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="tab = 'create_sale'" class="px-4 py-2.5 rounded-xl bg-[#9A6A52] hover:bg-[#845843] text-white text-xs font-bold shadow-xs cursor-pointer">
                    + Catat Penjualan Mitra
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Mitra Afiliator</span>
                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-[#9A6A52] flex items-center justify-center text-lg">🤝</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total_affiliates'] }} <span class="text-xs font-normal text-slate-500">Akun</span></p>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1">{{ $stats['active_affiliates'] }} mitra aktif</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Omzet Penjualan Mitra</span>
                    <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">🛍️</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($stats['approved_sales_amount'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-amber-600 font-semibold mt-1">{{ $stats['pending_sales_count'] }} penjualan menunggu verifikasi</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Komisi Diberikan</span>
                    <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">💰</span>
                </div>
                <p class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($stats['total_commission_accrued'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-500 mt-1">Akumulasi komisi dari penjualan valid</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border-2 {{ $stats['pending_withdrawals_count'] > 0 ? 'border-amber-400' : 'border-slate-200' }} shadow-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Pencairan Komisi (WD)</span>
                    <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg">🏦</span>
                </div>
                <p class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($stats['approved_withdrawals_amount'], 0, ',', '.') }}</p>
                <p class="text-[11px] font-bold {{ $stats['pending_withdrawals_count'] > 0 ? 'text-rose-600' : 'text-slate-500' }} mt-1">
                    {{ $stats['pending_withdrawals_count'] }} pengajuan baru (Rp {{ number_format($stats['pending_withdrawals_amount'], 0, ',', '.') }})
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex gap-1.5 overflow-x-auto bg-white p-1.5 rounded-2xl border border-slate-200 shadow-xs">
            <button type="button" @click="tab = 'sales'"
                    :class="tab === 'sales' ? 'bg-[#9A6A52] text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors flex items-center gap-2 cursor-pointer">
                <span>📦 Verifikasi Penjualan &amp; Komisi</span>
                @if($stats['pending_sales_count'] > 0)
                    <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[10px] font-extrabold">{{ $stats['pending_sales_count'] }}</span>
                @endif
            </button>

            <button type="button" @click="tab = 'withdrawals'"
                    :class="tab === 'withdrawals' ? 'bg-[#9A6A52] text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors flex items-center gap-2 cursor-pointer">
                <span>💸 Pencairan Komisi (Withdraw)</span>
                @if($stats['pending_withdrawals_count'] > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">{{ $stats['pending_withdrawals_count'] }}</span>
                @endif
            </button>

            <button type="button" @click="tab = 'affiliates'"
                    :class="tab === 'affiliates' ? 'bg-[#9A6A52] text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors cursor-pointer">
                🤝 Daftar Mitra &amp; Pengaturan Komisi ({{ $stats['total_affiliates'] }})
            </button>

            <button type="button" @click="tab = 'create_sale'"
                    :class="tab === 'create_sale' ? 'bg-[#9A6A52] text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold whitespace-nowrap transition-colors cursor-pointer">
                ➕ Catat Penjualan Manual
            </button>
        </div>

        <!-- TAB 1: VERIFIKASI PENJUALAN & KOMISI -->
        <section x-show="tab === 'sales'" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Transaksi Penjualan Mitra</h2>
                    <p class="text-xs text-slate-500">Klik <strong>Setujui</strong> setelah pembayaran pembeli terkonfirmasi agar komisi otomatis masuk ke saldo siap cair mitra.</p>
                </div>
            </div>

            @if($sales->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">Belum ada data penjualan afiliasi.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase bg-slate-50">
                                <th class="py-3 px-3">Order &amp; Tanggal</th>
                                <th class="py-3 px-3">Mitra / Afiliator</th>
                                <th class="py-3 px-3">Data Pembeli</th>
                                <th class="py-3 px-3">Paket &amp; Nilai</th>
                                <th class="py-3 px-3">Komisi Mitra</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Aksi Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sales as $sale)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3.5 px-3">
                                        <p class="font-mono font-bold text-slate-900">{{ $sale->order_code }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $sale->created_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-slate-900">{{ $sale->affiliate->name ?? 'Mitra Terhapus' }}</p>
                                        <span class="inline-block font-mono text-[10px] px-2 py-0.5 rounded bg-amber-100 text-[#9A6A52] font-bold">
                                            {{ $sale->affiliate->affiliate_code ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-slate-800">{{ $sale->customer_name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $sale->customer_phone }}</p>
                                        @if($sale->notes)
                                            <p class="text-[10px] text-slate-400 italic mt-0.5">"{{ $sale->notes }}"</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-semibold text-slate-800">{{ $sale->package_name }}</p>
                                        <p class="text-xs font-bold text-slate-900">{{ $sale->formatted_sale_amount }}</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="text-sm font-extrabold text-emerald-700">+{{ $sale->formatted_commission_amount }}</p>
                                        <p class="text-[10px] text-slate-500">Rate: {{ rtrim(rtrim(number_format($sale->commission_rate, 2), '0'), '.') }}%</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if($sale->status === 'approved')
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">✅ Disetujui</span>
                                        @elseif($sale->status === 'rejected')
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">✕ Ditolak</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">⏳ Pending</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if($sale->status !== 'approved')
                                                <form action="{{ route('admin.affiliates.sales.status', $sale) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold cursor-pointer">
                                                        ✅ Setujui Komisi
                                                    </button>
                                                </form>
                                            @endif
                                            @if($sale->status !== 'rejected')
                                                <form action="{{ route('admin.affiliates.sales.status', $sale) }}" method="POST" onsubmit="return confirm('Tolak/batalkan komisi penjualan ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[11px] font-bold cursor-pointer">
                                                        ✕ Tolak
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- TAB 2: PENCAIRAN KOMISI (WITHDRAWALS) -->
        <section x-show="tab === 'withdrawals'" style="display: none;" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900">Permintaan Pencairan Komisi (Withdraw)</h2>
                <p class="text-xs text-slate-500">Transfer nominal komisi ke rekening Bank / E-Wallet mitra, lalu klik <strong>Tandai Sudah Ditransfer</strong>.</p>
            </div>

            @if($withdrawals->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">Belum ada pengajuan pencairan komisi dari mitra.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase bg-slate-50">
                                <th class="py-3 px-3">Kode WD &amp; Waktu</th>
                                <th class="py-3 px-3">Mitra / Afiliator</th>
                                <th class="py-3 px-3">Rekening Tujuan Transfer</th>
                                <th class="py-3 px-3">Nominal Pencairan</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Tindakan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($withdrawals as $wd)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3.5 px-3">
                                        <p class="font-mono font-bold text-slate-900">{{ $wd->withdrawal_code }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $wd->created_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <p class="font-bold text-slate-900">{{ $wd->affiliate->name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $wd->affiliate->phone ?? $wd->affiliate->email ?? '' }}</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-800">{{ $wd->bank_name }}</span>
                                        <p class="font-mono font-bold text-slate-900 mt-1">{{ $wd->bank_account_number }}</p>
                                        <p class="text-[11px] text-slate-500">a.n. {{ $wd->bank_account_holder }}</p>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="text-base font-extrabold text-[#9A6A52]">{{ $wd->formatted_amount }}</span>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if($wd->status === 'approved')
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">✅ Sudah Ditransfer</span>
                                            @if($wd->processed_at)
                                                <p class="text-[10px] text-slate-400 mt-1">{{ $wd->processed_at->format('d M Y H:i') }}</p>
                                            @endif
                                        @elseif($wd->status === 'rejected')
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold">✕ Ditolak &amp; Saldo Kembali</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold">⏳ Menunggu Transfer</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if($wd->status === 'pending')
                                            <div class="space-y-2 max-w-xs">
                                                <form action="{{ route('admin.affiliates.withdrawals.status', $wd) }}" method="POST" class="flex flex-col gap-1.5">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="admin_notes" placeholder="Catatan bukti transfer (opsional)" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs">
                                                    <div class="flex items-center gap-1.5">
                                                        <button type="submit" name="status" value="approved" class="flex-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold cursor-pointer">
                                                            ✅ Tandai Sudah Ditransfer
                                                        </button>
                                                        <button type="submit" name="status" value="rejected" onclick="return confirm('Tolak pencairan ini dan kembalikan saldo ke mitra?')" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-[11px] font-bold cursor-pointer">
                                                            ✕ Tolak
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-slate-500">{{ $wd->admin_notes ?: 'Selesai diproses' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <!-- TAB 3: DAFTAR MITRA & PENGATURAN KOMISI -->
        <section x-show="tab === 'affiliates'" style="display: none;" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900">Daftar Akun Mitra / Afiliator &amp; Pengaturan Persentase Komisi</h2>
                <p class="text-xs text-slate-500">Anda dapat menyesuaikan persentase komisi (default 10%), kode referral, rekening, atau status masing-masing mitra.</p>
            </div>

            @if($affiliates->isEmpty())
                <p class="text-xs text-slate-400 py-8 text-center">Belum ada pengguna yang mendaftar sebagai Mitra / Afiliator.</p>
            @else
                <div class="space-y-4">
                    @foreach($affiliates as $mitra)
                        <form action="{{ route('admin.affiliates.update', $mitra) }}" method="POST" class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                                <div class="lg:col-span-3">
                                    <p class="font-bold text-sm text-slate-900">{{ $mitra->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $mitra->email }} • {{ $mitra->phone ?: '-' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-1.5 text-[11px]">
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold">
                                            Saldo Siap Cair: {{ $mitra->formatted_commission_balance }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">
                                            {{ $mitra->approved_sales_count }}/{{ $mitra->total_sales_count }} Terjual
                                        </span>
                                    </div>
                                </div>

                                <div class="lg:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Kode Afiliasi</label>
                                    <input type="text" name="affiliate_code" value="{{ $mitra->affiliate_code }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono uppercase font-bold">
                                </div>

                                <div class="lg:col-span-1">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Komisi (%)</label>
                                    <input type="number" step="0.5" min="1" max="50" name="commission_rate" value="{{ $mitra->commission_rate }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-center">
                                </div>

                                <div class="lg:col-span-4 grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Bank/Wallet</label>
                                        <input type="text" name="bank_name" value="{{ $mitra->bank_name }}" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">No. Rek</label>
                                        <input type="text" name="bank_account_number" value="{{ $mitra->bank_account_number }}" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Atas Nama</label>
                                        <input type="text" name="bank_account_holder" value="{{ $mitra->bank_account_holder }}" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                    </div>
                                </div>

                                <div class="lg:col-span-2 flex items-center gap-2">
                                    <select name="affiliate_status" class="px-2.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold">
                                        <option value="active" @selected($mitra->affiliate_status === 'active')>Aktif</option>
                                        <option value="suspended" @selected($mitra->affiliate_status === 'suspended')>Nonaktif</option>
                                    </select>
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold cursor-pointer">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- TAB 4: CATAT PENJUALAN MITRA MANUAL -->
        <section x-show="tab === 'create_sale'" style="display: none;" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 max-w-2xl">
            <h2 class="text-base font-bold text-slate-900 mb-1">Catat Penjualan Mitra &amp; Tambah Komisi</h2>
            <p class="text-xs text-slate-500 mb-5">Gunakan formulir ini jika Admin ingin menginput langsung penjualan yang berhasil dibawa oleh Mitra / Afiliator.</p>

            @if($affiliates->isEmpty())
                <p class="text-xs text-rose-600">Belum ada akun Mitra / Afiliator yang terdaftar.</p>
            @else
                <form action="{{ route('admin.affiliates.sales.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Mitra / Afiliator *</label>
                        <select name="affiliate_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold">
                            @foreach($affiliates as $mitra)
                                <option value="{{ $mitra->id }}">
                                    {{ $mitra->name }} ({{ $mitra->affiliate_code }} — Komisi {{ rtrim(rtrim(number_format($mitra->commission_rate, 2), '0'), '.') }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Pembeli *</label>
                            <input type="text" name="customer_name" required placeholder="Contoh: Dewi Lestari" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. WhatsApp Pembeli *</label>
                            <input type="text" name="customer_phone" required placeholder="0812xxxxxxx" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Paket / Produk Terjual *</label>
                            <select name="package_name"
                                    x-model="selectedPkgName"
                                    @change="const found = @js($packages).find(p => p.name === $event.target.value); if (found) selectedPkgPrice = found.price;"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold">
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg['name'] }}">{{ $pkg['name'] }} (Rp {{ number_format($pkg['price'] ?? 0, 0, ',', '.') }})</option>
                                @endforeach
                                <option value="Produk Satuan / Custom Order">Produk Satuan / Custom Order</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Total Nilai Transaksi (Rp) *</label>
                            <input type="number" name="sale_amount" x-model="selectedPkgPrice" min="1000" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan (Opsional)</label>
                        <input type="text" name="notes" placeholder="Lunas via QRIS URSKYND" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs">
                    </div>

                    <label class="inline-flex items-center gap-2 text-xs font-bold text-emerald-800 bg-emerald-50 px-3.5 py-2.5 rounded-xl border border-emerald-200">
                        <input type="checkbox" name="auto_approve" value="1" checked class="rounded text-emerald-600">
                        Langsung setujui &amp; masukkan komisi ke Saldo Siap Cair mitra
                    </label>

                    <div class="pt-2">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-[#9A6A52] hover:bg-[#845843] text-white text-xs font-bold shadow-md cursor-pointer">
                            💾 Simpan Penjualan &amp; Komisi Mitra
                        </button>
                    </div>
                </form>
            @endif
        </section>

    </div>
</x-admin-layout>

