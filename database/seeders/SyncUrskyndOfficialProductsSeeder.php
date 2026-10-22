<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncUrskyndOfficialProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Set all other products to inactive first
        Product::query()->update(['status' => 'inactive']);

        // 1. Cleanser
        $cleanserCategory = ProductCategory::firstOrCreate(
            ['slug' => 'cleanser'],
            ['name' => 'Cleanser', 'description' => 'Pembersih wajah']
        );

        $cleanser = Product::updateOrCreate(
            ['slug' => 'cleanser'],
            [
                'product_category_id' => $cleanserCategory->id,
                'brand' => 'URSKYND',
                'name' => 'URSKYND Foaming Face Wash',
                'size' => '100ml (Berat pengiriman: 250 gram)',
                'price' => 68000,
                'image' => 'images/products/lineup.jpeg',
                'description' => 'Foaming face wash lembut dengan Niacinamide, Licorice Extract, Aloe Vera, Snail Extract, dan Kojic Acid untuk membersihkan wajah tanpa membuat kulit kering.',
                'usage_instruction' => 'Basahi wajah dengan air bersih, pompa busa secukupnya pada telapak tangan, usapkan secara lembut ke seluruh wajah dengan gerakan melingkar, lalu bilas dengan air hingga bersih.',
                'status' => 'active',
            ]
        );

        // 2. Serum
        $serumCategory = ProductCategory::firstOrCreate(
            ['slug' => 'serum'],
            ['name' => 'Serum', 'description' => 'Kandungan aktif konsentrasi tinggi']
        );

        $serum = Product::updateOrCreate(
            ['slug' => 'serum'],
            [
                'product_category_id' => $serumCategory->id,
                'brand' => 'URSKYND',
                'name' => 'URSKYND Brightening & Anti-Aging Face Serum',
                'size' => '30ml (Berat pengiriman: 250 gram)',
                'price' => 189000,
                'image' => 'images/products/serum.jpeg',
                'description' => 'Face serum dengan Niacinamide 8%, Alpha Arbutin 2%, Kojic Acid 2%, Sodium Hyaluronate, dan Aloe Vera untuk membantu meratakan warna kulit serta memberi glow sehat.',
                'usage_instruction' => 'Teteskan 2-3 tetes serum pada telapak tangan atau langsung pada wajah yang telah dibersihkan. Usapkan secara merata dan tepuk perlahan hingga meresap sempurna. Gunakan pagi dan malam hari sebelum pelembap.',
                'status' => 'active',
            ]
        );

        // 3. Sunscreen
        $sunscreenCategory = ProductCategory::firstOrCreate(
            ['slug' => 'sunscreen'],
            ['name' => 'Sunscreen', 'description' => 'Perlindungan UV']
        );

        $sunscreen = Product::updateOrCreate(
            ['slug' => 'sunscreen'],
            [
                'product_category_id' => $sunscreenCategory->id,
                'brand' => 'URSKYND',
                'name' => 'URSKYND UV Sunscreen',
                'size' => '50ml (Berat pengiriman: 250 gram)',
                'price' => 149000,
                'image' => 'images/products/sunscreen.jpeg',
                'description' => 'UV Sunscreen dengan UV Filter, Allantoin, Aloe Vera Extract, dan Ceramide untuk perlindungan harian dari sinar UV dengan tekstur nyaman tanpa whitecast.',
                'usage_instruction' => 'Aplikasikan sebanyak dua ruas jari secara merata ke seluruh wajah dan leher sebagai langkah terakhir rutinitas skincare pagi hari, 15-20 menit sebelum beraktivitas di luar ruangan. Aplikasikan ulang setiap 2-3 jam.',
                'status' => 'active',
            ]
        );

        // Remap any existing user products / recommendations to point to active official products
        DB::table('user_products')->whereNotIn('product_id', [$cleanser->id, $serum->id, $sunscreen->id])
            ->update(['product_id' => $serum->id]);

        DB::table('recommendation_items')->whereNotIn('product_id', [$cleanser->id, $serum->id, $sunscreen->id])
            ->update(['product_id' => $serum->id]);
    }
}
