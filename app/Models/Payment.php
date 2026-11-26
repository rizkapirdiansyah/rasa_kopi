<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'proof_image',
        'sender_bank',
        'sender_name',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProofImageUrlAttribute(): ?string
    {
        if ($this->proof_image && Storage::disk('public')->exists($this->proof_image)) {
            return asset('storage/' . $this->proof_image);
        }

        return null;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi (Lunas)',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }
}
