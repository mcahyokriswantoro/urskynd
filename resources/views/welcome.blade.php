<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>URSKYND - Smart AI Skin Clinic & Analysis</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-brand-text bg-brand-cream overflow-x-hidden">
        
        <!-- Navigation -->
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 backdrop-blur-md border-b border-brand-border/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <x-application-logo class="h-16 w-auto mix-blend-multiply" />
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex space-x-8 items-center">
                        <a href="#fitur" class="text-brand-textAlt hover:text-brand-primary font-medium transition-colors">Fitur</a>
                        
                        @if (Route::has('login'))
                            <div class="flex items-center space-x-4 ml-6 pl-6 border-l border-brand-border">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-brand-primary font-medium hover:text-brand-primaryAlt transition-colors">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-brand-text font-medium hover:text-brand-primary transition-colors">Masuk</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-brand-primary text-white rounded-xl font-medium hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">Daftar</a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3">
                <div class="w-[600px] h-[600px] bg-brand-secondaryLight/30 rounded-full blur-3xl"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <span class="inline-block py-1 px-3 rounded-full bg-brand-primary/10 text-brand-primary text-sm font-semibold tracking-wide mb-6">
                        AI Skin Companion Pertama di Indonesia
                    </span>
                    <h1 class="text-4xl md:text-6xl font-serif font-bold text-brand-text leading-tight mb-6">
                        Kenali Kulitmu,<br/>Temukan <span class="text-brand-primary italic">Kilau Alami</span>.
                    </h1>
                    <p class="mt-4 text-lg md:text-xl text-brand-textAlt mb-10 leading-relaxed">
                        Analisis kondisi kulit dengan AI, pantau perkembangan, dan dapatkan rekomendasi skincare personal—semua dalam satu aplikasi cantik.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-brand-primary text-white rounded-2xl font-medium text-lg hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-brand-primary text-white rounded-2xl font-medium text-lg hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                                Mulai Perjalanan Kulitmu
                            </a>
                        @endauth
                        <a href="#fitur" class="px-8 py-4 bg-white text-brand-text border border-brand-border rounded-2xl font-medium text-lg hover:bg-brand-cream transition-all shadow-sm">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="fitur" class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-serif font-bold text-brand-text mb-4">Perawatan Kulit Cerdas</h2>
                    <p class="text-brand-textAlt max-w-2xl mx-auto">Semua yang Anda butuhkan untuk membangun kebiasaan skincare yang lebih baik.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-brand-cream/50 p-8 rounded-3xl border border-brand-border/50 hover:shadow-soft-lg transition-all duration-300">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-brand-primary shadow-sm mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold font-serif text-brand-text mb-3">AI Skin Analysis</h3>
                        <p class="text-brand-textAlt leading-relaxed">Cukup dengan satu foto selfie, AI kami menganalisis kondisi kulitmu mulai dari hidrasi, jerawat, hingga tanda penuaan.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-brand-cream/50 p-8 rounded-3xl border border-brand-border/50 hover:shadow-soft-lg transition-all duration-300 transform md:-translate-y-4">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-brand-primary shadow-sm mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold font-serif text-brand-text mb-3">Skin Journaling</h3>
                        <p class="text-brand-textAlt leading-relaxed">Catat kondisi kulit harian, mood, dan perubahan setelah menggunakan produk. Pantau progres kulitmu dengan grafik interaktif.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-brand-cream/50 p-8 rounded-3xl border border-brand-border/50 hover:shadow-soft-lg transition-all duration-300">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-brand-primary shadow-sm mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold font-serif text-brand-text mb-3">Rekomendasi Personal</h3>
                        <p class="text-brand-textAlt leading-relaxed">Dapatkan saran produk dan rutinitas skincare pagi & malam yang dirancang khusus untuk hasil analisis kulit unikmu.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-brand-text text-brand-cream py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl font-serif font-bold tracking-widest mb-4">URSKYND</h2>
                <p class="text-brand-creamAlt/70 mb-8">AI Skin Companion untuk kulit yang lebih sehat dan bersinar.</p>
                <div class="border-t border-white/10 pt-8 text-sm text-brand-creamAlt/50">
                    &copy; {{ date('Y') }} URSKYND. Hak cipta dilindungi.
                </div>
            </div>
        </footer>

    </body>
</html>
