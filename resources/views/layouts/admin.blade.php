<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Control Center - URSKYND</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=5">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=5">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased text-slate-800 bg-[#F4F6F8]" x-data="{ adminDrawerOpen: false }">
    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- Desktop Admin Sidebar -->
        <aside class="hidden md:flex md:flex-col md:w-64 md:fixed md:inset-y-0 bg-[#1E293B] text-slate-200 z-40 border-r border-slate-700/60 shadow-xl">
            <!-- Admin Brand Header -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-slate-700/60 bg-[#0F172A]/50">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="URSKYND Logo" class="h-8 w-auto object-contain brightness-110">
                    <div>
                        <span class="text-base font-serif font-bold tracking-wider text-white">URSKYND</span>
                        <span class="block text-[10px] font-mono tracking-widest text-[#E8B496] font-semibold uppercase">Admin Suite</span>
                    </div>
                </a>
            </div>

            <!-- System Health Status Pill -->
            <div class="px-6 py-3 bg-[#0F172A]/40 border-b border-slate-700/40 flex items-center justify-between text-xs">
                <span class="text-slate-400">Status Sistem</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Online
                </span>
            </div>

            <!-- Admin Navigation -->
            <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
                <p class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Main Management</p>

                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#9A6A52] text-white font-semibold shadow-md shadow-[#9A6A52]/20' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-[#E8B496]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard Overview
                </a>

                <a href="{{ route('admin.dashboard') }}#users-section" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors text-slate-300 hover:bg-slate-800/80 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-[#E8B496]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Manajemen Pengguna
                </a>

                <a href="{{ route('admin.dashboard') }}#products-section" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors text-slate-300 hover:bg-slate-800/80 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-[#E8B496]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Katalog & Produk
                </a>

                <a href="{{ route('admin.dashboard') }}#analyses-section" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors text-slate-300 hover:bg-slate-800/80 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-[#E8B496]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Log AI Skin Analyses
                </a>

                <p class="px-3 pt-4 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas & Log</p>

                <a href="{{ route('admin.dashboard') }}#activity-section" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors text-slate-300 hover:bg-slate-800/80 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-[#E8B496]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Jurnal & Transaksi Poin
                </a>
            </nav>

            <!-- Admin Profile & Logout (Bottom Sidebar) -->
            <div class="p-4 border-t border-slate-700/60 bg-[#0F172A]/70">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-[#9A6A52] text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                            A
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Admin Content Area -->
        <div class="flex-1 md:ml-64 flex flex-col min-h-screen">
            
            <!-- Mobile Admin Header -->
            <header class="flex md:hidden items-center justify-between h-16 px-4 bg-[#1E293B] text-white sticky top-0 z-30 shadow-md">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="URSKYND" class="h-7 w-auto object-contain brightness-110">
                    <div>
                        <span class="font-serif font-bold text-sm">URSKYND</span>
                        <span class="text-[9px] font-mono text-[#E8B496] ml-1">ADMIN</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="adminDrawerOpen = true" class="p-2 rounded-lg text-slate-300 hover:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </header>

            <!-- Desktop Admin Topbar -->
            <header class="hidden md:flex items-center justify-between h-20 px-8 bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
                <div>
                    <h2 class="text-xl font-serif font-bold text-slate-900">Control Panel & Monitoring</h2>
                    <p class="text-xs text-slate-500">Pusat kendali dan administrasi seluruh ekosistem URSKYND</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="px-3 py-1.5 bg-slate-100 rounded-xl border border-slate-200 text-xs text-slate-600 font-mono">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 text-xs font-semibold rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Main Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-8">
                {{ $slot }}
            </main>
        </div>

        <!-- Mobile Drawer for Admin -->
        <div x-show="adminDrawerOpen" class="fixed inset-0 z-50 md:hidden" style="display: none;">
            <div @click="adminDrawerOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>
            <div class="fixed inset-y-0 right-0 max-w-xs w-full bg-[#1E293B] text-slate-200 p-6 flex flex-col justify-between shadow-2xl">
                <div class="space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-700 pb-4">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-7 w-auto">
                            <span class="font-serif font-bold text-white">URSKYND ADMIN</span>
                        </div>
                        <button @click="adminDrawerOpen = false" class="text-slate-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <a href="{{ route('admin.dashboard') }}" @click="adminDrawerOpen = false" class="block px-3 py-2 rounded-lg bg-[#9A6A52] text-white font-medium text-sm">Dashboard Overview</a>
                        <a href="{{ route('admin.dashboard') }}#users-section" @click="adminDrawerOpen = false" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 text-sm">Manajemen Pengguna</a>
                        <a href="{{ route('admin.dashboard') }}#products-section" @click="adminDrawerOpen = false" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 text-sm">Katalog Produk</a>
                        <a href="{{ route('admin.dashboard') }}#analyses-section" @click="adminDrawerOpen = false" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 text-sm">Log AI Skin Analyses</a>
                        <a href="{{ route('admin.dashboard') }}#activity-section" @click="adminDrawerOpen = false" class="block px-3 py-2 rounded-lg hover:bg-slate-800 text-slate-300 text-sm">Jurnal & Poin</a>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-rose-600/20 border border-rose-500/40 text-rose-300 rounded-xl font-semibold text-sm">
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
