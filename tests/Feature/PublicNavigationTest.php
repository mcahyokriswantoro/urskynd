<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = ProductCategory::create([
            'name' => 'Serum',
            'slug' => 'serum',
            'description' => 'Serum wajah',
        ]);

        Product::create([
            'product_category_id' => $category->id,
            'brand' => 'URSKYND',
            'name' => 'Brightening Serum',
            'slug' => 'brightening-serum',
            'price' => 149000,
            'description' => 'Serum pencerah kulit',
            'status' => 'active',
        ]);

        Article::create([
            'title' => 'Tips Memperbaiki Skin Barrier',
            'slug' => 'tips-memperbaiki-skin-barrier',
            'category' => 'Tips & Perawatan',
            'excerpt' => 'Panduan memulihkan skin barrier',
            'content' => '<p>Konten artikel lengkap</p>',
            'published_at' => now(),
        ]);
    }

    public function test_guest_can_view_welcome_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Skin Check');
        $response->assertSee('Produk');
        $response->assertSee('Konsultasi');
        $response->assertSee('Berita');
    }

    public function test_guest_can_view_products_catalog(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Brightening Serum');
        $response->assertSee('Rp 149.000');
    }

    public function test_guest_can_view_product_detail(): void
    {
        $product = Product::first();
        $response = $this->get('/products/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee('Brightening Serum');
        $response->assertSee('Beli Sekarang');
    }

    public function test_guest_can_view_skin_check_page(): void
    {
        $response = $this->get('/skin-check');
        $response->assertStatus(200);
        $response->assertSee('Skin Check');
        $response->assertSee('Mulai Skin Check Lebih Lanjut');
    }

    public function test_guest_cannot_run_full_skin_analysis_post_without_login(): void
    {
        $response = $this->post('/skin-check', []);
        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_add_product_to_collection_without_login(): void
    {
        $product = Product::first();
        $response = $this->post('/products/' . $product->id . '/add-to-collection');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_view_consultation_page(): void
    {
        $response = $this->get('/konsultasi');
        $response->assertStatus(200);
        $response->assertSee('Konsultasi Online');
        $response->assertSee('dr. Amanda');
    }

    public function test_guest_can_submit_consultation_form(): void
    {
        $response = $this->post('/konsultasi', [
            'name' => 'Maya',
            'phone' => '081234567890',
            'concern' => 'Jerawat aktif & komedo membandel',
            'notes' => 'Tolong dibantu dok',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/konsultasi');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('consultation_bookings', [
            'name' => 'Maya',
            'concern' => 'Jerawat aktif & komedo membandel',
        ]);
    }

    public function test_guest_can_view_news_catalog(): void
    {
        $response = $this->get('/berita');
        $response->assertStatus(200);
        $response->assertSee('Tips Memperbaiki Skin Barrier');
    }

    public function test_guest_can_view_news_article_detail(): void
    {
        $response = $this->get('/berita/tips-memperbaiki-skin-barrier');
        $response->assertStatus(200);
        $response->assertSee('Tips Memperbaiki Skin Barrier');
        $response->assertSee('Konten artikel lengkap');
    }
}
