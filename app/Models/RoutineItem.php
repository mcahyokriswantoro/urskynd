<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'routine_id',
        'user_product_id',
        'order_number',
        'usage_instruction',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function userProduct()
    {
        return $this->belongsTo(UserProduct::class);
    }
}
