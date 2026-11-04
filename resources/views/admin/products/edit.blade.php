<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-[#9A6A52] hover:underline font-semibold flex items-center gap-1 mb-1">
                    &larr; Kembali ke Daftar Produk
                </a>
                <h1 class="text-2xl font-serif font-bold text-slate-900">Edit Produk: {{ $product->name }}</h1>
                <p class="text-xs text-slate-500">Perbarui informasi, harga resmi, atau status tayang produk ini.</p>
            </div>

            <a href="{{ route('products.show', $product->slug ?? $product->id) }}" target="_blank" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl flex items-center gap-1.5">
                Pratinjau Web &nearr;
            </a>
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

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6" x-data="{ imagePreview: @js($product->image_url) }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Brand / Merk <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Kategori Skincare <span class="text-rose-500">*</span></label>
                    <select name="product_category_id" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('product_category_id', $product->product_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Harga Resmi (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Komisi Afiliasi (%)</label>
                    <input type="number" step="0.5" min="1" max="50" name="commission_rate" value="{{ old('commission_rate', $product->commission_rate ?? 10) }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Ukuran / Volume</label>
                    <input type="text" name="size" value="{{ old('size', $product->size) }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Badge Promosi (Opsional)</label>
                    <input type="text" name="badge" value="{{ old('badge', $product->badge) }}" placeholder="Contoh: Best Seller, Hero Product" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Aktif (Tampil di Katalog Web & Rekomendasi AI)</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Draft (Disembunyikan)</option>
                    </select>
                </div>
            </div>

            <!-- Upload Gambar Produk -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Foto / Gambar Produk</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70">
                    <div class="w-28 h-28 rounded-2xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview Produk" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!imagePreview">
                            <div class="text-center text-slate-400 p-2">
                                <span class="text-2xl block mb-1">🧴</span>
                                <span class="text-[10px]">Belum ada foto</span>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 space-y-2.5">
                        <input type="file"
                               name="image_file"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file) {
                                       const reader = new FileReader();
                                       reader.onload = (e) => { imagePreview = e.target.result };
                                       reader.readAsDataURL(file);
                                   }
                               "
                               class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#9A6A52] file:text-white hover:file:bg-[#845843] file:cursor-pointer">
                        <p class="text-[11px] text-slate-500">Pilih file baru dari perangkat Anda jika ingin mengganti foto produk (Format: JPG, PNG, WEBP, maks. 4MB).</p>

                        @if($product->image)
                            <label class="inline-flex items-center gap-2 text-xs text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600">
                                <span>Hapus foto produk saat ini</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Deskripsi & Keunggulan Produk <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Petunjuk Pemakaian (Usage Instructions)</label>
                <textarea name="usage_instruction" rows="2" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#9A6A52] focus:ring-1 focus:ring-[#9A6A52] bg-slate-50">{{ old('usage_instruction', $product->usage_instruction) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-6 py-2.5 text-xs font-semibold text-white bg-[#9A6A52] hover:bg-[#855740] rounded-xl shadow-xs">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
