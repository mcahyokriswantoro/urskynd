<x-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 rounded-full border border-amber-200">
                    Katalog Manager
                </span>
                <span class="text-xs text-slate-400 font-mono">Total: {{ $products->total() }} Produk</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Produk & Skincare</h1>
            <p class="text-xs text-slate-500">Tambah, sunting, atur harga, dan kontrol status produk yang tampil di toko publik dan AI Scan.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 bg-[#9A6A52] hover:bg-[#855740] text-white text-xs font-semibold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Tambah Produk Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / brand..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
            </div>

            <div>
                <select name="category_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif (Tampil Publik)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif (Draft)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold rounded-xl transition-all">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'status']))
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold bg-slate-50/70">
                        <th class="py-3.5 px-6">Produk</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Harga Resmi</th>
                        <th class="py-3.5 px-6">Badge / Ukuran</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($p->image_url)
                                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <span class="text-lg">🧴</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $p->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $p->brand }} &bull; <span class="font-mono text-slate-400">/{{ $p->slug }}</span></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700 font-medium">
                                {{ $p->category?->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-xs font-bold text-emerald-700 font-mono">
                                {{ $p->formatted_price }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1">
                                    @if($p->badge)
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-800 rounded border border-amber-200 w-fit">{{ $p->badge }}</span>
                                    @endif
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $p->size ?? '50 ml' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $p->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} uppercase">
                                    {{ $p->status === 'active' ? 'Aktif' : 'Draft' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $p->slug ?? $p->id) }}" target="_blank" class="px-2.5 py-1 text-xs text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors font-medium">
                                        Lihat &nearr;
                                    </a>
                                    <a href="{{ route('admin.products.edit', $p) }}" class="px-2.5 py-1 text-xs text-[#9A6A52] hover:text-white bg-[#9A6A52]/10 hover:bg-[#9A6A52] rounded-lg transition-colors font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 text-xs text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                Tidak ada produk ditemukan. Silakan tambah produk baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
