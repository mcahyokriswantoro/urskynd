<section>
    <header>
        <h2 class="text-lg font-serif font-bold text-brand-text">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-brand-textAlt">
            Perbarui foto profil (DP), nama lengkap, nomor HP, alamat, dan email akunmu.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5" x-data="{ avatarPreview: '{{ $user->avatar_url }}' }">
        @csrf
        @method('patch')

        <!-- Foto Profil (Avatar / DP) -->
        <div class="pb-4 border-b border-brand-border/50">
            <x-input-label for="avatar" value="Foto Profil (DP)" class="mb-2" />
            
            <div class="flex items-center gap-5">
                <!-- Avatar Preview with Camera Overlay -->
                <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                    <div class="w-20 h-20 rounded-full overflow-hidden border-3 border-brand-creamAlt shadow-soft bg-brand-cream shrink-0">
                        <img :src="avatarPreview" alt="Foto Profil" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="absolute inset-0 rounded-full bg-black/40 text-white flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </div>

                <div>
                    <!-- Hidden file input triggered by button or avatar click -->
                    <input type="file" 
                           id="avatar" 
                           name="avatar" 
                           x-ref="avatarInput"
                           accept="image/png, image/jpeg, image/jpg, image/webp"
                           class="hidden"
                           @change="
                               const file = $refs.avatarInput.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => { avatarPreview = e.target.result };
                                   reader.readAsDataURL(file);
                               }
                           ">

                    <button type="button" 
                            @click="$refs.avatarInput.click()" 
                            class="px-4 py-2 bg-white border border-brand-border text-brand-primary text-xs font-semibold rounded-xl hover:bg-brand-cream hover:border-brand-primary/40 transition-colors shadow-xs">
                        Pilih Foto Baru
                    </button>
                    <p class="text-[11px] text-brand-textAlt mt-1.5">Format: JPG, PNG, WEBP. Maksimal 4MB.</p>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-brand-textAlt">
                        Email Anda belum diverifikasi.
                        <button form="send-verification" class="underline text-sm text-brand-primary hover:text-brand-primaryAlt rounded-md focus:outline-none">
                            Kirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-brand-success">
                            Link verifikasi baru telah dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" value="Nomor HP / WhatsApp" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->phone)" placeholder="08xxxxxxxxxx" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" value="Alamat Domisili" />
            <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-xs text-sm py-2.5 px-3.5" placeholder="Alamat lengkap...">{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>Simpan Perubahan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-brand-success font-medium flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Profil berhasil diperbarui!
                </p>
            @endif
        </div>
    </form>
</section>
