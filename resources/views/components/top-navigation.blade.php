<div x-data="{ mobileMenuOpen: false }">
    <!-- Desktop Top Header -->
    <header class="hidden md:flex items-center justify-between h-20 px-8 bg-white border-b border-brand-border/50 sticky top-0 z-30">
        <div class="flex-1">
            <!-- Search could go here -->
        </div>

        <div class="flex items-center gap-6">
            <!-- Points / Rewards -->
            <a href="{{ route('rewards.index') }}" class="flex items-center gap-2 px-3 py-1.5 bg-brand-cream rounded-full hover:bg-brand-cream/80 transition-colors">
                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-bold text-brand-primary">{{ auth()->user()->points ?? 0 }}</span>
                <span class="text-xs text-brand-textAlt">Pts</span>
            </a>

            <!-- Notifications Dropdown -->
            <div class="relative" x-data="{ notifOpen: false, hasUnread: true }">
                <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false" class="relative p-2 text-brand-textAlt hover:text-brand-primary transition-colors rounded-xl hover:bg-brand-cream/50 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span x-show="hasUnread" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-brand-danger border-2 border-white rounded-full animate-pulse"></span>
                </button>

                <!-- Notification Popup Panel -->
                <div x-show="notifOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-soft-xl border border-brand-border/70 py-2 z-50 overflow-hidden"
                     style="display: none;">
                    
                    <div class="px-4 py-2.5 border-b border-brand-border/50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-brand-text">Notifikasi & Pengingat</h3>
                            <span x-show="hasUnread" class="px-2 py-0.5 bg-brand-primary/10 text-brand-primary text-[10px] font-bold rounded-full">Baru</span>
                        </div>
                        <button @click="hasUnread = false" class="text-[11px] text-brand-primary hover:underline font-medium">Tandai dibaca</button>
                    </div>

                    <div class="divide-y divide-brand-border/40 max-h-80 overflow-y-auto">
                        <!-- Item 1: Routine -->
                        <a href="{{ route('routines.index') }}" class="p-3.5 flex gap-3 hover:bg-brand-cream/30 transition-colors block">
                            <div class="w-8 h-8 rounded-full bg-brand-cream text-brand-primary flex items-center justify-center shrink-0 text-sm">
                                🌙
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-brand-text">Pengingat Rutinitas Malam</p>
                                <p class="text-[11px] text-brand-textAlt mt-0.5">Waktunya skincare malam! Bersihkan wajah dan aplikasikan moisturizer.</p>
                                <span class="text-[9px] text-brand-textAlt mt-1 block">Baru saja</span>
                            </div>
                        </a>

                        <!-- Item 2: Journal -->
                        <a href="{{ route('journal.create') }}" class="p-3.5 flex gap-3 hover:bg-brand-cream/30 transition-colors block">
                            <div class="w-8 h-8 rounded-full bg-brand-cream text-brand-primary flex items-center justify-center shrink-0 text-sm">
                                📝
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-brand-text">Isi Skin Journal Hari Ini</p>
                                <p class="text-[11px] text-brand-textAlt mt-0.5">Catat kondisi kulit harianmu dan dapatkan bonus <strong>+5 Poin</strong>.</p>
                                <span class="text-[9px] text-brand-textAlt mt-1 block">1 jam yang lalu</span>
                            </div>
                        </a>

                        <!-- Item 3: Rewards -->
                        <a href="{{ route('rewards.index') }}" class="p-3.5 flex gap-3 hover:bg-brand-cream/30 transition-colors block">
                            <div class="w-8 h-8 rounded-full bg-brand-success/10 text-brand-success flex items-center justify-center shrink-0 text-sm">
                                🎁
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-brand-text">Bonus Poin Skin Check</p>
                                <p class="text-[11px] text-brand-textAlt mt-0.5">Selamat! Kamu mendapatkan <strong>+10 Points</strong> dari analisis kulit.</p>
                                <span class="text-[9px] text-brand-textAlt mt-1 block">Kemarin</span>
                            </div>
                        </a>
                    </div>

                    <div class="p-2 border-t border-brand-border/40 text-center bg-brand-cream/10">
                        <a href="{{ route('routines.index') }}" class="text-xs font-semibold text-brand-primary hover:underline">Kelola Pengingat Rutinitas &rarr;</a>
                    </div>
                </div>
            </div>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 focus:outline-none p-1.5 rounded-xl hover:bg-brand-cream/50 transition-colors">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-brand-cream object-cover">
                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-semibold text-brand-text leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-brand-textAlt capitalize">{{ auth()->user()->role ?? 'User' }}</p>
                    </div>
                    <svg class="w-4 h-4 text-brand-textAlt" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-soft-lg border border-brand-border/60 py-2 z-50 divide-y divide-brand-border/40"
                     style="display: none;">
                    <div class="px-4 py-2.5">
                        <p class="text-xs text-brand-textAlt">Masuk sebagai</p>
                        <p class="text-sm font-bold text-brand-text truncate">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-brand-text hover:bg-brand-cream/60 hover:text-brand-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Pengaturan Profil
                        </a>
                        <a href="{{ route('rewards.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-brand-text hover:bg-brand-cream/60 hover:text-brand-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Rewards & Badges
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-brand-text hover:bg-brand-cream/60 hover:text-brand-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Panel Admin
                            </a>
                        @endif
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-brand-danger hover:bg-brand-danger/10 transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Top Header Bar -->
    <header class="flex md:hidden items-center justify-between h-16 px-4 bg-white border-b border-brand-border/50 sticky top-0 z-30 shadow-xs">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="URSKYND Logo" class="h-8 w-auto object-contain">
            <span class="text-xl font-serif font-bold tracking-wider text-brand-text">URSKYND</span>
        </a>

        <div class="flex items-center gap-1.5 sm:gap-2.5">
            <!-- Mobile Points Pill -->
            <a href="{{ route('rewards.index') }}" class="flex items-center gap-1.5 px-2.5 py-1 bg-brand-cream border border-brand-border/60 rounded-full text-xs font-bold text-brand-primary">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ auth()->user()->points ?? 0 }}</span>
            </a>

            <!-- Mobile Notification Dropdown -->
            <div class="relative" x-data="{ mobileNotifOpen: false, hasUnread: true }">
                <button @click="mobileNotifOpen = !mobileNotifOpen" @click.outside="mobileNotifOpen = false" class="relative p-2 text-brand-textAlt hover:text-brand-primary transition-colors rounded-xl hover:bg-brand-cream/50 focus:outline-none">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span x-show="hasUnread" class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-danger border-2 border-white rounded-full"></span>
                </button>

                <!-- Mobile Notif Panel -->
                <div x-show="mobileNotifOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-soft-xl border border-brand-border/70 py-2 z-50 overflow-hidden"
                     style="display: none;">
                    
                    <div class="px-3.5 py-2 border-b border-brand-border/50 flex items-center justify-between">
                        <span class="text-xs font-bold text-brand-text">Notifikasi</span>
                        <button @click="hasUnread = false" class="text-[10px] text-brand-primary font-medium hover:underline">Tandai dibaca</button>
                    </div>

                    <div class="divide-y divide-brand-border/40 max-h-64 overflow-y-auto">
                        <a href="{{ route('routines.index') }}" class="p-3 flex gap-2.5 hover:bg-brand-cream/30 block">
                            <span class="text-base">🌙</span>
                            <div>
                                <p class="text-xs font-bold text-brand-text">Rutinitas Malam</p>
                                <p class="text-[10px] text-brand-textAlt">Waktunya skincare rutin malam!</p>
                            </div>
                        </a>
                        <a href="{{ route('journal.create') }}" class="p-3 flex gap-2.5 hover:bg-brand-cream/30 block">
                            <span class="text-base">📝</span>
                            <div>
                                <p class="text-xs font-bold text-brand-text">Isi Skin Journal</p>
                                <p class="text-[10px] text-brand-textAlt">Catat kondisi kulit hari ini (+5 Pts).</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile Drawer Button (Hamburger) -->
            <button @click="mobileMenuOpen = true" class="p-2 rounded-xl text-brand-text hover:bg-brand-cream/60 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </header>

    <!-- Mobile Full Drawer (Slide-Over) -->
    <div x-show="mobileMenuOpen" 
         class="fixed inset-0 z-50 md:hidden"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-xs"></div>

        <!-- Drawer Content -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl flex flex-col z-50 overflow-hidden">
            
            <!-- Drawer Header -->
            <div class="p-4 border-b border-brand-border flex items-center justify-between bg-brand-cream/30">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="URSKYND Logo" class="h-6 w-auto object-contain">
                    <span class="font-serif font-bold text-lg text-brand-text">URSKYND</span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-1.5 rounded-lg text-brand-textAlt hover:text-brand-text">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- User Quick Card in Drawer -->
            <div class="p-4 border-b border-brand-border/60 flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-12 h-12 rounded-full border-2 border-brand-cream object-cover">
                <div class="flex-1 min-w-0">
                    <h4 class="font-bold text-sm text-brand-text truncate">{{ auth()->user()->name }}</h4>
                    <p class="text-xs text-brand-textAlt truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-block px-2 py-0.5 mt-1 bg-brand-cream text-brand-primary text-[10px] font-bold rounded-full">
                        {{ auth()->user()->points ?? 0 }} Points
                    </span>
                </div>
            </div>

            <!-- Drawer Links (All Features) -->
            <div class="flex-1 overflow-y-auto py-3 px-3 space-y-1">
                <p class="px-3 py-1 text-[11px] font-semibold text-brand-textAlt uppercase tracking-wider">Utama</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard / Beranda</span>
                </a>

                <a href="{{ route('skin-check.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('skin-check.*') || request()->routeIs('analysis.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Skin Analysis & Scan</span>
                </a>

                <a href="{{ route('tracker.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('tracker.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    <span>Skin Progress Tracker</span>
                </a>

                <a href="{{ route('journal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('journal.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Skin Journal (Harian)</span>
                </a>

                <p class="px-3 pt-3 py-1 text-[11px] font-semibold text-brand-textAlt uppercase tracking-wider">Perawatan & Produk</p>

                <a href="{{ route('routines.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('routines.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>My Routine (Rutinitas)</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span>Katalog Produk</span>
                </a>

                <a href="{{ route('rewards.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('rewards.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    <span>Rewards & Badges</span>
                </a>

                <p class="px-3 pt-3 py-1 text-[11px] font-semibold text-brand-textAlt uppercase tracking-wider">Akun</p>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'bg-brand-primary/10 text-brand-primary font-bold' : 'text-brand-text hover:bg-brand-cream/60' }}">
                    <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Pengaturan Profil</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-brand-text hover:bg-brand-cream/60">
                        <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Panel Administrator</span>
                    </a>
                @endif
            </div>

            <!-- Drawer Footer (Logout) -->
            <div class="p-4 border-t border-brand-border bg-brand-cream/20">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-brand-border rounded-xl text-sm font-semibold text-brand-danger hover:bg-brand-danger/10 transition-colors shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
