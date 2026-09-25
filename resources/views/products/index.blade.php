<x-app-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-serif font-bold text-brand-text mb-2">Katalog Skincare</h1>
        <p class="text-sm text-brand-textAlt">Temukan produk yang paling cocok untuk kebutuhan kulitmu.</p>
    </div>

    <!-- Search & Filter -->
    <div class="mb-8 space-y-4">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-brand-textAlt">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk atau brand..." class="w-full pl-10 pr-4 py-3 border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-sm">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </div>
            <button type="submit" class="px-6 py-3 bg-brand-primary text-white font-medium rounded-xl hover:bg-brand-primaryAlt transition-all shadow-sm">
                Cari
            </button>
        </form>

        <!-- Category Pills -->
        <div class="flex overflow-x-auto hide-scrollbar gap-2 pb-2">
            <a href="{{ route('products.index', ['category' => 'all', 'search' => request('search')]) }}" 
               class="px-4 py-2 whitespace-nowrap rounded-full text-sm font-medium transition-all {{ $currentCategory === 'all' ? 'bg-brand-text text-white shadow-md' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
                Semua Produk
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug, 'search' => request('search')]) }}" 
                   class="px-4 py-2 whitespace-nowrap rounded-full text-sm font-medium transition-all {{ $currentCategory === $category->slug ? 'bg-brand-text text-white shadow-md' : 'bg-white border border-brand-border text-brand-text hover:border-brand-primary/50' }}">
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
            <p class="text-brand-textAlt">Coba gunakan kata kunci pencarian atau kategori yang berbeda.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-brand-primary font-medium hover:underline">Reset Pencarian</a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
                <a href="{{ route('products.show', $product) }}" class="group block bg-white rounded-2xl border border-brand-border overflow-hidden hover:shadow-soft-lg hover:border-brand-primary/30 transition-all duration-300">
                    <div class="aspect-square bg-brand-cream/30 p-6 flex items-center justify-center relative">
                        @if($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                        @else
                            @php
                                $bgPos = '20% 80%';
                                if ($product->category?->name === 'Serum') $bgPos = '8% 85%';
                                elseif ($product->category?->name === 'Cleanser') $bgPos = '22% 85%';
                                elseif ($product->category?->name === 'Sunscreen') $bgPos = '35% 85%';
                            @endphp
                            <div class="w-full h-full overflow-hidden flex items-center justify-center rounded-xl">
                                <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="object-position: {{ $bgPos }}; transform: scale(3.5);">
                            </div>
                        @endif
                        
                        <!-- AI Match Badge (Mockup feature) -->
                        <div class="absolute top-3 right-3 bg-brand-success text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Match
                        </div>
                    </div>
                    <div class="p-4 border-t border-brand-border/50">
                        <p class="text-[10px] uppercase tracking-wider font-bold text-brand-primary mb-1">{{ $product->brand }}</p>
                        <h3 class="font-bold text-brand-text text-sm mb-1 line-clamp-2 leading-tight group-hover:text-brand-primary transition-colors">{{ $product->name }}</h3>
                        <p class="text-xs text-brand-textAlt">{{ $product->category->name ?? 'Uncategorized' }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</x-app-layout>
