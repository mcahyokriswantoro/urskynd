<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <!-- Header -->
        <div class="mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-1 block">Anti-Aging &amp; Brightening</span>
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-brand-text mb-2">Produk URSKYND</h1>
            <p class="text-sm text-brand-textAlt">
                Cari dan saring produk berdasarkan kategori. Klik produk untuk melihat detail.
                <span class="font-bold text-brand-primary">({{ $products->total() }} ditampilkan)</span>
            </p>
        </div>

        <!-- Search & Filter -->
        <div class="mb-8 space-y-4">
            <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-textAlt">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="w-full pl-11 pr-4 py-3 border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 rounded-xl text-sm shadow-xs">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>
                <button type="submit" class="px-6 py-3 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt transition-all shadow-xs text-sm">
                    Cari Produk
                </button>
            </form>

            <!-- Category Pills -->
            <div class="flex overflow-x-auto hide-scrollbar gap-2 pb-2">
                <a href="{{ route('products.index', ['category' => 'all', 'search' => request('search')]) }}" 
                   class="px-4 py-2 whitespace-nowrap rounded-full text-xs font-bold transition-all {{ $currentCategory === 'all' ? 'bg-brand-text text-white shadow-sm' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
                    Semua Kategori
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug, 'search' => request('search')]) }}" 
                       class="px-4 py-2 whitespace-nowrap rounded-full text-xs font-bold transition-all {{ $currentCategory === $category->slug ? 'bg-brand-text text-white shadow-sm' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl border border-brand-border">
                <div class="w-16 h-16 bg-brand-cream rounded-full flex items-center justify-center text-brand-primary mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-brand-text mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-xs text-brand-textAlt">Tidak ada produk yang cocok dengan pencarianmu.</p>
                <a href="{{ route('products.index') }}" class="mt-4 inline-block text-brand-primary font-bold text-xs hover:underline">Reset Pencarian</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="group bg-white rounded-3xl border border-brand-border overflow-hidden hover:shadow-soft-xl hover:border-brand-primary/40 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Product Image & Badge -->
                            <a href="{{ route('products.show', $product) }}" class="aspect-square bg-gradient-to-br from-brand-cream/80 to-brand-border/40 p-6 flex items-center justify-center relative overflow-hidden block">
                                @if($product->image)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500 shadow-xs">
                                @else
                                    <div class="w-full h-full overflow-hidden flex items-center justify-center rounded-2xl bg-brand-cream">
                                        <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="object-position: 8% 85%; transform: scale(3.5);">
                                    </div>
                                @endif
                                
                                <!-- Official Badge -->
                                <span class="absolute top-3 left-3 px-3 py-1 bg-white/95 backdrop-blur-md text-brand-primary text-[11px] font-bold rounded-full shadow-xs border border-brand-border/40">
                                    {{ $product->badge }}
                                </span>

                                <span class="absolute top-3 right-3 px-2.5 py-1 bg-brand-primary/10 text-brand-primary text-[10px] font-bold rounded-full">
                                    {{ $product->category->name ?? 'Skincare' }}
                                </span>
                            </a>

                            <div class="p-6">
                                <p class="text-[11px] uppercase font-bold tracking-wider text-brand-primary mb-1">{{ $product->category->name ?? 'Skincare' }}</p>
                                <h3 class="font-bold text-brand-text text-base mb-2 leading-snug group-hover:text-brand-primary transition-colors">
                                    <a href="{{ route('products.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="text-xs text-brand-textAlt leading-relaxed mb-4 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                                <div class="flex items-center gap-1.5 text-xs text-brand-textAlt">
                                    <span class="text-amber-500">★</span>
                                    <span class="font-semibold text-brand-text">{{ $product->rating }}</span>
                                    <span>•</span>
                                    <span class="text-[11px] bg-brand-cream border border-brand-border/60 px-2 py-0.5 rounded-md">BPOM</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <div class="pt-4 border-t border-brand-border/40 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] text-brand-textAlt uppercase tracking-wider font-semibold">Harga</p>
                                    <p class="text-lg font-bold text-brand-primary">{{ $product->formatted_price }}</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="px-3 py-2 bg-brand-cream border border-brand-border text-brand-text rounded-xl text-xs font-bold hover:bg-brand-border/40 transition-all">
                                        Detail
                                    </a>

                                    <a href="{{ route('products.show', ['product' => $product, 'checkout' => 1]) }}" class="px-4 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primaryAlt transition-all shadow-xs">
                                        Beli Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        @endif

    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-dynamic-component>
