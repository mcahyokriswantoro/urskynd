<aside class="hidden md:flex md:flex-col md:w-64 md:fixed md:inset-y-0 bg-white border-r border-brand-border z-40 shadow-sm">
    <!-- Logo -->
    <div class="flex items-center justify-center h-20 border-b border-brand-border/50 px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/logo.png') }}" alt="URSKYND Logo" class="h-9 w-auto object-contain transition-transform group-hover:scale-105">
            <span class="text-xl font-serif font-bold tracking-wider text-brand-text">URSKYND</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>

        <a href="{{ route('analysis.index') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('analysis.*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            Skin Analysis
        </a>

        <a href="{{ route('tracker.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('tracker.*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            Skin Tracker
        </a>

        <a href="{{ route('journal.index') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('journal.*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Skin Journal
        </a>

        <div class="pt-4 mt-4 border-t border-brand-border/50">
            <p class="px-4 text-xs font-semibold text-brand-secondary uppercase tracking-wider mb-2">Skincare</p>
            <a href="{{ route('routines.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('routines.*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                My Routine
            </a>
            
            <a href="{{ route('products.index') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('products.*') ? 'bg-brand-primary/10 text-brand-primary font-medium' : 'text-brand-textAlt hover:bg-brand-cream hover:text-brand-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Products
            </a>
        </div>
    </nav>
</aside>
