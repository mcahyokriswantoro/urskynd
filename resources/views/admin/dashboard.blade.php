<x-admin-layout>
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                    Live System Active
                </span>
                <span class="text-xs text-slate-400 font-mono">Laravel 11 &bull; AI Engine Ready &bull; Official Store Synced</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Dashboard Utama Administrator</h1>
            <p class="text-xs text-slate-500">Ringkasan performa sistem, katalog produk resmi URSKYND, artikel edukasi, konsultasi, dan analisis AI.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('products.index') }}" target="_blank" class="px-3.5 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#9A6A52]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Lihat Web Publik
            </a>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-slate-900 text-white hover:bg-slate-800 text-xs font-semibold rounded-xl transition-all shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Refresh Data
            </a>
        </div>
    </div>

    <!-- 6 Core KPI Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Total Users -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pengguna</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalUsers) }}</h3>
            <p class="text-[10px] text-emerald-600 font-medium mt-1">Aktif & Terdaftar</p>
        </div>

        <!-- Total AI Analyses -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Skin Scan AI</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalAnalyses) }}</h3>
            <p class="text-[10px] text-slate-500 font-medium mt-1">Avg: <strong class="text-slate-900">{{ $avgSkinScore }}/100</strong></p>
        </div>

        <!-- Total Products (Active Official) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Produk Resmi</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalProducts) }}</h3>
            <p class="text-[10px] text-emerald-600 font-medium mt-1">Urskynd Official</p>
        </div>

        <!-- Total Articles & Berita -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Artikel & Berita</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalArticles) }}</h3>
            <p class="text-[10px] text-indigo-600 font-medium mt-1">Edukasi & Riset</p>
        </div>

        <!-- Total Journals -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Skin Journal</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalJournals) }}</h3>
            <p class="text-[10px] text-slate-500 font-medium mt-1">Catatan Harian</p>
        </div>

        <!-- Total Reward Points -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Poin Rewards</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-xl sm:text-2xl font-serif font-bold text-slate-900">{{ number_format($totalPoints) }}</h3>
            <p class="text-[10px] text-slate-500 font-medium mt-1">Gamifikasi Pts</p>
        </div>
    </div>

    <!-- Analytics Charts & User Demographics Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- 7-Day Activity Trends Chart -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-serif font-bold text-slate-900 text-base">Tren Aktivitas Sistem (7 Hari Terakhir)</h3>
                    <p class="text-xs text-slate-500">Volume pemindaian Skin Check AI dan entri Skin Journal</p>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="adminTrendChart"></canvas>
            </div>
        </div>

        <!-- Skin Type Distribution -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-serif font-bold text-slate-900 text-base mb-1">Distribusi Tipe Kulit Pengguna</h3>
                <p class="text-xs text-slate-500 mb-4">Profil jenis kulit terdaftar di sistem</p>

                <div class="space-y-3">
                    @foreach($skinTypes as $st)
                        @php
                            $percentage = $totalUsers > 0 ? round(($st->users_count / $totalUsers) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">{{ $st->name }}</span>
                                <span class="text-slate-900">{{ $st->users_count }} user ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#9A6A52] rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 flex justify-between items-center">
                <span>Top Keluhan Kulit:</span>
                <span class="font-bold text-slate-800">{{ $skinConcerns->first()?->name ?? 'Acne' }}</span>
            </div>
        </div>
    </div>

    <!-- Section: Manajemen Pengguna -->
    <div id="users-section" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-lg font-serif font-bold text-slate-900">Daftar Pengguna Terdaftar</h2>
                <p class="text-xs text-slate-500">Seluruh akun pengguna aplikasi URSKYND</p>
            </div>
            <span class="text-xs font-semibold bg-white border border-slate-200 px-3 py-1.5 rounded-xl text-slate-700">
                Menampilkan {{ $recentUsers->count() }} Pengguna Terbaru
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold bg-slate-50/70">
                        <th class="py-3 px-6">Pengguna</th>
                        <th class="py-3 px-6">Kontak & Alamat</th>
                        <th class="py-3 px-6">Tipe Kulit</th>
                        <th class="py-3 px-6">Poin</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Tgl Gabung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUsers as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-full border border-slate-200 object-cover shrink-0">
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600">
                                <p class="font-mono text-slate-800">{{ $user->phone ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400 truncate max-w-xs">{{ $user->address ?? 'Alamat belum diatur' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-2.5 py-1 bg-slate-100 border border-slate-200 text-slate-700 rounded-lg text-xs font-semibold">
                                    {{ $user->profile?->skinType?->name ?? 'Belum Set' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-bold text-[#9A6A52]">
                                {{ number_format($user->points) }} pts
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada pengguna terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Log Analisis Kulit AI & Produk -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Log Analisis Kulit AI -->
        <div id="analyses-section" class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="font-serif font-bold text-slate-900 text-base">Log Pemindaian AI Terbaru</h3>
                    <p class="text-xs text-slate-500">Hasil pemindaian kondisi kulit pengguna</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentAnalyses as $analysis)
                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                @if($analysis->image_url)
                                    <img src="{{ $analysis->image_url }}" alt="Scan" class="w-full h-full object-cover">
                                @else
                                    <span class="text-lg">✨</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $analysis->user?->name ?? 'Guest/Deleted' }}</p>
                                <p class="text-[11px] text-slate-500">
                                    Skin Age: <strong>{{ $analysis->estimated_skin_age }} Thn</strong> &bull; {{ $analysis->skinType?->name ?? 'Normal' }}
                                </p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $analysis->analysis_date?->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold {{ $analysis->overall_score >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($analysis->overall_score >= 60 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                Score: {{ $analysis->overall_score }}/100
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">Belum ada rekaman analisis kulit.</div>
                @endforelse
            </div>
        </div>

        <!-- Katalog Produk Terdaftar (Urskynd Official) -->
        <div id="products-section" class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="font-serif font-bold text-slate-900 text-base">Katalog Produk Resmi</h3>
                        <p class="text-xs text-slate-500">3 Produk Aktif URSKYND dengan harga resmi</p>
                    </div>
                    <a href="{{ route('products.index') }}" target="_blank" class="text-xs text-[#9A6A52] hover:underline font-semibold flex items-center gap-1">
                        Katalog Publik &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                    @if($p->image_url)
                                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <span class="text-base">🧴</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $p->name }}</p>
                                        @if($p->badge)
                                            <span class="px-1.5 py-0.2 text-[9px] font-bold rounded bg-amber-100 text-amber-800 shrink-0">{{ $p->badge }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500">{{ $p->brand }} &bull; <span class="font-semibold text-[#9A6A52]">{{ $p->category?->name ?? 'Skincare' }}</span></p>
                                    <p class="text-xs font-bold text-emerald-700 mt-0.5">{{ $p->formatted_price ?? 'Rp ' . number_format($p->price ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $p->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} uppercase block mb-1">
                                    {{ $p->status === 'active' ? 'Aktif' : 'Draft' }}
                                </span>
                                <a href="{{ route('products.show', $p->slug ?? $p->id) }}" target="_blank" class="text-[10px] font-medium text-slate-400 hover:text-[#9A6A52]">
                                    Lihat &nearr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">Belum ada produk di database.</div>
                    @endforelse
                </div>
            </div>

            <div class="p-3 bg-slate-50/70 border-t border-slate-100 text-[11px] text-slate-500 flex justify-between items-center">
                <span>Harga & Produk tersinkronisasi dengan urskynd.com</span>
                <span class="font-mono text-emerald-600 font-bold">100% Synced</span>
            </div>
        </div>
    </div>

    <!-- Section: Berita, Edukasi & Konsultasi Publik -->
    <div id="articles-section" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between sm:items-center gap-2 bg-slate-50/50">
            <div>
                <h3 class="font-serif font-bold text-slate-900 text-base">Artikel Edukasi Kulit & Berita Terkini</h3>
                <p class="text-xs text-slate-500">Materi edukasi sains kulit dan panduan skincare untuk pengguna</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('consultation.index') }}" target="_blank" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold border border-emerald-200 flex items-center gap-1 transition-colors">
                    <span>🩺</span> Halaman Konsultasi
                </a>
                <a href="{{ route('news.index') }}" target="_blank" class="text-xs px-3 py-1.5 rounded-lg bg-[#9A6A52]/10 text-[#9A6A52] hover:bg-[#9A6A52]/20 font-semibold border border-[#9A6A52]/20 flex items-center gap-1 transition-colors">
                    Lihat Semua Berita &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-5">
            @forelse($articles as $art)
                <div class="border border-slate-200 rounded-xl p-4 flex flex-col justify-between hover:border-[#9A6A52]/40 hover:shadow-xs transition-all bg-white">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                {{ $art->category }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $art->read_time_minutes }} mnt baca</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 line-clamp-2 mb-1.5">
                            {{ $art->title }}
                        </h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2">
                            {{ $art->excerpt }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                        <span class="text-slate-400 font-mono">{{ $art->published_at ? $art->published_at->format('d M Y') : 'Published' }}</span>
                        <a href="{{ route('news.show', $art->slug) }}" target="_blank" class="font-semibold text-[#9A6A52] hover:underline flex items-center gap-1">
                            Baca &nearr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 text-xs">Belum ada artikel terbit.</div>
            @endforelse
        </div>
    </div>

    <!-- Section: Log Jurnal & Transaksi Poin -->
    <div id="activity-section" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-serif font-bold text-slate-900 text-base">Log Transaksi Poin & Gamifikasi</h3>
                <p class="text-xs text-slate-500">Catatan perolehan poin rewards oleh seluruh pengguna</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold bg-slate-50/70">
                        <th class="py-3 px-6">Pengguna</th>
                        <th class="py-3 px-6">Aktivitas / Deskripsi</th>
                        <th class="py-3 px-6">Jumlah Poin</th>
                        <th class="py-3 px-6">Waktu Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentPoints as $pt)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-800 text-xs">{{ $pt->user?->name ?? 'User' }}</td>
                            <td class="py-3.5 px-6 text-xs text-slate-600">{{ $pt->description }}</td>
                            <td class="py-3.5 px-6 text-xs font-bold {{ $pt->points > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $pt->points > 0 ? '+' : '' }}{{ $pt->points }} Pts
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">{{ $pt->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 text-xs">Belum ada log transaksi poin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script for Admin Chart -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('adminTrendChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartDates),
                        datasets: [
                            {
                                label: 'Skin Checks (Analisis AI)',
                                data: @json($chartAnalyses),
                                backgroundColor: '#9A6A52',
                                borderRadius: 6,
                            },
                            {
                                label: 'Skin Journals Tercatat',
                                data: @json($chartJournals),
                                backgroundColor: '#CBD5E1',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { boxWidth: 12, font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-admin-layout>
