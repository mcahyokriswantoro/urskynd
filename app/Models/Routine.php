<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'routine_type',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RoutineItem::class)->orderBy('order_number');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function isMorning(): bool
    {
        return $this->routine_type === 'morning';
    }

    public function isNight(): bool
    {
        return $this->routine_type === 'night';
    }
}
