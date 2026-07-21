<?php

namespace App\Services;

use App\Models\ConsultationMessage;
use App\Models\ConsultationOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultationWhatsAppNotifier
{
    public function __construct(
        private ConsultationWhatsAppService $whatsapp,
    ) {}

    public function notifyNewUserMessage(ConsultationMessage $message, ConsultationOrder $order): bool
    {
        if ($message->notified_provider) {
            return true;
        }

        $providerKey = $order->provider_key;
        $number = $this->whatsapp->internationalNumber($providerKey);

        if ($number === '') {
            return false;
        }

        $patient = $order->user;
        $provider = $this->whatsapp->provider($providerKey);
        $providerName = $provider['short_name'] ?? $provider['name'] ?? 'Tenaga kesehatan';
        $patientName = $patient?->name ?? 'Pasien';
        $preview = mb_strlen($message->body) > 120
            ? mb_substr($message->body, 0, 117).'...'
            : $message->body;

        $replyUrl = url('/admin/konsultasi/chat/'.$order->id);

        $text = implode("\n", array_filter([
            'Salam dari *Nersia Health*! 🌿',
            '',
            'Yth. '.$providerName.',',
            'Terdapat pesan baru dari pasien pada sesi konsultasi Anda.',
            '',
            '👤 *Pasien:* '.$patientName,
            '',
            '*Pesan Pasien:*',
            '"'.$preview.'"',
            '',
            'Mohon kesediaannya untuk membalas pesan tersebut melalui ruang obrolan di Panel Admin:',
            $replyUrl,
            '',
            'Terima kasih atas dedikasi Anda. 🙏',
        ]));

        $sent = $this->dispatch($number, $text);

        if ($sent) {
            $message->update(['notified_provider' => true]);
        }

        return $sent;
    }

    public function notifyOrderApproved(ConsultationOrder $order): bool
    {
        $providerKey = $order->provider_key;
        $provider = $this->whatsapp->provider($providerKey);
        $providerName = $provider['short_name'] ?? $provider['name'] ?? 'Tenaga kesehatan';
        $providerNumber = $this->whatsapp->internationalNumber($providerKey);

        $patient = $order->user;
        $patientName = $patient?->name ?? 'Pasien';

        $expiresAt = $order->expires_at ? $order->expires_at->format('d M Y H:i') : '-';
        $adminChatUrl = url('/admin/konsultasi/chat/'.$order->id);

        $text = implode("\n", array_filter([
            'Salam dari *Nersia Health*! 🌿',
            '',
            'Yth. '.$providerName.',',
            'Sesi konsultasi baru telah *aktif* dan pembayaran pasien telah diverifikasi.',
            '',
            '👤 *Pasien:* '.$patientName,
            '💳 *Metode Pembayaran:* '.strtoupper($order->payment_method),
            '⏰ *Batas Waktu Sesi:* '.$expiresAt.' WIB',
            '',
            'Mohon kesediaannya untuk segera merespons pasien melalui Panel Admin:',
            $adminChatUrl,
            '',
            'Terima kasih atas pelayanan Anda. 🙏',
        ]));

        $sent = false;

        // 1. Notify the provider
        if ($providerNumber !== '') {
            $sent = $this->dispatch($providerNumber, $text);
        }

        // 2. Notify the admin/merchant phone (if different from provider number)
        $adminPhone = (string) config('consultation.dana.merchant_phone', '');
        if ($adminPhone !== '') {
            $adminNumber = \App\Models\ConsultationProvider::normalizeWhatsappIntl($adminPhone);
            if ($adminNumber !== '' && $adminNumber !== $providerNumber) {
                $this->dispatch($adminNumber, $text);
                $sent = true;
            }
        }

        return $sent;
    }

    private function dispatch(string $number, string $text): bool
    {
        $driver = (string) config('consultation.notification.driver', 'log');

        return match ($driver) {
            'fonnte' => $this->sendViaFonnte($number, $text),
            'wablas' => $this->sendViaWablas($number, $text),
            'disabled' => false,
            default => $this->logOnly($number, $text),
        };
    }

    private function sendViaFonnte(string $number, string $text): bool
    {
        $token = (string) config('consultation.notification.fonnte_token', '');

        if ($token === '') {
            Log::warning('Consultation WA notify: FONNTE_TOKEN kosong.');

            return $this->logOnly($number, $text);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $number,
                'message' => $text,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Consultation WA notify Fonnte gagal', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Consultation WA notify Fonnte error: '.$e->getMessage());
        }

        return false;
    }

    private function sendViaWablas(string $number, string $text): bool
    {
        $token = (string) config('consultation.notification.wablas_token', '');
        $secret = (string) config('consultation.notification.wablas_secret', '');

        if ($token === '' || $secret === '') {
            Log::warning('Consultation WA notify: WABLAS token/secret kosong.');

            return $this->logOnly($number, $text);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $secret.'.'.$token,
            ])->post('https://pati.wablas.com/api/send-message', [
                'phone' => $number,
                'message' => $text,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Consultation WA notify Wablas gagal', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Consultation WA notify Wablas error: '.$e->getMessage());
        }

        return false;
    }

    private function logOnly(string $number, string $text): bool
    {
        Log::info('Consultation WA notify (log driver)', [
            'to' => $number,
            'message' => $text,
        ]);

        return true;
    }
}
