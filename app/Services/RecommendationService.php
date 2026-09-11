<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Recommendation;
use App\Models\RecommendationItem;
use App\Models\SkinAnalysis;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Generate personalized recommendations based on skin analysis.
     */
    public function generateRecommendations(User $user, SkinAnalysis $analysis): Recommendation
    {
        return DB::transaction(function () use ($user, $analysis) {
            $recommendation = Recommendation::create([
                'user_id' => $user->id,
                'skin_analysis_id' => $analysis->id,
                'title' => 'Rekomendasi Personal Untukmu',
                'description' => 'Rangkaian produk & rutinitas yang disesuaikan dengan kondisi kulitmu.',
            ]);

            // Get available products grouped by category
            $products = Product::with('category')->where('status', 'active')->get()->groupBy('product_category_id');

            // Morning routine recommendations
            $morningOrder = ['Cleanser', 'Toner', 'Serum', 'Moisturizer', 'Sunscreen'];
            $this->createRoutineItems($recommendation, $products, $morningOrder, 'morning', $analysis);

            // Night routine recommendations
            $nightOrder = ['Cleanser', 'Toner', 'Serum', 'Moisturizer'];
            $this->createRoutineItems($recommendation, $products, $nightOrder, 'night', $analysis);

            return $recommendation->load('items.product');
        });
    }

    private function createRoutineItems(
        Recommendation $recommendation,
        $products,
        array $categoryOrder,
        string $routineType,
        SkinAnalysis $analysis
    ): void {
        $order = 1;
        foreach ($categoryOrder as $categoryName) {
            $categoryProducts = $products->filter(function ($items) use ($categoryName) {
                return $items->first()?->category?->name === $categoryName;
            })->flatten();

            if ($categoryProducts->isNotEmpty()) {
                $product = $categoryProducts->random();

                RecommendationItem::create([
                    'recommendation_id' => $recommendation->id,
                    'product_id' => $product->id,
                    'routine_type' => $routineType,
                    'order_number' => $order,
                    'reason' => $this->generateReason($categoryName, $analysis),
                    'usage_instruction' => $product->usage_instruction,
                ]);
            }
            $order++;
        }
    }

    private function generateReason(string $category, SkinAnalysis $analysis): string
    {
        $score = $analysis->overall_score;
        return match ($category) {
            'Cleanser' => 'Membersihkan kulit dari kotoran dan sisa makeup tanpa menghilangkan kelembaban alami.',
            'Toner' => 'Menyeimbangkan pH kulit dan mempersiapkan kulit untuk menyerap produk selanjutnya.',
            'Serum' => $score < 70 ? 'Kandungan aktif untuk memperbaiki masalah kulit yang terdeteksi.' : 'Menutrisi kulit dengan bahan aktif untuk menjaga kesehatan kulit.',
            'Moisturizer' => 'Mengunci kelembaban dan memperkuat skin barrier.',
            'Sunscreen' => 'Melindungi kulit dari sinar UV yang dapat mempercepat penuaan.',
            default => 'Direkomendasikan berdasarkan kondisi kulit Anda saat ini.',
        };
    }
}
