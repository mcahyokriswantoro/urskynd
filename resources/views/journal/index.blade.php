<x-app-layout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text">Skin Journal</h1>
            <p class="text-sm text-brand-textAlt">Pantau perkembangan kondisi kulitmu dari hari ke hari.</p>
        </div>
        
        <a href="{{ route('journal.create') }}" class="px-5 py-2.5 bg-brand-primary text-white text-sm font-medium rounded-xl shadow-soft hover:shadow-soft-lg hover:bg-brand-primaryAlt transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Isi Jurnal Hari Ini
        </a>
    </div>

    @if(session('success'))
        <div class="bg-brand-success/10 text-brand-success p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('warning'))
        <div class="bg-brand-warning/10 text-brand-warning p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            {{ session('warning') }}
        </div>
    @endif

    @if($journals->isEmpty())
        <x-card class="text-center py-12 border-dashed border-2 border-brand-border bg-transparent shadow-none">
            <div class="w-16 h-16 bg-brand-cream rounded-full flex items-center justify-center text-brand-primary mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-brand-text mb-2">Belum Ada Catatan</h3>
            <p class="text-sm text-brand-textAlt mb-6 max-w-sm mx-auto">Mulai catat kondisi kulit harianmu untuk melihat pola dan mengetahui produk apa yang cocok untukmu.</p>
            <a href="{{ route('journal.create') }}" class="inline-flex px-6 py-3 bg-white border border-brand-border text-brand-primary text-sm font-medium rounded-xl hover:bg-brand-cream transition-colors shadow-sm">
                Isi Jurnal Pertamamu
            </a>
        </x-card>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Journal Timeline -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($journals as $journal)
                    <x-card class="hover:border-brand-primary/30 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-brand-cream flex items-center justify-center text-xl shadow-inner border border-white">
                                    {{ match($journal->mood) { 'happy' => '😊', 'neutral' => '😐', 'sad' => '😔', 'tired' => '🥱', 'stressed' => '😫', default => '😶' } }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-brand-text">{{ \Carbon\Carbon::parse($journal->journal_date)->translatedFormat('l, d M Y') }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <x-badge type="{{ match($journal->skin_condition) { 'excellent' => 'success', 'good' => 'primary', 'moderate' => 'warning', default => 'danger' } }}" size="sm">
                                            Kondisi: {{ ucfirst($journal->skin_condition) }}
                                        </x-badge>
                                    </div>
                                </div>
                            </div>
                            @if($journal->skin_score)
                                <div class="text-right">
                                    <p class="text-[10px] uppercase tracking-wider font-semibold text-brand-primary">Score</p>
                                    <p class="text-xl font-serif font-bold text-brand-text">{{ $journal->skin_score }}</p>
                                </div>
                            @endif
                        </div>

                        @if($journal->notes)
                            <p class="text-sm text-brand-textAlt mb-4 p-3 bg-brand-cream/30 rounded-lg italic">
                                "{{ $journal->notes }}"
                            </p>
                        @endif

                        @if($journal->concerns->count() > 0)
                            <div class="flex flex-wrap gap-2 mt-2 pt-4 border-t border-brand-border/50">
                                @foreach($journal->concerns as $concern)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-brand-text px-2 py-1 bg-white border border-brand-border rounded-md">
                                        {{ $concern->icon }} {{ $concern->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </x-card>
                @endforeach

                <div class="mt-6">
                    {{ $journals->links() }}
                </div>
            </div>

            <!-- Stats/Summary Sidebar -->
            <div class="space-y-6">
                <x-card class="bg-gradient-to-br from-brand-cream to-white border-brand-primary/20">
                    <h3 class="font-serif font-bold text-brand-text mb-4 border-b border-brand-border/50 pb-2">Konsistensi Bulan Ini</h3>
                    <div class="flex items-end gap-2 mb-2">
                        <span class="text-4xl font-serif font-bold text-brand-primary">{{ $journals->count() }}</span>
                        <span class="text-sm text-brand-textAlt pb-1">entri dicatat</span>
                    </div>
                    <p class="text-xs text-brand-textAlt mt-2">Semakin sering mencatat, semakin mudah mengetahui pemicu masalah kulitmu.</p>
                </x-card>

                <!-- Gamification Hook -->
                <x-card class="bg-[#FDFBF9] border-brand-warning/30">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-brand-warning/10 text-brand-warning rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-brand-text text-sm">7 Day Streak!</h3>
                    </div>
                    <p class="text-xs text-brand-textAlt mb-3">Isi jurnal 3 hari lagi tanpa putus untuk mendapatkan badge "Consistency is Beauty" dan +60 Points!</p>
                    <div class="w-full h-1.5 bg-brand-cream rounded-full overflow-hidden">
                        <div class="bg-brand-warning h-full rounded-full" style="width: 57%"></div>
                    </div>
                </x-card>
            </div>
        </div>
    @endif
</x-app-layout>
