<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>URSKYND - Smart AI Skin Clinic & Companion</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-brand-text bg-brand-cream overflow-x-hidden"
          x-data="{ 
              mobileMenuOpen: false, 
              authModalOpen: false,
              authModalTitle: 'Masuk Diperlukan',
              authModalDesc: 'Silakan masuk atau daftar terlebih dahulu untuk melakukan transaksi pembelian produk ini.'
          }"
          @open-auth-modal.window="
              authModalTitle = $event.detail.title || 'Masuk Diperlukan';
              authModalDesc = $event.detail.description || 'Silakan masuk atau daftar terlebih dahulu.';
              authModalOpen = true;
          ">
        
        <!-- Navigation -->
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-brand-border/60 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                            <x-application-logo class="h-14 sm:h-16 w-auto mix-blend-multiply transition-transform group-hover:scale-105" />
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex space-x-1 lg:space-x-2 items-center">
                        <a href="{{ route('home') }}" class="px-3.5 py-2 text-sm font-bold text-brand-primary bg-brand-primary/10 rounded-xl transition-colors">
                            Beranda
                        </a>
                        <a href="{{ route('skin-check.index') }}" class="px-3.5 py-2 text-sm font-medium text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60 rounded-xl transition-colors flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                            Skin Check
                        </a>
                        <a href="{{ route('products.index') }}" class="px-3.5 py-2 text-sm font-medium text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60 rounded-xl transition-colors">
                            Produk
                        </a>
                        <a href="{{ route('consultation.index') }}" class="px-3.5 py-2 text-sm font-medium text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60 rounded-xl transition-colors">
                            Konsultasi
                        </a>
                        <a href="{{ route('news.index') }}" class="px-3.5 py-2 text-sm font-medium text-brand-textAlt hover:text-brand-primary hover:bg-brand-cream/60 rounded-xl transition-colors">
                            Berita & Tips
                        </a>
                        
                        @if (Route::has('login'))
                            <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-brand-border">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary text-white rounded-xl text-sm font-medium hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-medium text-brand-text hover:text-brand-primary transition-colors">
                                        Masuk
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-brand-primary text-white rounded-xl text-sm font-medium hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg">
                                            Daftar Gratis
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Menu Actions -->
                    <div class="flex md:hidden items-center space-x-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-3 py-1.5 bg-brand-primary text-white rounded-lg text-xs font-medium hover:bg-brand-primaryAlt transition-all shadow-soft">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs text-brand-text font-medium hover:text-brand-primary px-2 py-1.5">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-3 py-1.5 bg-brand-primary text-white rounded-lg text-xs font-medium hover:bg-brand-primaryAlt transition-all shadow-soft">Daftar</a>
                            @endif
                        @endauth
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-brand-textAlt hover:text-brand-primary rounded-lg focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
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
                <a href="{{ route('home') }}" class="block px-4 py-3 rounded-xl text-base font-bold text-brand-primary bg-brand-primary/10">Beranda</a>
                <a href="{{ route('skin-check.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl text-base font-medium text-brand-text hover:bg-brand-cream">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                        Skin Check
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-brand-primary/10 text-brand-primary font-semibold">AI Scan</span>
                </a>
                <a href="{{ route('products.index') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-brand-text hover:bg-brand-cream">Katalog Produk</a>
                <a href="{{ route('consultation.index') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-brand-text hover:bg-brand-cream">Konsultasi Dokter</a>
                <a href="{{ route('news.index') }}" class="block px-4 py-3 rounded-xl text-base font-medium text-brand-text hover:bg-brand-cream">Berita & Edukasi Skincare</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 lg:pt-44 lg:pb-28 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 pointer-events-none">
                <div class="w-[600px] h-[600px] bg-brand-secondaryLight/30 rounded-full blur-3xl"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto">
                    <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-brand-primary/10 text-brand-primary text-xs sm:text-sm font-semibold tracking-wide mb-6">
                        <span class="w-2 h-2 rounded-full bg-brand-primary animate-ping"></span>
                        AI Skin Companion Pertama di Indonesia
                    </span>
                    <h1 class="text-4xl md:text-6xl font-serif font-bold text-brand-text leading-tight mb-6">
                        Kenali Kulitmu,<br/>Temukan <span class="text-brand-primary italic">Kilau Alami</span>.
                    </h1>
                    <p class="mt-4 text-base md:text-xl text-brand-textAlt mb-10 leading-relaxed">
                        Analisis kondisi kulit dengan AI, konsultasi dokter spesialis, temukan skincare yang tepat sasaran, dan pantau perkembangan kulitmu setiap hari.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('skin-check.index') }}" class="px-8 py-4 bg-brand-primary text-white rounded-2xl font-bold text-base hover:bg-brand-primaryAlt transition-all shadow-soft hover:shadow-soft-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                            Mulai Skin Check Sekarang
                        </a>
                        <a href="{{ route('products.index') }}" class="px-8 py-4 bg-white text-brand-text border border-brand-border rounded-2xl font-bold text-base hover:bg-brand-cream transition-all shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Jelajahi Produk
                        </a>
                    </div>

                    <!-- Trust Stats -->
                    <div class="mt-14 pt-10 border-t border-brand-border/60 grid grid-cols-3 gap-4 max-w-2xl mx-auto text-center">
                        <div>
                            <p class="text-2xl md:text-3xl font-serif font-bold text-brand-text">98.4%</p>
                            <p class="text-xs text-brand-textAlt mt-1">Akurasi Deteksi AI</p>
                        </div>
                        <div>
                            <p class="text-2xl md:text-3xl font-serif font-bold text-brand-text">100%</p>
                            <p class="text-xs text-brand-textAlt mt-1">BPOM Registered</p>
                        </div>
                        <div>
                            <p class="text-2xl md:text-3xl font-serif font-bold text-brand-text">24/7</p>
                            <p class="text-xs text-brand-textAlt mt-1">Konsultasi Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Skin Check Spotlight Section -->
        <div class="py-20 bg-gradient-to-b from-brand-cream to-white border-y border-brand-border/40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl border border-brand-border p-8 md:p-14 shadow-soft-xl flex flex-col lg:flex-row items-center gap-12">
                    <div class="flex-1 space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-primary/10 text-brand-primary text-xs font-bold rounded-full">
                            ✨ Fitur Unggulan
                        </div>
                        <h2 class="text-3xl md:text-4xl font-serif font-bold text-brand-text leading-tight">
                            AI Skin Check Wajah:<br/>Diagnosis Kulit Akurat dalam 10 Detik
                        </h2>
                        <p class="text-brand-textAlt leading-relaxed text-sm md:text-base">
                            Cukup ambil satu foto selfie jernih. Algoritma visi komputer kami akan memindai 6 indikator mikro wajah: kelembapan, jerawat, pori-pori, pigmentasi noda hitam, kerutan, dan tekstur kulit.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-xs md:text-sm font-medium text-brand-text">
                            <div class="flex items-center gap-2 bg-brand-cream/50 p-3 rounded-xl border border-brand-border/50">
                                <span class="text-brand-success font-bold">✓</span> Deteksi Tipe Kulit Akurat
                            </div>
                            <div class="flex items-center gap-2 bg-brand-cream/50 p-3 rounded-xl border border-brand-border/50">
                                <span class="text-brand-success font-bold">✓</span> Analisis Masalah Kulit
                            </div>
                            <div class="flex items-center gap-2 bg-brand-cream/50 p-3 rounded-xl border border-brand-border/50">
                                <span class="text-brand-success font-bold">✓</span> Rekomendasi Skincare
                            </div>
                            <div class="flex items-center gap-2 bg-brand-cream/50 p-3 rounded-xl border border-brand-border/50">
                                <span class="text-brand-success font-bold">✓</span> Catat Riwayat Progres
                            </div>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('skin-check.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-primaryAlt transition-all shadow-soft text-sm">
                                Coba Skin Check Sekarang &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="w-full lg:w-5/12">
                        <div class="bg-brand-cream p-6 rounded-3xl border border-brand-border shadow-inner relative overflow-hidden">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-bold text-brand-primary uppercase tracking-wider">AI Scanner Live Preview</span>
                                <span class="px-2 py-0.5 bg-brand-success/10 text-brand-success text-[10px] font-bold rounded-full">Calibrated</span>
                            </div>
                            <div class="aspect-[4/3] bg-black/5 rounded-2xl border border-dashed border-brand-border flex flex-col items-center justify-center p-6 text-center relative overflow-hidden group">
                                <div class="w-20 h-20 rounded-full border-2 border-dashed border-brand-primary/60 flex items-center justify-center animate-pulse mb-3">
                                    <svg class="w-10 h-10 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                </div>
                                <p class="text-xs font-bold text-brand-text">Area Pindai Wajah AI</p>
                                <p class="text-[11px] text-brand-textAlt mt-1">Deteksi otomatis batas dahi, pipi, hidung, dan dagu</p>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="bg-white p-2 rounded-xl border border-brand-border/60">
                                    <p class="text-[10px] text-brand-textAlt">Hidrasi</p>
                                    <p class="text-xs font-bold text-brand-primary">78%</p>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-brand-border/60">
                                    <p class="text-[10px] text-brand-textAlt">Pori-Pori</p>
                                    <p class="text-xs font-bold text-brand-success">Kecil</p>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-brand-border/60">
                                    <p class="text-[10px] text-brand-textAlt">Skin Barrier</p>
                                    <p class="text-xs font-bold text-brand-text">Sehat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Products Section -->
        <div class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-2 block">Koleksi Teruji Klinis</span>
                        <h2 class="text-3xl font-serif font-bold text-brand-text">Katalog Produk Skincare</h2>
                        <p class="text-brand-textAlt text-sm mt-2">Diformulasikan khusus untuk iklim tropis dan terdaftar resmi di BPOM.</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-primary hover:text-brand-primaryAlt transition-colors">
                            Lihat Semua Produk
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredProducts as $product)
                        <div class="group bg-white rounded-3xl border border-brand-border overflow-hidden hover:shadow-soft-xl hover:border-brand-primary/40 transition-all flex flex-col justify-between">
                            <a href="{{ route('products.show', $product) }}" class="aspect-square bg-gradient-to-br from-brand-cream/80 to-brand-border/40 p-6 flex items-center justify-center relative overflow-hidden block">
                                @if($product->image)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500 shadow-xs">
                                @else
                                    <div class="w-full h-full overflow-hidden flex items-center justify-center rounded-2xl bg-brand-cream">
                                        <img src="{{ asset('images/brand-assets.jpg') }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="object-position: 8% 85%; transform: scale(3.5);">
                                    </div>
                                @endif
                                
                                <span class="absolute top-3 left-3 px-3 py-1 bg-white/95 backdrop-blur-md text-brand-primary text-[11px] font-bold rounded-full shadow-xs border border-brand-border/40">
                                    {{ $product->badge }}
                                </span>

                                <span class="absolute top-3 right-3 px-2.5 py-1 bg-brand-primary/10 text-brand-primary text-[10px] font-bold rounded-full">
                                    {{ $product->category->name ?? 'Skincare' }}
                                </span>
                            </a>

                            <div class="p-6 flex-1 flex flex-col justify-between border-t border-brand-border/40">
                                <div>
                                    <p class="text-[11px] uppercase font-bold tracking-wider text-brand-primary mb-1">{{ $product->category->name ?? 'Skincare' }}</p>
                                    <a href="{{ route('products.show', $product) }}" class="font-bold text-brand-text text-base mb-2 leading-snug line-clamp-2 hover:text-brand-primary transition-colors block">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex items-center gap-1.5 text-xs text-brand-textAlt mb-3">
                                        <span class="text-amber-500">★</span>
                                        <span class="font-semibold text-brand-text">{{ $product->rating }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-brand-border/40 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-brand-textAlt uppercase font-semibold">Harga</p>
                                        <p class="text-lg font-bold text-brand-primary">{{ $product->formatted_price }}</p>
                                    </div>

                                    @auth
                                        <a href="{{ route('products.show', $product) }}" class="px-4 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primaryAlt transition-all shadow-xs">
                                            Beli Sekarang
                                        </a>
                                    @else
                                        <button type="button" 
                                                @click="$dispatch('open-auth-modal', { 
                                                    title: 'Masuk untuk Memesan Produk', 
                                                    description: 'Silakan masuk atau buat akun baru terlebih dahulu untuk melanjutkan pemesanan {{ addslashes($product->name) }} ({{ $product->formatted_price }}).' 
                                                })"
                                                class="px-4 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primaryAlt transition-all shadow-xs">
                                            Beli Sekarang
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Consultation Spotlight Section -->
        <div class="py-24 bg-brand-cream/40 border-t border-brand-border/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-2 block">Tim Medis Berpengalaman</span>
                    <h2 class="text-3xl font-serif font-bold text-brand-text mb-4">Konsultasi Dokter Spesialis Kulit</h2>
                    <p class="text-brand-textAlt text-sm leading-relaxed">
                        Dapatkan diagnosis akurat dan panduan resep skincare langsung dari dermatologis dan formulator kecantikan terpercaya.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white p-6 rounded-3xl border border-brand-border shadow-soft hover:shadow-soft-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=150" alt="dr. Amanda" class="w-14 h-14 rounded-2xl object-cover border border-brand-border shadow-xs">
                                <div>
                                    <h4 class="font-bold text-brand-text text-base">dr. Amanda Sp.KK</h4>
                                    <p class="text-xs text-brand-primary font-medium">Spesialis Kulit & Jerawat</p>
                                    <p class="text-[11px] text-brand-textAlt mt-0.5">8 Thn Pengalaman • ⭐ 4.9</p>
                                </div>
                            </div>
                            <p class="text-xs text-brand-textAlt leading-relaxed mb-4">Fokus penanganan jerawat kistik meradang, skin barrier rusak, dan dermatitis seboroik.</p>
                        </div>
                        <a href="{{ route('consultation.index') }}" class="w-full py-2.5 bg-brand-cream text-brand-text font-bold text-xs rounded-xl hover:bg-brand-primary hover:text-white transition-all text-center border border-brand-border">
                            Konsultasi Sekarang
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-brand-border shadow-soft hover:shadow-soft-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=150" alt="dr. Reza Pratama" class="w-14 h-14 rounded-2xl object-cover border border-brand-border shadow-xs">
                                <div>
                                    <h4 class="font-bold text-brand-text text-base">dr. Reza Pratama Sp.DV</h4>
                                    <p class="text-xs text-brand-primary font-medium">Anti-Aging & Melasma</p>
                                    <p class="text-[11px] text-brand-textAlt mt-0.5">10 Thn Pengalaman • ⭐ 5.0</p>
                                </div>
                            </div>
                            <p class="text-xs text-brand-textAlt leading-relaxed mb-4">Ahli dalam penanganan flek hitam melasma, garis halus penuaan, dan tekstur pori-pori besar.</p>
                        </div>
                        <a href="{{ route('consultation.index') }}" class="w-full py-2.5 bg-brand-cream text-brand-text font-bold text-xs rounded-xl hover:bg-brand-primary hover:text-white transition-all text-center border border-brand-border">
                            Konsultasi Sekarang
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-brand-border shadow-soft hover:shadow-soft-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=150" alt="Apt. Nadia" class="w-14 h-14 rounded-2xl object-cover border border-brand-border shadow-xs">
                                <div>
                                    <h4 class="font-bold text-brand-text text-base">Apt. Nadia S.Farm</h4>
                                    <p class="text-xs text-brand-primary font-medium">Beauty Advisor & Formulator</p>
                                    <p class="text-[11px] text-brand-textAlt mt-0.5">5 Thn Pengalaman • ⭐ 4.9</p>
                                </div>
                            </div>
                            <p class="text-xs text-brand-textAlt leading-relaxed mb-4">Membantu mencocokkan formula aktif skincare harian Anda agar tidak saling bentrok/iritasi.</p>
                        </div>
                        <a href="{{ route('consultation.index') }}" class="w-full py-2.5 bg-brand-cream text-brand-text font-bold text-xs rounded-xl hover:bg-brand-primary hover:text-white transition-all text-center border border-brand-border">
                            Konsultasi Sekarang
                        </a>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('consultation.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-brand-text text-white font-bold rounded-2xl hover:bg-black transition-all shadow-md text-sm">
                        Buka Halaman Konsultasi Lengkap &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Latest Articles / News Section -->
        <div class="py-24 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-primary mb-2 block">Edukasi & Tren Terkini</span>
                        <h2 class="text-3xl font-serif font-bold text-brand-text">Berita & Artikel Skincare</h2>
                        <p class="text-brand-textAlt text-sm mt-2">Wawasan ilmiah dan tips praktis merawat kulit dari para dokter spesialis.</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-brand-primary hover:text-brand-primaryAlt transition-colors">
                            Lihat Semua Berita
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($latestArticles as $article)
                        <article class="bg-brand-cream/40 rounded-3xl border border-brand-border overflow-hidden hover:shadow-soft-lg hover:border-brand-primary/40 transition-all flex flex-col justify-between">
                            <div class="p-6">
                                <div class="flex items-center justify-between text-xs mb-3">
                                    <span class="px-2.5 py-1 bg-brand-primary/10 text-brand-primary font-bold rounded-full">
                                        {{ $article->category }}
                                    </span>
                                    <span class="text-brand-textAlt text-[11px]">{{ $article->read_time }}</span>
                                </div>
                                <h3 class="text-lg font-serif font-bold text-brand-text mb-2 line-clamp-2 hover:text-brand-primary transition-colors">
                                    <a href="{{ route('news.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-brand-textAlt leading-relaxed line-clamp-3">
                                    {{ $article->excerpt }}
                                </p>
                            </div>
                            <div class="px-6 py-4 bg-white border-t border-brand-border/40 flex items-center justify-between text-xs">
                                <span class="font-medium text-brand-text">{{ $article->author_name }}</span>
                                <a href="{{ route('news.show', $article->slug) }}" class="text-brand-primary font-bold hover:underline">
                                    Baca &rarr;
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-brand-text text-brand-cream pt-16 pb-12 border-t border-brand-border/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                    <div class="md:col-span-1">
                        <span class="text-2xl font-serif font-bold tracking-widest text-white block mb-4">URSKYND</span>
                        <p class="text-sm text-brand-creamAlt/70 leading-relaxed mb-6">
                            AI Skin Companion pertama di Indonesia untuk kulit sehat, bersih, dan percaya diri.
                        </p>
                        <p class="text-xs text-brand-creamAlt/60">Layanan Konsultasi Kulit: 08.00 - 21.00 WIB</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Menu Utama</h3>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="{{ route('skin-check.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">AI Skin Check</a></li>
                            <li><a href="{{ route('products.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Katalog Produk Skincare</a></li>
                            <li><a href="{{ route('consultation.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Konsultasi Dokter</a></li>
                            <li><a href="{{ route('news.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Berita & Edukasi Kulit</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Layanan & Akun</h3>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="{{ route('login') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Masuk ke Akun</a></li>
                            <li><a href="{{ route('register') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Daftar Akun Baru</a></li>
                            <li><a href="{{ route('consultation.index') }}" class="text-brand-creamAlt/70 hover:text-white transition-colors">Booking Jadwal Telekonsultasi</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Hubungi Kami</h3>
                        <p class="text-sm text-brand-creamAlt/70 leading-relaxed">
                            Jl. Raya Plalangan Plosowahyu Km. 3, Kec. Lamongan, Kab. Lamongan, Jawa Timur 62218 (Kampus UMLA)<br/>
                            Email: support@urskynd.com
                        </p>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-brand-creamAlt/50 gap-4">
                    <p>&copy; {{ date('Y') }} URSKYND. Hak cipta dilindungi undang-undang.</p>
                    <p>Designed for Smart Skin Health.</p>
                </div>
            </div>
        </footer>

        <!-- Global Auth Modal -->
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
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-brand-border"
                 @click.outside="authModalOpen = false">
                <div class="w-16 h-16 bg-brand-primary/10 text-brand-primary rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <h3 class="text-xl font-serif font-bold text-center text-brand-text mb-2" x-text="authModalTitle">
                    Masuk untuk Membeli Produk
                </h3>
                
                <p class="text-sm text-center text-brand-textAlt mb-6 leading-relaxed" x-text="authModalDesc">
                    Silakan masuk atau daftar terlebih dahulu untuk melanjutkan pembelian produk ini.
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
                        Batal dan Kembali
                    </button>
                </div>
            </div>
        </div>

    </body>
</html>
