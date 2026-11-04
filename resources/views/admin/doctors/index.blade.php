<x-admin-layout>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">
                    Medical & Consultation Clinic
                </span>
                <span class="text-xs text-slate-400 font-mono">Total Dokter: {{ $doctors->count() }}</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Dokter & Permohonan Konsultasi</h1>
            <p class="text-xs text-slate-500">Kelola tim dokter spesialis kulit dan pantau permohonan konsultasi yang masuk dari pasien.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.doctors.create') }}" class="px-4 py-2.5 bg-[#9A6A52] hover:bg-[#855740] text-white text-xs font-semibold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                + Tambah Dokter Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Doctors List Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-serif font-bold text-slate-900">Daftar Dokter & Formulator Aktif</h2>
            <a href="{{ route('consultation.index') }}" target="_blank" class="text-xs text-[#9A6A52] hover:underline font-semibold">
                Lihat Halaman Konsultasi Publik &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($doctors as $doc)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:border-[#9A6A52]/40 transition-all">
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $doc->avatar ?? 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=150' }}" alt="{{ $doc->name }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                            <div class="min-w-0">
                                <h3 class="font-bold text-xs text-slate-900 truncate">{{ $doc->name }}</h3>
                                <p class="text-[11px] text-[#9A6A52] font-medium truncate">{{ $doc->title }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $doc->experience }}</span>
                            </div>
                        </div>

                        <div class="text-[11px] text-slate-600 space-y-1 mb-3 pt-3 border-t border-slate-100">
                            <p class="truncate"><strong class="text-slate-800">Klinik:</strong> {{ $doc->hospital }}</p>
                            <p><strong class="text-slate-800">Rating:</strong> ⭐ {{ $doc->rating }} ({{ $doc->reviews_count }} ulasan)</p>
                            <p><strong class="text-slate-800">Biaya:</strong> <span class="text-emerald-700 font-semibold">{{ $doc->price }}</span></p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($doc->specialties as $sp)
                                    <span class="text-[9px] px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md">{{ $sp }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full inline-flex items-center gap-1.5 {{ $doc->available ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $doc->available ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                                {{ $doc->available ? 'Siap Melayani Konsultasi' : 'Segera Hadir (Coming Soon)' }}
                            </span>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.doctors.edit', $doc) }}" class="text-xs text-[#9A6A52] hover:underline font-semibold">
                                    Edit
                                </a>
                                <span class="text-slate-300">&bull;</span>
                                <form method="POST" action="{{ route('admin.doctors.destroy', $doc) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dokter ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:underline font-semibold cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Tombol Cepat Ganti Status Layanan Konsultasi -->
                        <form method="POST" action="{{ route('admin.doctors.availability', $doc) }}" class="grid grid-cols-2 gap-1.5 bg-slate-100 p-1 rounded-xl">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    name="available"
                                    value="1"
                                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold transition-all cursor-pointer {{ $doc->available ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-white' }}">
                                🟢 Siap Melayani
                            </button>
                            <button type="submit"
                                    name="available"
                                    value="0"
                                    class="py-1.5 px-2 rounded-lg text-[10px] font-bold transition-all cursor-pointer {{ !$doc->available ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:bg-white' }}">
                                ⏳ Segera Hadir
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-400 text-xs bg-white rounded-2xl border border-slate-200">
                    Belum ada dokter terdaftar. Silakan tambah dokter baru.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Patient Consultation Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
            <div>
                <h2 class="text-base font-serif font-bold text-slate-900">Permohonan Konsultasi Masuk</h2>
                <p class="text-xs text-slate-500">Daftar pengunjung / pasien yang mengisi formulir konsultasi klinik URSKYND</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-white border border-slate-200 rounded-xl text-slate-600">
                Total: {{ $bookings->total() }} Permohonan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold bg-slate-50/70">
                        <th class="py-3.5 px-6">Pasien / Nama</th>
                        <th class="py-3.5 px-6">Kontak WhatsApp</th>
                        <th class="py-3.5 px-6">Dokter Dituju</th>
                        <th class="py-3.5 px-6">Keluhan Kulit</th>
                        <th class="py-3.5 px-6">Status Tindak Lanjut</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $book)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6">
                                <p class="text-xs font-bold text-slate-900">{{ $book->name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $book->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono font-semibold text-slate-700">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $book->phone) }}" target="_blank" class="text-emerald-700 hover:underline flex items-center gap-1">
                                    <span>💬</span> {{ $book->phone }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-800">
                                {{ $book->doctor?->name ?? 'Dokter Jaga' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs">
                                <p class="font-semibold text-slate-800 truncate">{{ $book->concern }}</p>
                                @if($book->notes)
                                    <p class="text-[11px] text-slate-400 truncate">{{ $book->notes }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <form method="POST" action="{{ route('admin.bookings.status', $book) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-slate-200 bg-white {{ $book->status === 'completed' ? 'text-emerald-700 bg-emerald-50/50' : ($book->status === 'contacted' ? 'text-blue-700 bg-blue-50/50' : 'text-amber-700 bg-amber-50/50') }}">
                                        <option value="pending" {{ $book->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="contacted" {{ $book->status === 'contacted' ? 'selected' : '' }}>Sudah Dihubungi</option>
                                        <option value="completed" {{ $book->status === 'completed' ? 'selected' : '' }}>Selesai Konsultasi</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $book->phone) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold inline-flex items-center gap-1 shadow-xs">
                                    Chat WA &nearr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                Belum ada permohonan konsultasi masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
