<x-app-layout>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="p-2 bg-white rounded-full shadow-sm text-brand-textAlt hover:text-brand-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-xl font-serif font-bold text-brand-text">Detail Produk</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div class="bg-white rounded-3xl border border-brand-border p-8 flex items-center justify-center aspect-square relative shadow-soft">
            @if($product->image)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain">
            @else
                <svg class="w-32 h-32 text-brand-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            @endif

            <!-- AI Match Badge -->
            <div class="absolute top-4 right-4 bg-brand-success text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Sangat Cocok Untukmu
            </div>
        </div>

        <!-- Product Info -->
        <div class="flex flex-col">
            <p class="text-sm uppercase tracking-widest font-bold text-brand-primary mb-2">{{ $product->brand }}</p>
            <h1 class="text-3xl font-serif font-bold text-brand-text mb-4">{{ $product->name }}</h1>
            
            <div class="flex flex-wrap gap-2 mb-6">
                <span class="px-3 py-1 bg-brand-cream border border-brand-border rounded-full text-xs font-medium text-brand-text">
                    {{ $product->category->name ?? 'Skincare' }}
                </span>
                <span class="px-3 py-1 bg-brand-cream border border-brand-border rounded-full text-xs font-medium text-brand-text">
                    {{ $product->skin_type_recommended ?? 'Semua Tipe Kulit' }}
                </span>
            </div>

            <div class="prose prose-sm text-brand-textAlt mb-8">
                <p>{{ $product->description }}</p>
            </div>

            @if($product->ingredients)
                <div class="mb-8">
                    <h3 class="font-bold text-brand-text text-sm mb-2 uppercase tracking-wider">Key Ingredients</h3>
                    <p class="text-sm text-brand-textAlt leading-relaxed">{{ $product->ingredients }}</p>
                </div>
            @endif

            <div class="mt-auto pt-6 border-t border-brand-border/50">
                @if(session('success'))
                    <div class="mb-4 text-sm font-medium text-brand-success bg-brand-success/10 p-3 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if($isInCollection)
                    <a href="{{ route('routines.index') }}" class="inline-flex w-full sm:w-auto px-8 py-4 bg-brand-cream border border-brand-border text-brand-text font-medium rounded-xl hover:bg-brand-border/50 transition-all shadow-sm items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-brand-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Sudah di Rak Skincare
                    </a>
                @else
                    <form action="{{ route('products.add-to-collection', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-brand-text text-white font-medium rounded-xl hover:bg-black transition-all shadow-md flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah ke Rak Skincare
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Similar Products -->
    @if($similarProducts->isNotEmpty())
        <div class="border-t border-brand-border/50 pt-12 mt-12">
            <h2 class="text-xl font-serif font-bold text-brand-text mb-6">Produk Serupa</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($similarProducts as $similar)
                    <a href="{{ route('products.show', $similar) }}" class="group block bg-white rounded-xl border border-brand-border overflow-hidden hover:shadow-soft hover:border-brand-primary/30 transition-all">
                        <div class="aspect-square bg-brand-cream/30 p-4 flex items-center justify-center">
                            @if($similar->image)
                                <img src="{{ $similar->image_url }}" alt="{{ $similar->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                            @else
                                <svg class="w-10 h-10 text-brand-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            @endif
                        </div>
                        <div class="p-3 border-t border-brand-border/50">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-brand-primary mb-1">{{ $similar->brand }}</p>
                            <h3 class="font-bold text-brand-text text-xs line-clamp-2">{{ $similar->name }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
