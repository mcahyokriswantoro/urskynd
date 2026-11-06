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
        'is_affiliate',
        'affiliate_code',
        'affiliate_status',
        'commission_rate',
        'commission_balance',
        'total_commission_earned',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'avatar',
        'phone',
        'address',
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
            'is_affiliate' => 'boolean',
            'commission_rate' => 'float',
            'commission_balance' => 'integer',
            'total_commission_earned' => 'integer',
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

    public function isAffiliate(): bool
    {
        return (bool) $this->is_affiliate;
    }

    public static function generateUniqueAffiliateCode(string $name, ?string $preferred = null): string
    {
        $base = $preferred
            ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $preferred))
            : 'URS' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 5)) . rand(10, 99);

        if (strlen($base) < 4) {
            $base = 'URSMITRA' . rand(100, 999);
        }

        $code = substr($base, 0, 20);
        $counter = 1;
        while (static::where('affiliate_code', $code)->exists()) {
            $code = substr($base, 0, 16) . $counter . rand(1, 9);
            $counter++;
        }

        return $code;
    }

    public function getFormattedCommissionBalanceAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->commission_balance, 0, ',', '.');
    }

    public function getFormattedTotalCommissionAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->total_commission_earned, 0, ',', '.');
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

    public function products()
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

    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class, 'affiliate_id')->latest();
    }

    public function affiliateClicks()
    {
        return $this->hasMany(AffiliateClick::class, 'affiliate_id')->latest();
    }

    public function affiliateWithdrawals()
    {
        return $this->hasMany(AffiliateWithdrawal::class, 'affiliate_id')->latest();
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }
}
