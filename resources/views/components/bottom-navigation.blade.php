<div class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur-md border-t border-brand-border md:hidden z-40 pb-safe">
    <div class="grid grid-cols-5 h-16 items-center">
        <!-- Beranda -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('dashboard') ? 'text-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px]">Beranda</span>
        </a>

        <!-- Skin Check / Analisis -->
        <a href="{{ route('skin-check.index') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('skin-check.*') || request()->routeIs('analysis.*') ? 'text-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-[10px]">Analisis</span>
        </a>

        <!-- Tracker -->
        <a href="{{ route('tracker.index') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('tracker.*') ? 'text-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
            <span class="text-[10px]">Tracker</span>
        </a>

        <!-- Routine -->
        <a href="{{ route('routines.index') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('routines.*') ? 'text-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-[10px]">Rutin</span>
        </a>

        <!-- Profil -->
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center h-full transition-colors {{ request()->routeIs('profile.*') ? 'text-brand-primary font-bold' : 'text-brand-textAlt hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-[10px]">Profil</span>
        </a>
    </div>
</div>
