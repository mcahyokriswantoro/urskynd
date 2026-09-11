<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'started_at',
        'expired_at',
        'purchase_date',
        'opened_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'expired_at' => 'date',
            'purchase_date' => 'date',
            'opened_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function routineItems()
    {
        return $this->hasMany(RoutineItem::class);
    }

    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }
}
