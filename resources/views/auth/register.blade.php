@php
    $defaultType = old('account_type', (request('type') === 'affiliate' || request()->boolean('mitra')) ? 'affiliate' : 'regular');
@endphp

<x-guest-layout>
    <div x-data="{ accountType: @js($defaultType) }">
        <div class="mb-5 text-center">
            <h2 class="text-2xl font-serif font-bold text-brand-text" x-text="accountType === 'affiliate' ? 'Daftar Mitra & Afiliator' : 'Buat Akun Baru'">Buat Akun Baru</h2>
            <p class="text-xs text-brand-textAlt mt-1" x-text="accountType === 'affiliate' ? 'Jual produk resmi URSKYND & raih komisi yang bisa dicairkan kapan saja' : 'Mulai perjalanan kulit sehat impianmu bersama URSKYND'"></p>
        </div>

        <!-- Menu Pilihan Jenis Akun: Member Reguler vs Mitra / Afiliator -->
        <div class="mb-5">
            <label class="block text-[11px] font-bold uppercase tracking-wider text-brand-textAlt mb-2 text-center">
                Pilih Jenis Pendaftaran Akun
            </label>
            <div class="grid grid-cols-2 gap-2.5 p-1.5 bg-brand-cream/70 rounded-2xl border border-brand-border">
                <button type="button"
                        @click="accountType = 'regular'"
                        :class="accountType === 'regular' ? 'bg-white text-brand-primary shadow-soft border-brand-primary/30 font-bold' : 'text-brand-textAlt hover:text-brand-text border-transparent'"
                        class="py-2.5 px-3 rounded-xl border text-xs flex flex-col items-center justify-center gap-1 transition-all cursor-pointer">
                    <span class="text-lg">👤</span>
                    <span>Member Reguler</span>
                    <span class="text-[10px] font-normal opacity-80">Perawatan & Skin Check</span>
                </button>

                <button type="button"
                        @click="accountType = 'affiliate'"
                        :class="accountType === 'affiliate' ? 'bg-brand-primary text-white shadow-soft border-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-text border-transparent'"
                        class="py-2.5 px-3 rounded-xl border text-xs flex flex-col items-center justify-center gap-1 transition-all relative cursor-pointer">
                    <span class="absolute -top-2 right-2 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[9px] font-extrabold uppercase tracking-wider shadow-xs">
                        Komisi 10%+
                    </span>
                    <span class="text-lg">🤝</span>
                    <span>Mitra / Afiliator</span>
                    <span class="text-[10px] font-normal opacity-90">Dapat Komisi Penjualan</span>
                </button>
            </div>
        </div>

        <!-- Info Keuntungan Mitra / Afiliator -->
        <div x-show="accountType === 'affiliate'" x-transition class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-1.5">
            <p class="font-bold flex items-center gap-1.5 text-emerald-800">
                <span>✨</span> Keuntungan Menjadi Mitra / Afiliator Resmi URSKYND:
            </p>
            <ul class="space-y-1 text-[11px] text-emerald-800 list-disc list-inside">
                <li>Dapatkan <strong>Link &amp; Kode Referral Unik</strong> otomatis setelah daftar.</li>
                <li>Komisi langsung <strong>10%</strong> dari setiap paket/produk yang berhasil terjual.</li>
                <li>Saldo komisi transparan &amp; <strong>bisa dicairkan (withdraw)</strong> ke Bank atau E-Wallet.</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="account_type" :value="accountType">

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
                    <x-input-label for="phone" value="Nomor HP / WhatsApp Aktif" />
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

            <!-- Khusus Mitra / Afiliator: Data Kode Referral & Rekening Pencairan Komisi -->
            <div x-show="accountType === 'affiliate'" x-transition class="p-4 rounded-2xl bg-brand-cream/60 border border-brand-primary/30 space-y-3.5">
                <div class="flex items-center justify-between border-b border-brand-border pb-2">
                    <span class="text-xs font-bold text-brand-primary uppercase tracking-wider">💳 Data Mitra &amp; Rekening Pencairan Komisi</span>
                </div>

                <div>
                    <x-input-label for="affiliate_code" value="Kode Afiliasi / Referral Pilihan (Opsional)" />
                    <x-text-input id="affiliate_code" class="block mt-1 w-full uppercase font-mono" type="text" name="affiliate_code" :value="old('affiliate_code')" placeholder="Cth: SARAHGLOW (Kosongkan agar dibuat otomatis)" />
                    <p class="text-[10px] text-brand-textAlt mt-1">Hanya huruf &amp; angka tanpa spasi (4–20 karakter).</p>
                    <x-input-error :messages="$errors->get('affiliate_code')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="bank_name" value="Bank / E-Wallet Tujuan Pencairan *" />
                    <select id="bank_name" name="bank_name" :required="accountType === 'affiliate'" class="block mt-1 w-full border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/30 rounded-xl shadow-xs text-sm py-2.5 px-3">
                        <option value="">-- Pilih Bank / E-Wallet --</option>
                        @foreach(['BCA', 'Mandiri', 'BRI', 'BNI', 'BSI (Bank Syariah Indonesia)', 'CIMB Niaga', 'Bank Jago', 'SeaBank', 'DANA', 'OVO', 'GoPay', 'ShopeePay'] as $bank)
                            <option value="{{ $bank }}" @selected(old('bank_name') === $bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('bank_name')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="bank_account_number" value="No. Rekening / E-Wallet *" />
                        <x-text-input id="bank_account_number" class="block mt-1 w-full" type="text" name="bank_account_number" :value="old('bank_account_number')" ::required="accountType === 'affiliate'" placeholder="Cth: 1234567890" />
                        <x-input-error :messages="$errors->get('bank_account_number')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="bank_account_holder" value="Atas Nama Pemilik *" />
                        <x-text-input id="bank_account_holder" class="block mt-1 w-full" type="text" name="bank_account_holder" :value="old('bank_account_holder')" ::required="accountType === 'affiliate'" placeholder="Cth: Sarah Amelia" />
                        <x-input-error :messages="$errors->get('bank_account_holder')" class="mt-1" />
                    </div>
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
                    <span x-text="accountType === 'affiliate' ? '🤝 Daftar Jadi Mitra / Afiliator' : 'Daftar Akun Baru'">Daftar Akun Baru</span>
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
    </div>
</x-guest-layout>
