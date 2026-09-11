<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skin_analysis_id',
        'title',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skinAnalysis()
    {
        return $this->belongsTo(SkinAnalysis::class);
    }

    public function items()
    {
        return $this->hasMany(RecommendationItem::class)->orderBy('order_number');
    }

    public function morningItems()
    {
        return $this->items()->where('routine_type', 'morning');
    }

    public function nightItems()
    {
        return $this->items()->where('routine_type', 'night');
    }
}
