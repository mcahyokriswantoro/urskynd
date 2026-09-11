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

    public function skinAnalyses()
    {
        return $this->hasMany(SkinAnalysis::class);
    }
}
