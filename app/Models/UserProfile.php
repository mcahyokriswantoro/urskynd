<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skin_type_id',
        'skin_goal',
        'allergies',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }
}
