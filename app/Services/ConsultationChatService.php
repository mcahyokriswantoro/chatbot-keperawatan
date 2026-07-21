<?php

namespace App\Services;

use App\Models\ConsultationMessage;
use App\Models\ConsultationOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultationChatService
{
    public function __construct(
        private ConsultationAccessService $access,
        private ConsultationWhatsAppService $whatsapp,
        private ConsultationWhatsAppNotifier $notifier,
        private OrderWhatsAppNotifier $orderNotifier,
    ) {}

    public function requireActiveOrder(User $user, string $providerKey): ConsultationOrder
    {
        $order = $this->access->activeOrder($user, $providerKey);

        if (! $order) {
            throw ValidationException::withMessages([
                'message' => 'Sesi chat belum aktif. Silakan bayar atau tunggu verifikasi admin.',
            ]);
        }

        return $order;
    }

    /**
     * @return Collection<int, ConsultationMessage>
     */
    public function messagesForOrder(ConsultationOrder $order, ?int $afterId = null): Collection
    {
        return ConsultationMessage::query()
            ->where('consultation_order_id', $order->id)
            ->when($afterId, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function messagesPayload(ConsultationOrder $order, ?int $afterId = null): array
    {
        return $this->messagesForOrder($order, $afterId)
            ->map(fn (ConsultationMessage $m) => $m->toChatArray())
            ->values()
            ->all();
    }

    public function ensureWelcomeMessage(ConsultationOrder $order): void
    {
        if (! ConsultationMessage::tableReady()) {
            return;
        }

        $exists = ConsultationMessage::query()
            ->where('consultation_order_id', $order->id)
            ->exists();

        if ($exists) {
            return;
        }

        $provider = $this->whatsapp->provider($order->provider_key);
        $greeting = trim((string) ($provider['greeting'] ?? ''));

        if ($greeting === '') {
            $name = $provider['short_name'] ?? $provider['name'] ?? 'Tenaga kesehatan';
            $greeting = 'Halo! Saya '.$name.'. Silakan tuliskan keluhan atau pertanyaan Anda. Tim kesehatan akan membalas di chat ini.';
        }

        ConsultationMessage::create([
            'consultation_order_id' => $order->id,
            'user_id' => $order->user_id,
            'provider_key' => $order->provider_key,
            'sender_type' => ConsultationMessage::SENDER_SYSTEM,
            'body' => $greeting,
            'read_by_user_at' => now(),
            'read_by_provider_at' => now(),
            'notified_provider' => true,
        ]);
    }

    public function sendUserMessage(User $user, string $providerKey, string $body): ConsultationMessage
    {
        $body = trim($body);

        if ($body === '') {
            throw ValidationException::withMessages([
                'message' => 'Pesan tidak boleh kosong.',
            ]);
        }

        if (mb_strlen($body) > 2000) {
            throw ValidationException::withMessages([
                'message' => 'Pesan maksimal 2000 karakter.',
            ]);
        }

        $order = $this->requireActiveOrder($user, $providerKey);

        return DB::transaction(function () use ($order, $user, $providerKey, $body) {
            $message = ConsultationMessage::create([
                'consultation_order_id' => $order->id,
                'user_id' => $user->id,
                'provider_key' => $providerKey,
                'sender_type' => ConsultationMessage::SENDER_USER,
                'body' => $body,
                'read_by_user_at' => now(),
            ]);

            $this->notifier->notifyNewUserMessage($message, $order);

            return $message;
        });
    }

    public function sendProviderReply(User $admin, ConsultationOrder $order, string $body): ConsultationMessage
    {
        if (! $order->isActive()) {
            throw ValidationException::withMessages([
                'message' => 'Sesi konsultasi sudah berakhir.',
            ]);
        }

        $body = trim($body);

        if ($body === '') {
            throw ValidationException::withMessages([
                'message' => 'Balasan tidak boleh kosong.',
            ]);
        }

        $provider = $this->whatsapp->provider($order->provider_key);
        $providerName = $provider['short_name'] ?? $provider['name'] ?? 'Tenaga kesehatan';

        $message = ConsultationMessage::create([
            'consultation_order_id' => $order->id,
            'user_id'               => $order->user_id,
            'provider_key'          => $order->provider_key,
            'sender_type'           => ConsultationMessage::SENDER_PROVIDER,
            'sender_user_id'        => $admin->id,
            'body'                  => $body,
            'read_by_provider_at'   => now(),
        ]);

        // Notify the patient via WA when provider replies
        $patientPhone = $order->user?->phone ?? '';
        $patientName  = $order->user?->name ?? 'Pasien';
        if ($patientPhone !== '') {
            rescue(fn () => $this->orderNotifier->notifyPatientChatReplied(
                $patientPhone,
                $patientName,
                $providerName,
                $body,
            ));
        }

        return $message;
    }

    public function markReadByUser(ConsultationOrder $order): void
    {
        ConsultationMessage::query()
            ->where('consultation_order_id', $order->id)
            ->whereIn('sender_type', [ConsultationMessage::SENDER_PROVIDER, ConsultationMessage::SENDER_SYSTEM])
            ->whereNull('read_by_user_at')
            ->update(['read_by_user_at' => now()]);
    }

    public function markReadByProvider(ConsultationOrder $order): void
    {
        ConsultationMessage::query()
            ->where('consultation_order_id', $order->id)
            ->where('sender_type', ConsultationMessage::SENDER_USER)
            ->whereNull('read_by_provider_at')
            ->update(['read_by_provider_at' => now()]);
    }

    public function unreadCountForProvider(?string $providerKey = null): int
    {
        if (! ConsultationMessage::tableReady()) {
            return 0;
        }

        return ConsultationMessage::query()
            ->where('sender_type', ConsultationMessage::SENDER_USER)
            ->whereNull('read_by_provider_at')
            ->when($providerKey, fn ($q) => $q->where('provider_key', $providerKey))
            ->count();
    }

    /**
     * @return Collection<int, ConsultationOrder>
     */
    public function activeThreads(?string $providerKey = null): Collection
    {
        return ConsultationOrder::query()
            ->with(['user'])
            ->where('status', 'paid')
            ->where('expires_at', '>', now())
            ->when($providerKey, fn ($q) => $q->where('provider_key', $providerKey))
            ->whereHas('messages')
            ->withCount([
                'messages as unread_count' => fn ($q) => $q
                    ->where('sender_type', ConsultationMessage::SENDER_USER)
                    ->whereNull('read_by_provider_at'),
            ])
            ->withMax('messages as last_message_at', 'created_at')
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function latestMessagePreview(ConsultationOrder $order): ?string
    {
        $latest = ConsultationMessage::query()
            ->where('consultation_order_id', $order->id)
            ->latest('id')
            ->first();

        return $latest?->body;
    }
}
