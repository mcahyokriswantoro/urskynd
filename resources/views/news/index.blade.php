<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4">
                Edukasi & Jurnal Klinis
            </span>
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-brand-text mb-4">
                Berita & Edukasi Skincare
            </h1>
            <p class="text-brand-textAlt text-sm md:text-base leading-relaxed">
                Panduan ilmiah seputar kesehatan kulit, kandungan aktif skincare, serta tips perawatan harian yang ditulis dan ditinjau oleh dokter spesialis.
            </p>
        </div>

        <!-- Search & Category Filters -->
        <div class="mb-10 space-y-4">
            <form action="{{ route('news.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-textAlt">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel seputar jerawat, skin barrier, retinol, dsb..." class="w-full pl-11 pr-4 py-3 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>
                <button type="submit" class="px-6 py-3 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt transition-all shadow-xs text-sm">
                    Cari Artikel
                </button>
            </form>

            <!-- Categories -->
            <div class="flex overflow-x-auto hide-scrollbar gap-2 pb-2">
                <a href="{{ route('news.index', ['category' => 'all', 'search' => request('search')]) }}" 
                   class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $currentCategory === 'all' ? 'bg-brand-text text-white shadow-sm' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
                    Semua Topik
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('news.index', ['category' => $cat, 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all {{ $currentCategory === $cat ? 'bg-brand-text text-white shadow-sm' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Featured Article (if present) -->
        @if($featuredArticle)
            <div class="bg-white rounded-3xl border border-brand-border overflow-hidden shadow-soft-lg hover:shadow-soft-xl transition-all mb-12">
                <div class="grid grid-cols-1 lg:grid-cols-12">
                    <div class="lg:col-span-5 bg-brand-cream/60 p-8 flex flex-col justify-center relative overflow-hidden">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-brand-cream border border-brand-border/60">
                            <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover" style="object-position: 8% 85%; transform: scale(2.5);">
                        </div>
                    </div>
                    <div class="lg:col-span-7 p-6 md:p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-brand-primary/10 text-brand-primary text-xs font-bold rounded-full">
                                    ⭐ Artikel Utama • {{ $featuredArticle->category }}
                                </span>
                                <span class="text-xs text-brand-textAlt">{{ $featuredArticle->read_time }}</span>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-text mb-3 leading-snug">
                                <a href="{{ route('news.show', $featuredArticle->slug) }}" class="hover:text-brand-primary transition-colors">
                                    {{ $featuredArticle->title }}
                                </a>
                            </h2>
                            <p class="text-sm text-brand-textAlt leading-relaxed mb-6">
                                {{ $featuredArticle->excerpt }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-brand-border/40 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-brand-text">{{ $featuredArticle->author_name }}</p>
                                <p class="text-[11px] text-brand-textAlt">{{ $featuredArticle->author_role }}</p>
                            </div>
                            <a href="{{ route('news.show', $featuredArticle->slug) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-brand-primary text-white font-bold rounded-xl text-xs hover:bg-brand-primaryAlt transition-all shadow-xs">
                                <span>Baca Artikel</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Articles Grid -->
        @if($articles->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl border border-brand-border">
                <div class="w-16 h-16 bg-brand-cream rounded-full flex items-center justify-center text-brand-primary mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-brand-text mb-2">Artikel Tidak Ditemukan</h3>
                <p class="text-sm text-brand-textAlt">Coba gunakan kata kunci lain atau pilih kategori topik yang berbeda.</p>
                <a href="{{ route('news.index') }}" class="mt-4 inline-block text-brand-primary font-bold text-sm hover:underline">Reset Filter</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach($articles as $article)
                    <article class="bg-white rounded-3xl border border-brand-border overflow-hidden hover:shadow-soft-lg hover:border-brand-primary/40 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="aspect-[16/9] bg-brand-cream/50 overflow-hidden relative border-b border-brand-border/40">
                                @php
                                    $bgPos = '20% 80%';
                                    if ($article->category === 'Kandungan Aktif') $bgPos = '8% 85%';
                                    elseif ($article->category === 'Tips & Perawatan') $bgPos = '22% 85%';
                                    elseif ($article->category === 'Tren & Riset') $bgPos = '35% 85%';
                                @endphp
                                <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" style="object-position: {{ $bgPos }}; transform: scale(2.8);">
                                <span class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-md text-brand-primary text-xs font-bold rounded-full shadow-xs">
                                    {{ $article->category }}
                                </span>
                            </div>

                            <div class="p-6">
                                <div class="flex items-center justify-between text-xs text-brand-textAlt mb-2.5">
                                    <span>{{ $article->published_at ? $article->published_at->format('d M Y') : 'Terbaru' }}</span>
                                    <span>{{ $article->read_time }}</span>
                                </div>
                                <h3 class="text-lg font-serif font-bold text-brand-text mb-2 leading-snug line-clamp-2 group-hover:text-brand-primary transition-colors">
                                    <a href="{{ route('news.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-brand-textAlt leading-relaxed line-clamp-3">
                                    {{ $article->excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="p-6 pt-0 border-t border-brand-border/40 mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-brand-text">{{ $article->author_name }}</p>
                                <p class="text-[10px] text-brand-textAlt">{{ $article->author_role }}</p>
                            </div>
                            <a href="{{ route('news.show', $article->slug) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-primary hover:text-brand-primaryAlt">
                                <span>Baca</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @endif

    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-dynamic-component>
