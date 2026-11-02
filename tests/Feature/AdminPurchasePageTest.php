<?php

namespace Tests\Feature;

use App\Models\PageContent;
use App\Models\User;
use App\Support\PurchasePageDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPurchasePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_access_editor(): void
    {
        $this->get('/admin/detail-pembelian')->assertRedirect('/login');

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get('/admin/detail-pembelian')->assertForbidden();
    }

    private function png(string $name): UploadedFile
    {
        $bytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $bytes);
    }

    public function test_admin_can_update_content_and_upload_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/detail-pembelian')->assertOk();

        $defaults = PurchasePageDefaults::get();
        $payload = [
            'general' => array_merge($defaults['general'], [
                'hero_title' => 'Judul Baru Admin',
                'whatsapp_number' => '6281111111111',
                'show_countdown' => '1',
            ]),
            'problem' => $defaults['problem'],
            'trust' => $defaults['trust'],
            'packages' => [[
                'name' => 'Paket Uji Coba',
                'price' => 150000,
                'original_price' => 200000,
                'items' => "1x Serum\nBonus: Pouch",
                'popular' => '1',
                'image_file' => $this->png('paket.png'),
            ]],
            'products' => [[
                'name' => 'Produk Uji',
                'price' => 50000,
                'image' => 'images/products/serum.jpeg',
            ]],
            'testimonials' => [[
                'name' => 'Budi',
                'text' => 'Mantap sekali',
                'rating' => 4,
                'photo_file' => $this->png('budi.png'),
            ]],
            'faqs' => [['question' => 'Tanya?', 'answer' => 'Jawab.']],
        ];

        $this->actingAs($admin)->put('/admin/detail-pembelian', $payload)
            ->assertRedirect('/admin/detail-pembelian')
            ->assertSessionHasNoErrors();

        $content = PageContent::where('key', PurchasePageDefaults::KEY)->first()->content;
        $this->assertSame('Judul Baru Admin', $content['general']['hero_title']);
        $this->assertCount(1, $content['packages']);
        $this->assertStringStartsWith('storage/pembelian/', $content['packages'][0]['image']);
        Storage::disk('public')->assertExists(substr($content['packages'][0]['image'], 8));
        Storage::disk('public')->assertExists(substr($content['testimonials'][0]['photo'], 8));

        $this->get('/detail-pembelian')
            ->assertOk()
            ->assertSee('Judul Baru Admin')
            ->assertSee('Paket Uji Coba')
            ->assertSee('Rp 150.000')
            ->assertSee('Mantap sekali')
            ->assertSee('6281111111111');

        // Reset kembali ke default
        $this->actingAs($admin)->post('/admin/detail-pembelian/reset')->assertRedirect('/admin/detail-pembelian');
        $this->get('/detail-pembelian')->assertSee('Paket Glowing Starter');
    }
}
