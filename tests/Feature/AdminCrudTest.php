<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Doctor;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ConsultationBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_normal_user_cannot_access_admin_crud(): void
    {
        // Guest
        $this->get('/admin/products')->assertRedirect('/login');
        $this->get('/admin/articles')->assertRedirect('/login');
        $this->get('/admin/doctors')->assertRedirect('/login');

        // Regular user
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get('/admin/products')->assertForbidden();
        $this->actingAs($user)->get('/admin/articles')->assertForbidden();
        $this->actingAs($user)->get('/admin/doctors')->assertForbidden();
    }

    public function test_admin_can_manage_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = ProductCategory::create([
            'name' => 'Toner',
            'slug' => 'toner',
            'icon' => 'sparkles',
            'order' => 1,
        ]);

        // View Index & Create page
        $this->actingAs($admin)->get('/admin/products')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/products/create')->assertStatus(200);

        // Store new Product
        $response = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'URSKYND Calming Toner Essence',
            'brand' => 'URSKYND',
            'product_category_id' => $category->id,
            'price' => 119000,
            'size' => '100 ml',
            'badge' => 'New Arrival',
            'status' => 'active',
            'description' => 'Toner menenangkan kulit kemerahan dan memperkuat skin barrier.',
            'usage_instruction' => 'Gunakan setelah mencuci muka.',
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'name' => 'URSKYND Calming Toner Essence',
            'price' => 119000,
        ]);

        $product = Product::where('name', 'URSKYND Calming Toner Essence')->first();

        // Edit & Update
        $this->actingAs($admin)->get("/admin/products/{$product->id}/edit")->assertStatus(200);
        $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => 'URSKYND Calming Toner Essence Pro',
            'brand' => 'URSKYND',
            'product_category_id' => $category->id,
            'price' => 125000,
            'size' => '100 ml',
            'badge' => 'Best Formula',
            'status' => 'active',
            'description' => 'Formula upgrade dengan 5x Ceramide.',
        ])->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'URSKYND Calming Toner Essence Pro',
            'price' => 125000,
        ]);

        // Delete
        $this->actingAs($admin)->delete("/admin/products/{$product->id}")->assertRedirect('/admin/products');
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_admin_can_manage_articles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // View Index & Create
        $this->actingAs($admin)->get('/admin/articles')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/articles/create')->assertStatus(200);

        // Store new Article
        $response = $this->actingAs($admin)->post('/admin/articles', [
            'title' => 'Cara Tepat Mengatasi Kulit Dehidrasi',
            'category' => 'Edukasi Kulit',
            'excerpt' => 'Panduan lengkap hidrasi kulit.',
            'content' => 'Air dan kelembapan sangat dibutuhkan lapisan epidermis...',
            'read_time' => '5 menit',
            'author_name' => 'dr. Amanda Sp.KK',
            'author_role' => 'Dermatologist',
            'is_published' => '1',
        ]);

        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'title' => 'Cara Tepat Mengatasi Kulit Dehidrasi',
        ]);

        $article = Article::where('title', 'Cara Tepat Mengatasi Kulit Dehidrasi')->first();

        // Edit & Update
        $this->actingAs($admin)->get("/admin/articles/{$article->id}/edit")->assertStatus(200);
        $this->actingAs($admin)->put("/admin/articles/{$article->id}", [
            'title' => 'Cara Tepat Mengatasi Kulit Dehidrasi Parah',
            'category' => 'Edukasi Kulit',
            'excerpt' => 'Panduan lengkap hidrasi kulit revisi.',
            'content' => 'Konten revisi lengkap...',
            'read_time' => '6 menit',
            'author_name' => 'dr. Amanda Sp.KK',
            'is_published' => '1',
        ])->assertRedirect('/admin/articles');

        $this->assertDatabaseHas('articles', [
            'title' => 'Cara Tepat Mengatasi Kulit Dehidrasi Parah',
        ]);

        // Delete
        $this->actingAs($admin)->delete("/admin/articles/{$article->id}")->assertRedirect('/admin/articles');
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    public function test_admin_can_manage_doctors_and_bookings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // View Index & Create
        $this->actingAs($admin)->get('/admin/doctors')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/doctors/create')->assertStatus(200);

        // Store new Doctor
        $response = $this->actingAs($admin)->post('/admin/doctors', [
            'name' => 'dr. Budi Santoso Sp.KK',
            'title' => 'Spesialis Kulit & Kelamin',
            'experience' => '7 Tahun Pengalaman',
            'hospital' => 'Klinik URSKYND Surabaya',
            'rating' => '4.9',
            'specialties' => 'Jerawat, Eksim, Alergi',
            'price' => 'Gratis via URSKYND Support',
            'available' => '1',
        ]);

        $response->assertRedirect('/admin/doctors');
        $this->assertDatabaseHas('doctors', [
            'name' => 'dr. Budi Santoso Sp.KK',
        ]);

        $doctor = Doctor::where('name', 'dr. Budi Santoso Sp.KK')->first();

        // Edit & Update Doctor
        $this->actingAs($admin)->get("/admin/doctors/{$doctor->id}/edit")->assertStatus(200);
        $this->actingAs($admin)->put("/admin/doctors/{$doctor->id}", [
            'name' => 'dr. Budi Santoso Sp.DV, FINSDV',
            'title' => 'Spesialis Dermatologi & Venereologi Senior',
            'experience' => '8 Tahun Pengalaman',
            'hospital' => 'Klinik URSKYND Surabaya',
            'rating' => '5.0',
            'specialties' => 'Jerawat Hormonal, Flek, Anti-Aging',
            'price' => 'Gratis via URSKYND Support',
            'available' => '1',
        ])->assertRedirect('/admin/doctors');

        $this->assertDatabaseHas('doctors', [
            'name' => 'dr. Budi Santoso Sp.DV, FINSDV',
            'rating' => '5.0',
        ]);

        // Create booking & update status
        $booking = ConsultationBooking::create([
            'doctor_id' => $doctor->id,
            'name' => 'Siti Rahma',
            'phone' => '081298765432',
            'concern' => 'Jerawat meradang di pipi',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'contacted',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('consultation_bookings', [
            'id' => $booking->id,
            'status' => 'contacted',
        ]);

        // Delete Doctor
        $this->actingAs($admin)->delete("/admin/doctors/{$doctor->id}")->assertRedirect('/admin/doctors');
        $this->assertDatabaseMissing('doctors', [
            'id' => $doctor->id,
        ]);
    }
}
