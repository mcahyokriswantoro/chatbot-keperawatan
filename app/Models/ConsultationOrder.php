<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ConsultationOrder extends Model
{
    protected $fillable = [
        'user_id',
        'provider_key',
        'reference_code',
        'amount',
        'discount_amount',
        'total_paid',
        'consultation_voucher_id',
        'voucher_code',
        'status',
        'payment_method',
        'dana_phone',
        'payment_proof',
        'paid_at',
        'verified_at',
        'verified_by',
        'admin_note',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'discount_amount' => 'integer',
            'total_paid' => 'integer',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(ConsultationVoucher::class, 'consultation_voucher_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConsultationMessage::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isActive(): bool
    {
        return $this->status === 'paid'
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    public function paymentProofUrl(): ?string
    {
        if (! $this->payment_proof || ! Storage::disk('public')->exists($this->payment_proof)) {
            return null;
        }

        return Storage::disk('public')->url($this->payment_proof);
    }

    public function paymentProofIsImage(): bool
    {
        return $this->payment_proof !== null
            && (bool) preg_match('/\.(jpe?g|png|webp)$/i', $this->payment_proof);
    }
}
