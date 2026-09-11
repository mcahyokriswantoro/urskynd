<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-brand-danger/10 text-brand-danger text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Admin Mode</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-brand-text mb-1">Overview Sistem</h1>
            <p class="text-sm text-brand-textAlt">Pantau aktivitas pengguna dan penggunaan fitur URSKYND.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-card class="border-l-4 border-l-brand-primary">
            <p class="text-sm text-brand-textAlt mb-1">Total Pengguna</p>
            <h3 class="text-3xl font-serif font-bold text-brand-text">{{ number_format($stats['total_users']) }}</h3>
        </x-card>
        
        <x-card class="border-l-4 border-l-brand-success">
            <p class="text-sm text-brand-textAlt mb-1">Analisis Kulit Dilakukan</p>
            <h3 class="text-3xl font-serif font-bold text-brand-text">{{ number_format($stats['total_analyses']) }}</h3>
        </x-card>
        
        <x-card class="border-l-4 border-l-brand-warning">
            <p class="text-sm text-brand-textAlt mb-1">Katalog Produk</p>
            <h3 class="text-3xl font-serif font-bold text-brand-text">{{ number_format($stats['total_products']) }}</h3>
        </x-card>
    </div>

    <!-- Recent Users -->
    <x-card>
        <h2 class="font-serif font-bold text-brand-text mb-4 text-lg">Pengguna Baru Terdaftar</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border/50 text-brand-textAlt uppercase tracking-wider text-[10px] font-bold">
                        <th class="pb-3 font-medium">Nama</th>
                        <th class="pb-3 font-medium">Email</th>
                        <th class="pb-3 font-medium">Bergabung</th>
                        <th class="pb-3 font-medium text-right">Points</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border/30">
                    @forelse($stats['recent_users'] as $u)
                        <tr>
                            <td class="py-3 font-medium text-brand-text">{{ $u->name }}</td>
                            <td class="py-3 text-brand-textAlt">{{ $u->email }}</td>
                            <td class="py-3 text-brand-textAlt">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="py-3 text-right font-bold text-brand-primary">{{ number_format($u->reward_points) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-brand-textAlt">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
