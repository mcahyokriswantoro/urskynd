<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProduct;
use App\Models\SkinAnalysis;
use App\Models\SkinAnalysisMetric;
use App\Models\SkinJournal;
use App\Models\Routine;
use App\Models\RoutineItem;
use App\Models\Reminder;
use App\Models\Product;
use App\Models\SkinType;
use App\Models\PointTransaction;
use App\Models\Recommendation;
use App\Models\RecommendationItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@urskynd.test')->first();
        if (!$user) return;

        $this->createUserProfile($user);
        $this->createUserSkinConcerns($user);
        $this->createSkinAnalyses($user);
        $this->createUserProducts($user);
        $this->createRoutines($user);
        $this->createReminders($user);
        $this->createJournals($user);
        $this->createPointTransactions($user);
        $this->createAchievements($user);
    }

    private function createUserProfile(User $user): void
    {
        $skinType = SkinType::where('name', 'Combination')->first();
        UserProfile::create([
            'user_id' => $user->id,
            'skin_type_id' => $skinType?->id,
            'skin_goal' => 'Clear Skin, Brightening, Hydration',
            'allergies' => null,
            'notes' => 'Kulit cenderung berminyak di T-zone',
        ]);
    }

    private function createUserSkinConcerns(User $user): void
    {
        $concerns = \App\Models\SkinConcern::whereIn('slug', ['acne', 'large-pores', 'dark-spot'])->get();
        foreach ($concerns as $concern) {
            $user->skinConcerns()->attach($concern->id, ['severity' => 'moderate']);
        }
    }

    private function createSkinAnalyses(User $user): void
    {
        $skinType = SkinType::where('name', 'Combination')->first();

        // Create 6 analyses over the past months for progress tracking
        $analysisData = [
            ['days_ago' => 150, 'score' => 68, 'skin_age' => 27.2],
            ['days_ago' => 120, 'score' => 71, 'skin_age' => 26.8],
            ['days_ago' => 90, 'score' => 74, 'skin_age' => 26.5],
            ['days_ago' => 60, 'score' => 77, 'skin_age' => 26.2],
            ['days_ago' => 30, 'score' => 80, 'skin_age' => 26.0],
            ['days_ago' => 0, 'score' => 82, 'skin_age' => 26.0],
        ];

        $metricsProgression = [
            // [hydration, pores, acne, pigmentation, wrinkles, redness, texture, sensitivity]
            [65, 58, 60, 55, 82, 62, 68, 50],
            [68, 62, 63, 58, 83, 65, 72, 53],
            [70, 65, 66, 60, 84, 68, 74, 55],
            [73, 67, 68, 62, 85, 70, 76, 57],
            [76, 69, 70, 64, 85, 72, 78, 58],
            [78, 70, 72, 65, 86, 74, 80, 60],
        ];

        foreach ($analysisData as $i => $data) {
            $analysis = SkinAnalysis::create([
                'user_id' => $user->id,
                'analysis_date' => now()->subDays($data['days_ago'])->toDateString(),
                'overall_score' => $data['score'],
                'estimated_skin_age' => $data['skin_age'],
                'skin_type_id' => $skinType?->id,
                'summary' => $data['score'] >= 80
                    ? 'Kulit Anda dalam kondisi sangat baik! Tipe kulit terdeteksi kombinasi. Terus pertahankan rutinitas skincare Anda.'
                    : 'Kulit Anda dalam kondisi baik dengan tipe kulit kombinasi. Ada beberapa area yang bisa ditingkatkan.',
            ]);

            $metricTypes = ['hydration', 'pores', 'acne', 'pigmentation', 'wrinkles', 'redness', 'texture', 'sensitivity'];
            foreach ($metricTypes as $j => $type) {
                $score = $metricsProgression[$i][$j];
                SkinAnalysisMetric::create([
                    'skin_analysis_id' => $analysis->id,
                    'metric_type' => $type,
                    'score' => $score,
                    'severity' => match(true) {
                        $score >= 80 => 'excellent',
                        $score >= 65 => 'good',
                        $score >= 45 => 'moderate',
                        $score >= 25 => 'poor',
                        default => 'critical',
                    },
                    'description' => $this->getMetricDescription($type, $score),
                ]);
            }

            // Create recommendation for latest analysis
            if ($i === count($analysisData) - 1) {
                $this->createRecommendation($user, $analysis);
            }
        }
    }

    private function getMetricDescription(string $type, int $score): string
    {
        $good = $score >= 65;
        return match ($type) {
            'hydration' => $good ? 'Kulit terhidrasi dengan baik.' : 'Kulit membutuhkan lebih banyak hidrasi.',
            'pores' => $good ? 'Pori-pori terlihat halus dan minimal.' : 'Pori-pori tampak membesar di beberapa area.',
            'acne' => $good ? 'Kulit bersih dengan sedikit tanda jerawat.' : 'Terdeteksi beberapa area dengan jerawat aktif.',
            'pigmentation' => $good ? 'Warna kulit merata dan cerah.' : 'Terdapat beberapa area hiperpigmentasi.',
            'wrinkles' => $good ? 'Garis halus minimal, kulit tampak muda.' : 'Terlihat beberapa garis halus.',
            'redness' => $good ? 'Kulit tenang tanpa kemerahan signifikan.' : 'Terdeteksi kemerahan di beberapa area.',
            'texture' => $good ? 'Tekstur kulit halus dan merata.' : 'Tekstur kulit tidak merata.',
            'sensitivity' => $good ? 'Kulit tidak menunjukkan tanda sensitivitas.' : 'Kulit cenderung sensitif dan reaktif.',
            default => '',
        };
    }

    private function createRecommendation(User $user, SkinAnalysis $analysis): void
    {
        $recommendation = Recommendation::create([
            'user_id' => $user->id,
            'skin_analysis_id' => $analysis->id,
            'title' => 'Rekomendasi Personal Untukmu',
            'description' => 'Rangkaian produk & rutinitas yang disesuaikan dengan kondisi kulitmu.',
        ]);

        $products = Product::with('category')->get();

        // Morning routine
        $morningProducts = ['Cleanser' => 1, 'Serum' => 2, 'Moisturizer' => 3, 'Sunscreen' => 4];
        foreach ($morningProducts as $cat => $order) {
            $product = $products->filter(fn($p) => $p->category->name === $cat)->first();
            if ($product) {
                RecommendationItem::create([
                    'recommendation_id' => $recommendation->id,
                    'product_id' => $product->id,
                    'routine_type' => 'morning',
                    'order_number' => $order,
                    'reason' => 'Direkomendasikan berdasarkan kondisi kulit Anda.',
                    'usage_instruction' => $product->usage_instruction,
                ]);
            }
        }

        // Night routine
        $nightProducts = ['Cleanser' => 1, 'Serum' => 2, 'Moisturizer' => 3];
        foreach ($nightProducts as $cat => $order) {
            $product = $products->filter(fn($p) => $p->category->name === $cat)->last();
            if ($product) {
                RecommendationItem::create([
                    'recommendation_id' => $recommendation->id,
                    'product_id' => $product->id,
                    'routine_type' => 'night',
                    'order_number' => $order,
                    'reason' => 'Direkomendasikan untuk rutinitas malam Anda.',
                    'usage_instruction' => $product->usage_instruction,
                ]);
            }
        }
    }

    private function createUserProducts(User $user): void
    {
        $products = Product::whereIn('slug', [
            'urskynd-foaming-face-wash',
            'urskynd-brightening-anti-aging-serum',
            'urskynd-daily-moisture-cream',
            'urskynd-uv-sunscreen-spf-50',
            'urskynd-night-recovery-cream',
        ])->get();

        foreach ($products as $product) {
            UserProduct::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'started_at' => now()->subMonths(3),
                'purchase_date' => now()->subMonths(3),
                'opened_date' => now()->subMonths(3),
                'expired_at' => now()->addMonths(9),
                'status' => 'active',
            ]);
        }
    }

    private function createRoutines(User $user): void
    {
        $userProducts = $user->userProducts()->with('product.category')->get();

        // Morning routine
        $morningRoutine = Routine::create([
            'user_id' => $user->id,
            'name' => 'Routine Pagi',
            'routine_type' => 'morning',
            'active' => true,
        ]);

        $morningOrder = ['Cleanser', 'Serum', 'Moisturizer', 'Sunscreen'];
        $order = 1;
        foreach ($morningOrder as $catName) {
            $up = $userProducts->first(fn($up) => $up->product->category->name === $catName);
            if ($up) {
                RoutineItem::create([
                    'routine_id' => $morningRoutine->id,
                    'user_product_id' => $up->id,
                    'order_number' => $order++,
                    'usage_instruction' => $up->product->usage_instruction,
                ]);
            }
        }

        // Night routine
        $nightRoutine = Routine::create([
            'user_id' => $user->id,
            'name' => 'Routine Malam',
            'routine_type' => 'night',
            'active' => true,
        ]);

        $nightOrder = ['Cleanser', 'Serum', 'Moisturizer'];
        $order = 1;
        foreach ($nightOrder as $catName) {
            $up = $userProducts->first(fn($up) => $up->product->category->name === $catName);
            if ($up) {
                RoutineItem::create([
                    'routine_id' => $nightRoutine->id,
                    'user_product_id' => $up->id,
                    'order_number' => $order++,
                    'usage_instruction' => $up->product->usage_instruction,
                ]);
            }
        }
    }

    private function createReminders(User $user): void
    {
        $morningRoutine = $user->routines()->where('routine_type', 'morning')->first();
        $nightRoutine = $user->routines()->where('routine_type', 'night')->first();

        if ($morningRoutine) {
            Reminder::create([
                'user_id' => $user->id,
                'routine_id' => $morningRoutine->id,
                'title' => 'Routine Pagi',
                'description' => 'Waktunya skincare routine pagi!',
                'reminder_time' => '07:00',
                'repeat_type' => 'daily',
                'active' => true,
            ]);
        }

        if ($nightRoutine) {
            Reminder::create([
                'user_id' => $user->id,
                'routine_id' => $nightRoutine->id,
                'title' => 'Routine Malam',
                'description' => 'Waktunya skincare routine malam!',
                'reminder_time' => '21:00',
                'repeat_type' => 'daily',
                'active' => true,
            ]);
        }

        // Sunscreen reapply reminder
        Reminder::create([
            'user_id' => $user->id,
            'title' => 'Reapply Sunscreen',
            'description' => 'Sudah 2 jam, saatnya reapply sunscreen!',
            'reminder_time' => '12:00',
            'repeat_type' => 'daily',
            'active' => true,
        ]);
    }

    private function createJournals(User $user): void
    {
        $conditions = ['good', 'good', 'moderate', 'good', 'good', 'moderate', 'good'];
        $moods = ['happy', 'neutral', 'happy', 'tired', 'happy', 'stressed', 'happy'];

        for ($i = 6; $i >= 0; $i--) {
            SkinJournal::create([
                'user_id' => $user->id,
                'journal_date' => now()->subDays($i)->toDateString(),
                'skin_condition' => $conditions[$i],
                'mood' => $moods[$i],
                'notes' => $i === 0
                    ? 'Kulit terasa lebih halus dan lembab hari ini. Sepertinya serum baru mulai bekerja!'
                    : 'Rutinitas skincare berjalan dengan baik.',
                'skin_score' => rand(75, 85),
            ]);
        }
    }

    private function createPointTransactions(User $user): void
    {
        $transactions = [
            ['points' => 20, 'description' => 'Achievement: First Skin Check', 'reference_type' => 'achievement'],
            ['points' => 10, 'description' => 'Skin Check completed', 'reference_type' => 'skin_analysis'],
            ['points' => 10, 'description' => 'Skin Check completed', 'reference_type' => 'skin_analysis'],
            ['points' => 5, 'description' => 'Daily journal entry', 'reference_type' => 'journal'],
            ['points' => 5, 'description' => 'Daily journal entry', 'reference_type' => 'journal'],
            ['points' => 10, 'description' => 'Skin Check completed', 'reference_type' => 'skin_analysis'],
            ['points' => 30, 'description' => 'Achievement: 7 Day Streak', 'reference_type' => 'achievement'],
            ['points' => 10, 'description' => 'Skin Check completed', 'reference_type' => 'skin_analysis'],
            ['points' => 50, 'description' => 'Achievement: Skin Explorer', 'reference_type' => 'achievement'],
        ];

        foreach ($transactions as $i => $tx) {
            PointTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'points' => $tx['points'],
                'description' => $tx['description'],
                'reference_type' => $tx['reference_type'],
                'created_at' => now()->subDays(count($transactions) - $i),
            ]);
        }
    }

    private function createAchievements(User $user): void
    {
        $achievementNames = ['First Skin Check', 'Skin Explorer', '7 Day Streak'];
        $achievements = \App\Models\Achievement::whereIn('name', $achievementNames)->get();

        foreach ($achievements as $achievement) {
            $user->achievements()->attach($achievement->id, [
                'achieved_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
