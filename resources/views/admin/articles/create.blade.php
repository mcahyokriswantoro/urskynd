<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-[#9A6A52] hover:underline font-semibold flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Artikel
                </a>
                <h1 class="text-2xl font-serif font-bold text-slate-900">Tulis Berita / Edukasi Baru</h1>
                <p class="text-xs text-slate-500">Buat artikel edukasi mengenai sains perawatan kulit, kandungan bahan aktif, atau tips kecantikan.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <p class="font-bold mb-1">Terdapat kesalahan input:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.articles.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Judul Artikel <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Mengapa Ceramide Kunci Utama Perbaikan Skin Barrier?" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" value="{{ old('category', 'Edukasi Kulit') }}" placeholder="Contoh: Edukasi Kulit, Bahan Aktif, Skincare Tips" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Estimasi Waktu Baca <span class="text-rose-500">*</span></label>
                    <input type="text" name="read_time" value="{{ old('read_time', '4 menit') }}" placeholder="Contoh: 3 menit, 5 menit" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Penulis / Sumber <span class="text-rose-500">*</span></label>
                    <input type="text" name="author_name" value="{{ old('author_name', 'Tim Riset & Medis URSKYND') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Role / Gelar Penulis</label>
                    <input type="text" name="author_role" value="{{ old('author_role', 'Dermatology & Clinical Advisor') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">URL Gambar Cover</label>
                    <input type="text" name="image" value="{{ old('image') }}" placeholder="https://images.unsplash.com/... atau images/news/..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ringkasan Singkat (Excerpt) <span class="text-rose-500">*</span></label>
                <textarea name="excerpt" rows="2" required placeholder="Ringkasan 1-2 kalimat yang tampil di kartu berita..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">{{ old('excerpt') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Isi Lengkap Artikel <span class="text-rose-500">*</span></label>
                <textarea name="content" rows="10" required placeholder="Tuliskan isi artikel lengkap di sini..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50 font-sans leading-relaxed">{{ old('content') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }} class="rounded border-slate-300 text-[#9A6A52] focus:ring-[#9A6A52]">
                <label for="is_published" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Publikasikan Langsung ke Halaman Berita Publik
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-white bg-[#9A6A52] hover:bg-[#855740] rounded-xl shadow-xs">
                    Terbitkan Berita
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
