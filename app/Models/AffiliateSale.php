<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateSale extends Model
{
    protected $fillable = [
        'affiliate_id',
        'order_code',
        'customer_name',
        'customer_phone',
        'package_name',
        'sale_amount',
        'commission_rate',
        'commission_amount',
        'payment_method',
        'shipping_address',
        'notes',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'sale_amount' => 'integer',
            'commission_rate' => 'float',
            'commission_amount' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function getFormattedSaleAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->sale_amount, 0, ',', '.');
    }

    public function getFormattedCommissionAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->commission_amount, 0, ',', '.');
    }
}

