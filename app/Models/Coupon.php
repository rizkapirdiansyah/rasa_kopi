<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(float $orderSubtotal = 0): array
    {
        if (! $this->is_active) {
            return ['valid' => false, 'message' => 'Kupon diskon tidak aktif.'];
        }

        $today = Carbon::today();
        if ($this->start_date && $today->lt($this->start_date)) {
            return ['valid' => false, 'message' => 'Kupon diskon belum berlaku.'];
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            return ['valid' => false, 'message' => 'Kupon diskon sudah kedaluwarsa.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Batas penggunaan kupon telah habis.'];
        }

        if ($orderSubtotal < (float) $this->min_order_amount) {
            $minFormatted = 'Rp ' . number_format((float) $this->min_order_amount, 0, ',', '.');
            return ['valid' => false, 'message' => "Minimal belanja untuk kupon ini adalah {$minFormatted}."];
        }

        return ['valid' => true, 'message' => 'Kupon berhasil diterapkan!'];
    }

    public function calculateDiscount(float $orderSubtotal): float
    {
        if ($this->type === 'percent') {
            $discount = ($orderSubtotal * (float) $this->value) / 100;
            if ($this->max_discount !== null && $discount > (float) $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
            return min($discount, $orderSubtotal);
        }

        return min((float) $this->value, $orderSubtotal);
    }
}
