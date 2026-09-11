<x-app-layout>
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('journal.index') }}" class="p-2 bg-white rounded-full shadow-sm text-brand-textAlt hover:text-brand-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-serif font-bold text-brand-text">Jurnal Hari Ini</h1>
            <p class="text-sm text-brand-textAlt">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('journal.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Skin Condition -->
            <x-card>
                <h3 class="font-serif font-bold text-brand-text mb-4 text-lg">Bagaimana kondisi kulitmu hari ini?</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @php
                        $conditions = [
                            'excellent' => ['label' => 'Sangat Baik', 'icon' => '✨', 'color' => 'success'],
                            'good' => ['label' => 'Baik', 'icon' => '😊', 'color' => 'primary'],
                            'moderate' => ['label' => 'Biasa', 'icon' => '😐', 'color' => 'warning'],
                            'poor' => ['label' => 'Kurang', 'icon' => '😔', 'color' => 'danger'],
                            'critical' => ['label' => 'Bermasalah', 'icon' => '😫', 'color' => 'danger'],
                        ];
                    @endphp
                    
                    @foreach($conditions as $value => $data)
                        <label class="cursor-pointer group">
                            <input type="radio" name="skin_condition" value="{{ $value }}" class="peer sr-only" required @checked(old('skin_condition') == $value)>
                            <div class="p-3 text-center bg-white border border-brand-border rounded-xl hover:border-brand-primary/50 peer-checked:border-brand-{{ $data['color'] }} peer-checked:bg-brand-{{ $data['color'] }}/10 peer-checked:ring-1 peer-checked:ring-brand-{{ $data['color'] }} transition-all h-full flex flex-col justify-center">
                                <span class="text-2xl block mb-1">{{ $data['icon'] }}</span>
                                <span class="text-xs font-semibold text-brand-text">{{ $data['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('skin_condition')" class="mt-2" />
            </x-card>

            <!-- Mood -->
            <x-card>
                <h3 class="font-serif font-bold text-brand-text mb-4 text-lg">Bagaimana perasaanmu (Mood)?</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @php
                        $moods = [
                            'happy' => ['label' => 'Senang', 'icon' => '🥰'],
                            'neutral' => ['label' => 'Biasa', 'icon' => '🙂'],
                            'stressed' => ['label' => 'Stres', 'icon' => '🤯'],
                            'tired' => ['label' => 'Lelah', 'icon' => '🥱'],
                            'sad' => ['label' => 'Sedih', 'icon' => '😢'],
                        ];
                    @endphp
                    
                    @foreach($moods as $value => $data)
                        <label class="cursor-pointer group">
                            <input type="radio" name="mood" value="{{ $value }}" class="peer sr-only" required @checked(old('mood') == $value)>
                            <div class="p-3 text-center bg-white border border-brand-border rounded-xl hover:border-brand-primary/50 peer-checked:border-brand-primary peer-checked:bg-brand-cream/30 peer-checked:ring-1 peer-checked:ring-brand-primary transition-all h-full flex flex-col justify-center">
                                <span class="text-2xl block mb-1">{{ $data['icon'] }}</span>
                                <span class="text-xs font-semibold text-brand-text">{{ $data['label'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('mood')" class="mt-2" />
            </x-card>

            <!-- Current Concerns -->
            <x-card>
                <h3 class="font-serif font-bold text-brand-text mb-1 text-lg">Keluhan Spesifik Hari Ini</h3>
                <p class="text-xs text-brand-textAlt mb-4">Pilih jika ada (bisa lebih dari satu)</p>
                
                <div class="flex flex-wrap gap-2">
                    @foreach($concerns as $concern)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="concerns[]" value="{{ $concern->id }}" class="peer sr-only" @checked(is_array(old('concerns')) && in_array($concern->id, old('concerns')))>
                            <div class="px-4 py-2 bg-white border border-brand-border rounded-full hover:border-brand-primary/50 peer-checked:border-brand-primary peer-checked:bg-brand-primary peer-checked:text-white text-brand-text transition-all flex items-center gap-2">
                                <span>{{ $concern->icon }}</span>
                                <span class="text-xs font-medium">{{ $concern->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('concerns')" class="mt-2" />
            </x-card>

            <!-- Notes -->
            <x-card>
                <h3 class="font-serif font-bold text-brand-text mb-4 text-lg">Catatan Tambahan</h3>
                <textarea name="notes" rows="4" class="w-full border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-sm py-3 px-4" placeholder="Cth: Hari ini mencoba serum baru, kulit terasa sedikit perih di awal...">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </x-card>

            <div class="flex justify-end">
                <x-primary-button class="px-8">
                    Simpan Jurnal
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
