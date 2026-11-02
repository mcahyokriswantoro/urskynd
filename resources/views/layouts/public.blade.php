<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'URSKYND') }} - Smart AI Skin Clinic & Skincare Companion</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">

    <!-- Fonts & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-brand-text bg-brand-cream overflow-x-hidden min-h-screen flex flex-col"
      x-data="{ 
          mobileMenuOpen: false, 
          authModalOpen: false,
          authModalTitle: 'Masuk Diperlukan',
          authModalDesc: 'Silakan masuk atau buat akun baru untuk melanjutkan tindakan ini.',
          authModalAction: 'login'
      }"
      @open-auth-modal.window="
          authModalTitle = $event.detail.title || 'Masuk Diperlukan';
          authModalDesc = $event.detail.description || 'Silakan masuk atau buat akun baru untuk melanjutkan tindakan ini.';
          authModalOpen = true;
      ">

    <!-- Public Header / Navigation Bar -->
    <nav class="fixed top-0 left-0 w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-brand-border/60 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <x-application-logo class="h-14 sm:h-16 w-auto mix-blend-multiply transition-transform group-hover:scale-105" />
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('home') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        Beranda
                    </a>

                    <!-- Skin Check Menu -->
                    <a href="{{ route('skin-check.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5 {{ request()->routeIs('skin-check.*') || request()->routeIs('analysis.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        <span class="inline-block w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                        Skin Check
                    </a>

                    <!-- Produk Menu -->
                    <a href="{{ route('products.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        Produk
                    </a>

                    <!-- Detail Pembelian Menu -->
                    <a href="{{ route('pembelian.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-1.5 {{ request()->routeIs('pembelian.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        <span class="text-xs">🛍️</span>
                        <span>Detail Pembelian</span>
                    </a>

                    <!-- Konsultasi Menu -->
                    <a href="{{ route('consultation.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('consultation.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        Konsultasi
                    </a>

                    <!-- Berita Menu -->
                    <a href="{{ route('news.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('news.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        Berita & Tips
                    </a>

                    <!-- Kontak Menu -->
                    <a href="{{ route('contact.index') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('contact.*') ? 'text-brand-primary bg-brand-primary/10 font-bold' : 'text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60' }}">
                        Kontak
                    </a>
                </div>

                <!-- Desktop Auth Actions -->
                <div class="hidden md:flex items-center space-x-3 pl-4 border-l border-brand-border">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary text-white rounded-xl font-medium text-sm hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-brand-text hover:text-brand-primary transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-brand-primary text-white rounded-xl font-medium text-sm hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex md:hidden items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="p-2 rounded-xl text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream focus:outline-none"
                            aria-label="Toggle Navigation">
                        <svg class="w-6 h-6" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6" x-show="mobileMenuOpen" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden bg-white border-b border-brand-border px-4 pt-3 pb-6 space-y-2 shadow-lg"
             style="display: none;">
            
            <a href="{{ route('home') }}" 
               class="block px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('home') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                Beranda
            </a>

            <a href="{{ route('skin-check.index') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('skin-check.*') || request()->routeIs('analysis.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                    Skin Check
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-brand-primary/10 text-brand-primary font-semibold">AI Scan</span>
            </a>

            <a href="{{ route('products.index') }}" 
               class="block px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('products.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                Katalog Produk
            </a>

            <a href="{{ route('pembelian.index') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('pembelian.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                <span class="flex items-center gap-2">
                    <span>🛍️</span>
                    <span>Detail Pembelian Produk</span>
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Promo</span>
            </a>

            <a href="{{ route('consultation.index') }}" 
               class="block px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('consultation.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                Konsultasi Dokter
            </a>

            <a href="{{ route('news.index') }}" 
               class="block px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('news.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                Berita & Edukasi Skincare
            </a>

            <a href="{{ route('contact.index') }}" 
               class="block px-4 py-3 rounded-xl text-base font-medium {{ request()->routeIs('contact.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream' }}">
                Kontak & Customer Support
            </a>

            <div class="pt-4 border-t border-brand-border space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 py-3 bg-brand-primary text-white rounded-xl font-bold shadow-soft">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Ke Dashboard Saya
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}" class="py-2.5 text-center border border-brand-border text-brand-text font-bold rounded-xl hover:bg-brand-cream">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="py-2.5 text-center bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt shadow-soft">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content Container -->
    <main class="flex-1 pt-24 pb-16">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-brand-text text-brand-cream pt-16 pb-12 mt-auto border-t border-brand-border/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                
                <!-- Brand Info -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl font-serif font-bold tracking-widest text-white">URSKYND</span>
                    </div>
                    <p class="text-sm text-brand-creamAlt/70 leading-relaxed mb-6">
                        AI Skin Companion pertama di Indonesia. Analisis kondisi kulit secara presisi, dapatkan rekomendasi skincare personal, dan konsultasi langsung dengan dokter spesialis.
                    <div class="flex items-center gap-2 text-xs text-brand-creamAlt/70">
                        <span class="w-2 h-2 rounded-full bg-brand-success"></span>
                        <span>Layanan Konsultasi Online Aktif 08.00 - 21.00 WIB</span>
                    </div>
                </div>

                <!-- Navigasi Cepat -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Layanan Utama</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('skin-check.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">AI Skin Check</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Katalog Skincare</a></li>
                        <li><a href="{{ route('pembelian.index') }}" class="text-emerald-400 font-bold hover:text-white transition-colors">🛍️ Detail Pembelian Produk</a></li>
                        <li><a href="{{ route('consultation.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Konsultasi Dokter Spesialis</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Berita & Edukasi Kulit</a></li>
                        <li><a href="{{ route('contact.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Kategori Skincare -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Solusi Masalah Kulit</h3>
                    <ul class="space-y-2.5 text-sm text-brand-creamAlt/70">
                        <li>Perawatan Jerawat & Komedo</li>
                        <li>Pemulihan Skin Barrier Rusak</li>
                        <li>Mencerahkan Flek & Noda Hitam</li>
                        <li>Anti-Aging & Pori-pori Besar</li>
                        <li>Kulit Sensitif & Kemerahan</li>
                    </ul>
                </div>

                <!-- Kontak & Alamat -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Hubungi Kami</h3>
                    <div class="space-y-3 text-sm text-brand-creamAlt/70">
                        <p class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">WhatsApp:</span>
                            <a href="https://wa.me/6285282784356" target="_blank" rel="noopener" class="text-white hover:text-emerald-400 font-bold">085282784356</a>
                        </p>
                        <p class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Jl. Raya Plalangan Plosowahyu Km. 3, Kec. Lamongan, Kab. Lamongan, Jawa Timur 62218 (Kampus UMLA)</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>support@urskynd.com</span>
                        </p>
                    </div>
                </div>

            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-brand-creamAlt/50 gap-4">
                <p>&copy; {{ date('Y') }} URSKYND. Hak cipta dilindungi undang-undang.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                    <a href="{{ route('consultation.index') }}" class="hover:text-white transition-colors">Bantuan Konsultasi</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global Auth Required Modal -->
    <div x-show="authModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="authModalOpen = false">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-brand-border transform transition-all"
             @click.outside="authModalOpen = false">
            
            <div class="w-16 h-16 bg-brand-primary/10 text-brand-primary rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>

            <h3 class="text-xl font-serif font-bold text-center text-brand-text mb-2" x-text="authModalTitle">
                Masuk untuk Melanjutkan
            </h3>
            
            <p class="text-sm text-center text-brand-textAlt mb-6 leading-relaxed" x-text="authModalDesc">
                Silakan masuk atau buat akun baru terlebih dahulu.
            </p>

            <div class="space-y-3">
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-3.5 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt transition-all shadow-soft text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Masuk ke Akun Saya
                </a>

                <a href="{{ route('register') }}" class="w-full flex items-center justify-center py-3.5 bg-brand-cream border border-brand-border text-brand-text font-bold rounded-xl hover:bg-brand-border/40 transition-all text-sm">
                    Buat Akun Baru (Daftar Gratis)
                </a>

                <button type="button" @click="authModalOpen = false" class="w-full text-center text-xs text-brand-textAlt hover:text-brand-text pt-2">
                    Tutup dan Kembali Jelajahi
                </button>
            </div>
        </div>
    </div>

</body>
</html>
