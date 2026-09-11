<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'journal_date',
        'skin_condition',
        'mood',
        'notes',
        'photo',
        'skin_score',
    ];

    protected function casts(): array
    {
        return [
            'journal_date' => 'date',
            'skin_score' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function concerns()
    {
        return $this->hasMany(JournalConcern::class);
    }

    public function skinConcerns()
    {
        return $this->belongsToMany(SkinConcern::class, 'journal_concerns', 'skin_journal_id', 'skin_concern_id')
            ->withPivot('severity')
            ->withTimestamps();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->skin_condition) {
            'good' => 'Baik',
            'moderate' => 'Cukup',
            'bad' => 'Buruk',
            default => $this->skin_condition ?? '-',
        };
    }

    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood) {
            'happy' => '😊 Senang',
            'neutral' => '😐 Biasa',
            'stressed' => '😰 Stres',
            'tired' => '😴 Lelah',
            default => $this->mood ?? '-',
        };
    }
}
