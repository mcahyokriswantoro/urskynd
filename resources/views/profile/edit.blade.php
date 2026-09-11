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

        <x-card class="border-brand-danger/30">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </div>
</x-app-layout>
