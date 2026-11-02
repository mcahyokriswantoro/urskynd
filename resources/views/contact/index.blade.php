<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4 border border-brand-primary/20">
                <span class="w-2 h-2 rounded-full bg-brand-success animate-pulse"></span>
                Hubungi Kami &bull; Layanan Aktif 08.00 - 21.00 WIB
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-serif font-bold text-brand-text mb-4">
                Pusat Kontak &amp; Bantuan <span class="text-brand-primary">URSKYND</span>
            </h1>
            <p class="text-brand-textAlt text-sm sm:text-base leading-relaxed">
                Punya pertanyaan seputar hasil skin check, rekomendasi produk, konfirmasi pemesanan, atau ingin konsultasi dengan dokter? Tim kami siap mendampingi perjalanan kulit sehat Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
            
            <!-- Contact Info Cards (Column 1) -->
            <div class="space-y-4">
                
                <!-- WhatsApp Priority Card -->
                <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-soft-lg">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-4">
                        💬
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-200">Customer Support Cepat</span>
                    <h3 class="text-xl font-bold mt-1 mb-2">WhatsApp Resmi</h3>
                    <p class="text-xs text-emerald-100 leading-relaxed mb-4">
                        Respon cepat untuk pertanyaan produk, konsultasi pemesanan, dan konfirmasi pengiriman paket.
                    </p>
                    <p class="font-mono font-bold text-lg text-white mb-4 tracking-wide">{{ $contactData['whatsapp'] }}</p>
                    
                    <a href="{{ $contactData['whatsapp_link'] }}" target="_blank" rel="noopener" class="w-full py-3 px-4 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                        <span>Chat WhatsApp Sekarang</span>
                    </a>
                </div>

                <!-- Email Card -->
                <div class="p-6 rounded-3xl bg-white border border-brand-border shadow-soft">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-xl mb-3">
                        ✉️
                    </div>
                    <h4 class="font-bold text-sm text-brand-text mb-1">Email Resmi</h4>
                    <p class="text-xs text-brand-textAlt mb-3">Untuk kerjasama bisnis, klinik partner, dan pertanyaan resmi.</p>
                    <a href="mailto:{{ $contactData['email'] }}" class="text-xs font-bold text-brand-primary hover:underline block break-all">
                        {{ $contactData['email'] }}
                    </a>
                </div>

                <!-- Address & Hours Card -->
                <div class="p-6 rounded-3xl bg-white border border-brand-border shadow-soft">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-xl mb-3">
                        📍
                    </div>
                    <h4 class="font-bold text-sm text-brand-text mb-1">Kantor &amp; Lab Riset</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed mb-3">
                        {{ $contactData['address'] }}
                    </p>
                    <div class="pt-3 border-t border-brand-border/60">
                        <p class="text-[11px] font-bold text-brand-text uppercase tracking-wider">Jam Operasional CS:</p>
                        <p class="text-xs text-brand-textAlt">{{ $contactData['hours'] }}</p>
                    </div>
                </div>

            </div>

            <!-- Contact Form (Columns 2 & 3) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-brand-border shadow-soft">
                    
                    <div class="mb-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-brand-primary/10 px-3 py-1 rounded-full">
                            Formulir Pesan
                        </span>
                        <h2 class="text-2xl font-serif font-bold text-brand-text mt-2">Kirim Pesan Kepada Tim Kami</h2>
                        <p class="text-xs text-brand-textAlt mt-1">
                            Silakan isi detail pertanyaan Anda. Anda dapat memilih untuk langsung terhubung ke WhatsApp CS kami.
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
                            <p class="font-bold mb-1">Mohon perbaiki formulir berikut:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="Contoh: Maya Anggraeni" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                    Kontak (No. WA / Email) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="contact" value="{{ old('contact', auth()->user()->email ?? '') }}" placeholder="Contoh: 081234567890 atau email@domain.com" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                Topik / Subjek Pertanyaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Contoh: Konsultasi Paket Pembelian Skincare / Hasil AI Skin Check" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-text uppercase tracking-wider mb-2">
                                Isi Pesan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" rows="5" placeholder="Tuliskan pertanyaan atau kebutuhan Anda secara detail..." class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text text-sm focus:border-brand-primary focus:ring-brand-primary/20" required>{{ old('message') }}</textarea>
                        </div>

                        <!-- Checkbox Direct to WhatsApp -->
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="redirect_wa" value="1" checked class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-xs font-bold text-emerald-900 block">Kirim langsung pesan ini via WhatsApp Customer Service (085282784356)</span>
                                    <span class="text-[11px] text-emerald-700 block mt-0.5">Setelah klik tombol kirim, Anda akan langsung diarahkan ke chat WhatsApp resmi dengan teks pesan rapi yang terisi otomatis.</span>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-3.5 px-6 bg-brand-primary hover:bg-brand-primaryAlt text-white font-bold rounded-xl transition-all shadow-soft flex items-center justify-center gap-2 text-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span>Kirim Pesan Sekarang</span>
                        </button>
                    </form>

                </div>
            </div>

        </div>

    </div>
</x-dynamic-component>
