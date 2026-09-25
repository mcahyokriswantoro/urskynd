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
                
                <!-- Selection Mode -->
                <div id="selectionMode">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <button type="button" id="startCameraBtn" class="py-12 bg-brand-cream/50 border border-brand-border rounded-2xl flex flex-col items-center gap-3 hover:border-brand-primary transition-all text-brand-primary shadow-sm hover:shadow-soft group">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <span class="font-bold text-sm">Gunakan Kamera</span>
                            <span class="text-xs text-brand-textAlt font-normal">Ambil foto langsung</span>
                        </button>
                        
                        <label class="py-12 bg-brand-cream/50 border border-brand-border rounded-2xl flex flex-col items-center gap-3 hover:border-brand-primary transition-all text-brand-text cursor-pointer shadow-sm hover:shadow-soft group">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm text-brand-textAlt group-hover:text-brand-primary">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            </div>
                            <span class="font-bold text-sm">Upload File</span>
                            <span class="text-xs text-brand-textAlt font-normal">Pilih dari galeri</span>
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
                        <button type="button" id="retakeBtn" class="absolute top-4 right-4 px-4 py-2 bg-white/90 backdrop-blur-md text-brand-text text-sm font-bold rounded-xl shadow-sm hover:bg-white transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Ganti Foto
                        </button>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('photo')" class="mt-2 text-center" />

                <div class="mt-8 flex justify-center">
                    <x-primary-button id="submitBtn" class="w-full md:w-auto md:px-12 py-4 text-lg shadow-soft opacity-50 cursor-not-allowed" disabled>
                        Mulai Analisis
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        <div class="mt-8">
            <h4 class="font-serif font-bold text-brand-text mb-4">Tips untuk hasil terbaik:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-soft border border-brand-border/50">
                    <div class="w-8 h-8 rounded-full bg-brand-cream flex items-center justify-center shrink-0">☀️</div>
                    <p class="text-xs text-brand-text leading-relaxed mt-1">Gunakan pencahayaan alami yang terang dari depan wajah.</p>
                </div>
                <div class="flex items-start gap-3 p-4 bg-white rounded-2xl shadow-soft border border-brand-border/50">
                    <div class="w-8 h-8 rounded-full bg-brand-cream flex items-center justify-center shrink-0">👓</div>
                    <p class="text-xs text-brand-text leading-relaxed mt-1">Pastikan wajah bersih dari makeup dan lepaskan kacamata.</p>
                </div>
            </div>
        </div>
    </div>

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
            document.getElementById('startCameraBtn').addEventListener('click', async () => {
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
            document.getElementById('closeCameraBtn').addEventListener('click', () => {
                stopCamera();
                cameraView.classList.add('hidden');
                selectionMode.classList.remove('hidden');
            });

            // Capture Photo
            document.getElementById('captureBtn').addEventListener('click', () => {
                // Flash effect
                const flash = document.createElement('div');
                flash.className = 'absolute inset-0 bg-white z-50 transition-opacity duration-300';
                cameraView.querySelector('.relative').appendChild(flash);
                setTimeout(() => flash.classList.add('opacity-0'), 50);
                setTimeout(() => flash.remove(), 300);

                // Draw to canvas
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                // Flip horizontally if front camera is mirrored, but usually getUserMedia doesn't mirror the actual output
                canvas.getContext('2d').drawImage(video, 0, 0);
                
                // Convert canvas to File and set to input
                canvas.toBlob((blob) => {
                    const file = new File([blob], "camera-capture.jpg", { type: "image/jpeg" });
                    
                    // Create a DataTransfer to simulate a file drop/selection
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    photoInput.files = dataTransfer.files;
                    
                    // Show Preview
                    imagePreview.src = URL.createObjectURL(blob);
                    
                    stopCamera();
                    cameraView.classList.add('hidden');
                    previewView.classList.remove('hidden');
                    
                    // Enable Submit
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }, 'image/jpeg', 0.9);
            });

            // Handle normal File Upload
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if(file.size > 5 * 1024 * 1024) {
                        alert('Ukuran gambar terlalu besar. Maksimal 5MB.');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        selectionMode.classList.add('hidden');
                        previewView.classList.remove('hidden');
                        
                        submitBtn.removeAttribute('disabled');
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Retake / Replace Photo
            document.getElementById('retakeBtn').addEventListener('click', () => {
                photoInput.value = ''; // clear file
                imagePreview.src = '#';
                previewView.classList.add('hidden');
                selectionMode.classList.remove('hidden');
                
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        });
    </script>
</x-app-layout>
