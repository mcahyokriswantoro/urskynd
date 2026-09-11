<?php

namespace App\Contracts;

interface SkinAnalysisProviderInterface
{
    /**
     * Analyze a skin image and return analysis results.
     *
     * @param string $imagePath Path to the uploaded skin image
     * @param array $userContext Additional context about the user (skin_type, age, concerns)
     * @return array Analysis results with structure:
     *   - overall_score: int (0-100)
     *   - estimated_skin_age: float
     *   - skin_type: string
     *   - summary: string
     *   - metrics: array of [metric_type => [score, severity, description]]
     */
    public function analyze(string $imagePath, array $userContext = []): array;

    /**
     * Check if the provider is available/configured.
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name.
     */
    public function getName(): string;
}
