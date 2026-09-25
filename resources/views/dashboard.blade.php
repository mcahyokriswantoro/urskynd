<x-app-layout>
    <!-- Header Greeting -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text flex items-center gap-2">
                Halo, {{ explode(' ', $user->name)[0] }} 👋
            </h1>
            <p class="text-brand-textAlt text-sm mt-1">Kulit sehat dimulai dari konsistensi setiap hari.</p>
        </div>
        <div class="md:hidden">
            <!-- Mobile Notification Bell -->
            <button class="p-2 bg-white rounded-full shadow-sm text-brand-textAlt">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <!-- Main Column (Mobile: Full, Desktop: 8 cols) -->
        <div class="md:col-span-8 space-y-6">
            
            <!-- Skin Score Card -->
            <x-card padding="p-0" class="bg-gradient-to-br from-[#FDFBF9] to-[#F4EAE1] border-brand-primary/20">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <p class="text-xs font-semibold text-brand-primary tracking-wider uppercase mb-1">Skin Score</p>
                            @if($latestAnalysis)
                                <x-skin-score :score="$latestAnalysis->overall_score" size="lg" />
                                <p class="text-sm text-brand-text mt-2 font-medium">Kulitmu dalam kondisi baik!</p>
                            @else
                                <div class="text-2xl font-serif font-bold text-brand-textAlt">Belum Ada Data</div>
                                <p class="text-sm text-brand-text mt-2">Lakukan Skin Check pertamamu.</p>
                            @endif
                        </div>
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full overflow-hidden border-4 border-white shadow-soft">
                            <img src="{{ $latestAnalysis?->image_url ?? $user->avatar_url }}" alt="Skin Photo" class="w-full h-full object-cover">
                        </div>
                    </div>
                    
                    <a href="{{ $latestAnalysis ? route('analysis.show', $latestAnalysis) : route('skin-check.index') }}" 
                       class="block w-full py-3.5 px-4 bg-gradient-to-r from-brand-primary to-brand-primaryAlt text-white text-center rounded-xl font-medium shadow-soft hover:shadow-soft-lg transition-all">
                        {{ $latestAnalysis ? 'Lihat Analisis Lengkap' : 'Mulai Skin Check' }}
                    </a>
                </div>
            </x-card>

            <!-- Quick Actions (Mobile Grid) -->
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2.5 md:hidden">
                <a href="{{ route('skin-check.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Skin Check</span>
                </a>
                
                <a href="{{ route('tracker.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Tracker</span>
                </a>

                <a href="{{ route('routines.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Routine</span>
                </a>

                <a href="{{ route('journal.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Journal</span>
                </a>

                <a href="{{ route('rewards.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Rewards</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex flex-col items-center p-2.5 bg-white rounded-xl shadow-soft border border-brand-border/50 text-center hover:border-brand-primary/40 transition-all">
                    <div class="w-9 h-9 mb-1.5 rounded-full bg-brand-cream flex items-center justify-center text-brand-primary">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <span class="text-[10px] font-medium text-brand-text">Produk</span>
                </a>
            </div>

            <!-- Products Section -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-serif font-bold text-lg text-brand-text">Produk Terdaftar</h3>
                    <a href="{{ route('products.index') }}" class="text-xs font-medium text-brand-primary hover:underline">Lihat Semua</a>
                </div>
                
                @if($activeProducts->count() > 0)
                    <div class="flex overflow-x-auto pb-4 gap-4 snap-x hide-scrollbar">
                        @foreach($activeProducts as $userProduct)
                            <div class="snap-start min-w-[200px] bg-white rounded-xl p-3 shadow-soft border border-brand-border/50 flex gap-3 items-center">
                                <div class="w-12 h-16 bg-brand-cream rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden p-1">
                                    @if($userProduct->product->image)
                                        <img src="{{ $userProduct->product->image_url }}" alt="{{ $userProduct->product->name }}" class="object-contain h-full">
                                    @else
                                        <svg class="w-6 h-6 text-brand-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-semibold text-brand-primary uppercase tracking-wide truncate">{{ $userProduct->product->brand }}</p>
                                    <p class="text-xs font-medium text-brand-text truncate">{{ $userProduct->product->name }}</p>
                                    <p class="text-[10px] text-brand-textAlt mt-1">Sejak {{ $userProduct->started_at?->format('M Y') ?? 'Baru' }}</p>
                                </div>
                            </div>
                        @endforeach
                        
                        <a href="{{ route('products.index') }}" class="snap-start min-w-[120px] bg-brand-cream/50 border border-dashed border-brand-primary/30 rounded-xl p-3 flex flex-col items-center justify-center text-brand-primary hover:bg-brand-cream transition-colors">
                            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            <span class="text-xs font-medium">Tambah</span>
                        </a>
                    </div>
                @else
                    <x-card padding="p-6">
                        <div class="text-center">
                            <p class="text-sm text-brand-textAlt mb-3">Belum ada produk skincare yang terdaftar.</p>
                            <a href="{{ route('products.index') }}" class="text-sm font-medium text-brand-primary">Tambah Produk +</a>
                        </div>
                    </x-card>
                @endif
            </div>

            <!-- Progress Chart -->
            <div>
                <h3 class="font-serif font-bold text-lg text-brand-text mb-3">Progress Kulitmu</h3>
                <x-card>
                    @if($latestAnalysis)
                        <div class="h-64 w-full relative">
                            <canvas id="progressChart"></canvas>
                        </div>
                    @else
                        <div class="h-48 flex items-center justify-center border border-dashed border-brand-border rounded-lg bg-brand-cream/20">
                            <p class="text-sm text-brand-textAlt text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-brand-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                                Grafik progress akan muncul setelah 2x Skin Check
                            </p>
                        </div>
                    @endif
                </x-card>
            </div>

        </div>

        <!-- Sidebar Column (Desktop: 4 cols) -->
        <div class="md:col-span-4 space-y-6">
            
            <!-- Today's Routine -->
            <x-card padding="p-0">
                <div class="p-5 border-b border-brand-border/50 flex justify-between items-center bg-gradient-to-r from-brand-cream to-white">
                    <h3 class="font-serif font-bold text-lg text-brand-text flex items-center gap-2">
                        @if($isMorning)
                            <svg class="w-5 h-5 text-brand-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Routine Pagi
                        @else
                            <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            Routine Malam
                        @endif
                    </h3>
                    <a href="{{ route('routines.index') }}" class="text-xs font-medium text-brand-primary hover:underline">Edit</a>
                </div>
                
                <div class="p-5">
                    @if($todayRoutine && $todayRoutine->items->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayRoutine->items as $index => $item)
                                <div class="flex items-start gap-3 relative">
                                    <!-- Timeline line -->
                                    @if(!$loop->last)
                                        <div class="absolute left-3.5 top-8 bottom-[-16px] w-[2px] bg-brand-creamAlt"></div>
                                    @endif
                                    
                                    <div class="w-7 h-7 rounded-full bg-brand-cream border-2 border-white shadow-sm flex items-center justify-center text-[10px] font-bold text-brand-primary relative z-10 shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    
                                    <div class="flex-1 pb-1">
                                        <p class="text-xs font-semibold text-brand-primary uppercase tracking-wide">{{ $item->userProduct->product->category->name ?? 'Produk' }}</p>
                                        <p class="text-sm font-medium text-brand-text">{{ $item->userProduct->product->name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-brand-textAlt mb-3">Rutinitas belum diatur.</p>
                            <a href="{{ route('routines.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-cream text-brand-primary text-xs font-medium rounded-lg hover:bg-brand-creamAlt transition-colors">
                                Buat Routine
                            </a>
                        </div>
                    @endif
                </div>
            </x-card>

        </div>
    </div>
    
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @if($latestAnalysis && !empty($progressData['dates']))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('progressChart').getContext('2d');
                
                // Create gradient for line area
                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(154, 106, 82, 0.2)'); // brand-primary with opacity
                gradient.addColorStop(1, 'rgba(154, 106, 82, 0)');

                const data = {
                    labels: @json($progressData['dates']),
                    datasets: [{
                        label: 'Skin Score',
                        data: @json($progressData['scores']),
                        borderColor: '#9A6A52', // brand-primary
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#9A6A52',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Smooth curves
                    }]
                };

                const config = {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#352922', // brand-text
                                padding: 12,
                                titleFont: { family: 'Inter', size: 13 },
                                bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Score: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#A97A61' // brand-primaryAlt
                                }
                            },
                            y: {
                                min: 0,
                                max: 100,
                                grid: {
                                    color: '#E7DBD1', // brand-border
                                    borderDash: [5, 5],
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { family: 'Inter', size: 11 },
                                    color: '#A97A61',
                                    stepSize: 20
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                    }
                };

                new Chart(ctx, config);
            });
        </script>
    @endif
</x-app-layout>
