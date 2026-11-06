<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'brand',
        'name',
        'slug',
        'description',
        'image',
        'size',
        'weight',
        'stock',
        'price',
        'commission_rate',
        'badge',
        'usage_instruction',
        'status',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price ?? 129000, 0, ',', '.');
    }

    public function getCommissionAmountAttribute(): int
    {
        $rate = (float) ($this->commission_rate ?? 10);
        return (int) round(($this->price ?? 0) * ($rate / 100));
    }

    public function getFormattedCommissionAttribute(): string
    {
        return 'Rp ' . number_format($this->commission_amount, 0, ',', '.');
    }

    public function affiliateClicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->orWhere('slug', $value)->firstOrFail();
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function userProducts()
    {
        return $this->hasMany(UserProduct::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('storage/' . $this->image);
    }

    public function getBadgeAttribute(?string $value = null): string
    {
        if (!empty($value)) {
            return $value;
        }

        return match ($this->slug) {
            'serum' => 'Terlaris',
            'sunscreen' => 'Best Seller',
            'cleanser' => 'Gentle',
            default => 'BPOM Certified',
        };
    }

    public function getRatingAttribute(): string
    {
        return match ($this->slug) {
            'serum' => '4.9 (987 ulasan)',
            'sunscreen' => '4.9 (1.234 ulasan)',
            'cleanser' => '4.8 (754 ulasan)',
            default => '4.9 (500+ ulasan)',
        };
    }

    public function getFullNameAttribute(): string
    {
        return $this->brand . ' ' . $this->name;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
