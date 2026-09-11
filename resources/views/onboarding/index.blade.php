<x-guest-layout>
    <div class="max-w-2xl mx-auto w-full px-4 py-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-serif font-bold text-brand-text mb-3">Mari Saling Mengenal</h1>
            <p class="text-brand-textAlt">Bantu kami memberikan rekomendasi yang paling tepat untuk kulitmu.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-10">
            @csrf

            <!-- Personal Info -->
            <x-card>
                <h2 class="text-lg font-serif font-bold text-brand-text mb-4 border-b border-brand-border/50 pb-3">Profil Dasar</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="birth_date" value="Tanggal Lahir" />
                        <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date')" required />
                        <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-sm py-3 px-4 w-full mt-1" required>
                            <option value="">Pilih Gender...</option>
                            <option value="female" @selected(old('gender') == 'female')>Perempuan</option>
                            <option value="male" @selected(old('gender') == 'male')>Laki-laki</option>
                            <option value="other" @selected(old('gender') == 'other')>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                </div>
            </x-card>

            <!-- Skin Type -->
            <x-card>
                <h2 class="text-lg font-serif font-bold text-brand-text mb-4 border-b border-brand-border/50 pb-3">Apa Tipe Kulitmu?</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($skinTypes as $type)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="skin_type_id" value="{{ $type->id }}" class="peer sr-only" required @checked(old('skin_type_id') == $type->id)>
                            <div class="p-4 bg-white border border-brand-border rounded-2xl hover:border-brand-primary/50 peer-checked:border-brand-primary peer-checked:bg-brand-cream/30 peer-checked:ring-1 peer-checked:ring-brand-primary transition-all">
                                <p class="font-bold text-brand-text mb-1">{{ $type->name }}</p>
                                <p class="text-xs text-brand-textAlt">{{ $type->description }}</p>
                            </div>
                            <div class="absolute top-4 right-4 text-brand-primary opacity-0 peer-checked:opacity-100 transition-opacity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('skin_type_id')" class="mt-2" />
            </x-card>

            <!-- Skin Concerns -->
            <x-card>
                <h2 class="text-lg font-serif font-bold text-brand-text mb-4 border-b border-brand-border/50 pb-3">Apa Fokus Utama Perawatanmu? <span class="text-xs font-normal text-brand-textAlt">(Pilih lebih dari satu)</span></h2>
                
                <div class="flex flex-wrap gap-3">
                    @foreach($skinConcerns as $concern)
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="concerns[]" value="{{ $concern->id }}" class="peer sr-only" @checked(is_array(old('concerns')) && in_array($concern->id, old('concerns')))>
                            <div class="px-4 py-2 bg-white border border-brand-border rounded-full hover:border-brand-primary/50 peer-checked:border-brand-primary peer-checked:bg-brand-primary peer-checked:text-white text-brand-text transition-all flex items-center gap-2">
                                <span>{{ $concern->icon }}</span>
                                <span class="text-sm font-medium">{{ $concern->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('concerns')" class="mt-2" />
            </x-card>

            <!-- Additional Info -->
            <x-card>
                <h2 class="text-lg font-serif font-bold text-brand-text mb-4 border-b border-brand-border/50 pb-3">Informasi Tambahan <span class="text-xs font-normal text-brand-textAlt">(Opsional)</span></h2>
                
                <div class="space-y-4">
                    <div>
                        <x-input-label for="allergies" value="Alergi Kandungan (Ingredients)" />
                        <x-text-input id="allergies" class="block mt-1 w-full" type="text" name="allergies" :value="old('allergies')" placeholder="Cth: Alkohol, Fragrance..." />
                    </div>

                    <div>
                        <x-input-label for="skin_goal" value="Skin Goal (Target Kulitmu)" />
                        <x-text-input id="skin_goal" class="block mt-1 w-full" type="text" name="skin_goal" :value="old('skin_goal')" placeholder="Cth: Ingin kulit glowing dan bebas jerawat" />
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end mt-8">
                <x-primary-button class="ml-4 px-10">
                    {{ __('Simpan & Lanjutkan') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
