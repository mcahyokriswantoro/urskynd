<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_be_rendered_with_latest_updates(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $cat = \App\Models\ProductCategory::create([
            'name' => 'Cleanser',
            'slug' => 'cleanser',
            'icon' => 'sparkles',
            'order' => 1,
        ]);

        Product::create([
            'product_category_id' => $cat->id,
            'name' => 'URSKYND Hydrating Gentle Cleanser',
            'slug' => 'urskynd-hydrating-gentle-cleanser',
            'brand' => 'URSKYND',
            'price' => 68000,
            'badge' => 'Best Seller',
            'status' => 'active',
            'rating' => 4.9,
            'description' => 'Test cleanser',
            'usage_step' => 'cleanser',
        ]);

        Article::create([
            'title' => 'Pentingnya Menjaga Skin Barrier di Iklim Tropis',
            'slug' => 'pentingnya-menjaga-skin-barrier',
            'category' => 'Edukasi Kulit',
            'excerpt' => 'Panduan merawat skin barrier.',
            'content' => 'Konten lengkap edukasi skin barrier.',
            'read_time_minutes' => 4,
            'published_at' => now(),
            'is_featured' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('URSKYND Hydrating Gentle Cleanser');
        $response->assertSee('Rp 68.000');
        $response->assertSee('Gentle');
        $response->assertSee('Pentingnya Menjaga Skin Barrier');
        $response->assertSee('Artikel Edukasi Kulit');
    }
}
