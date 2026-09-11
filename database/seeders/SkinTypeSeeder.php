<?php

namespace Database\Seeders;

use App\Models\SkinType;
use Illuminate\Database\Seeder;

class SkinTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Normal', 'description' => 'Kulit seimbang, tidak terlalu berminyak atau kering. Pori-pori halus dengan sedikit masalah kulit.'],
            ['name' => 'Dry', 'description' => 'Kulit terasa kencang dan kering. Mudah mengelupas dan tampak kusam. Membutuhkan hidrasi ekstra.'],
            ['name' => 'Oily', 'description' => 'Kulit cenderung berminyak, terutama di T-zone. Pori-pori tampak besar dan rentan berjerawat.'],
            ['name' => 'Combination', 'description' => 'Kombinasi area berminyak (T-zone) dan kering (pipi). Membutuhkan perawatan yang seimbang.'],
            ['name' => 'Sensitive', 'description' => 'Kulit mudah bereaksi terhadap produk atau lingkungan. Sering mengalami kemerahan atau iritasi.'],
        ];

        foreach ($types as $type) {
            SkinType::create($type);
        }
    }
}
