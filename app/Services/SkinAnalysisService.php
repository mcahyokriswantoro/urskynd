<?php

namespace App\Services;

use App\Contracts\SkinAnalysisProviderInterface;
use App\Models\SkinAnalysis;
use App\Models\SkinAnalysisMetric;
use App\Models\SkinType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SkinAnalysisService
{
    public function __construct(
        private SkinAnalysisProviderInterface $provider
    ) {}

    /**
     * Process a skin analysis for the given user.
     */
    public function analyze(User $user, string $imagePath): SkinAnalysis
    {
        // Gather user context for the analysis
        $userContext = $this->buildUserContext($user);

        // Get analysis from the provider
        $results = $this->provider->analyze($imagePath, $userContext);

        // Determine skin type
        $skinType = SkinType::where('name', 'like', '%' . $results['skin_type'] . '%')->first();

        return DB::transaction(function () use ($user, $imagePath, $results, $skinType) {
            // Create the analysis record
            $analysis = SkinAnalysis::create([
                'user_id' => $user->id,
                'image_path' => $imagePath,
                'analysis_date' => now()->toDateString(),
                'overall_score' => $results['overall_score'],
                'estimated_skin_age' => $results['estimated_skin_age'],
                'skin_type_id' => $skinType?->id,
                'summary' => $results['summary'],
            ]);

            // Create metric records
            foreach ($results['metrics'] as $type => $metric) {
                SkinAnalysisMetric::create([
                    'skin_analysis_id' => $analysis->id,
                    'metric_type' => $type,
                    'score' => $metric['score'],
                    'severity' => $metric['severity'],
                    'description' => $metric['description'],
                ]);
            }

            // Award points for skin check
            app(RewardService::class)->awardPoints(
                $user,
                10,
                'Skin Check completed',
                'skin_analysis',
                $analysis->id
            );

            return $analysis->load('metrics', 'skinType');
        });
    }

    /**
     * Build user context array for the analysis provider.
     */
    private function buildUserContext(User $user): array
    {
        $context = [];

        if ($user->birth_date) {
            $context['age'] = $user->birth_date->age;
        }

        $profile = $user->profile;
        if ($profile && $profile->skinType) {
            $context['skin_type'] = strtolower($profile->skinType->name);
        }

        $concerns = $user->skinConcerns->pluck('slug')->toArray();
        if (!empty($concerns)) {
            $context['concerns'] = $concerns;
        }

        return $context;
    }

    /**
     * Get analysis history for tracker charts.
     */
    public function getProgressData(User $user, string $period = 'monthly', int $limit = 12): array
    {
        $query = $user->skinAnalyses()->with('metrics');

        $analyses = match ($period) {
            'weekly' => $query->where('analysis_date', '>=', now()->subWeeks($limit))->orderBy('analysis_date', 'asc')->get(),
            'yearly' => $query->where('analysis_date', '>=', now()->subYears($limit))->orderBy('analysis_date', 'asc')->get(),
            default => $query->where('analysis_date', '>=', now()->subMonths($limit))->orderBy('analysis_date', 'asc')->get(),
        };

        return [
            'dates' => $analyses->pluck('analysis_date')->map(fn($d) => $d->format('d M'))->toArray(),
            'scores' => $analyses->pluck('overall_score')->toArray(),
            'skin_ages' => $analyses->pluck('estimated_skin_age')->toArray(),
            'metrics' => $this->aggregateMetrics($analyses),
        ];
    }

    /**
     * Aggregate metrics across analyses for chart data.
     */
    private function aggregateMetrics($analyses): array
    {
        $metricTypes = ['hydration', 'pores', 'acne', 'pigmentation', 'wrinkles', 'redness', 'texture', 'sensitivity'];
        $result = [];

        foreach ($metricTypes as $type) {
            $result[$type] = $analyses->map(function ($analysis) use ($type) {
                $metric = $analysis->metrics->firstWhere('metric_type', $type);
                return $metric ? $metric->score : null;
            })->filter()->values()->toArray();
        }

        return $result;
    }
}
