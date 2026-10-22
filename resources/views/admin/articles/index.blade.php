<x-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 rounded-full border border-indigo-200">
                    Content & News Manager
                </span>
                <span class="text-xs text-slate-400 font-mono">Total: {{ $articles->total() }} Artikel</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Berita & Edukasi Kulit</h1>
            <p class="text-xs text-slate-500">Tulis artikel baru, riset skincare, tips dermatologi, dan panduan perawatan kulit pengguna.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articles.create') }}" class="px-4 py-2.5 bg-[#9A6A52] hover:bg-[#855740] text-white text-xs font-semibold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Tulis Berita Baru
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
        <form method="GET" action="{{ route('admin.articles.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / ringkasan artikel..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
            </div>

            <div>
                <select name="category" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold rounded-xl transition-all">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('admin.articles.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold bg-slate-50/70">
                        <th class="py-3.5 px-6">Artikel & Judul</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Penulis</th>
                        <th class="py-3.5 px-6">Waktu Tayang</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                        @if($art->image)
                                            <img src="{{ $art->image }}" alt="Cover" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-base">📰</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 line-clamp-1">{{ $art->title }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">/{{ $art->slug }} &bull; {{ $art->read_time }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-700 rounded-md">
                                    {{ $art->category }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                <p class="font-medium text-slate-900">{{ $art->author_name }}</p>
                                <span class="text-[10px] text-slate-400">{{ $art->author_role }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono">
                                @if($art->published_at)
                                    <span class="text-emerald-700 font-semibold">{{ $art->published_at->format('d M Y') }}</span>
                                @else
                                    <span class="text-amber-600 font-semibold">Draft</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('news.show', $art->slug) }}" target="_blank" class="px-2.5 py-1 text-xs text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors font-medium">
                                        Baca &nearr;
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $art) }}" class="px-2.5 py-1 text-xs text-[#9A6A52] hover:text-white bg-[#9A6A52]/10 hover:bg-[#9A6A52] rounded-lg transition-colors font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $art) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');" class="inline">
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
                            <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                Tidak ada artikel ditemukan. Silakan tulis berita baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
