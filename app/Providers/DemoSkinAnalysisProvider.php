<?php

namespace App\Providers;

use App\Contracts\SkinAnalysisProviderInterface;

class DemoSkinAnalysisProvider implements SkinAnalysisProviderInterface
{
    /**
     * Generate demo/dummy skin analysis results.
     */
    public function analyze(string $imagePath, array $userContext = []): array
    {
        // Generate realistic but randomized demo results
        $baseScore = rand(65, 90);
        $userAge = $userContext['age'] ?? 25;

        // Skin age varies slightly from actual age
        $skinAgeOffset = rand(-30, 20) / 10; // -3.0 to +2.0
        $estimatedSkinAge = max(18, $userAge + $skinAgeOffset);

        $metrics = $this->generateMetrics($baseScore);
        $overallScore = (int) round(collect($metrics)->avg('score'));

        $skinTypes = ['normal', 'dry', 'oily', 'combination', 'sensitive'];
        $detectedSkinType = $userContext['skin_type'] ?? $skinTypes[array_rand($skinTypes)];

        return [
            'overall_score' => $overallScore,
            'estimated_skin_age' => round($estimatedSkinAge, 1),
            'skin_type' => $detectedSkinType,
            'summary' => $this->generateSummary($overallScore, $detectedSkinType),
            'metrics' => $metrics,
        ];
    }

    /**
     * Generate individual metric scores.
     */
    private function generateMetrics(int $baseScore): array
    {
        $metricTypes = [
            'hydration' => ['min' => -15, 'max' => 10, 'desc_good' => 'Kulit terhidrasi dengan baik.', 'desc_bad' => 'Kulit membutuhkan lebih banyak hidrasi.'],
            'pores' => ['min' => -20, 'max' => 5, 'desc_good' => 'Pori-pori terlihat halus dan minimal.', 'desc_bad' => 'Pori-pori tampak membesar di beberapa area.'],
            'acne' => ['min' => -18, 'max' => 8, 'desc_good' => 'Kulit bersih dengan sedikit tanda jerawat.', 'desc_bad' => 'Terdeteksi beberapa area dengan jerawat aktif.'],
            'pigmentation' => ['min' => -25, 'max' => 5, 'desc_good' => 'Warna kulit merata dan cerah.', 'desc_bad' => 'Terdapat beberapa area hiperpigmentasi.'],
            'wrinkles' => ['min' => -10, 'max' => 15, 'desc_good' => 'Garis halus minimal, kulit tampak muda.', 'desc_bad' => 'Terlihat beberapa garis halus di area mata dan dahi.'],
            'redness' => ['min' => -20, 'max' => 10, 'desc_good' => 'Kulit tenang tanpa kemerahan signifikan.', 'desc_bad' => 'Terdeteksi kemerahan di beberapa area wajah.'],
            'texture' => ['min' => -12, 'max' => 12, 'desc_good' => 'Tekstur kulit halus dan merata.', 'desc_bad' => 'Tekstur kulit tidak merata di beberapa area.'],
            'sensitivity' => ['min' => -25, 'max' => 5, 'desc_good' => 'Kulit tidak menunjukkan tanda sensitivitas.', 'desc_bad' => 'Kulit cenderung sensitif dan reaktif.'],
        ];

        $results = [];
        foreach ($metricTypes as $type => $config) {
            $variation = rand($config['min'], $config['max']);
            $score = max(0, min(100, $baseScore + $variation));

            $severity = match (true) {
                $score >= 80 => 'excellent',
                $score >= 65 => 'good',
                $score >= 45 => 'moderate',
                $score >= 25 => 'poor',
                default => 'critical',
            };

            $description = $score >= 60 ? $config['desc_good'] : $config['desc_bad'];

            $results[$type] = [
                'score' => $score,
                'severity' => $severity,
                'description' => $description,
            ];
        }

        return $results;
    }

    /**
     * Generate a summary based on the overall score.
     */
    private function generateSummary(int $score, string $skinType): string
    {
        $typeLabels = [
            'normal' => 'normal',
            'dry' => 'kering',
            'oily' => 'berminyak',
            'combination' => 'kombinasi',
            'sensitive' => 'sensitif',
        ];

        $typeLabel = $typeLabels[$skinType] ?? $skinType;

        if ($score >= 80) {
            return "Kulit Anda dalam kondisi sangat baik! Tipe kulit terdeteksi {$typeLabel}. Terus pertahankan rutinitas skincare Anda saat ini dan pastikan hidrasi serta perlindungan UV tetap terjaga.";
        } elseif ($score >= 60) {
            return "Kulit Anda dalam kondisi baik dengan tipe kulit {$typeLabel}. Ada beberapa area yang bisa ditingkatkan. Fokus pada hidrasi dan perlindungan dari sinar matahari untuk hasil optimal.";
        } else {
            return "Kulit Anda memerlukan perhatian lebih. Tipe kulit terdeteksi {$typeLabel}. Disarankan untuk menggunakan produk yang sesuai dan konsisten dalam rutinitas skincare harian.";
        }
    }

    public function isAvailable(): bool
    {
        return true; // Demo provider is always available
    }

    public function getName(): string
    {
        return 'Demo Skin Analysis Provider';
    }
}
