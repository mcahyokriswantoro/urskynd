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
        'usage_instruction',
        'status',
    ];

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
        return $this->image ? asset('storage/' . $this->image) : null;
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
