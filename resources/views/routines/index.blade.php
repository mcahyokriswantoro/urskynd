<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text mb-1">Skincare Routine</h1>
            <p class="text-sm text-brand-textAlt">Atur produk yang kamu gunakan setiap pagi dan malam.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-brand-success/10 text-brand-success p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
            <span class="text-xl">✨</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div x-data="{ activeTab: 'morning' }">
        <div class="flex border-b border-brand-border/50 mb-6">
            <button @click="activeTab = 'morning'" 
                :class="{ 'border-brand-primary text-brand-primary': activeTab === 'morning', 'border-transparent text-brand-textAlt hover:text-brand-text hover:border-brand-border': activeTab !== 'morning' }"
                class="flex-1 py-4 border-b-2 font-medium text-sm transition-colors flex items-center justify-center gap-2">
                ☀️ Rutinitas Pagi
            </button>
            <button @click="activeTab = 'night'" 
                :class="{ 'border-brand-primary text-brand-primary': activeTab === 'night', 'border-transparent text-brand-textAlt hover:text-brand-text hover:border-brand-border': activeTab !== 'night' }"
                class="flex-1 py-4 border-b-2 font-medium text-sm transition-colors flex items-center justify-center gap-2">
                🌙 Rutinitas Malam
            </button>
        </div>

        <!-- Morning Routine Tab -->
        <div x-show="activeTab === 'morning'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-serif font-bold text-lg text-brand-text">Pagi Hari (07:00)</h2>
                <a href="{{ route('routines.edit', $morningRoutine) }}" class="text-sm font-medium text-brand-primary hover:underline">Edit Rutinitas</a>
            </div>

            @if($morningRoutine->items->isEmpty())
                <x-card class="text-center py-10 border-dashed border-2 border-brand-border bg-transparent shadow-none">
                    <p class="text-sm text-brand-textAlt mb-4">Belum ada produk di rutinitas pagimu.</p>
                    <a href="{{ route('routines.edit', $morningRoutine) }}" class="px-5 py-2.5 bg-white border border-brand-border text-brand-primary text-sm font-medium rounded-xl hover:bg-brand-cream transition-colors shadow-sm inline-block">
                        Tambah Produk
                    </a>
                </x-card>
            @else
                <div class="space-y-3 mb-6">
                    @foreach($morningRoutine->items->sortBy('order_number') as $index => $item)
                        <div class="flex items-center gap-4 p-4 bg-white border border-brand-border rounded-2xl shadow-sm">
                            <div class="w-8 h-8 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary font-bold text-sm shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div class="w-12 h-12 bg-brand-cream rounded-lg p-1 shrink-0 flex items-center justify-center">
                                @if($item->userProduct->product->image)
                                    <img src="{{ $item->userProduct->product->image_url }}" alt="Product" class="object-contain h-full">
                                @else
                                    <svg class="w-6 h-6 text-brand-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] uppercase tracking-wider font-bold text-brand-primary">{{ $item->userProduct->product->category->name ?? 'Skincare' }}</p>
                                <h3 class="font-bold text-brand-text text-sm truncate">{{ $item->userProduct->product->name }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('routines.complete', $morningRoutine) }}" method="POST">
                    @csrf
                    <x-primary-button class="w-full justify-center py-4 text-base">
                        Selesai untuk Hari Ini
                    </x-primary-button>
                </form>
            @endif
        </div>

        <!-- Night Routine Tab -->
        <div x-show="activeTab === 'night'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-serif font-bold text-lg text-brand-text">Malam Hari (20:00)</h2>
                <a href="{{ route('routines.edit', $nightRoutine) }}" class="text-sm font-medium text-brand-primary hover:underline">Edit Rutinitas</a>
            </div>

            @if($nightRoutine->items->isEmpty())
                <x-card class="text-center py-10 border-dashed border-2 border-brand-border bg-transparent shadow-none">
                    <p class="text-sm text-brand-textAlt mb-4">Belum ada produk di rutinitas malammu.</p>
                    <a href="{{ route('routines.edit', $nightRoutine) }}" class="px-5 py-2.5 bg-white border border-brand-border text-brand-primary text-sm font-medium rounded-xl hover:bg-brand-cream transition-colors shadow-sm inline-block">
                        Tambah Produk
                    </a>
                </x-card>
            @else
                <div class="space-y-3 mb-6">
                    @foreach($nightRoutine->items->sortBy('order_number') as $index => $item)
                        <div class="flex items-center gap-4 p-4 bg-white border border-brand-border rounded-2xl shadow-sm">
                            <div class="w-8 h-8 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary font-bold text-sm shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <div class="w-12 h-12 bg-brand-cream rounded-lg p-1 shrink-0 flex items-center justify-center">
                                @if($item->userProduct->product->image)
                                    <img src="{{ $item->userProduct->product->image_url }}" alt="Product" class="object-contain h-full">
                                @else
                                    <svg class="w-6 h-6 text-brand-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] uppercase tracking-wider font-bold text-brand-primary">{{ $item->userProduct->product->category->name ?? 'Skincare' }}</p>
                                <h3 class="font-bold text-brand-text text-sm truncate">{{ $item->userProduct->product->name }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('routines.complete', $nightRoutine) }}" method="POST">
                    @csrf
                    <x-primary-button class="w-full justify-center py-4 text-base">
                        Selesai untuk Hari Ini
                    </x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
