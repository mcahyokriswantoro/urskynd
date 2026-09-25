<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, UserProfile::class, 'skin_type_id', 'id', 'id', 'user_id');
    }

    public function skinAnalyses()
    {
        return $this->hasMany(SkinAnalysis::class);
    }
}
