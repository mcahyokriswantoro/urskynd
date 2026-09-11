<x-app-layout>
    <div class="max-w-xl mx-auto flex flex-col items-center justify-center min-h-[60vh] text-center">
        
        <h1 class="text-2xl font-serif font-bold text-brand-text mb-2">Menganalisis Kulitmu...</h1>
        <p class="text-sm text-brand-textAlt mb-10" id="statusText">Mendeteksi tekstur kulit dan tingkat hidrasi</p>

        <!-- Scanning Animation Container -->
        <div class="relative w-64 h-64 rounded-full border-4 border-brand-creamAlt overflow-hidden shadow-soft-lg bg-white mb-8">
            <!-- Simulated uploaded image (blurred/filtered) -->
            <img src="{{ $analysis->image_url }}" alt="Skin Scan" class="absolute inset-0 w-full h-full object-cover filter brightness-90 sepia-[.2]">
            
            <!-- Scanning Line -->
            <div class="absolute inset-0 w-full animate-scan">
                <div class="h-1 w-full bg-brand-primary shadow-[0_0_15px_3px_rgba(154,106,82,0.6)]"></div>
                <div class="h-16 w-full bg-gradient-to-b from-brand-primary/30 to-transparent"></div>
            </div>

            <!-- Overlay grids -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.1)_1px,transparent_1px)] bg-[size:20px_20px] mix-blend-overlay"></div>
        </div>

        <div class="w-full max-w-xs bg-brand-cream rounded-full h-2 overflow-hidden">
            <div class="bg-brand-primary h-2 rounded-full transition-all duration-300 ease-out w-0" id="progressBar"></div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% { transform: translateY(-10%); }
            50% { transform: translateY(110%); }
            100% { transform: translateY(-10%); }
        }
        .animate-scan {
            animation: scan 3s ease-in-out infinite;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusText = document.getElementById('statusText');
            const progressBar = document.getElementById('progressBar');
            
            const messages = [
                "Mendeteksi tekstur kulit...",
                "Menganalisis tingkat hidrasi...",
                "Mengevaluasi tanda penuaan...",
                "Mencari area hiperpigmentasi...",
                "Menghitung Skin Score...",
                "Menyiapkan rekomendasi personal..."
            ];

            let progress = 0;
            let step = 0;

            const interval = setInterval(() => {
                progress += Math.random() * 15; // Random progress increments
                if (progress > 100) progress = 100;
                
                progressBar.style.width = progress + '%';

                if (progress > (step + 1) * (100 / messages.length) && step < messages.length - 1) {
                    step++;
                    statusText.style.opacity = 0;
                    setTimeout(() => {
                        statusText.textContent = messages[step];
                        statusText.style.opacity = 1;
                    }, 300);
                }

                if (progress === 100) {
                    clearInterval(interval);
                    // Redirect to results page
                    setTimeout(() => {
                        window.location.href = "{{ route('analysis.show', $analysis) }}";
                    }, 500);
                }
            }, 500);
        });
    </script>
</x-app-layout>
