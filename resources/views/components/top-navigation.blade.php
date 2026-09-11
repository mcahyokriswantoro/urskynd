<header class="hidden md:flex items-center justify-between h-20 px-8 bg-white border-b border-brand-border/50 sticky top-0 z-30">
    <div class="flex-1">
        <!-- Search could go here -->
    </div>

    <div class="flex items-center gap-6">
        <!-- Points / Rewards -->
        <div class="flex items-center gap-2 px-3 py-1.5 bg-brand-cream rounded-full">
            <span class="text-sm font-medium text-brand-primary">{{ auth()->user()->points ?? 0 }}</span>
            <span class="text-xs text-brand-textAlt">Pts</span>
        </div>

        <!-- Notifications -->
        <button class="relative p-2 text-brand-textAlt hover:text-brand-primary transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-danger rounded-full"></span>
        </button>

        <!-- Profile Dropdown -->
        <div class="relative x-data="{ open: false }">
            <button class="flex items-center gap-3 focus:outline-none">
                <img src="{{ auth()->user()->avatar_url }}" alt="Profile" class="w-10 h-10 rounded-full border-2 border-brand-cream object-cover">
                <div class="hidden lg:block text-left">
                    <p class="text-sm font-medium text-brand-text">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-brand-textAlt">Level 1</p>
                </div>
            </button>
        </div>
    </div>
</header>
