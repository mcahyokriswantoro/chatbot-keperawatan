<?php

namespace App\Services;

use App\Models\ConsultationOrder;
use App\Models\ConsultationProvider;
use App\Models\ConsultationVoucher;
use App\Models\User;
use App\Services\ConsultationWhatsAppNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConsultationAccessService
{
    public function __construct(
        private ConsultationWhatsAppNotifier $notifier,
        private OrderWhatsAppNotifier $orderNotifier,
    ) {}

    public function priceFor(string $providerKey): int
    {
        if (ConsultationProvider::tableReady()) {
            $model = ConsultationProvider::query()->where('key', $providerKey)->first();

            if ($model?->price) {
                return (int) $model->price;
            }

            if ($model?->category_key) {
                return (int) config("consultation.pricing.{$model->category_key}", config('consultation.pricing.default', 25000));
            }
        }

        return (int) config("consultation.pricing.{$providerKey}", config('consultation.pricing.default', 25000));
    }

    public function sessionHours(): int
    {
        return max(1, (int) config('consultation.session_hours', 24));
    }

    public function hasAccess(User $user, string $providerKey): bool
    {
        return ConsultationOrder::query()
            ->where('user_id', $user->id)
            ->where('provider_key', $providerKey)
            ->where('status', 'paid')
            ->where('expires_at', '>', now())
            ->where(function ($query) {
                $query->where('payment_method', 'voucher')
                    ->orWhereNotNull('verified_at');
            })
            ->exists();
    }

    public function activeOrder(User $user, string $providerKey): ?ConsultationOrder
    {
        return ConsultationOrder::query()
            ->where('user_id', $user->id)
            ->where('provider_key', $providerKey)
            ->where('status', 'paid')
            ->where('expires_at', '>', now())
            ->latest('paid_at')
            ->first();
    }

    public function pendingOrder(User $user, string $providerKey): ?ConsultationOrder
    {
        return ConsultationOrder::query()
            ->where('user_id', $user->id)
            ->where('provider_key', $providerKey)
            ->where('status', 'pending')
            ->latest('created_at')
            ->first();
    }

    public function latestOrder(User $user, string $providerKey): ?ConsultationOrder
    {
        return ConsultationOrder::query()
            ->where('user_id', $user->id)
            ->where('provider_key', $providerKey)
            ->latest('created_at')
            ->first();
    }

    public function submitDanaPayment(User $user, string $providerKey, string $danaPhone, string $referenceCode, string $paymentProof): ConsultationOrder
    {
        $pending = $this->pendingOrder($user, $providerKey);

        if ($pending) {
            if ($pending->payment_proof && $pending->payment_proof !== $paymentProof) {
                Storage::disk('public')->delete($pending->payment_proof);
            }

            $pending->update([
                'dana_phone' => $danaPhone,
                'reference_code' => $referenceCode,
                'payment_proof' => $paymentProof,
            ]);

            return $pending->fresh();
        }

        $amount = $this->priceFor($providerKey);

        return ConsultationOrder::create([
            'user_id' => $user->id,
            'provider_key' => $providerKey,
            'reference_code' => $referenceCode,
            'amount' => $amount,
            'discount_amount' => 0,
            'total_paid' => $amount,
            'status' => 'pending',
            'payment_method' => 'dana',
            'dana_phone' => $danaPhone,
            'payment_proof' => $paymentProof,
        ]);
    }

    public function approveOrder(ConsultationOrder $order, User $admin): ConsultationOrder
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'Pesanan ini sudah diproses.',
            ]);
        }

        $now = now();

        $order->update([
            'status' => 'paid',
            'paid_at' => $now,
            'verified_at' => $now,
            'verified_by' => $admin->id,
            'expires_at' => $now->copy()->addHours($this->sessionHours()),
        ]);

        $this->notifier->notifyOrderApproved($order);

        // Notify the patient too
        $patientPhone = $order->user?->phone ?? '';
        $patientName  = $order->user?->name ?? 'Pasien';
        if ($patientPhone !== '') {
            rescue(fn () => $this->orderNotifier->notifyPatientConsultationApproved(
                $patientPhone,
                $patientName,
                $order,
            ));
        }

        return $order->fresh();
    }

    public function rejectOrder(ConsultationOrder $order, User $admin, ?string $note = null): ConsultationOrder
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'order' => 'Pesanan ini sudah diproses.',
            ]);
        }

        $order->update([
            'status'      => 'rejected',
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'admin_note'  => $note,
        ]);

        // Notify the patient about the rejection
        $patientPhone = $order->user?->phone ?? '';
        $patientName  = $order->user?->name ?? 'Pasien';
        if ($patientPhone !== '') {
            rescue(fn () => $this->orderNotifier->notifyPatientConsultationRejected(
                $patientPhone,
                $patientName,
                $note,
            ));
        }

        return $order->fresh();
    }

    /**
     * @return array{voucher: ConsultationVoucher, discount: int, total: int}
     */
    public function validateVoucher(string $code, string $providerKey, User $user): array
    {
        $voucher = ConsultationVoucher::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->first();

        if (! $voucher || ! $voucher->isValidFor($providerKey)) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Voucher tidak valid, kadaluarsa, atau sudah habis.',
            ]);
        }

        $alreadyUsed = ConsultationOrder::query()
            ->where('user_id', $user->id)
            ->where('consultation_voucher_id', $voucher->id)
            ->whereIn('status', ['paid', 'pending'])
            ->exists();

        if ($alreadyUsed) {
            throw ValidationException::withMessages([
                'voucher_code' => 'Anda sudah pernah menggunakan voucher ini.',
            ]);
        }

        $amount = $this->priceFor($providerKey);
        $discount = min($amount, (int) round($amount * $voucher->discount_percent / 100));

        return [
            'voucher' => $voucher,
            'discount' => $discount,
            'total' => max(0, $amount - $discount),
        ];
    }

    public function redeemVoucher(User $user, string $providerKey, string $code): ConsultationOrder
    {
        return DB::transaction(function () use ($user, $providerKey, $code) {
            $validated = $this->validateVoucher($code, $providerKey, $user);
            $voucher = $validated['voucher'];
            $amount = $this->priceFor($providerKey);

            $this->pendingOrder($user, $providerKey)?->delete();

            $voucher->increment('uses_count');

            if ($validated['total'] === 0) {
                return $this->createPaidOrder(
                    $user,
                    $providerKey,
                    $amount,
                    $validated['discount'],
                    0,
                    $voucher,
                    'voucher'
                );
            }

            return ConsultationOrder::create([
                'user_id' => $user->id,
                'provider_key' => $providerKey,
                'reference_code' => 'CK-VCH-'.strtoupper($providerKey).'-'.now()->format('ymdHis'),
                'amount' => $amount,
                'discount_amount' => $validated['discount'],
                'total_paid' => $validated['total'],
                'consultation_voucher_id' => $voucher->id,
                'voucher_code' => $voucher->code,
                'status' => 'pending',
                'payment_method' => 'dana',
            ]);
        });
    }

    public function dueAmountForPayment(User $user, string $providerKey): int
    {
        $pending = $this->pendingOrder($user, $providerKey);

        if ($pending) {
            return max(0, (int) $pending->total_paid);
        }

        return $this->priceFor($providerKey);
    }

    public function processPayment(User $user, string $providerKey, string $method = 'simulation'): ConsultationOrder
    {
        if ($this->hasAccess($user, $providerKey)) {
            return $this->activeOrder($user, $providerKey);
        }

        $amount = $this->priceFor($providerKey);

        return $this->createPaidOrder(
            $user,
            $providerKey,
            $amount,
            0,
            $amount,
            null,
            $method
        );
    }

    public function redirectIfNoAccess(User $user, string $providerKey): ?RedirectResponse
    {
        if ($this->hasAccess($user, $providerKey)) {
            return null;
        }

        if ($this->pendingOrder($user, $providerKey)) {
            return redirect()->route('consultation.payment.status', $providerKey);
        }

        return redirect()->route('consultation.checkout', $providerKey);
    }

    private function createPaidOrder(
        User $user,
        string $providerKey,
        int $amount,
        int $discount,
        int $totalPaid,
        ?ConsultationVoucher $voucher,
        string $method
    ): ConsultationOrder {
        $now = now();

        $order = ConsultationOrder::create([
            'user_id' => $user->id,
            'provider_key' => $providerKey,
            'reference_code' => null,
            'amount' => $amount,
            'discount_amount' => $discount,
            'total_paid' => $totalPaid,
            'consultation_voucher_id' => $voucher?->id,
            'voucher_code' => $voucher?->code,
            'status' => 'paid',
            'payment_method' => $method,
            'paid_at' => $now,
            'expires_at' => $now->copy()->addHours($this->sessionHours()),
        ]);

        $this->notifier->notifyOrderApproved($order);

        return $order;
    }

    public function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
