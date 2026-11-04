<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use App\Support\PurchasePageDefaults;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPurchasePageController extends Controller
{
    /**
     * Ambil konten halaman detail pembelian (gabungan DB + default).
     */
    public static function content(): array
    {
        $defaults = PurchasePageDefaults::get();
        $content = PageContent::getContent(PurchasePageDefaults::KEY, $defaults);

        // Pastikan key baru di section asosiatif selalu ada
        foreach (['general', 'problem'] as $section) {
            $content[$section] = array_replace($defaults[$section], $content[$section] ?? []);
        }

        return $content;
    }

    public function edit()
    {
        $content = self::content();

        return view('admin.pembelian.edit', compact('content'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'general' => 'required|array',
            'general.whatsapp_number' => ['required', 'regex:/^[0-9]{8,15}$/'],
            'general.hero_title' => 'required|string|max:200',
            'general.banner_image_file' => 'nullable|image|max:4096',
            'general.hero_image_file' => 'nullable|image|max:4096',
            'general.qris_image_file' => 'nullable|image|max:4096',
            'problem' => 'required|array',
            'trust' => 'nullable|array',
            'packages' => 'nullable|array',
            'packages.*.name' => 'required|string|max:150',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.original_price' => 'nullable|numeric|min:0',
            'packages.*.image_file' => 'nullable|image|max:4096',
            'products' => 'nullable|array',
            'products.*.name' => 'required|string|max:150',
            'products.*.price' => 'nullable|numeric|min:0',
            'products.*.image_file' => 'nullable|image|max:4096',
            'testimonials' => 'nullable|array',
            'testimonials.*.name' => 'required|string|max:100',
            'testimonials.*.text' => 'required|string|max:1000',
            'testimonials.*.rating' => 'nullable|integer|min:1|max:5',
            'testimonials.*.photo_file' => 'nullable|image|max:4096',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required|string|max:255',
            'faqs.*.answer' => 'required|string|max:2000',
        ], [
            'general.whatsapp_number.regex' => 'Nomor WhatsApp harus angka saja dengan format 62xxx (8-15 digit).',
            '*.image' => 'File harus berupa gambar (jpg, png, webp).',
        ]);

        $old = self::content();

        // General
        $general = collect($request->input('general', []))
            ->except(['banner_image_file', 'remove_banner_image', 'hero_image_file', 'remove_hero_image', 'qris_image_file', 'remove_qris_image'])
            ->map(fn ($v) => is_string($v) ? trim($v) : $v)
            ->all();
        $general['show_countdown'] = $request->boolean('general.show_countdown');
        $general['banner_image'] = $old['general']['banner_image'] ?? 'images/promo-banner.jpg';
        if ($request->boolean('general.remove_banner_image')) {
            $this->deleteImage($general['banner_image']);
            $general['banner_image'] = null;
        }
        if ($file = $request->file('general.banner_image_file')) {
            $this->deleteImage($general['banner_image']);
            $general['banner_image'] = $this->storeImage($file);
        }

        $general['hero_image'] = $old['general']['hero_image'] ?? null;
        if ($request->boolean('general.remove_hero_image')) {
            $this->deleteImage($general['hero_image']);
            $general['hero_image'] = null;
        }
        if ($file = $request->file('general.hero_image_file')) {
            $this->deleteImage($general['hero_image']);
            $general['hero_image'] = $this->storeImage($file);
        }

        $general['qris_image'] = $old['general']['qris_image'] ?? 'images/qris-urskynd.jpg';
        if ($request->boolean('general.remove_qris_image')) {
            $this->deleteImage($general['qris_image']);
            $general['qris_image'] = null;
        }
        if ($file = $request->file('general.qris_image_file')) {
            $this->deleteImage($general['qris_image']);
            $general['qris_image'] = $this->storeImage($file);
        }

        $problem = $request->input('problem', []);

        $trust = collect($request->input('trust', []))
            ->map(fn ($t) => [
                'icon' => $t['icon'] ?? '',
                'title' => $t['title'] ?? '',
                'subtitle' => $t['subtitle'] ?? '',
            ])->filter(fn ($t) => $t['title'] !== '')->values()->all();

        $packages = [];
        foreach ($request->input('packages', []) as $i => $p) {
            $packages[] = [
                'name' => $p['name'],
                'tag' => $p['tag'] ?? '',
                'badge' => $p['badge'] ?? '',
                'items' => $p['items'] ?? '',
                'original_price' => (int) ($p['original_price'] ?? 0),
                'price' => (int) $p['price'],
                'free_shipping' => $p['free_shipping'] ?? '',
                'popular' => !empty($p['popular']),
                'wa_message' => $p['wa_message'] ?? '',
                'image' => $this->resolveImage($request, "packages.$i", 'image', $p),
            ];
        }

        $products = [];
        foreach ($request->input('products', []) as $i => $p) {
            $products[] = [
                'step' => $p['step'] ?? '',
                'name' => $p['name'],
                'description' => $p['description'] ?? '',
                'price' => (int) ($p['price'] ?? 0),
                'image' => $this->resolveImage($request, "products.$i", 'image', $p),
            ];
        }

        $testimonials = [];
        foreach ($request->input('testimonials', []) as $i => $t) {
            $testimonials[] = [
                'name' => $t['name'],
                'location' => $t['location'] ?? '',
                'rating' => (int) ($t['rating'] ?? 5),
                'text' => $t['text'],
                'photo' => $this->resolveImage($request, "testimonials.$i", 'photo', $t),
            ];
        }

        $faqs = collect($request->input('faqs', []))
            ->map(fn ($f) => ['question' => $f['question'], 'answer' => $f['answer']])
            ->values()->all();

        // Hapus file upload lama yang sudah tidak dipakai lagi
        $newContent = compact('general', 'problem', 'trust', 'packages', 'products', 'testimonials', 'faqs');
        $this->cleanupUnusedImages($old, $newContent);

        PageContent::saveContent(PurchasePageDefaults::KEY, $newContent);

        return redirect()->route('admin.pembelian.edit')->with('success', 'Halaman Detail Pembelian berhasil diperbarui!');
    }

    public function reset()
    {
        PageContent::where('key', PurchasePageDefaults::KEY)->delete();

        return redirect()->route('admin.pembelian.edit')->with('success', 'Konten dikembalikan ke pengaturan awal.');
    }

    private function resolveImage(Request $request, string $prefix, string $field, array $data): ?string
    {
        $current = $data[$field] ?? null;

        if (!empty($data["remove_{$field}"])) {
            $current = null;
        }

        if ($file = $request->file("{$prefix}.{$field}_file")) {
            $current = $this->storeImage($file);
        }

        return $current ?: null;
    }

    private function storeImage($file): string
    {
        return 'storage/' . $file->store('pembelian', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/pembelian/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }

    private function cleanupUnusedImages(array $old, array $new): void
    {
        $collect = function (array $c) {
            $paths = [$c['general']['banner_image'] ?? null, $c['general']['hero_image'] ?? null, $c['general']['qris_image'] ?? null];
            foreach ($c['packages'] ?? [] as $p) $paths[] = $p['image'] ?? null;
            foreach ($c['products'] ?? [] as $p) $paths[] = $p['image'] ?? null;
            foreach ($c['testimonials'] ?? [] as $t) $paths[] = $t['photo'] ?? null;
            return array_filter($paths);
        };

        foreach (array_diff($collect($old), $collect($new)) as $path) {
            $this->deleteImage($path);
        }
    }
}
