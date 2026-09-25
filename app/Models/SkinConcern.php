<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinConcern extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skin_concerns')
            ->withPivot('severity')
            ->withTimestamps();
    }

    public function journals()
    {
        return $this->belongsToMany(SkinJournal::class, 'journal_concerns', 'skin_concern_id', 'skin_journal_id')
            ->withPivot('severity')
            ->withTimestamps();
    }
}
