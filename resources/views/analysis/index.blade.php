<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                AI Computer Vision Diagnostics
            </span>
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-brand-text mb-3">Skin Check AI</h1>
            <p class="text-brand-textAlt text-sm md:text-base max-w-2xl mx-auto leading-relaxed">
                Pindai kondisi mikro kulit wajahmu dalam hitungan detik. Algoritma kami mengevaluasi hidrasi, jerawat, pori-pori, pigmentasi, kerutan, dan tekstur untuk rekomendasi presisi.
            </p>
        </div>

        @if(session('error'))
            <div class="bg-brand-danger/10 text-brand-danger p-4 rounded-2xl mb-8 text-sm text-center font-medium border border-brand-danger/20">
                {{ session('error') }}
            </div>
        @endif

        @auth
            <!-- Authenticated User: Full AI Scanner Interface -->
            <x-card padding="p-6 md:p-10" class="shadow-soft-lg">
                <form action="{{ route('skin-check.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <!-- Selection Mode -->
                    <div id="selectionMode">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                            <button type="button" id="startCameraBtn" class="py-12 bg-brand-cream/50 border border-brand-border rounded-3xl flex flex-col items-center gap-3 hover:border-brand-primary transition-all text-brand-primary shadow-xs hover:shadow-soft group">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="font-bold text-sm text-brand-text group-hover:text-brand-primary">Gunakan Kamera</span>
                                <span class="text-xs text-brand-textAlt font-normal">Ambil foto langsung</span>
                            </button>
                            
                            <label class="py-12 bg-brand-cream/50 border border-brand-border rounded-3xl flex flex-col items-center gap-3 hover:border-brand-primary transition-all text-brand-text cursor-pointer shadow-xs hover:shadow-soft group">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs text-brand-textAlt group-hover:text-brand-primary">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                                <span class="font-bold text-sm text-brand-text group-hover:text-brand-primary">Upload File Foto</span>
                                <span class="text-xs text-brand-textAlt font-normal">Pilih dari galeri foto HP / PC</span>
                                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>

                    <!-- Camera View -->
                    <div id="cameraView" class="hidden">
                        <div class="relative rounded-3xl overflow-hidden bg-black aspect-[3/4] md:aspect-square mb-6 shadow-inner border border-brand-border">
                            <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            
                            <!-- Camera Overlay UI (Face Frame) -->
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center border-8 border-brand-cream/20">
                                <div class="w-2/3 h-2/3 md:h-3/4 border-2 border-dashed border-white/60 rounded-[40%] animate-pulse"></div>
                                <div class="absolute bottom-24 text-white text-xs font-medium bg-black/40 px-3 py-1 rounded-full backdrop-blur-sm">Posisikan wajahmu di dalam garis</div>
                            </div>

                            <!-- Capture Button -->
                            <button type="button" id="captureBtn" class="absolute bottom-6 left-1/2 -translate-x-1/2 w-16 h-16 bg-white/30 backdrop-blur-md rounded-full border-4 border-white flex items-center justify-center hover:bg-white/50 transition-colors z-20">
                                <div class="w-12 h-12 bg-white rounded-full shadow-sm"></div>
                            </button>

                            <!-- Close Button -->
                            <button type="button" id="closeCameraBtn" class="absolute top-4 right-4 p-2 bg-black/50 text-white rounded-full hover:bg-black/70 z-20 backdrop-blur-md">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Preview View -->
                    <div id="previewView" class="hidden">
                        <div class="relative rounded-3xl overflow-hidden aspect-[3/4] md:aspect-square mb-6 shadow-soft border border-brand-border bg-brand-cream/20">
                            <img id="imagePreview" src="#" alt="Preview" class="w-full h-full object-contain">
                            
                            <!-- Retake Button -->
                            <button type="button" id="retakeBtn" class="absolute top-4 right-4 px-4 py-2 bg-white/90 backdrop-blur-md text-brand-text text-sm font-bold rounded-xl shadow-xs hover:bg-white transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Ganti Foto
                            </button>
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('photo')" class="mt-2 text-center" />

                    <div class="mt-6 flex justify-center">
                        <x-primary-button id="submitBtn" class="w-full md:w-auto md:px-12 py-4 text-base shadow-soft opacity-50 cursor-not-allowed" disabled>
                            Mulai Analisis AI Sekarang
                        </x-primary-button>
                    </div>
                </form>
            </x-card>

        @else
            <!-- Guest View: Public Overview & Teaser with Auth Gate for 'Skin Check Lebih Lanjut' -->
            <div class="space-y-12">
                <!-- Main Teaser Card -->
                <div class="bg-white rounded-3xl border border-brand-border p-8 md:p-12 shadow-soft-lg relative overflow-hidden">
                    <div class="max-w-2xl mx-auto text-center space-y-6">
                        <div class="w-20 h-20 bg-brand-primary/10 text-brand-primary rounded-3xl flex items-center justify-center mx-auto shadow-xs">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>

                        <h2 class="text-2xl md:text-3xl font-serif font-bold text-brand-text">
                            Analisis Wajah Digital Berbasis Kecerdasan Buatan
                        </h2>

                        <p class="text-sm text-brand-textAlt leading-relaxed">
                            Ketahui profil kulit wajahmu secara akurat. Dapatkan laporan visual skor hidrasi, pori-pori, jerawat, hingga rekomendasi produk skincare harian yang dirancang personal.
                        </p>

                        <!-- Features Pill Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-left">
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">💧 Hidrasi Seluler</span>
                                <span class="text-[11px] text-brand-textAlt">Tingkat kelembapan & kadar air</span>
                            </div>
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">🔍 Kepadatan Pori</span>
                                <span class="text-[11px] text-brand-textAlt">Pori tersumbat di zona T</span>
                            </div>
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">🔴 Indeks Jerawat</span>
                                <span class="text-[11px] text-brand-textAlt">Jerawat aktif & kemerahan</span>
                            </div>
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">🛡️ Skin Barrier</span>
                                <span class="text-[11px] text-brand-textAlt">Kesehatan lipid pelindung</span>
                            </div>
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">✨ Noda & Flek</span>
                                <span class="text-[11px] text-brand-textAlt">Hiperpigmentasi bekas jerawat</span>
                            </div>
                            <div class="bg-brand-cream/60 p-3.5 rounded-2xl border border-brand-border/60">
                                <span class="text-xs font-bold text-brand-primary block mb-0.5">🧬 Perkiraan Usia Kulit</span>
                                <span class="text-[11px] text-brand-textAlt">Elastisitas & garis halus</span>
                            </div>
                        </div>

                        <!-- CTA Button requiring login for further check -->
                        <div class="pt-6">
                            <button type="button" 
                                    @click="$dispatch('open-auth-modal', { 
                                        title: 'Masuk untuk Skin Check Lebih Lanjut', 
                                        description: 'Fitur pemindaian foto wajah dengan AI membutuhkan akun agar hasil diagnosis, catatan riwayat berkala, dan rekomendasi personal dapat tersimpan aman untuk Anda.' 
                                    })"
                                    class="w-full sm:w-auto px-8 py-4 bg-brand-primary text-white font-bold rounded-2xl hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg text-base inline-flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                <span>Mulai Skin Check Lebih Lanjut (Scan AI)</span>
                            </button>
                            <p class="text-xs text-brand-textAlt mt-3">Gratis dan hasil diagnosis keluar dalam 10 detik setelah masuk.</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Interactive Quiz Preview (Gratis Tanpa Login) -->
                <div class="bg-brand-cream/60 rounded-3xl border border-brand-border p-6 md:p-10" x-data="{
                    step: 1,
                    answers: {},
                    result: null,
                    calculate() {
                        if (this.answers.oil === 'lots' && this.answers.tight === 'no') {
                            this.result = 'Kulit Berminyak (Oily Skin)';
                        } else if (this.answers.oil === 'dry' && this.answers.tight === 'yes') {
                            this.result = 'Kulit Kering (Dry Skin)';
                        } else if (this.answers.oil === 'tzone') {
                            this.result = 'Kulit Kombinasi (Combination Skin)';
                        } else {
                            this.result = 'Kulit Normal / Sensitif';
                        }
                    }
                }">
                    <div class="text-center max-w-xl mx-auto mb-8">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-primary block mb-1">Simulasi Cepat</span>
                        <h3 class="text-2xl font-serif font-bold text-brand-text mb-2">Tes Awal Tipe Kulit</h3>
                        <p class="text-xs text-brand-textAlt">Jawab 2 pertanyaan singkat ini untuk perkiraan awal tipe kulitmu sebelum melakukan AI Face Scan lengkap.</p>
                    </div>

                    <div class="max-w-xl mx-auto bg-white p-6 rounded-2xl border border-brand-border shadow-xs">
                        <!-- Q1 -->
                        <div x-show="step === 1">
                            <p class="font-bold text-sm text-brand-text mb-4">1. Bagaimana kondisi wajahmu 2 jam setelah cuci muka tanpa skincare?</p>
                            <div class="space-y-2">
                                <button type="button" @click="answers.oil = 'lots'; answers.tight = 'no'; step = 2" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    A. Berminyak dan mengkilap di seluruh area wajah
                                </button>
                                <button type="button" @click="answers.oil = 'tzone'; answers.tight = 'no'; step = 2" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    B. Berminyak di hidung dan dahi (T-Zone), tapi pipi normal/kering
                                </button>
                                <button type="button" @click="answers.oil = 'dry'; answers.tight = 'yes'; step = 2" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    C. Terasa kencang tertarik, kering, dan kusam
                                </button>
                            </div>
                        </div>

                        <!-- Q2 -->
                        <div x-show="step === 2" style="display: none;">
                            <p class="font-bold text-sm text-brand-text mb-4">2. Seberapa sering kulitmu mengalami kemerahan atau iritasi?</p>
                            <div class="space-y-2">
                                <button type="button" @click="calculate(); step = 3" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    A. Jarang sekali atau hampir tidak pernah
                                </button>
                                <button type="button" @click="calculate(); step = 3" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    B. Sering muncul jerawat kecil atau bruntusan saat cuaca panas
                                </button>
                                <button type="button" @click="calculate(); step = 3" class="w-full text-left p-3.5 rounded-xl border border-brand-border hover:border-brand-primary text-xs font-medium text-brand-text hover:bg-brand-cream/40 transition-colors">
                                    C. Sangat mudah memerah, perih saat mencoba produk baru
                                </button>
                            </div>
                        </div>

                        <!-- Result -->
                        <div x-show="step === 3" style="display: none;" class="text-center py-4 space-y-4">
                            <span class="text-3xl">✨</span>
                            <div>
                                <p class="text-xs text-brand-textAlt">Perkiraan Awal Tipe Kulitmu:</p>
                                <h4 class="text-xl font-serif font-bold text-brand-primary mt-1" x-text="result"></h4>
                            </div>
                            <p class="text-xs text-brand-textAlt max-w-sm mx-auto leading-relaxed">
                                Untuk analisis 6 parameter mendalam menggunakan foto wajah dan kamera AI, lanjutkan ke Skin Check Lengkap.
                            </p>
                            <button type="button" 
                                    @click="$dispatch('open-auth-modal', { 
                                        title: 'Masuk untuk Skin Check Lebih Lanjut', 
                                        description: 'Silakan masuk atau daftar akun untuk memindai wajah dengan kamera AI dan mendapatkan rekomendasi produk yang cocok untuk ' + result + '.' 
                                    })"
                                    class="px-6 py-3 bg-brand-primary text-white font-bold rounded-xl text-xs hover:bg-brand-primaryAlt transition-all shadow-soft">
                                Lanjutkan ke Analisis AI Lengkap &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endauth

        <!-- Best Practices Tips -->
        <div class="mt-12 mb-8">
            <h4 class="font-serif font-bold text-brand-text mb-4 text-center">Tips untuk Hasil Scan Terbaik:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-xs border border-brand-border/60">
                    <div class="w-10 h-10 rounded-xl bg-brand-cream flex items-center justify-center shrink-0 text-lg">☀️</div>
                    <div>
                        <p class="text-xs font-bold text-brand-text">Pencahayaan Alami</p>
                        <p class="text-xs text-brand-textAlt leading-relaxed mt-0.5">Gunakan cahaya matahari atau ruangan terang dari depan wajah, hindari backlight.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-xs border border-brand-border/60">
                    <div class="w-10 h-10 rounded-xl bg-brand-cream flex items-center justify-center shrink-0 text-lg">👓</div>
                    <div>
                        <p class="text-xs font-bold text-brand-text">Wajah Bersih Tanpa Kacamata</p>
                        <p class="text-xs text-brand-textAlt leading-relaxed mt-0.5">Pastikan wajah bersih dari makeup tebal dan lepaskan kacamata agar AI dapat membaca pori dengan akurat.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const photoInput = document.getElementById('photo');
                const selectionMode = document.getElementById('selectionMode');
                const cameraView = document.getElementById('cameraView');
                const previewView = document.getElementById('previewView');
                const video = document.getElementById('video');
                const canvas = document.getElementById('canvas');
                const imagePreview = document.getElementById('imagePreview');
                const submitBtn = document.getElementById('submitBtn');

                let stream = null;

                // Start Camera
                document.getElementById('startCameraBtn')?.addEventListener('click', async () => {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, 
                            audio: false 
                        });
                        video.srcObject = stream;
                        selectionMode.classList.add('hidden');
                        cameraView.classList.remove('hidden');
                    } catch (err) {
                        alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera ke browser ini.');
                        console.error(err);
                    }
                });

                // Stop Camera
                function stopCamera() {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }
                }

                // Close Camera View
                document.getElementById('closeCameraBtn')?.addEventListener('click', () => {
                    stopCamera();
                    cameraView.classList.add('hidden');
                    selectionMode.classList.remove('hidden');
                });

                // Capture Photo
                document.getElementById('captureBtn')?.addEventListener('click', () => {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);

                    canvas.toBlob(blob => {
                        const file = new File([blob], 'skin-analysis.jpg', { type: 'image/jpeg' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        photoInput.files = dataTransfer.files;

                        imagePreview.src = canvas.toDataURL('image/jpeg');
                        stopCamera();
                        cameraView.classList.add('hidden');
                        previewView.classList.remove('hidden');

                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }, 'image/jpeg', 0.9);
                });

                // File Upload handler
                photoInput?.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            selectionMode.classList.add('hidden');
                            previewView.classList.remove('hidden');

                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });

                // Retake
                document.getElementById('retakeBtn')?.addEventListener('click', () => {
                    previewView.classList.add('hidden');
                    selectionMode.classList.remove('hidden');
                    photoInput.value = '';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            });
        </script>
    @endauth
</x-dynamic-component>
