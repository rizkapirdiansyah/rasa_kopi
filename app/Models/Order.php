<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_type',
        'shipping_address',
        'shipping_city',
        'shipping_cost',
        'pickup_time',
        'coupon_id',
        'coupon_code',
        'discount_amount',
        'subtotal',
        'total_amount',
        'payment_method_id',
        'payment_method_name',
        'status',
        'customer_notes',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }

    public function getFormattedShippingCostAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->shipping_cost, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->discount_amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dibayar' => 'Dibayar (Menunggu Konfirmasi)',
            'diproses' => 'Diproses Barista',
            'siap_diambil' => 'Siap Diambil di Toko',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-secondary',
            'menunggu_pembayaran' => 'bg-warning text-dark',
            'dibayar' => 'bg-info text-dark',
            'diproses' => 'bg-primary',
            'siap_diambil' => 'bg-purple text-white',
            'dikirim' => 'bg-info text-white',
            'selesai' => 'bg-success',
            'dibatalkan' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'menunggu_pembayaran'], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'selesai';
    }
}
