<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-serif font-bold text-brand-text mb-2">Skin Check</h1>
            <p class="text-brand-textAlt">Analisis kondisi kulitmu menggunakan AI. Ambil foto wajah dari depan tanpa kacamata untuk hasil terbaik.</p>
        </div>

        @if(session('error'))
            <div class="bg-brand-danger/10 text-brand-danger p-4 rounded-xl mb-6 text-sm text-center">
                {{ session('error') }}
            </div>
        @endif

        <x-card padding="p-8">
            <form action="{{ route('skin-check.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                
                <div class="border-2 border-dashed border-brand-border hover:border-brand-primary/50 bg-brand-cream/30 rounded-2xl p-8 text-center transition-colors cursor-pointer relative" id="dropzone">
                    <input type="file" name="photo" id="photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                    
                    <div id="previewContainer" class="hidden">
                        <img id="imagePreview" src="#" alt="Preview" class="max-h-[300px] mx-auto rounded-xl shadow-sm mb-4">
                        <p class="text-sm text-brand-primary font-medium">Ketuk untuk mengganti foto</p>
                    </div>

                    <div id="placeholderContainer">
                        <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center text-brand-primary shadow-sm mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="font-medium text-brand-text mb-1">Ambil Foto Wajah</h3>
                        <p class="text-xs text-brand-textAlt mb-4">Maksimal ukuran file 5MB</p>
                        <span class="px-4 py-2 bg-white text-brand-primary text-xs font-semibold rounded-lg shadow-sm">Pilih Gambar</span>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('photo')" class="mt-2 text-center" />

                <div class="mt-8 flex justify-center">
                    <x-primary-button id="submitBtn" class="w-full md:w-auto md:px-12 opacity-50 cursor-not-allowed" disabled>
                        Mulai Analisis
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        <div class="mt-8">
            <h4 class="font-serif font-bold text-brand-text mb-4">Tips untuk hasil terbaik:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-4 bg-white rounded-xl shadow-soft">
                    <span class="text-brand-primary">☀️</span>
                    <p class="text-xs text-brand-text">Gunakan pencahayaan alami atau di tempat yang terang.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white rounded-xl shadow-soft">
                    <span class="text-brand-primary">👤</span>
                    <p class="text-xs text-brand-text">Pastikan wajah terlihat jelas tanpa terhalang rambut atau kacamata.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white rounded-xl shadow-soft">
                    <span class="text-brand-primary">📱</span>
                    <p class="text-xs text-brand-text">Pegang kamera setinggi mata (sejajar).</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white rounded-xl shadow-soft">
                    <span class="text-brand-primary">🧴</span>
                    <p class="text-xs text-brand-text">Lakukan dengan wajah bersih tanpa makeup.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const btn = document.getElementById('submitBtn');
            const previewContainer = document.getElementById('previewContainer');
            const placeholderContainer = document.getElementById('placeholderContainer');
            const imagePreview = document.getElementById('imagePreview');

            if (file) {
                // Check size (5MB)
                if(file.size > 5 * 1024 * 1024) {
                    alert('Ukuran gambar terlalu besar. Maksimal 5MB.');
                    this.value = '';
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholderContainer.classList.add('hidden');
                }
                reader.readAsDataURL(file);

                // Enable button
                btn.removeAttribute('disabled');
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.setAttribute('disabled', 'disabled');
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                previewContainer.classList.add('hidden');
                placeholderContainer.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
