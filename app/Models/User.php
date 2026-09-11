<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'birth_date',
        'gender',
        'points',
        'status',
        'onboarding_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'onboarding_completed' => 'boolean',
            'points' => 'integer',
        ];
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=9A6A52&color=fff';
    }

    // ── Relationships ───────────────────────────────────────

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function skinConcerns()
    {
        return $this->belongsToMany(SkinConcern::class, 'user_skin_concerns')
            ->withPivot('severity')
            ->withTimestamps();
    }

    public function skinAnalyses()
    {
        return $this->hasMany(SkinAnalysis::class)->orderByDesc('analysis_date');
    }

    public function latestAnalysis()
    {
        return $this->hasOne(SkinAnalysis::class)->latestOfMany('analysis_date');
    }

    public function skinJournals()
    {
        return $this->hasMany(SkinJournal::class)->orderByDesc('journal_date');
    }

    public function userProducts()
    {
        return $this->hasMany(UserProduct::class);
    }

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('achieved_at')
            ->withTimestamps();
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }
}
