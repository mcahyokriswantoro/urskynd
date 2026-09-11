<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalConcern extends Model
{
    use HasFactory;

    protected $fillable = [
        'skin_journal_id',
        'skin_concern_id',
        'severity',
    ];

    public function journal()
    {
        return $this->belongsTo(SkinJournal::class, 'skin_journal_id');
    }

    public function skinConcern()
    {
        return $this->belongsTo(SkinConcern::class);
    }
}
