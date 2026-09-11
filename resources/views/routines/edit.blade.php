<x-app-layout>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('routines.index') }}" class="p-2 bg-white rounded-full shadow-sm text-brand-textAlt hover:text-brand-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text mb-1">Edit {{ $routine->name }}</h1>
            <p class="text-sm text-brand-textAlt">Atur susunan produk untuk rutinitas ini.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-brand-success/10 text-brand-success p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Current Routine List -->
        <div>
            <h2 class="font-bold text-brand-text mb-4 text-lg">Produk di Rutinitas Ini</h2>
            
            @if($routine->items->isEmpty())
                <div class="p-8 border border-dashed border-brand-border rounded-2xl text-center">
                    <p class="text-sm text-brand-textAlt">Belum ada produk. Tambahkan dari daftarmu di sebelah kanan.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($routine->items->sortBy('order_number') as $index => $item)
                        <div class="flex items-center gap-4 p-3 bg-white border border-brand-border rounded-2xl shadow-sm group">
                            <div class="cursor-move text-brand-textAlt hover:text-brand-primary p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </div>
                            <div class="w-10 h-10 bg-brand-cream rounded-lg p-1 shrink-0 flex items-center justify-center">
                                @if($item->userProduct->product->image)
                                    <img src="{{ $item->userProduct->product->image_url }}" alt="Product" class="object-contain h-full">
                                @else
                                    <svg class="w-5 h-5 text-brand-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] uppercase tracking-wider font-bold text-brand-primary">{{ $item->userProduct->product->category->name ?? 'Skincare' }}</p>
                                <h3 class="font-bold text-brand-text text-sm truncate">{{ $item->userProduct->product->name }}</h3>
                            </div>
                            
                            <form action="{{ route('routines.items.remove', [$routine, $item]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-brand-danger hover:bg-brand-danger/10 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Available Products -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-brand-text text-lg">Pilih dari Rakmu</h2>
                <a href="{{ route('products.index') }}" class="text-xs text-brand-primary hover:underline">Tambah Koleksi Baru</a>
            </div>
            
            <x-card class="bg-[#FDFBF9]">
                @if($availableProducts->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-sm text-brand-textAlt mb-3">Tidak ada produk lain di Rak Skincare-mu.</p>
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-brand-primary text-white text-sm font-medium rounded-lg shadow-sm hover:bg-brand-primaryAlt transition-all inline-block">Cari Produk Skincare</a>
                    </div>
                @else
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 hide-scrollbar">
                        @foreach($availableProducts as $userProduct)
                            <div class="flex justify-between items-center p-3 bg-white border border-brand-border rounded-xl shadow-sm hover:border-brand-primary/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-brand-cream rounded-lg p-1 shrink-0 flex items-center justify-center">
                                        @if($userProduct->product->image)
                                            <img src="{{ $userProduct->product->image_url }}" alt="Product" class="object-contain h-full">
                                        @else
                                            <svg class="w-5 h-5 text-brand-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[9px] uppercase tracking-wider font-bold text-brand-primary">{{ $userProduct->product->category->name ?? 'Skincare' }}</p>
                                        <h3 class="font-bold text-brand-text text-sm leading-tight">{{ $userProduct->product->name }}</h3>
                                    </div>
                                </div>
                                <form action="{{ route('routines.items.add', $routine) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_product_id" value="{{ $userProduct->id }}">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-brand-cream text-brand-primary hover:bg-brand-primary hover:text-white rounded-full transition-colors shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>
