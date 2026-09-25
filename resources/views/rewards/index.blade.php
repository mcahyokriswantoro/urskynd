<x-app-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-serif font-bold text-brand-text mb-1">Rewards & Achievements</h1>
        <p class="text-sm text-brand-textAlt">Kumpulkan poin dan badge dari setiap progres perawatan kulitmu!</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Points & History -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Points Card -->
            <x-card class="bg-gradient-to-br from-brand-primary to-[#7a4e37] text-white overflow-hidden relative shadow-lg border-0">
                <!-- Decorative background elements -->
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                <div class="absolute -bottom-8 -left-8 w-24 h-24 bg-black/10 rounded-full blur-lg"></div>
                
                <div class="relative z-10">
                    <p class="text-white/80 text-sm font-medium uppercase tracking-wider mb-2">Total Points</p>
                    <div class="flex items-end gap-2 mb-4">
                        <span class="text-5xl font-serif font-bold">{{ number_format($user->points) }}</span>
                        <span class="text-white/80 font-medium pb-1">pts</span>
                    </div>
                    
                    <div class="pt-4 border-t border-white/20">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-white/80">Status Member</span>
                            <span class="font-bold bg-white/20 px-3 py-1 rounded-full text-xs">
                                {{ $user->points >= 500 ? 'Gold' : ($user->points >= 100 ? 'Silver' : 'Bronze') }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Points History -->
            <x-card>
                <h3 class="font-serif font-bold text-brand-text mb-4 text-lg border-b border-brand-border/50 pb-2">Riwayat Poin</h3>
                
                @if($pointHistory->isEmpty())
                    <p class="text-sm text-brand-textAlt text-center py-4">Belum ada riwayat poin. Mulai isi jurnal atau analisis kulitmu!</p>
                @else
                    <div class="space-y-4">
                        @foreach($pointHistory as $history)
                            <div class="flex items-center justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $history->points > 0 ? 'bg-brand-success/10 text-brand-success' : 'bg-brand-danger/10 text-brand-danger' }} flex items-center justify-center shrink-0">
                                        @if($history->points > 0)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-brand-text">{{ $history->description }}</p>
                                        <p class="text-[10px] text-brand-textAlt">{{ $history->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="font-bold {{ $history->points > 0 ? 'text-brand-success' : 'text-brand-danger' }}">
                                    {{ $history->points > 0 ? '+' : '' }}{{ $history->points }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-brand-border/50">
                        {{ $pointHistory->links() }}
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Right Column: Achievements -->
        <div class="lg:col-span-2">
            <x-card>
                <div class="flex justify-between items-center mb-6 border-b border-brand-border/50 pb-4">
                    <h2 class="font-serif font-bold text-brand-text text-xl">Badges & Achievements</h2>
                    <span class="text-sm font-medium bg-brand-cream text-brand-primary px-3 py-1 rounded-full">
                        Terbuka: {{ $user->achievements->count() }} / {{ $allAchievements->count() }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @php
                        $userAchievementIds = $user->achievements->pluck('id')->toArray();
                    @endphp

                    @foreach($allAchievements as $achievement)
                        @php
                            $isUnlocked = in_array($achievement->id, $userAchievementIds);
                        @endphp
                        
                        <div class="p-4 rounded-2xl border text-center transition-all duration-300 {{ $isUnlocked ? 'bg-white border-brand-primary/30 shadow-soft hover:shadow-soft-lg' : 'bg-[#FDFBF9] border-brand-border opacity-70 grayscale' }}">
                            <div class="w-16 h-16 mx-auto mb-3 relative">
                                <!-- Badge Icon Background -->
                                <div class="absolute inset-0 bg-brand-cream rounded-full rotate-45"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-3xl z-10">
                                    {{ $achievement->icon }}
                                </div>
                            </div>
                            
                            <h4 class="font-bold text-sm text-brand-text mb-1">{{ $achievement->name }}</h4>
                            <p class="text-[10px] text-brand-textAlt mb-3 leading-tight">{{ $achievement->description }}</p>
                            
                            @if($isUnlocked)
                                <div class="inline-block px-2 py-1 bg-brand-success/10 text-brand-success text-[10px] font-bold rounded">
                                    Unlocked
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1 text-[10px] font-bold text-brand-primary">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Terkunci
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
