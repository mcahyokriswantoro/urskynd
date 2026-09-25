<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-serif font-bold text-brand-text mb-1">Pengaturan Profil</h1>
        <p class="text-sm text-brand-textAlt">Kelola informasi akun dan kata sandimu.</p>
    </div>

    <div class="space-y-6 max-w-3xl">
        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <x-card class="border-brand-border">
            <div class="max-w-xl flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-brand-text text-base">Keluar dari Akun</h3>
                    <p class="text-xs text-brand-textAlt mt-0.5">Akhiri sesi login Anda di perangkat ini.</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-brand-cream border border-brand-border text-brand-danger hover:bg-brand-danger/10 text-sm font-semibold rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </x-card>

        <x-card class="border-brand-danger/30">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
</x-app-layout>
