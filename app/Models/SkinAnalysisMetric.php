<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinAnalysisMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'skin_analysis_id',
        'metric_type',
        'score',
        'severity',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function skinAnalysis()
    {
        return $this->belongsTo(SkinAnalysis::class);
    }

    public function getMetricLabelAttribute(): string
    {
        return match ($this->metric_type) {
            'hydration' => 'Kelembaban',
            'pores' => 'Pori',
            'acne' => 'Elastisitas',
            'pigmentation' => 'Flek',
            'wrinkles' => 'Kerutan',
            'redness' => 'Kemerahan',
            'texture' => 'Tekstur',
            'sensitivity' => 'Sensitivitas',
            default => ucfirst($this->metric_type),
        };
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'excellent' => 'Sangat Baik',
            'good' => 'Baik',
            'moderate' => 'Cukup',
            'poor' => 'Kurang',
            'critical' => 'Buruk',
            default => $this->severity,
        };
    }
}
