<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'analysis_date',
        'overall_score',
        'estimated_skin_age',
        'skin_type_id',
        'summary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'analysis_date' => 'date',
            'overall_score' => 'integer',
            'estimated_skin_age' => 'decimal:1',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }

    public function metrics()
    {
        return $this->hasMany(SkinAnalysisMetric::class);
    }

    public function recommendation()
    {
        return $this->hasOne(Recommendation::class)->latestOfMany();
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getScoreLabelAttribute(): string
    {
        return match (true) {
            $this->overall_score >= 80 => 'Excellent',
            $this->overall_score >= 60 => 'Good',
            $this->overall_score >= 40 => 'Moderate',
            $this->overall_score >= 20 => 'Poor',
            default => 'Critical',
        };
    }
}
