<x-dynamic-component :component="auth()->check() ? 'app-layout' : 'public-layout'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Back Navigation -->
        <div class="mb-6">
            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-textAlt hover:text-brand-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Berita & Tips
            </a>
        </div>

        <article class="bg-white rounded-3xl border border-brand-border p-6 md:p-12 shadow-soft-lg mb-12">
            <!-- Article Header -->
            <div class="mb-8 pb-8 border-b border-brand-border/50">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-brand-primary/10 text-brand-primary text-xs font-bold rounded-full">
                        {{ $article->category }}
                    </span>
                    <span class="text-xs text-brand-textAlt">•</span>
                    <span class="text-xs text-brand-textAlt">{{ $article->read_time }}</span>
                    <span class="text-xs text-brand-textAlt">•</span>
                    <span class="text-xs text-brand-textAlt">{{ $article->published_at ? $article->published_at->format('d F Y') : 'Terbaru' }}</span>
                </div>

                <h1 class="text-2xl md:text-4xl font-serif font-bold text-brand-text mb-6 leading-tight">
                    {{ $article->title }}
                </h1>

                <!-- Author info -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold text-sm">
                            🩺
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand-text">{{ $article->author_name }}</p>
                            <p class="text-xs text-brand-textAlt">{{ $article->author_role }}</p>
                        </div>
                    </div>

                    <!-- Share action -->
                    <div class="flex items-center gap-2">
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" class="p-2.5 rounded-xl border border-brand-border hover:bg-brand-cream text-brand-text transition-colors" title="Bagikan via WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.586 1.761.882 2.796.883 3.182 0 5.768-2.587 5.768-5.766.001-3.18-2.585-5.77-5.768-5.77zm0-2.172c4.379 0 7.938 3.558 7.938 7.938 0 4.38-3.559 7.939-7.938 7.939-1.341 0-2.604-.337-3.712-.927l-4.319 1.132 1.155-4.215c-.687-1.162-1.062-2.502-1.062-3.929 0-4.38 3.559-7.938 7.938-7.938z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lead Excerpt -->
            <div class="p-4 md:p-6 bg-brand-cream/50 rounded-2xl border-l-4 border-brand-primary mb-8 text-sm md:text-base text-brand-text font-serif italic leading-relaxed">
                "{{ $article->excerpt }}"
            </div>

            <!-- Body Content -->
            <div class="prose prose-sm md:prose-base max-w-none text-brand-text leading-relaxed space-y-4">
                {!! $article->content !!}
            </div>

            <!-- CTA Banner inside article -->
            <div class="mt-12 p-6 md:p-8 bg-brand-cream rounded-3xl border border-brand-border text-center">
                <span class="w-2 h-2 rounded-full bg-brand-primary inline-block mb-2"></span>
                <h3 class="text-xl font-serif font-bold text-brand-text mb-2">Sudahkah Kamu Tahu Kondisi Kulitmu Saat Ini?</h3>
                <p class="text-xs md:text-sm text-brand-textAlt mb-6 max-w-md mx-auto">
                    Gunakan AI Skin Check untuk memindai hidrasi, jerawat, dan pori-pori wajahmu secara gratis dalam 10 detik.
                </p>
                <a href="{{ route('skin-check.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white font-bold rounded-xl text-xs hover:bg-brand-primaryAlt transition-all shadow-soft">
                    Mulai AI Skin Check Sekarang &rarr;
                </a>
            </div>
        </article>

        <!-- Related Articles -->
        @if($relatedArticles->isNotEmpty())
            <div class="mb-16">
                <h3 class="text-xl font-serif font-bold text-brand-text mb-6">Artikel Terkait Lainnya</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedArticles as $rel)
                        <a href="{{ route('news.show', $rel->slug) }}" class="bg-white p-5 rounded-2xl border border-brand-border hover:shadow-soft hover:border-brand-primary/40 transition-all flex flex-col justify-between block group">
                            <div>
                                <span class="px-2.5 py-0.5 bg-brand-primary/10 text-brand-primary text-[10px] font-bold rounded-full mb-2 inline-block">
                                    {{ $rel->category }}
                                </span>
                                <h4 class="font-serif font-bold text-brand-text text-sm mb-2 line-clamp-2 group-hover:text-brand-primary transition-colors">
                                    {{ $rel->title }}
                                </h4>
                                <p class="text-xs text-brand-textAlt line-clamp-2">{{ $rel->excerpt }}</p>
                            </div>
                            <div class="pt-3 border-t border-brand-border/40 mt-4 flex items-center justify-between text-[11px] text-brand-textAlt">
                                <span>{{ $rel->author_name }}</span>
                                <span class="text-brand-primary font-bold">Baca &rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-dynamic-component>
