<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'day_of_week',
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    public function getDayNameAttribute(): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$this->day_of_week] ?? '';
    }
}
