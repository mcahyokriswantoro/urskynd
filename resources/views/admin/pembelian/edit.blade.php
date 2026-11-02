@php
    $inputCls = 'w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50';
    $labelCls = 'block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider';
    $withUid = fn ($rows) => collect($rows)->values()->map(fn ($r, $i) => array_merge($r, ['_uid' => 'u' . $i . '_' . uniqid()]))->all();
    $editorState = [
        'packages' => $withUid($content['packages'] ?? []),
        'products' => $withUid($content['products'] ?? []),
        'testimonials' => $withUid($content['testimonials'] ?? []),
        'faqs' => $withUid($content['faqs'] ?? []),
        'trust' => $withUid($content['trust'] ?? []),
    ];
    $g = $content['general'];
    $pr = $content['problem'];
@endphp

<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-6"
         x-data="purchasePageEditor(@js($editorState))">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-[11px] font-mono uppercase tracking-widest text-[#9A6A52] font-bold mb-1">Konten Halaman Publik</p>
                <h1 class="text-2xl font-serif font-bold text-slate-900">Kelola Halaman Detail Pembelian</h1>
                <p class="text-xs text-slate-500">Edit teks, harga paket, foto produk, testimoni, dan FAQ di halaman <span class="font-mono">/detail-pembelian</span>.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pembelian.index') }}" target="_blank" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl">Pratinjau Halaman &nearr;</a>
                <form action="{{ route('admin.pembelian.reset') }}" method="POST" onsubmit="return confirm('Kembalikan semua konten ke pengaturan awal? Perubahan Anda akan hilang.')">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl">Reset Default</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

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

        <!-- Tabs -->
        <div class="sticky top-16 md:top-20 z-20 bg-[#F4F6F8]/95 backdrop-blur py-2 -mx-1 px-1">
            <div class="flex gap-1.5 overflow-x-auto bg-white p-1.5 rounded-2xl border border-slate-200 shadow-xs">
                <template x-for="t in tabs" :key="t.id">
                    <button type="button" @click="tab = t.id"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-colors"
                            :class="tab === t.id ? 'bg-[#9A6A52] text-white shadow' : 'text-slate-600 hover:bg-slate-100'"
                            x-text="t.label"></button>
                </template>
            </div>
        </div>

        <form action="{{ route('admin.pembelian.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- ============ UMUM ============ -->
            <section x-show="tab === 'general'" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
                <h2 class="text-sm font-bold text-slate-900">📞 Kontak WhatsApp</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $labelCls }}">Nomor WA (format 62xxx) <span class="text-rose-500">*</span></label>
                        <input type="text" name="general[whatsapp_number]" value="{{ old('general.whatsapp_number', $g['whatsapp_number']) }}" required class="{{ $inputCls }}" placeholder="6285282784356">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Nomor yang Ditampilkan</label>
                        <input type="text" name="general[whatsapp_display]" value="{{ old('general.whatsapp_display', $g['whatsapp_display']) }}" class="{{ $inputCls }}" placeholder="085282784356">
                    </div>
                </div>

                <hr class="border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">🔥 Banner Promo</h2>
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="{{ $labelCls }}">Judul Banner</label>
                        <input type="text" name="general[banner_title]" value="{{ old('general.banner_title', $g['banner_title']) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Sub-judul Banner</label>
                        <input type="text" name="general[banner_subtitle]" value="{{ old('general.banner_subtitle', $g['banner_subtitle']) }}" class="{{ $inputCls }}">
                    </div>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                        <input type="checkbox" name="general[show_countdown]" value="1" @checked(old('general.show_countdown', $g['show_countdown'])) class="rounded text-[#9A6A52] focus:ring-[#9A6A52]">
                        Tampilkan hitung mundur (countdown) promo harian
                    </label>
                </div>

                <hr class="border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">✨ Hero / Judul Utama</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-3">
                        <label class="{{ $labelCls }}">Label Kecil di Atas Judul</label>
                        <input type="text" name="general[hero_badge]" value="{{ old('general.hero_badge', $g['hero_badge']) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Judul (awal) <span class="text-rose-500">*</span></label>
                        <input type="text" name="general[hero_title]" value="{{ old('general.hero_title', $g['hero_title']) }}" required class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Kata Berwarna</label>
                        <input type="text" name="general[hero_highlight]" value="{{ old('general.hero_highlight', $g['hero_highlight']) }}" class="{{ $inputCls }}">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">Judul (akhir)</label>
                        <input type="text" name="general[hero_title_suffix]" value="{{ old('general.hero_title_suffix', $g['hero_title_suffix']) }}" class="{{ $inputCls }}">
                    </div>
                    <div class="md:col-span-3">
                        <label class="{{ $labelCls }}">Deskripsi</label>
                        <textarea name="general[hero_description]" rows="3" class="{{ $inputCls }}">{{ old('general.hero_description', $g['hero_description']) }}</textarea>
                    </div>
                    <div class="md:col-span-3" x-data="{ preview: @js(\App\Models\PageContent::imageUrl($g['hero_image'])), removed: false }">
                        <label class="{{ $labelCls }}">Foto Banner Hero (opsional)</label>
                        <div class="flex items-start gap-4">
                            <div class="w-40 h-24 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">
                                <template x-if="preview && !removed"><img :src="preview" class="w-full h-full object-cover"></template>
                                <template x-if="!preview || removed"><span class="text-[10px] text-slate-400">Belum ada foto</span></template>
                            </div>
                            <div class="space-y-2 text-xs">
                                <input type="file" name="general[hero_image_file]" accept="image/*" @change="const f=$event.target.files[0]; if(f){ preview = URL.createObjectURL(f); removed=false }" class="text-xs file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#9A6A52]/10 file:text-[#9A6A52] file:font-semibold">
                                <label class="flex items-center gap-2 text-slate-600" x-show="preview">
                                    <input type="checkbox" name="general[remove_hero_image]" value="1" x-model="removed" class="rounded text-rose-500"> Hapus foto
                                </label>
                                <p class="text-[10px] text-slate-400">JPG/PNG/WEBP, maks 4MB.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">🏷️ Judul Setiap Bagian</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach([
                        'packages_title' => 'Judul Bagian Paket', 'packages_subtitle' => 'Sub-judul Bagian Paket',
                        'products_title' => 'Judul Katalog Produk', 'products_subtitle' => 'Sub-judul Katalog Produk',
                        'order_title' => 'Judul Form Pemesanan', 'testimonials_title' => 'Judul Testimoni',
                        'faq_title' => 'Judul FAQ',
                    ] as $key => $label)
                        <div>
                            <label class="{{ $labelCls }}">{{ $label }}</label>
                            <input type="text" name="general[{{ $key }}]" value="{{ old("general.$key", $g[$key]) }}" class="{{ $inputCls }}">
                        </div>
                    @endforeach
                    <div class="md:col-span-2">
                        <label class="{{ $labelCls }}">Deskripsi Form Pemesanan</label>
                        <textarea name="general[order_description]" rows="2" class="{{ $inputCls }}">{{ old('general.order_description', $g['order_description']) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $labelCls }}">Metode Pembayaran (satu per baris)</label>
                        <textarea name="general[payment_methods]" rows="2" class="{{ $inputCls }}">{{ old('general.payment_methods', $g['payment_methods']) }}</textarea>
                    </div>
                </div>

                <hr class="border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">📲 Pengaturan QRIS Resmi</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="{{ $labelCls }}">Nama Merchant QRIS</label>
                        <input type="text" name="general[qris_merchant]" value="{{ old('general.qris_merchant', $g['qris_merchant'] ?? 'URSKYND SKINCARE') }}" class="{{ $inputCls }}" placeholder="URSKYND SKINCARE">
                    </div>
                    <div>
                        <label class="{{ $labelCls }}">NMID QRIS</label>
                        <input type="text" name="general[qris_nmid]" value="{{ old('general.qris_nmid', $g['qris_nmid'] ?? 'ID1026569298288') }}" class="{{ $inputCls }}" placeholder="ID1026569298288">
                    </div>
                    <div class="md:col-span-2" x-data="{ preview: @js(\App\Models\PageContent::imageUrl($g['qris_image'] ?? null)), removed: false }">
                        <label class="{{ $labelCls }}">Gambar Kode QRIS</label>
                        <div class="flex items-start gap-4">
                            <div class="w-32 h-40 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">
                                <template x-if="preview && !removed"><img :src="preview" class="w-full h-full object-contain"></template>
                                <template x-if="!preview || removed"><span class="text-[10px] text-slate-400">Belum ada QRIS</span></template>
                            </div>
                            <div class="space-y-2 text-xs">
                                <input type="file" name="general[qris_image_file]" accept="image/*" @change="const f=$event.target.files[0]; if(f){ preview = URL.createObjectURL(f); removed=false }" class="text-xs file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#9A6A52]/10 file:text-[#9A6A52] file:font-semibold">
                                <label class="flex items-center gap-2 text-slate-600" x-show="preview">
                                    <input type="checkbox" name="general[remove_qris_image]" value="1" x-model="removed" class="rounded text-rose-500"> Sembunyikan gambar QRIS
                                </label>
                                <p class="text-[10px] text-slate-400">JPG/PNG/WEBP, maks 4MB. Default menggunakan QRIS URSKYND SKINCARE.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============ KEUNGGULAN & MASALAH ============ -->
            <section x-show="tab === 'problem'" x-cloak class="space-y-6">
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-slate-900">🛡️ Poin Kepercayaan (di bawah judul)</h2>
                        <button type="button" @click="add('trust', { icon: '⭐', title: '', subtitle: '' })" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#9A6A52] text-white hover:bg-[#845843]">+ Tambah</button>
                    </div>
                    <template x-for="(t, i) in trust" :key="t._uid">
                        <div class="grid grid-cols-12 gap-3 items-end">
                            <div class="col-span-2"><label class="{{ $labelCls }}">Ikon</label><input type="text" :name="`trust[${i}][icon]`" x-model="t.icon" class="{{ $inputCls }} text-center"></div>
                            <div class="col-span-4"><label class="{{ $labelCls }}">Judul</label><input type="text" :name="`trust[${i}][title]`" x-model="t.title" class="{{ $inputCls }}"></div>
                            <div class="col-span-5"><label class="{{ $labelCls }}">Keterangan</label><input type="text" :name="`trust[${i}][subtitle]`" x-model="t.subtitle" class="{{ $inputCls }}"></div>
                            <div class="col-span-1"><button type="button" @click="remove('trust', i)" class="w-full py-2.5 text-rose-500 hover:bg-rose-50 rounded-xl" title="Hapus">✕</button></div>
                        </div>
                    </template>
                </div>

                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h2 class="text-sm font-bold text-rose-600">❌ Masalah Pelanggan</h2>
                        <div><label class="{{ $labelCls }}">Judul</label><input type="text" name="problem[title]" value="{{ old('problem.title', $pr['title']) }}" class="{{ $inputCls }}"></div>
                        <div><label class="{{ $labelCls }}">Deskripsi</label><textarea name="problem[description]" rows="4" class="{{ $inputCls }}">{{ old('problem.description', $pr['description']) }}</textarea></div>
                        <div><label class="{{ $labelCls }}">Poin Masalah (satu per baris)</label><textarea name="problem[points]" rows="4" class="{{ $inputCls }}">{{ old('problem.points', $pr['points']) }}</textarea></div>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-sm font-bold text-emerald-700">✅ Solusi URSKYND</h2>
                        <div><label class="{{ $labelCls }}">Judul</label><input type="text" name="problem[solution_title]" value="{{ old('problem.solution_title', $pr['solution_title']) }}" class="{{ $inputCls }}"></div>
                        <div><label class="{{ $labelCls }}">Deskripsi</label><textarea name="problem[solution_description]" rows="4" class="{{ $inputCls }}">{{ old('problem.solution_description', $pr['solution_description']) }}</textarea></div>
                        <div><label class="{{ $labelCls }}">Poin Solusi (satu per baris)</label><textarea name="problem[solution_points]" rows="4" class="{{ $inputCls }}">{{ old('problem.solution_points', $pr['solution_points']) }}</textarea></div>
                    </div>
                </div>
            </section>

            <!-- ============ PAKET ============ -->
            <section x-show="tab === 'packages'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500"><span x-text="packages.length"></span> paket aktif. Urutan di sini = urutan di halaman.</p>
                    <button type="button" @click="add('packages', { name: 'Paket Baru', tag: '', badge: '', items: '', original_price: 0, price: 0, free_shipping: '', popular: false, wa_message: '', image: null })" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#9A6A52] text-white hover:bg-[#845843]">+ Tambah Paket</button>
                </div>

                <template x-for="(p, i) in packages" :key="p._uid">
                    <div class="bg-white p-6 rounded-2xl border shadow-xs space-y-4" :class="p.popular ? 'border-[#9A6A52] ring-2 ring-[#9A6A52]/15' : 'border-slate-200'">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-7 h-7 rounded-lg bg-slate-900 text-white text-xs font-bold flex items-center justify-center shrink-0" x-text="i + 1"></span>
                                <h3 class="text-sm font-bold text-slate-900 truncate" x-text="p.name || 'Paket tanpa nama'"></h3>
                                <span x-show="p.popular" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">⭐ Populer</span>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" @click="move('packages', i, -1)" :disabled="i === 0" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 disabled:opacity-30" title="Naik">↑</button>
                                <button type="button" @click="move('packages', i, 1)" :disabled="i === packages.length - 1" class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 disabled:opacity-30" title="Turun">↓</button>
                                <button type="button" @click="confirm('Hapus paket ini?') && remove('packages', i)" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:row-span-3">
                                <label class="{{ $labelCls }}">Foto Paket</label>
                                @include('admin.pembelian._image', ['list' => 'packages', 'field' => 'image'])
                            </div>
                            <div class="md:col-span-3"><label class="{{ $labelCls }}">Nama Paket *</label><input type="text" :name="`packages[${i}][name]`" x-model="p.name" required class="{{ $inputCls }}"></div>
                            <div class="md:col-span-1"><label class="{{ $labelCls }}">Label / Tag</label><input type="text" :name="`packages[${i}][tag]`" x-model="p.tag" class="{{ $inputCls }}"></div>
                            <div class="md:col-span-1"><label class="{{ $labelCls }}">Badge Diskon</label><input type="text" :name="`packages[${i}][badge]`" x-model="p.badge" class="{{ $inputCls }}"></div>
                            <div class="md:col-span-1"><label class="{{ $labelCls }}">Info Ongkir</label><input type="text" :name="`packages[${i}][free_shipping]`" x-model="p.free_shipping" class="{{ $inputCls }}"></div>
                            <div><label class="{{ $labelCls }}">Harga Coret (Rp)</label><input type="number" min="0" :name="`packages[${i}][original_price]`" x-model="p.original_price" class="{{ $inputCls }}"></div>
                            <div><label class="{{ $labelCls }}">Harga Jual (Rp) *</label><input type="number" min="0" :name="`packages[${i}][price]`" x-model="p.price" required class="{{ $inputCls }}"></div>
                            <div class="flex items-end">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 pb-2.5">
                                    <input type="checkbox" :name="`packages[${i}][popular]`" value="1" x-model="p.popular" class="rounded text-[#9A6A52] focus:ring-[#9A6A52]"> Tandai Paling Populer
                                </label>
                            </div>
                            <div class="md:col-span-2"><label class="{{ $labelCls }}">Isi Paket & Bonus (satu per baris)</label><textarea rows="5" :name="`packages[${i}][items]`" x-model="p.items" class="{{ $inputCls }}" placeholder="1x Face Wash&#10;Bonus: Pouch"></textarea><p class="text-[10px] text-slate-400 mt-1">Baris yang mengandung kata "Bonus" akan ditebalkan.</p></div>
                            <div class="md:col-span-2"><label class="{{ $labelCls }}">Pesan Otomatis WhatsApp</label><textarea rows="5" :name="`packages[${i}][wa_message]`" x-model="p.wa_message" class="{{ $inputCls }}"></textarea></div>
                        </div>
                        <p class="text-[11px] text-slate-500" x-show="Number(p.original_price) > Number(p.price) && Number(p.price) > 0">
                            💡 Hemat <strong x-text="Math.round((1 - p.price / p.original_price) * 100) + '%'"></strong> dari harga normal.
                        </p>
                    </div>
                </template>
            </section>

            <!-- ============ PRODUK ============ -->
            <section x-show="tab === 'products'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Kartu produk di bagian "Katalog Produk Resmi".</p>
                    <button type="button" @click="add('products', { step: '', name: 'Produk Baru', description: '', price: 0, image: null })" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#9A6A52] text-white hover:bg-[#845843]">+ Tambah Produk</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="(p, i) in products" :key="p._uid">
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-400" x-text="'#' + (i + 1)"></span>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="move('products', i, -1)" :disabled="i === 0" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↑</button>
                                    <button type="button" @click="move('products', i, 1)" :disabled="i === products.length - 1" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↓</button>
                                    <button type="button" @click="confirm('Hapus produk ini?') && remove('products', i)" class="px-2 py-1 rounded text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                </div>
                            </div>
                            @include('admin.pembelian._image', ['list' => 'products', 'field' => 'image'])
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="{{ $labelCls }}">Label Langkah</label><input type="text" :name="`products[${i}][step]`" x-model="p.step" class="{{ $inputCls }}" placeholder="Langkah 1: Cleanser"></div>
                                <div><label class="{{ $labelCls }}">Harga (Rp)</label><input type="number" min="0" :name="`products[${i}][price]`" x-model="p.price" class="{{ $inputCls }}"></div>
                            </div>
                            <div><label class="{{ $labelCls }}">Nama Produk *</label><input type="text" :name="`products[${i}][name]`" x-model="p.name" required class="{{ $inputCls }}"></div>
                            <div><label class="{{ $labelCls }}">Deskripsi</label><textarea rows="3" :name="`products[${i}][description]`" x-model="p.description" class="{{ $inputCls }}"></textarea></div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- ============ TESTIMONI ============ -->
            <section x-show="tab === 'testimonials'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Ulasan pelanggan. Foto opsional (jika kosong, tampil inisial nama).</p>
                    <button type="button" @click="add('testimonials', { name: '', location: '', rating: 5, text: '', photo: null })" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#9A6A52] text-white hover:bg-[#845843]">+ Tambah Testimoni</button>
                </div>
                <template x-for="(t, i) in testimonials" :key="t._uid">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="{{ $labelCls }}">Foto</label>
                            @include('admin.pembelian._image', ['list' => 'testimonials', 'field' => 'photo', 'round' => true])
                        </div>
                        <div class="md:col-span-4 space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div><label class="{{ $labelCls }}">Nama *</label><input type="text" :name="`testimonials[${i}][name]`" x-model="t.name" required class="{{ $inputCls }}"></div>
                                <div><label class="{{ $labelCls }}">Kota</label><input type="text" :name="`testimonials[${i}][location]`" x-model="t.location" class="{{ $inputCls }}"></div>
                                <div>
                                    <label class="{{ $labelCls }}">Rating</label>
                                    <input type="hidden" :name="`testimonials[${i}][rating]`" :value="t.rating">
                                    <div class="flex items-center gap-0.5 py-1.5">
                                        <template x-for="s in 5" :key="s">
                                            <button type="button" @click="t.rating = s" class="text-xl leading-none" :class="s <= t.rating ? 'text-amber-400' : 'text-slate-200'">★</button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div><label class="{{ $labelCls }}">Isi Testimoni *</label><textarea rows="3" :name="`testimonials[${i}][text]`" x-model="t.text" required class="{{ $inputCls }}"></textarea></div>
                            <div class="flex justify-end gap-1">
                                <button type="button" @click="move('testimonials', i, -1)" :disabled="i === 0" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↑</button>
                                <button type="button" @click="move('testimonials', i, 1)" :disabled="i === testimonials.length - 1" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↓</button>
                                <button type="button" @click="confirm('Hapus testimoni ini?') && remove('testimonials', i)" class="px-2 py-1 rounded text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                            </div>
                        </div>
                    </div>
                </template>
            </section>

            <!-- ============ FAQ ============ -->
            <section x-show="tab === 'faqs'" x-cloak class="space-y-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500">Tanya jawab seputar pemesanan.</p>
                    <button type="button" @click="add('faqs', { question: '', answer: '' })" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#9A6A52] text-white hover:bg-[#845843]">+ Tambah FAQ</button>
                </div>
                <template x-for="(f, i) in faqs" :key="f._uid">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-400" x-text="'Q' + (i + 1)"></span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="move('faqs', i, -1)" :disabled="i === 0" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↑</button>
                                <button type="button" @click="move('faqs', i, 1)" :disabled="i === faqs.length - 1" class="p-1 rounded text-slate-500 hover:bg-slate-100 disabled:opacity-30">↓</button>
                                <button type="button" @click="remove('faqs', i)" class="px-2 py-1 rounded text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                            </div>
                        </div>
                        <div><label class="{{ $labelCls }}">Pertanyaan *</label><input type="text" :name="`faqs[${i}][question]`" x-model="f.question" required class="{{ $inputCls }}"></div>
                        <div><label class="{{ $labelCls }}">Jawaban *</label><textarea rows="3" :name="`faqs[${i}][answer]`" x-model="f.answer" required class="{{ $inputCls }}"></textarea></div>
                    </div>
                </template>
            </section>

            <!-- Sticky Save Bar -->
            <div class="sticky bottom-4 z-20">
                <div class="bg-slate-900 text-white rounded-2xl px-5 py-3.5 shadow-2xl flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-300 hidden sm:block">Perubahan di semua tab disimpan sekaligus.</p>
                    <button type="submit" class="ml-auto px-6 py-2.5 bg-[#9A6A52] hover:bg-[#B07C61] text-white text-sm font-bold rounded-xl shadow-lg transition-colors">💾 Simpan Semua Perubahan</button>
                </div>
            </div>
        </form>
    </div>

    <style>[x-cloak]{display:none!important}</style>
    <script>
        function purchasePageEditor(initial) {
            return {
                tab: 'general',
                tabs: [
                    { id: 'general', label: '⚙️ Umum & Hero' },
                    { id: 'problem', label: '🛡️ Keunggulan & Masalah' },
                    { id: 'packages', label: '📦 Paket Promo' },
                    { id: 'products', label: '🧴 Katalog Produk' },
                    { id: 'testimonials', label: '💬 Testimoni' },
                    { id: 'faqs', label: '❓ FAQ' },
                ],
                ...initial,
                uid() { return 'n' + Date.now() + Math.random().toString(36).slice(2, 7); },
                add(list, item) { this[list].push({ ...item, _uid: this.uid() }); },
                remove(list, i) { this[list].splice(i, 1); },
                move(list, i, dir) {
                    const j = i + dir;
                    if (j < 0 || j >= this[list].length) return;
                    const arr = this[list];
                    [arr[i], arr[j]] = [arr[j], arr[i]];
                    this[list] = [...arr];
                },
                imageUrl(path) {
                    if (!path) return null;
                    return /^(https?:|blob:)/.test(path) ? path : @js(url('/')) + '/' + path;
                },
            };
        }
    </script>
</x-admin-layout>
