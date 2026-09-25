<x-app-layout>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="p-2 bg-white rounded-full shadow-sm text-brand-textAlt hover:text-brand-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <h1 class="text-2xl font-serif font-bold text-brand-text">Hasil Analisis</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <!-- Left Column: Summary & Photo -->
        <div class="md:col-span-4 space-y-6">
            <x-card padding="p-0" class="overflow-hidden bg-gradient-to-br from-[#FDFBF9] to-[#F4EAE1]">
                <!-- Photo -->
                <div class="h-64 w-full relative bg-[#EBE0D8] overflow-hidden flex items-center justify-center">
                    @if($analysis->image_url)
                        <img src="{{ $analysis->image_url }}" alt="Analyzed Photo" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#E2D2C3] via-[#D5BEAD] to-[#BA9E8C] flex flex-col items-center justify-center p-6 text-brand-primary">
                            <svg class="w-16 h-16 opacity-30 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-semibold tracking-wider uppercase text-brand-primary/60">Data Simulasi</span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-6">
                        <div class="text-white">
                            <p class="text-xs uppercase tracking-widest font-semibold opacity-80 mb-1">Skin Age</p>
                            <p class="text-3xl font-serif font-bold">{{ $analysis->estimated_skin_age }} <span class="text-sm font-sans font-normal opacity-80">Tahun</span></p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-end mb-6 border-b border-brand-border/50 pb-6">
                        <div>
                            <p class="text-xs font-semibold text-brand-primary tracking-wider uppercase mb-2">Overall Score</p>
                            <x-skin-score :score="$analysis->overall_score" size="lg" />
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold text-brand-primary tracking-wider uppercase mb-1">Tipe Kulit</p>
                            <span class="inline-block px-3 py-1 bg-white rounded-lg border border-brand-border text-sm font-bold text-brand-text">
                                {{ $analysis->skinType?->name ?? 'Normal' }}
                            </span>
                        </div>
                    </div>

                    <h3 class="font-bold text-brand-text mb-2">Ringkasan AI</h3>
                    <p class="text-sm text-brand-textAlt leading-relaxed">{{ $analysis->summary }}</p>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Metrics & Recommendations -->
        <div class="md:col-span-8 space-y-6">
            
            <!-- Detailed Metrics -->
            <x-card>
                <h2 class="text-lg font-serif font-bold text-brand-text mb-6">Detail Kondisi Kulit</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($analysis->metrics as $metric)
                        <div class="p-4 bg-brand-cream/30 border border-brand-border rounded-xl">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-brand-text capitalize">{{ $metric->metric_type }}</h4>
                                <x-badge type="{{ match($metric->severity) { 'excellent' => 'success', 'good' => 'primary', 'moderate' => 'warning', default => 'danger' } }}">
                                    {{ $metric->score }}/100
                                </x-badge>
                            </div>
                            <!-- Small Progress Bar -->
                            <div class="w-full h-1.5 bg-brand-cream rounded-full mb-3">
                                <div class="h-1.5 rounded-full {{ match($metric->severity) { 'excellent' => 'bg-brand-success', 'good' => 'bg-brand-primary', 'moderate' => 'bg-brand-warning', default => 'bg-brand-danger' } }}" style="width: {{ $metric->score }}%"></div>
                            </div>
                            <p class="text-xs text-brand-textAlt">{{ $metric->description }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Recommendations -->
            @if($analysis->recommendation)
                <x-card class="border-brand-primary/20">
                    <div class="flex items-center gap-3 mb-6 border-b border-brand-border/50 pb-4">
                        <div class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-serif font-bold text-brand-text">{{ $analysis->recommendation->title }}</h2>
                            <p class="text-xs text-brand-textAlt">{{ $analysis->recommendation->description }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach(['morning' => 'Pagi', 'night' => 'Malam'] as $type => $label)
                            @php
                                $items = $analysis->recommendation->items->where('routine_type', $type)->sortBy('order_number');
                            @endphp
                            
                            @if($items->isNotEmpty())
                                <div>
                                    <h3 class="text-sm font-bold text-brand-text mb-3 uppercase tracking-wider flex items-center gap-2">
                                        {{ $type === 'morning' ? '☀️' : '🌙' }} Rutinitas {{ $label }}
                                    </h3>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($items as $item)
                                            <div class="flex gap-3 p-3 bg-white border border-brand-border rounded-xl shadow-sm hover:border-brand-primary/50 transition-colors">
                                                <div class="w-12 h-16 bg-brand-cream rounded-lg flex-shrink-0 flex items-center justify-center p-1">
                                                    @if($item->product->image)
                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="object-contain h-full">
                                                    @else
                                                        <svg class="w-6 h-6 text-brand-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-brand-primary uppercase">{{ $item->product->category->name ?? '' }}</p>
                                                    <p class="text-sm font-medium text-brand-text leading-tight mb-1">{{ $item->product->name }}</p>
                                                    <p class="text-[10px] text-brand-textAlt line-clamp-2">{{ $item->reason }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </x-card>
            @endif

        </div>
    </div>
</x-app-layout>
