<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MedicineOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_code',
        'total_amount',
        'address',
        'closest_pharmacy',
        'latitude',
        'longitude',
        'distance_km',
        'shipping_fee',
        'sender_identity',
        'payment_proof',
        'status', // pending, paid, delivered, rejected
        'admin_note',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'shipping_fee' => 'integer',
        'total_amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MedicineOrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function paymentProofUrl(): ?string
    {
        if ($this->payment_proof) {
            return Storage::disk('public')->url($this->payment_proof);
        }

        return null;
    }

    public function paymentProofIsImage(): bool
    {
        if (! $this->payment_proof) {
            return false;
        }

        $ext = strtolower(pathinfo($this->payment_proof, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}
