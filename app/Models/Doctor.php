<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'experience',
        'hospital',
        'rating',
        'reviews_count',
        'specialties',
        'available',
        'price',
        'avatar',
        'whatsapp_number',
    ];

    protected $casts = [
        'available' => 'boolean',
        'reviews_count' => 'integer',
    ];

    public function getSpecialtiesAttribute($value): array
    {
        if (empty($value)) {
            return [];
        }
        if (is_array($value)) {
            return $value;
        }
        return array_map('trim', explode(',', $value));
    }

    public function setSpecialtiesAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['specialties'] = implode(', ', $value);
        } else {
            $this->attributes['specialties'] = $value;
        }
    }

    public function bookings()
    {
        return $this->hasMany(ConsultationBooking::class);
    }
}
