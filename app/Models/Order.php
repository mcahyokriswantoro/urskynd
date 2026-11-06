<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'payment_gateway',
        'payment_method',
        'payment_reference',
        'snap_token',
        'snap_redirect_url',
        'payment_proof',
        'payment_expires_at',
        'paid_at',
        'recipient_name',
        'recipient_phone',
        'province',
        'city',
        'district',
        'postal_code',
        'address',
        'shipping_zone_id',
        'shipping_zone_name',
        'courier',
        'courier_service',
        'shipping_etd',
        'total_weight',
        'subtotal',
        'shipping_cost',
        'total',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
        'cancel_reason',
        'notes',
        'admin_notes',
        'affiliate_id',
    ];

    protected function casts(): array
    {
        return [
            'total_weight' => 'integer',
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'total' => 'integer',
            'payment_expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->orWhere('order_number', $value)->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT => 'Belum Bayar',
            self::STATUS_PAID => 'Pembayaran Diterima',
            self::STATUS_PROCESSING => 'Sedang Dikemas',
            self::STATUS_SHIPPED => 'Barang Dikirim (J&T)',
            self::STATUS_DELIVERED => 'Paket Telah Sampai',
            self::STATUS_COMPLETED => 'Pesanan Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return $this->shipping_cost === 0 ? 'GRATIS' : 'Rp ' . number_format($this->shipping_cost, 0, ',', '.');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    public function logHistory(string $status, string $title, ?string $note = null, ?int $userId = null): void
    {
        $this->statusHistories()->create([
            'status' => $status,
            'title' => $title,
            'note' => $note,
            'created_by' => $userId,
        ]);
    }

    /**
     * Tandai pesanan sudah lunas (baik otomatis dari Payment Gateway / Midtrans maupun verifikasi Admin)
     * dan otomatis setujui komisi afiliasi terkait.
     */
    public function markAsPaid(string $method = 'Payment Gateway Otomatis', ?string $reference = null, ?int $actorId = null): void
    {
        DB::transaction(function () use ($method, $reference, $actorId) {
            $wasUnpaid = $this->payment_status !== 'paid';

            $this->update([
                'status' => in_array($this->status, [self::STATUS_PENDING_PAYMENT, self::STATUS_CANCELLED], true)
                    ? self::STATUS_PAID
                    : $this->status,
                'payment_status' => 'paid',
                'payment_method' => $method ?: $this->payment_method,
                'payment_reference' => $reference ?: $this->payment_reference,
                'paid_at' => $this->paid_at ?: now(),
            ]);

            if ($wasUnpaid) {
                $this->logHistory(
                    self::STATUS_PAID,
                    'Pembayaran Diterima (' . ($method ?: 'Otomatis') . ')',
                    'Pembayaran sebesar ' . $this->formatted_total . ' telah terkonfirmasi lunas. Pesanan diteruskan ke gudang Lamongan untuk pengemasan.',
                    $actorId
                );

                // Otomatis setujui komisi mitra afiliator begitu pembayaran lunas!
                $sales = AffiliateSale::where('order_id', $this->id)
                    ->orWhere('order_code', $this->order_number)
                    ->get();

                foreach ($sales as $sale) {
                    if ($sale->status !== 'approved') {
                        $affiliate = User::lockForUpdate()->find($sale->affiliate_id);
                        if ($affiliate) {
                            $commission = (int) $sale->commission_amount;
                            $affiliate->update([
                                'commission_balance' => $affiliate->commission_balance + $commission,
                                'total_commission_earned' => $affiliate->total_commission_earned + $commission,
                            ]);
                        }
                        $sale->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);
                    }
                }
            }
        });
    }
}

