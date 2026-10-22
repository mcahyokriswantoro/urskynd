<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.doctors.index') }}" class="text-xs text-[#9A6A52] hover:underline font-semibold flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Dokter & Konsultasi
                </a>
                <h1 class="text-2xl font-serif font-bold text-slate-900">Tambah Dokter Konsultasi Baru</h1>
                <p class="text-xs text-slate-500">Daftarkan dokter spesialis kulit atau formulator ke direktori konsultasi URSKYND.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Terdapat kesalahan pengisian data:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.doctors.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: dr. Amanda Sp.KK, FINSDV" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Spesialisasi / Jabatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Spesialis Dermatologi & Venereologi" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Pengalaman Praktik <span class="text-rose-500">*</span></label>
                    <input type="text" name="experience" value="{{ old('experience', '8 Tahun Pengalaman') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Rumah Sakit / Klinik <span class="text-rose-500">*</span></label>
                    <input type="text" name="hospital" value="{{ old('hospital', 'Klinik Utama Kulit & Estetika URSKYND') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Rating Tampilan <span class="text-rose-500">*</span></label>
                    <input type="text" name="rating" value="{{ old('rating', '4.9') }}" placeholder="Contoh: 4.9, 5.0" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Biaya Konsultasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="price" value="{{ old('price', 'Gratis via URSKYND Support') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nomor WhatsApp Khusus (Opsional)</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="Kosongkan jika belum ada nomor khusus" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">URL Foto / Avatar Dokter</label>
                <input type="text" name="avatar" value="{{ old('avatar') }}" placeholder="https://images.unsplash.com/... atau images/doctors/..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Fokus Keahlian (Pisahkan dengan koma) <span class="text-rose-500">*</span></label>
                <input type="text" name="specialties" value="{{ old('specialties') }}" placeholder="Contoh: Jerawat Parah (Cystic), Skin Barrier Rusak, Eksim & Dermatitis" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="available" name="available" value="1" {{ old('available', '1') ? 'checked' : '' }} class="rounded border-slate-300 text-[#9A6A52] focus:ring-[#9A6A52]">
                <label for="available" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Dokter Tersedia untuk Menerima Konsultasi
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.doctors.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-white bg-[#9A6A52] hover:bg-[#855740] rounded-xl shadow-xs">
                    Simpan Dokter
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
