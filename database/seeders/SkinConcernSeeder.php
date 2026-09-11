<?php

namespace Database\Seeders;

use App\Models\SkinConcern;
use Illuminate\Database\Seeder;

class SkinConcernSeeder extends Seeder
{
    public function run(): void
    {
        $concerns = [
            ['name' => 'Acne', 'slug' => 'acne', 'description' => 'Jerawat aktif, komedo, atau bekas jerawat', 'icon' => '🔴'],
            ['name' => 'Dryness', 'slug' => 'dryness', 'description' => 'Kulit kering, mengelupas, atau dehidrasi', 'icon' => '💧'],
            ['name' => 'Dark Spot', 'slug' => 'dark-spot', 'description' => 'Noda hitam, hiperpigmentasi, atau flek', 'icon' => '🟤'],
            ['name' => 'Wrinkle', 'slug' => 'wrinkle', 'description' => 'Garis halus dan kerutan', 'icon' => '〰️'],
            ['name' => 'Redness', 'slug' => 'redness', 'description' => 'Kemerahan, rosacea, atau iritasi', 'icon' => '🌡️'],
            ['name' => 'Large Pores', 'slug' => 'large-pores', 'description' => 'Pori-pori besar dan tampak jelas', 'icon' => '⭕'],
            ['name' => 'Pigmentation', 'slug' => 'pigmentation', 'description' => 'Warna kulit tidak merata', 'icon' => '🎨'],
        ];

        foreach ($concerns as $concern) {
            SkinConcern::create($concern);
        }
    }
}
