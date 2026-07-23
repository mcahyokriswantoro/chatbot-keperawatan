<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HomecareBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'homecare_package_id',
        'reference_code',
        'patient_name',
        'patient_phone',
        'booking_date',
        'address',
        'latitude',
        'longitude',
        'distance_km',
        'transport_fee',
        'sender_identity',
        'payment_proof',
        'status', // pending, paid, completed, rejected
        'admin_note',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'distance_km' => 'float',
        'transport_fee' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(HomecarePackage::class, 'homecare_package_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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

    public function totalPrice(): int
    {
        return $this->package->price + ($this->transport_fee ?? 0);
    }
}
