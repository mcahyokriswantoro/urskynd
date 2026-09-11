<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recommendation_id',
        'product_id',
        'routine_type',
        'order_number',
        'reason',
        'usage_instruction',
    ];

    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
