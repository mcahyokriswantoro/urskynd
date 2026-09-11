<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category' => 'Cleanser', 'brand' => 'URSKYND', 'name' => 'Foaming Face Wash', 'size' => '150ml', 'description' => 'Pembersih wajah lembut dengan formula busa halus yang membersihkan tanpa membuat kulit kering.', 'usage_instruction' => 'Basahi wajah, aplikasikan secukupnya, pijat lembut, lalu bilas bersih.'],
            ['category' => 'Cleanser', 'brand' => 'URSKYND', 'name' => 'Gentle Milk Cleanser', 'size' => '120ml', 'description' => 'Pembersih susu yang lembut untuk kulit sensitif dan kering.', 'usage_instruction' => 'Aplikasikan pada wajah kering, pijat lembut, lalu bilas atau lap dengan kapas basah.'],
            ['category' => 'Toner', 'brand' => 'URSKYND', 'name' => 'Hydrating Toner', 'size' => '200ml', 'description' => 'Toner dengan hyaluronic acid untuk menyeimbangkan pH dan menghidrasi kulit.', 'usage_instruction' => 'Tuangkan pada kapas atau telapak tangan, tepuk-tepuk lembut ke seluruh wajah.'],
            ['category' => 'Serum', 'brand' => 'URSKYND', 'name' => 'Brightening & Anti-Aging Serum', 'size' => '30ml', 'description' => 'Serum dengan Vitamin C dan Niacinamide untuk mencerahkan dan anti-aging.', 'usage_instruction' => 'Teteskan 3-4 tetes pada wajah dan leher, tepuk lembut hingga meresap.'],
            ['category' => 'Serum', 'brand' => 'URSKYND', 'name' => 'Acne Control Serum', 'size' => '30ml', 'description' => 'Serum dengan Salicylic Acid dan Tea Tree untuk mengontrol jerawat.', 'usage_instruction' => 'Aplikasikan 2-3 tetes pada area berjerawat setelah toner.'],
            ['category' => 'Moisturizer', 'brand' => 'URSKYND', 'name' => 'Daily Moisture Cream', 'size' => '50ml', 'description' => 'Pelembab harian dengan Ceramide dan Squalane untuk memperkuat skin barrier.', 'usage_instruction' => 'Aplikasikan secukupnya pada seluruh wajah setelah serum.'],
            ['category' => 'Moisturizer', 'brand' => 'URSKYND', 'name' => 'Night Recovery Cream', 'size' => '50ml', 'description' => 'Krim malam dengan Retinol dan Peptide untuk regenerasi kulit saat tidur.', 'usage_instruction' => 'Aplikasikan pada malam hari sebagai langkah terakhir skincare routine.'],
            ['category' => 'Sunscreen', 'brand' => 'URSKYND', 'name' => 'UV Sunscreen SPF 50+', 'size' => '40ml', 'description' => 'Sunscreen ringan dengan SPF 50+ PA++++ yang tidak meninggalkan white cast.', 'usage_instruction' => 'Aplikasikan secukupnya 15 menit sebelum terpapar sinar matahari. Reapply setiap 2-3 jam.'],
            ['category' => 'Mask', 'brand' => 'URSKYND', 'name' => 'Hydrating Sheet Mask', 'size' => '25ml', 'description' => 'Sheet mask dengan Hyaluronic Acid untuk hidrasi intens.', 'usage_instruction' => 'Tempelkan pada wajah bersih selama 15-20 menit, lepaskan dan tepuk sisa essence.'],
            ['category' => 'Exfoliant', 'brand' => 'URSKYND', 'name' => 'Gentle Peeling Gel', 'size' => '100ml', 'description' => 'Peeling gel lembut untuk mengangkat sel kulit mati tanpa iritasi.', 'usage_instruction' => 'Gunakan 2-3 kali seminggu pada wajah kering, pijat melingkar lalu bilas.'],
            ['category' => 'Eye Cream', 'brand' => 'URSKYND', 'name' => 'Revitalizing Eye Cream', 'size' => '15ml', 'description' => 'Krim mata dengan Caffeine dan Peptide untuk mengurangi kantung dan lingkaran hitam.', 'usage_instruction' => 'Tepuk lembut di area bawah mata menggunakan jari manis.'],
            ['category' => 'Spot Treatment', 'brand' => 'URSKYND', 'name' => 'Acne Spot Gel', 'size' => '15ml', 'description' => 'Gel spot treatment untuk mengeringkan jerawat dalam semalam.', 'usage_instruction' => 'Aplikasikan tipis pada jerawat sebagai langkah terakhir sebelum tidur.'],
        ];

        foreach ($products as $productData) {
            $category = ProductCategory::where('name', $productData['category'])->first();
            if ($category) {
                Product::create([
                    'product_category_id' => $category->id,
                    'brand' => $productData['brand'],
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['brand'] . ' ' . $productData['name']),
                    'description' => $productData['description'],
                    'size' => $productData['size'],
                    'usage_instruction' => $productData['usage_instruction'],
                    'status' => 'active',
                ]);
            }
        }
    }
}
