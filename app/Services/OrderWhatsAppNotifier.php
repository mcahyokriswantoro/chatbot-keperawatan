<?php

namespace App\Services;

use App\Models\HomecareBooking;
use App\Models\ConsultationOrder;
use App\Models\MedicineOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Kirim notifikasi WhatsApp ke pasien untuk perubahan status
 * pesanan obat & booking homecare.
 *
 * Driver dikonfigurasi via CONSULTATION_WA_NOTIFY_DRIVER di .env
 * (fonnte | wablas | log | disabled)
 */
class OrderWhatsAppNotifier
{
    // ---------------------------------------------------------------
    // MEDICINE ORDER
    // ---------------------------------------------------------------

    public function notifyMedicineApproved(MedicineOrder $order): bool
    {
        $phone = $this->resolvePhone($order->user?->phone);
        if ($phone === '') {
            return false;
        }

        $items = $order->items->map(fn ($i) => '- '.$i->medicine->name.' x'.$i->quantity)->implode("\n");

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$order->user?->name.'*,',
            'Kami menginformasikan bahwa pesanan obat Anda telah *disetujui* dan saat ini sedang dalam proses penyiapan oleh apotek mitra kami.',
            '',
            '📋 *Kode Pesanan:* '.$order->reference_code,
            '🏥 *Apotek Penanggung Jawab:* '.($order->closest_pharmacy ?? 'Apotek Mitra Nersia'),
            '',
            '*Rincian Pesanan:*',
            $items,
            '',
            'Kami akan segera memberikan informasi lanjutan apabila pesanan Anda telah masuk ke tahap pengiriman.',
            '',
            'Untuk informasi lebih lanjut, silakan kunjungi portal kami di: '.url('/obat'),
            '',
            'Terima kasih atas kepercayaan Anda kepada layanan Nersia Health. 🙏',
        ]);

        return $this->dispatch($phone, $text);
    }

    public function notifyMedicineDelivered(MedicineOrder $order): bool
    {
        $phone = $this->resolvePhone($order->user?->phone);
        if ($phone === '') {
            return false;
        }

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$order->user?->name.'*,',
            'Kabar baik! Pesanan obat Anda dengan kode referensi *'.$order->reference_code.'* saat ini telah *dikirimkan* menuju alamat Anda.',
            '',
            '🏥 *Dikirim Dari:* '.($order->closest_pharmacy ?? 'Apotek Mitra Nersia'),
            '📍 *Tujuan Pengiriman:*',
            $order->address,
            '',
            'Mohon perkenan Bapak/Ibu untuk menyiapkan diri menerima kedatangan kurir kami di lokasi tujuan.',
            '',
            'Cek status pesanan Anda melalui tautan berikut: '.url('/obat'),
            '',
            'Semoga Bapak/Ibu lekas sembuh. Terima kasih telah menggunakan layanan Nersia Health! 🙏',
        ]);

        return $this->dispatch($phone, $text);
    }

    public function notifyMedicineRejected(MedicineOrder $order): bool
    {
        $phone = $this->resolvePhone($order->user?->phone);
        if ($phone === '') {
            return false;
        }

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$order->user?->name.'*,',
            'Dengan berat hati kami menyampaikan permohonan maaf bahwa pesanan obat Anda (Kode: *'.$order->reference_code.'*) saat ini tidak dapat kami proses lebih lanjut.',
            '',
            '📝 *Keterangan Pembatalan:*',
            ($order->admin_note ?? 'Kendala operasional. Silakan menghubungi layanan pelanggan kami untuk informasi lebih detail.'),
            '',
            'Bapak/Ibu senantiasa dapat melakukan pemesanan ulang melalui aplikasi kami kapan saja.',
            '',
            'Kunjungi platform kami: '.url('/obat'),
            '',
            'Kami memohon maaf atas ketidaknyamanan ini dan berterima kasih atas pengertian Bapak/Ibu. 🙏',
        ]);

        return $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // HOMECARE BOOKING
    // ---------------------------------------------------------------

    public function notifyHomecareApproved(HomecareBooking $booking): bool
    {
        $phone = $this->resolvePhone($booking->patient_phone ?: $booking->user?->phone);
        if ($phone === '') {
            return false;
        }

        $date = $booking->booking_date?->translatedFormat('l, d F Y') ?? '-';
        $time = $booking->booking_date?->format('H:i') ?? '-';

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$booking->patient_name.'*,',
            'Kami menginformasikan bahwa pemesanan layanan Homecare Anda telah *disetujui*. Tim tenaga medis kami siap memberikan pelayanan terbaik untuk Anda.',
            '',
            '📋 *Kode Reservasi:* '.$booking->reference_code,
            '🏥 *Layanan:* '.($booking->package?->name ?? 'Layanan Homecare Umum'),
            '📅 *Jadwal Kunjungan:* '.$date,
            '⏰ *Waktu:* '.$time.' WIB',
            '📍 *Lokasi Perawatan:*',
            $booking->address,
            '',
            'Mohon perkenan Bapak/Ibu untuk bersiap di lokasi pada waktu yang telah disepakati.',
            '',
            'Untuk melihat detail reservasi, silakan kunjungi: '.url('/homecare'),
            '',
            'Terima kasih telah mempercayakan layanan kesehatan Anda kepada Nersia Health. 🙏',
        ]);

        return $this->dispatch($phone, $text);
    }

    public function notifyHomecareCompleted(HomecareBooking $booking): bool
    {
        $phone = $this->resolvePhone($booking->patient_phone ?: $booking->user?->phone);
        if ($phone === '') {
            return false;
        }

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$booking->patient_name.'*,',
            'Kunjungan layanan Homecare Anda (Kode Reservasi: *'.$booking->reference_code.'*) telah *selesai dilaksanakan*.',
            '',
            '🏥 *Layanan:* '.($booking->package?->name ?? 'Layanan Homecare Umum'),
            '',
            'Kami berharap perawatan yang diberikan dapat membantu pemulihan dan menjaga kesehatan Bapak/Ibu.',
            '',
            'Bapak/Ibu dapat melihat riwayat perawatan melalui platform kami: '.url('/homecare'),
            '',
            'Semoga Bapak/Ibu lekas pulih dan senantiasa sehat. Terima kasih atas kepercayaan Anda kepada Nersia Health. 💙',
        ]);

        return $this->dispatch($phone, $text);
    }

    public function notifyHomecareRejected(HomecareBooking $booking): bool
    {
        $phone = $this->resolvePhone($booking->patient_phone ?: $booking->user?->phone);
        if ($phone === '') {
            return false;
        }

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$booking->patient_name.'*,',
            'Dengan berat hati kami menyampaikan permohonan maaf bahwa pemesanan layanan Homecare Anda (Kode: *'.$booking->reference_code.'*) saat ini tidak dapat kami tindak lanjuti.',
            '',
            '📝 *Keterangan Pembatalan:*',
            ($booking->admin_note ?? 'Kendala penjadwalan/operasional. Silakan menghubungi admin kami untuk informasi lebih lanjut.'),
            '',
            'Bapak/Ibu dapat melakukan pemesanan ulang untuk jadwal lainnya melalui platform kami kapan saja.',
            '',
            'Kunjungi platform kami: '.url('/homecare'),
            '',
            'Kami memohon maaf atas ketidaknyamanan yang terjadi. 🙏',
        ]);

        return $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // NOTIFY PASIEN KONSULTASI — disetujui / ditolak admin
    // ---------------------------------------------------------------

    public function notifyPatientConsultationApproved(
        string $patientPhone,
        string $patientName,
        ConsultationOrder $order,
    ): void {
        $phone = $this->resolvePhone($patientPhone);
        if ($phone === '') {
            return;
        }

        $expiresAt = $order->expires_at?->format('d M Y H:i') ?? '-';

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$patientName.'*,',
            'Kami memberitahukan bahwa pembayaran konsultasi Anda telah berhasil kami verifikasi dan sesi konsultasi saat ini telah *aktif*.',
            '',
            '⏰ *Batas Waktu Sesi:* '.$expiresAt.' WIB',
            '',
            'Tenaga kesehatan kami telah bersiap untuk membantu Bapak/Ibu. Silakan masuk ke aplikasi untuk memulai percakapan medis.',
            '',
            'Akses ruang konsultasi Anda melalui tautan berikut: '.url('/konsultasi'),
            '',
            'Terima kasih telah mempercayakan layanan kesehatan Anda kepada Nersia Health. 🙏',
        ]);

        $this->dispatch($phone, $text);
    }

    public function notifyPatientConsultationRejected(
        string $patientPhone,
        string $patientName,
        ?string $adminNote,
    ): void {
        $phone = $this->resolvePhone($patientPhone);
        if ($phone === '') {
            return;
        }

        $text = implode("\n", [
            'Salam sehat dari *Nersia Health*! 🌿',
            '',
            'Yth. Bapak/Ibu *'.$patientName.'*,',
            'Mohon maaf sebesar-besarnya, pembayaran untuk sesi konsultasi Anda tidak dapat kami verifikasi pada saat ini.',
            '',
            '📝 *Keterangan:* '.($adminNote ?? 'Silakan menghubungi pusat bantuan kami untuk mendapatkan informasi lebih lanjut.'),
            '',
            'Bapak/Ibu diperkenankan untuk mengulangi proses pembayaran atau membuat sesi konsultasi yang baru melalui platform kami.',
            '',
            'Kunjungi aplikasi kami di: '.url('/konsultasi'),
            '',
            'Kami memohon maaf atas ketidaknyamanan yang dialami. Terima kasih atas pengertian Bapak/Ibu. 🙏',
        ]);

        $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // NOTIFY MITRA APOTEK — pesanan obat baru masuk
    // ---------------------------------------------------------------

    public function notifyPharmacyNewOrder(MedicineOrder $order): void
    {
        $pharmacy = $order->closest_pharmacy ?? '';

        // Tentukan nomor apotek berdasarkan nama yang dipilih sistem
        if (str_contains($pharmacy, '1')) {
            $raw = (string) \App\Models\Setting::getValue('umla_farma1_phone', config('consultation.notification.umla_farma1_phone', ''));
        } else {
            $raw = (string) \App\Models\Setting::getValue('umla_farma2_phone', config('consultation.notification.umla_farma2_phone', ''));
        }

        $phone = $this->resolvePhone($raw);

        if ($phone === '') {
            return;
        }

        $items = $order->items->map(fn ($i) => '- '.$i->medicine->name.' x'.$i->quantity)->implode("\n");

        $text = implode("\n", [
            '🔔 *Pesanan Obat Baru Masuk*',
            '',
            '📋 *Kode:* '.$order->reference_code,
            '👤 *Pasien:* '.($order->user?->name ?? '-'),
            '📞 *HP Pasien:* '.($order->user?->phone ?? '-'),
            '',
            $items,
            '',
            '📍 *Alamat pengiriman:*',
            $order->address,
            '',
            'Silakan proses pesanan via panel admin.',
        ]);

        $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // NOTIFY MITRA HOMECARE — booking baru masuk
    // ---------------------------------------------------------------

    public function notifyHomecareProviderNewBooking(HomecareBooking $booking): void
    {
        $provider = $booking->closest_provider ?? '';

        if (str_contains($provider, '1')) {
            $raw = (string) \App\Models\Setting::getValue('medical_center1_phone', config('consultation.notification.medical_center1_phone', ''));
        } else {
            $raw = (string) \App\Models\Setting::getValue('medical_center2_phone', config('consultation.notification.medical_center2_phone', ''));
        }

        $phone = $this->resolvePhone($raw);

        if ($phone === '') {
            return;
        }

        $date = $booking->booking_date?->translatedFormat('l, d F Y') ?? '-';
        $time = $booking->booking_date?->format('H:i') ?? '-';

        $text = implode("\n", [
            '🔔 *Booking Homecare Baru Masuk*',
            '',
            '📋 *Kode:* '.$booking->reference_code,
            '🏥 *Layanan:* '.($booking->package?->name ?? 'Layanan Homecare'),
            '👤 *Pasien:* '.$booking->patient_name,
            '📞 *HP Pasien:* '.($booking->patient_phone ?: ($booking->user?->phone ?? '-')),
            '📅 *Tanggal:* '.$date.' '.$time.' WIB',
            '📍 *Alamat:*',
            $booking->address,
            '',
            'Silakan proses booking via panel admin.',
        ]);

        $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // NOTIFY ADMIN — semua order/booking baru masuk
    // ---------------------------------------------------------------

    public function notifyAdminNewMedicineOrder(MedicineOrder $order): void
    {
        $raw = (string) \App\Models\Setting::getValue('order_admin_phone', config('consultation.notification.admin_phone', ''));
        $phone = $this->resolvePhone($raw);
        if ($phone === '') {
            return;
        }

        $items = $order->items->map(fn ($i) => '- '.$i->medicine->name.' x'.$i->quantity)->implode("\n");

        $text = implode("\n", [
            '💊 *[ADMIN] Pesanan Obat Baru*',
            '',
            '📋 *Kode:* '.$order->reference_code,
            '👤 *Pasien:* '.($order->user?->name ?? '-'),
            '🏥 *Apotek:* '.($order->closest_pharmacy ?? '-'),
            '',
            $items,
            '',
            'Silakan setujui pesanan di panel admin.',
        ]);

        $this->dispatch($phone, $text);
    }

    public function notifyAdminNewHomecareBooking(HomecareBooking $booking): void
    {
        $raw = (string) \App\Models\Setting::getValue('order_admin_phone', config('consultation.notification.admin_phone', ''));
        $phone = $this->resolvePhone($raw);
        if ($phone === '') {
            return;
        }

        $date = $booking->booking_date?->translatedFormat('l, d F Y') ?? '-';
        $time = $booking->booking_date?->format('H:i') ?? '-';

        $text = implode("\n", [
            '🏥 *[ADMIN] Booking Homecare Baru*',
            '',
            '📋 *Kode:* '.$booking->reference_code,
            '🏥 *Layanan:* '.($booking->package?->name ?? 'Layanan Homecare'),
            '👤 *Pasien:* '.$booking->patient_name,
            '📅 *Tanggal:* '.$date.' '.$time.' WIB',
            '',
            'Silakan setujui booking di panel admin.',
        ]);

        $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // NOTIFY PASIEN KONSULTASI — chat dibalas oleh perawat/dokter
    // ---------------------------------------------------------------

    public function notifyPatientChatReplied(
        string $patientPhone,
        string $patientName,
        string $providerName,
        string $messagePreview,
    ): void {
        $phone = $this->resolvePhone($patientPhone);
        if ($phone === '') {
            return;
        }

        $preview = mb_strlen($messagePreview) > 120
            ? mb_substr($messagePreview, 0, 117).'...'
            : $messagePreview;

        $text = implode("\n", [
            '💬 *Pesan Konsultasi Baru*',
            '',
            'Halo, *'.$patientName.'*!',
            '*'.$providerName.'* baru saja membalas pertanyaan Anda:',
            '',
            '"'.$preview.'"',
            '',
            'Buka aplikasi untuk melihat balasan lengkap.',
        ]);

        $this->dispatch($phone, $text);
    }

    // ---------------------------------------------------------------
    // INTERNAL HELPERS
    // ---------------------------------------------------------------

    /**
     * Normalize an Indonesian phone number to international format (62xxx).
     */
    private function resolvePhone(?string $rawPhone): string
    {
        if ($rawPhone === null || trim($rawPhone) === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return '62'.$digits;
    }

    private function dispatch(string $number, string $text): bool
    {
        $driver = (string) config('consultation.notification.driver', 'log');

        return match ($driver) {
            'fonnte'   => $this->sendViaFonnte($number, $text),
            'wablas'   => $this->sendViaWablas($number, $text),
            'disabled' => false,
            default    => $this->logOnly($number, $text),
        };
    }

    private function sendViaFonnte(string $number, string $text): bool
    {
        $token = (string) config('consultation.notification.fonnte_token', '');

        if ($token === '') {
            Log::warning('OrderWA notify: FONNTE_TOKEN kosong.');
            return $this->logOnly($number, $text);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target'      => $number,
                'message'     => $text,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('OrderWA notify Fonnte gagal', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('OrderWA notify Fonnte error: '.$e->getMessage());
        }

        return false;
    }

    private function sendViaWablas(string $number, string $text): bool
    {
        $token  = (string) config('consultation.notification.wablas_token', '');
        $secret = (string) config('consultation.notification.wablas_secret', '');

        if ($token === '' || $secret === '') {
            Log::warning('OrderWA notify: WABLAS token/secret kosong.');
            return $this->logOnly($number, $text);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $secret.'.'.$token,
            ])->post('https://pati.wablas.com/api/send-message', [
                'phone'   => $number,
                'message' => $text,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('OrderWA notify Wablas gagal', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('OrderWA notify Wablas error: '.$e->getMessage());
        }

        return false;
    }

    private function logOnly(string $number, string $text): bool
    {
        Log::info('OrderWA notify (log driver)', [
            'to'      => $number,
            'message' => $text,
        ]);

        return true;
    }
}
