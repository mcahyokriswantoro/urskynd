<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text">Skin Progress Tracker</h1>
            <p class="text-sm text-brand-textAlt">Pantau grafik perkembangan dan perubahan kondisi kulitmu dari waktu ke waktu.</p>
        </div>
        
        <a href="{{ route('skin-check.index') }}" class="px-5 py-2.5 bg-brand-primary text-white text-sm font-medium rounded-xl shadow-soft hover:shadow-soft-lg hover:bg-brand-primaryAlt transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Skin Check Baru
        </a>
    </div>

    @if(empty($progressData['dates']) || count($progressData['dates']) === 0)
        <!-- Empty State -->
        <x-card class="text-center py-16 border-dashed border-2 border-brand-border bg-transparent shadow-none">
            <div class="w-20 h-20 bg-brand-cream rounded-full flex items-center justify-center text-brand-primary mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            </div>
            <h3 class="text-xl font-serif font-bold text-brand-text mb-2">Belum Ada Riwayat Analisis</h3>
            <p class="text-sm text-brand-textAlt mb-6 max-w-md mx-auto">Lakukan Skin Check pertamamu untuk mulai melacak tren kesehatan kulit, tingkat hidrasi, dan perbaikan metrik kulitmu secara visual.</p>
            <a href="{{ route('skin-check.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-medium rounded-xl hover:bg-brand-primaryAlt transition-all shadow-soft">
                Mulai Skin Check Sekarang
            </a>
        </x-card>
    @else
        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <!-- Overall Score Card -->
            <x-card class="relative overflow-hidden bg-gradient-to-br from-white to-[#FAF6F2]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-semibold text-brand-primary mb-1">Skor Kulit Terkini</p>
                        <h3 class="text-3xl font-serif font-bold text-brand-text">{{ $latestAnalysis?->overall_score ?? '-' }}<span class="text-base font-normal text-brand-textAlt">/100</span></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    @if($scoreDiff > 0)
                        <span class="text-brand-success font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            +{{ $scoreDiff }} poin
                        </span>
                        <span class="text-brand-textAlt ml-1.5">sejak analisis pertama</span>
                    @elseif($scoreDiff < 0)
                        <span class="text-brand-danger font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            {{ $scoreDiff }} poin
                        </span>
                        <span class="text-brand-textAlt ml-1.5">sejak analisis pertama</span>
                    @else
                        <span class="text-brand-textAlt">Konsisten stabil</span>
                    @endif
                </div>
            </x-card>

            <!-- Skin Age Card -->
            <x-card class="relative overflow-hidden bg-gradient-to-br from-white to-[#FAF6F2]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-semibold text-brand-primary mb-1">Estimasi Skin Age</p>
                        <h3 class="text-3xl font-serif font-bold text-brand-text">{{ $latestAnalysis?->estimated_skin_age ?? '-' }} <span class="text-base font-normal text-brand-textAlt">Tahun</span></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs">
                    @if($skinAgeDiff < 0)
                        <span class="text-brand-success font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            {{ abs($skinAgeDiff) }} tahun lebih muda
                        </span>
                    @elseif($skinAgeDiff > 0)
                        <span class="text-brand-warning font-semibold">
                            +{{ $skinAgeDiff }} tahun
                        </span>
                    @else
                        <span class="text-brand-textAlt">Sesuai usia biologis</span>
                    @endif
                </div>
            </x-card>

            <!-- Total Checkups Card -->
            <x-card class="relative overflow-hidden bg-gradient-to-br from-white to-[#FAF6F2]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-semibold text-brand-primary mb-1">Total Skin Check</p>
                        <h3 class="text-3xl font-serif font-bold text-brand-text">{{ count($progressData['dates']) }} <span class="text-base font-normal text-brand-textAlt">Kali</span></h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center text-xs text-brand-textAlt">
                    Tipe kulit: <strong class="text-brand-text ml-1">{{ $latestAnalysis?->skinType?->name ?? 'Normal' }}</strong>
                </div>
            </x-card>
        </div>

        <!-- Period Filter & Main Trend Chart -->
        <x-card class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center pb-4 mb-4 border-b border-brand-border/50 gap-3">
                <div>
                    <h2 class="text-lg font-serif font-bold text-brand-text">Grafik Tren Kondisi Kulit</h2>
                    <p class="text-xs text-brand-textAlt">Perbandingan skor keseluruhan dan estimasi usia kulit</p>
                </div>

                <!-- Filter Period Tabs -->
                <div class="flex items-center bg-brand-cream/60 p-1 rounded-xl border border-brand-border">
                    <a href="{{ route('tracker.index', ['period' => 'weekly']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $period === 'weekly' ? 'bg-white text-brand-primary font-bold shadow-sm' : 'text-brand-textAlt hover:text-brand-text' }}">Mingguan</a>
                    <a href="{{ route('tracker.index', ['period' => 'monthly']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $period === 'monthly' ? 'bg-white text-brand-primary font-bold shadow-sm' : 'text-brand-textAlt hover:text-brand-text' }}">Bulanan</a>
                    <a href="{{ route('tracker.index', ['period' => 'yearly']) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $period === 'yearly' ? 'bg-white text-brand-primary font-bold shadow-sm' : 'text-brand-textAlt hover:text-brand-text' }}">Tahunan</a>
                </div>
            </div>

            <!-- Canvas for Chart -->
            <div class="h-72 w-full">
                <canvas id="skinTrendChart"></canvas>
            </div>
        </x-card>

        <!-- Metric Details & Radar -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            <!-- Radar Chart: Current Skin Health Balance -->
            <div class="lg:col-span-5">
                <x-card class="h-full flex flex-col">
                    <h2 class="text-lg font-serif font-bold text-brand-text mb-1">Keseimbangan Parameter Kulit</h2>
                    <p class="text-xs text-brand-textAlt mb-4">Profil kekuatan & area perawatan kulit saat ini</p>
                    <div class="flex-1 min-h-[260px] flex items-center justify-center">
                        <canvas id="skinRadarChart"></canvas>
                    </div>
                </x-card>
            </div>

            <!-- Detailed Parameter Progression -->
            <div class="lg:col-span-7">
                <x-card class="h-full">
                    <h2 class="text-lg font-serif font-bold text-brand-text mb-1">Detail Perkembangan Metrik</h2>
                    <p class="text-xs text-brand-textAlt mb-4">Perubahan metrik dari scan pertama hingga sekarang</p>

                    @php
                        $metricLabels = [
                            'hydration' => ['label' => 'Hidrasi Kulit', 'icon' => '💧'],
                            'pores' => ['label' => 'Kerapatan Pori', 'icon' => '🔍'],
                            'acne' => ['label' => 'Kejernihan (Bebas Jerawat)', 'icon' => '✨'],
                            'pigmentation' => ['label' => 'Kemerataan Warna', 'icon' => '🎨'],
                            'wrinkles' => ['label' => 'Elastisitas & Kerutan', 'icon' => '⏳'],
                            'redness' => ['label' => 'Ketenangan (Anti-Kemerahan)', 'icon' => '🌿'],
                            'texture' => ['label' => 'Kehalusan Tekstur', 'icon' => '🪞'],
                            'sensitivity' => ['label' => 'Ketahanan Barrier', 'icon' => '🛡️'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        @foreach($metricLabels as $key => $meta)
                            @php
                                $values = $progressData['metrics'][$key] ?? [];
                                $latestVal = end($values) ?: 0;
                                $firstVal = reset($values) ?: 0;
                                $diff = $latestVal - $firstVal;
                            @endphp
                            <div class="p-3 bg-brand-cream/30 border border-brand-border rounded-xl">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-semibold text-brand-text flex items-center gap-1.5">
                                        <span>{{ $meta['icon'] }}</span>
                                        <span>{{ $meta['label'] }}</span>
                                    </span>
                                    <span class="text-xs font-bold text-brand-primary">{{ $latestVal }}/100</span>
                                </div>
                                <div class="w-full h-1.5 bg-brand-cream rounded-full mb-1.5 overflow-hidden">
                                    <div class="h-full bg-brand-primary rounded-full" style="width: {{ $latestVal }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-brand-textAlt">
                                    <span>Skor Awal: {{ $firstVal }}</span>
                                    <span class="{{ $diff >= 0 ? 'text-brand-success font-semibold' : 'text-brand-danger font-semibold' }}">
                                        {{ $diff >= 0 ? "+$diff" : "$diff" }} pts
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>

        <!-- History of Analyses -->
        <x-card>
            <div class="flex items-center justify-between mb-4 border-b border-brand-border/50 pb-3">
                <div>
                    <h2 class="text-lg font-serif font-bold text-brand-text">Riwayat Analisis Kulit</h2>
                    <p class="text-xs text-brand-textAlt">Daftar rekaman Skin Check yang telah dilakukan</p>
                </div>
            </div>

            <div class="divide-y divide-brand-border/50">
                @foreach($analyses as $analysis)
                    <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-brand-cream/20 px-2 rounded-xl transition-colors">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-brand-cream flex-shrink-0 flex items-center justify-center border border-brand-border/50">
                                @if($analysis->image_url)
                                    <img src="{{ $analysis->image_url }}" alt="Skin Scan" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-brand-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand-text">{{ $analysis->analysis_date->translatedFormat('d F Y') }}</p>
                                <p class="text-xs text-brand-textAlt">Skin Age: <strong class="text-brand-text">{{ $analysis->estimated_skin_age }} Thn</strong> &bull; Tipe: {{ $analysis->skinType?->name ?? 'Normal' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 self-end sm:self-center">
                            <x-badge type="{{ match(true) { $analysis->overall_score >= 80 => 'success', $analysis->overall_score >= 60 => 'primary', $analysis->overall_score >= 40 => 'warning', default => 'danger' } }}">
                                Score: {{ $analysis->overall_score }}/100
                            </x-badge>

                            <a href="{{ route('analysis.show', $analysis) }}" class="px-3.5 py-1.5 bg-white border border-brand-border text-brand-primary text-xs font-semibold rounded-lg hover:bg-brand-cream hover:border-brand-primary/40 transition-all shadow-sm">
                                Detail Hasil
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $analyses->links() }}
            </div>
        </x-card>
    @endif

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($progressData);
            
            if (chartData.dates && chartData.dates.length > 0) {
                // Line Chart (Trend Score & Skin Age)
                const ctx = document.getElementById('skinTrendChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: chartData.dates,
                            datasets: [
                                {
                                    label: 'Overall Skin Score',
                                    data: chartData.scores,
                                    borderColor: '#9A6A52',
                                    backgroundColor: 'rgba(154, 106, 82, 0.1)',
                                    fill: true,
                                    tension: 0.35,
                                    borderWidth: 3,
                                    pointBackgroundColor: '#9A6A52',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    yAxisID: 'y',
                                },
                                {
                                    label: 'Skin Age (Tahun)',
                                    data: chartData.skin_ages,
                                    borderColor: '#C49A82',
                                    borderDash: [5, 5],
                                    backgroundColor: 'transparent',
                                    fill: false,
                                    tension: 0.35,
                                    borderWidth: 2,
                                    pointBackgroundColor: '#C49A82',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'y1',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        font: { family: 'sans-serif', size: 12 }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#2D2926',
                                    padding: 10,
                                    cornerRadius: 8,
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    min: 0,
                                    max: 100,
                                    title: { display: true, text: 'Score' },
                                    grid: { color: 'rgba(0,0,0,0.05)' }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    title: { display: true, text: 'Skin Age' }
                                }
                            }
                        }
                    });
                }

                // Radar Chart (Latest Metrics)
                const radarCtx = document.getElementById('skinRadarChart');
                if (radarCtx && chartData.metrics) {
                    const metricKeys = ['hydration', 'pores', 'acne', 'pigmentation', 'wrinkles', 'redness', 'texture', 'sensitivity'];
                    const metricLabels = ['Hidrasi', 'Pori-pori', 'Jerawat', 'Pigmentasi', 'Kerutan', 'Kemerahan', 'Tekstur', 'Sensitivitas'];
                    
                    const latestValues = metricKeys.map(k => {
                        const vals = chartData.metrics[k] || [];
                        return vals.length > 0 ? vals[vals.length - 1] : 70;
                    });

                    new Chart(radarCtx, {
                        type: 'radar',
                        data: {
                            labels: metricLabels,
                            datasets: [{
                                label: 'Kondisi Terkini',
                                data: latestValues,
                                backgroundColor: 'rgba(154, 106, 82, 0.2)',
                                borderColor: '#9A6A52',
                                borderWidth: 2,
                                pointBackgroundColor: '#9A6A52',
                                pointBorderColor: '#fff',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: '#9A6A52'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                r: {
                                    angleLines: { color: 'rgba(0,0,0,0.08)' },
                                    grid: { color: 'rgba(0,0,0,0.08)' },
                                    suggestedMin: 0,
                                    suggestedMax: 100,
                                    ticks: { stepSize: 20, display: false }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
