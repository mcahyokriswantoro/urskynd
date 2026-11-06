<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'courier',
        'service',
        'match_cities',
        'match_provinces',
        'cost_first_kg',
        'cost_next_kg',
        'etd',
        'free_shipping_min',
        'sort_order',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost_first_kg' => 'integer',
            'cost_next_kg' => 'integer',
            'free_shipping_min' => 'integer',
            'sort_order' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public static function splitList(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($v) => mb_strtolower(trim($v)))
            ->filter()
            ->values()
            ->all();
    }

    public function getCityListAttribute(): array
    {
        return self::splitList($this->match_cities);
    }

    public function getProvinceListAttribute(): array
    {
        return self::splitList($this->match_provinces);
    }
}

