<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-serif font-bold text-brand-text">Buat Akun Baru</h2>
        <p class="text-xs text-brand-textAlt mt-1">Mulai perjalanan kulit sehat impianmu bersama URSKYND</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" placeholder="Cth: Sarah Amelia" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Phone & Age Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Nomor HP -->
            <div>
                <x-input-label for="phone" value="Nomor HP / WhatsApp" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="08xxxxxxxxxx" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>

            <!-- Umur -->
            <div>
                <x-input-label for="age" value="Umur (Tahun)" />
                <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" min="10" max="120" :value="old('age')" placeholder="Cth: 24" required />
                <x-input-error :messages="$errors->get('age')" class="mt-1" />
            </div>
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" value="Alamat Domisili (Opsional)" />
            <textarea id="address" 
                      name="address" 
                      rows="2" 
                      class="block mt-1 w-full border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-xs text-sm py-2 px-3" 
                      placeholder="Cth: Jl. Sudirman No. 12, Jakarta">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" 
                            placeholder="Ulangi kata sandi"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 text-base shadow-soft hover:shadow-soft-lg">
                Daftar Akun Baru
            </x-primary-button>
        </div>

        <div class="text-center pt-2">
            <p class="text-xs text-brand-textAlt">
                Sudah memiliki akun?
                <a class="font-bold text-brand-primary hover:underline ml-1" href="{{ route('login') }}">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
