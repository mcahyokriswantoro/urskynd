<x-guest-layout>
    <div class="mb-5 text-center">
        <div class="w-12 h-12 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-2xl mx-auto mb-3">
            🔐
        </div>
        <h2 class="text-2xl font-serif font-bold text-brand-text">Lupa Kata Sandi?</h2>
        <p class="text-xs text-brand-textAlt mt-1 leading-relaxed">
            Masukkan alamat email yang terdaftar pada akun URSKYND Anda. Kami akan mengirimkan tautan pemulihan ke email Anda sekaligus menyediakan akses reset langsung.
        </p>
    </div>

    <!-- Session Status & Tombol Lanjut Reset Langsung -->
    @if (session('status'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-3">
            <div class="flex items-start gap-2.5">
                <span class="text-lg">✅</span>
                <div class="text-xs">
                    <p class="font-bold text-emerald-800">{{ session('status') }}</p>
                    @if(session('reset_email'))
                        <p class="text-emerald-700 mt-0.5">Email tujuan: <strong>{{ session('reset_email') }}</strong></p>
                    @endif
                </div>
            </div>

            @if (session('direct_reset_url'))
                <div class="pt-2 border-t border-emerald-200/80">
                    <p class="text-[11px] text-emerald-800 mb-2">
                        Belum menerima email atau ingin langsung mengganti kata sandi sekarang?
                    </p>
                    <a href="{{ session('direct_reset_url') }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-colors">
                        <span>⚡ Lanjut Reset Kata Sandi Sekarang</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email Terdaftar" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', session('reset_email'))" placeholder="nama@email.com" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full justify-center py-3">
                Kirim Link Reset &amp; Buat Token Pemulihan
            </x-primary-button>
        </div>

        <div class="text-center pt-2 border-t border-brand-border/60">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-brand-primary hover:underline">
                &larr; Kembali ke Halaman Masuk (Login)
            </a>
        </div>
    </form>
</x-guest-layout>
