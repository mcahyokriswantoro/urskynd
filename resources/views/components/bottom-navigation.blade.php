<div class="fixed bottom-0 left-0 w-full bg-white border-t border-brand-border md:hidden z-50 pb-safe">
    <div class="flex justify-around items-center h-16">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-brand-secondary hover:text-brand-primary {{ request()->routeIs('dashboard') ? 'text-brand-primary font-medium' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px]">Beranda</span>
        </a>

        <a href="{{ route('analysis.index') ?? '#' }}" class="flex flex-col items-center justify-center w-full h-full text-brand-secondary hover:text-brand-primary {{ request()->routeIs('analysis.*') ? 'text-brand-primary font-medium' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-[10px]">Analisis</span>
        </a>

        <a href="{{ route('tracker.index') ?? '#' }}" class="flex flex-col items-center justify-center w-full h-full text-brand-secondary hover:text-brand-primary {{ request()->routeIs('tracker.*') ? 'text-brand-primary font-medium' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
            <span class="text-[10px]">Tracker</span>
        </a>

        <a href="{{ route('products.index') ?? '#' }}" class="flex flex-col items-center justify-center w-full h-full text-brand-secondary hover:text-brand-primary {{ request()->routeIs('products.*') ? 'text-brand-primary font-medium' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="text-[10px]">Produk</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center w-full h-full text-brand-secondary hover:text-brand-primary {{ request()->routeIs('profile.*') ? 'text-brand-primary font-medium' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[10px]">Profil</span>
        </a>
    </div>
</div>
<!-- Padding to prevent content from hiding behind bottom nav on mobile -->
<div class="h-16 md:hidden"></div>
