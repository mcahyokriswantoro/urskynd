<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cleanser', 'slug' => 'cleanser', 'description' => 'Pembersih wajah'],
            ['name' => 'Toner', 'slug' => 'toner', 'description' => 'Penyeimbang pH kulit'],
            ['name' => 'Essence', 'slug' => 'essence', 'description' => 'Hidrasi ringan dan nutrisi'],
            ['name' => 'Serum', 'slug' => 'serum', 'description' => 'Kandungan aktif konsentrasi tinggi'],
            ['name' => 'Moisturizer', 'slug' => 'moisturizer', 'description' => 'Pelembab dan pengunci kelembaban'],
            ['name' => 'Sunscreen', 'slug' => 'sunscreen', 'description' => 'Perlindungan UV'],
            ['name' => 'Mask', 'slug' => 'mask', 'description' => 'Masker wajah'],
            ['name' => 'Exfoliant', 'slug' => 'exfoliant', 'description' => 'Eksfoliasi dan pengelupasan sel kulit mati'],
            ['name' => 'Eye Cream', 'slug' => 'eye-cream', 'description' => 'Perawatan area mata'],
            ['name' => 'Spot Treatment', 'slug' => 'spot-treatment', 'description' => 'Perawatan titik masalah'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
