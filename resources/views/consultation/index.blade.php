<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        
        <!-- Header / Banner -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                Layanan Klinis & Telekonsultasi
            </span>
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-brand-text mb-4">
                Konsultasi Dokter Spesialis Kulit
            </h1>
            <p class="text-brand-textAlt text-sm md:text-base leading-relaxed">
                Diskusikan masalah jerawat membandel, skin barrier rusak, atau noda flek hitam dengan dermatologis terpercaya. Dapatkan evaluasi medis dan panduan resep yang aman.
            </p>
        </div>

        <!-- Quick Consultation Action Card -->
        <div class="bg-white rounded-3xl border border-brand-border p-6 md:p-10 shadow-soft-lg mb-16 relative overflow-hidden">
        @if(session('success'))
            <div class="mb-8 p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm flex items-start gap-3">
                <span class="text-xl">✅</span>
                <div>
                    <h3 class="font-bold text-sm">Permohonan Konsultasi Terkirim!</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Consultation Booking Card -->
        <div id="consultation-form-section" class="bg-white rounded-3xl border border-brand-border p-6 md:p-10 shadow-soft mb-16 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-brand-secondaryLight/20 rounded-full blur-2xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-5 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary">Respon Cepat Tim Medis</span>
                    <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-text">
                        Konsultasi Online Kulit & Skincare
                    </h2>
                    <p class="text-sm text-brand-textAlt leading-relaxed">
                        Pilih keluhan kulit Anda di formulir ini. Permohonan Anda akan langsung masuk ke sistem dokter jaga dan beauty advisor URSKYND untuk dijadwalkan sesi konsultasi.
                    </p>
                    
                    <div class="space-y-2.5 pt-2 text-xs md:text-sm font-medium text-brand-text">
                        <div class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-success/20 text-brand-success flex items-center justify-center font-bold text-xs">✓</span>
                            <span>Privasi dan kerahasiaan data pasien terjamin 100%</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-success/20 text-brand-success flex items-center justify-center font-bold text-xs">✓</span>
                            <span>Rekomendasi disesuaikan dengan tipe & kondisi kulit</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-brand-success/20 text-brand-success flex items-center justify-center font-bold text-xs">✓</span>
                            <span>Bebas biaya konsultasi awal untuk pengguna URSKYND</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-brand-cream/40 p-6 md:p-8 rounded-2xl border border-brand-border">
                    <form action="{{ route('consultation.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text mb-1.5 uppercase tracking-wider">Nama Anda <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" value="{{ auth()->user()->name ?? old('name') }}" placeholder="Contoh: Rina Sari" required class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-text mb-1.5 uppercase tracking-wider">Nomor Ponsel / WhatsApp Aktif <span class="text-rose-500">*</span></label>
                                <input type="tel" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" placeholder="Contoh: 08xxxxxxxxxx" required class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-brand-text mb-1.5 uppercase tracking-wider">Pilih Dokter Tujuan</label>
                                <select name="doctor_id" class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs">
                                    <option value="">Dokter Jaga Hari Ini (Rekomendasi Cepat)</option>
                                    @foreach($doctors->where('available', true) as $doc)
                                        <option value="{{ $doc['id'] }}">{{ $doc['name'] }} ({{ $doc['specialties'][0] ?? 'Dermatologi' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-brand-text mb-1.5 uppercase tracking-wider">Keluhan Kulit Utama <span class="text-rose-500">*</span></label>
                                <select name="concern" required class="w-full px-4 py-3 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs">
                                    <option value="" disabled selected>Pilih salah satu keluhan...</option>
                                    @foreach($concerns as $concern)
                                        <option value="{{ $concern }}">{{ $concern }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-brand-text mb-1.5 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" rows="2" placeholder="Ceritakan riwayat skincare atau gejala yang Anda rasakan..." class="w-full px-4 py-2.5 rounded-xl border-brand-border bg-white text-brand-text focus:border-brand-primary focus:ring-brand-primary/20 text-sm shadow-xs"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt transition-all shadow-soft flex items-center justify-center gap-2 text-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Kirim Formulir Konsultasi Kulit
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dermatologists Directory -->
        <div class="mb-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-1 block">Tim Medis Terverifikasi</span>
                    <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-text">Daftar Dokter & Formulator Kami</h2>
                </div>
                <p class="text-xs text-brand-textAlt mt-2 sm:mt-0">Tersedia untuk konsultasi online terjadwal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($doctors as $doctor)
                    <div class="bg-white rounded-3xl border border-brand-border p-6 shadow-soft hover:shadow-soft-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-start gap-4 mb-4">
                                <img src="{{ $doctor['avatar'] }}" alt="{{ $doctor['name'] }}" class="w-20 h-20 rounded-2xl object-cover border border-brand-border shadow-xs shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h3 class="font-bold font-serif text-brand-text text-lg truncate">{{ $doctor['name'] }}</h3>
                                        @if($doctor['available'])
                                            <span class="px-2.5 py-0.5 bg-brand-success/10 text-brand-success text-[10px] font-bold rounded-full shrink-0 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-success animate-pulse"></span>
                                                Siap Melayani
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-full shrink-0 flex items-center gap-1">
                                                <span>⏳</span>
                                                Segera Hadir
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-brand-primary font-medium">{{ $doctor['title'] }}</p>
                                    <p class="text-xs text-brand-textAlt mt-0.5">{{ $doctor['hospital'] }}</p>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-brand-textAlt">
                                        <span class="font-medium text-brand-text">⭐ {{ $doctor['rating'] }} ({{ $doctor['reviews_count'] }} ulasan)</span>
                                        <span>•</span>
                                        <span>{{ $doctor['experience'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-brand-border/40 mb-4">
                                <p class="text-[10px] uppercase font-bold tracking-wider text-brand-textAlt mb-2">Fokus Keahlian:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($doctor['specialties'] as $spec)
                                        <span class="px-2.5 py-1 bg-brand-cream border border-brand-border/60 rounded-full text-xs text-brand-text">
                                            {{ $spec }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-brand-border/40 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-brand-textAlt">Biaya Konsultasi</p>
                                <p class="text-xs font-bold text-brand-success">{{ $doctor['price'] }}</p>
                            </div>

                            @if($doctor['available'])
                                <button type="button" onclick="const sel = document.querySelector('select[name=doctor_id]'); if(sel) { sel.value = '{{ $doctor['id'] }}'; } document.getElementById('consultation-form-section').scrollIntoView({behavior: 'smooth'});" class="px-4 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primaryAlt transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                    <span>Konsultasi Sekarang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            @else
                                <span class="px-4 py-2 bg-slate-100 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold flex items-center gap-1.5 cursor-not-allowed">
                                    <span>⏳ Jadwal Segera Hadir</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
            </div>
        </div>

        <!-- How Consultation Works -->
        <div class="bg-brand-cream/50 rounded-3xl border border-brand-border/70 p-8 md:p-12 mb-16">
            <div class="text-center max-w-xl mx-auto mb-10">
                <h3 class="text-2xl font-serif font-bold text-brand-text mb-2">3 Langkah Mudah Konsultasi</h3>
                <p class="text-xs md:text-sm text-brand-textAlt">Proses aman, cepat, dan sepenuhnya online dari mana saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white p-6 rounded-2xl border border-brand-border/50 shadow-xs">
                    <div class="w-12 h-12 bg-brand-primary/10 text-brand-primary font-bold font-serif text-lg rounded-2xl flex items-center justify-center mx-auto mb-4">1</div>
                    <h4 class="font-bold text-brand-text text-sm mb-1">Pilih Dokter & Keluhan</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed">Tentukan spesialisasi yang sesuai dengan problem kulit yang Anda hadapi.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-brand-border/50 shadow-xs">
                    <div class="w-12 h-12 bg-brand-primary/10 text-brand-primary font-bold font-serif text-lg rounded-2xl flex items-center justify-center mx-auto mb-4">2</div>
                    <h4 class="font-bold text-brand-text text-sm mb-1">Kirim Foto & Diskusi</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed">Sampaikan riwayat produk dan foto kondisi wajah dengan pencahayaan terang.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-brand-border/50 shadow-xs">
                    <div class="w-12 h-12 bg-brand-primary/10 text-brand-primary font-bold font-serif text-lg rounded-2xl flex items-center justify-center mx-auto mb-4">3</div>
                    <h4 class="font-bold text-brand-text text-sm mb-1">Dapatkan Solusi Personal</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed">Dermatologis merumuskan anjuran skincare dan gaya hidup yang tepat sasaran.</p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="max-w-3xl mx-auto mb-12">
            <h3 class="text-xl md:text-2xl font-serif font-bold text-center text-brand-text mb-6">Pertanyaan yang Sering Diajukan</h3>
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-xs">
                    <h4 class="font-bold text-sm text-brand-text mb-1">Apakah konsultasi online ini aman dan terpercaya?</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed">Ya, seluruh dokter yang tergabung memiliki Surat Tanda Registrasi (STR) aktif dan berpengalaman menangani ribuan kasus dermatologi klinis.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-xs">
                    <h4 class="font-bold text-sm text-brand-text mb-1">Kapan saya harus konsultasi daripada beli skincare biasa?</h4>
                    <p class="text-xs text-brand-textAlt leading-relaxed">Jika jerawat Anda berbentuk benjolan bernanah/kistik yang nyeri, atau kulit mengalami iritasi terbakar kemerahan hebat, penanganan dokter sangat disarankan untuk mencegah timbulnya bekas bopeng permanen.</p>
                </div>
            </div>
        </div>

    </div>
</x-dynamic-component>
