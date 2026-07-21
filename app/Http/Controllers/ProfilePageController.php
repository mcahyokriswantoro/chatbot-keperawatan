<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfilePageController extends Controller
{
    public function index(): View
    {
        $stats = null;

        if (auth()->check()) {
            $user = auth()->user();
            $bmi = null;
            $bmiLabel = null;
            $bmiTone = 'text-slate-600';

            if ($user->weight && $user->height && (float) $user->height > 0) {
                $heightM = (float) $user->height / 100;
                $bmi = round((float) $user->weight / ($heightM * $heightM), 1);

                if ($bmi < 18.5) {
                    $bmiLabel = 'Kurus';
                    $bmiTone = 'text-amber-600';
                } elseif ($bmi < 25) {
                    $bmiLabel = 'Normal';
                    $bmiTone = 'text-emerald-600';
                } elseif ($bmi < 30) {
                    $bmiLabel = 'Berlebih';
                    $bmiTone = 'text-amber-600';
                } else {
                    $bmiLabel = 'Obesitas';
                    $bmiTone = 'text-rose-600';
                }
            }

            $latestScreening = $user->screeningSessions()
                ->latest()
                ->first();

            $reminders = [];

            // 1. Cart items reminder
            $cart = session()->get('medicine_cart', []);
            $cartCount = array_sum($cart);
            if ($cartCount > 0) {
                $reminders[] = [
                    'type' => 'cart',
                    'title' => 'Keranjang Belanja Obat',
                    'description' => "Ada {$cartCount} obat di keranjang belanja Anda yang belum dicheckout.",
                    'action_label' => 'Buka Keranjang',
                    'action_url' => route('medicines.cart'),
                    'icon' => '🛒',
                    'color' => 'blue',
                ];
            }

            // 2. Pending Consultation payments
            $pendingConsultations = \App\Models\ConsultationOrder::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
            foreach ($pendingConsultations as $cOrder) {
                $hasProof = !empty($cOrder->payment_proof);
                $providerLabel = $cOrder->provider_key === 'perawat' ? 'Perawat' : 'Dokter';
                $reminders[] = [
                    'type' => 'consultation',
                    'title' => "Pembayaran Konsultasi ({$providerLabel})",
                    'description' => $hasProof 
                        ? "Pembayaran {$cOrder->reference_code} sedang diverifikasi oleh admin." 
                        : "Konsultasi ({$cOrder->reference_code}) menanti transfer & upload bukti.",
                    'action_label' => $hasProof ? 'Lihat Detail' : 'Kirim Bukti Transfer',
                    'action_url' => route('consultation.payment', $cOrder->provider_key),
                    'icon' => '💬',
                    'color' => $hasProof ? 'amber' : 'rose',
                ];
            }

            // 3. Pending Medicine Orders
            $pendingMedicineOrders = \App\Models\MedicineOrder::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
            foreach ($pendingMedicineOrders as $mOrder) {
                $hasProof = !empty($mOrder->payment_proof);
                $reminders[] = [
                    'type' => 'medicine_order',
                    'title' => "Pesanan Obat ({$mOrder->reference_code})",
                    'description' => $hasProof 
                        ? "Pembayaran pesanan sedang dalam verifikasi admin." 
                        : "Pemesanan obat belum dibayar. Silakan lakukan transfer.",
                    'action_label' => $hasProof ? 'Lihat Status' : 'Bayar Sekarang',
                    'action_url' => $hasProof ? route('medicines.status', $mOrder) : route('medicines.payment', $mOrder),
                    'icon' => '💊',
                    'color' => $hasProof ? 'amber' : 'rose',
                ];
            }

            // 4. Pending Homecare Bookings
            $pendingHomecareBookings = \App\Models\HomecareBooking::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
            foreach ($pendingHomecareBookings as $hBooking) {
                $hasProof = !empty($hBooking->payment_proof);
                $reminders[] = [
                    'type' => 'homecare_booking',
                    'title' => "Booking Homecare ({$hBooking->reference_code})",
                    'description' => $hasProof 
                        ? "Bukti pembayaran sedang diverifikasi untuk konfirmasi jadwal." 
                        : "Layanan homecare belum dibayar. Silakan transfer & kirim bukti.",
                    'action_label' => $hasProof ? 'Lihat Status' : 'Bayar Sekarang',
                    'action_url' => $hasProof ? route('homecare.status', $hBooking) : route('homecare.payment', $hBooking),
                    'icon' => '🏠',
                    'color' => $hasProof ? 'amber' : 'rose',
                ];
            }

            $stats = [
                'screening_count' => $user->screeningSessions()->count(),
                'monitoring_count' => $user->healthMonitorings()->count(),
                'bmi' => $bmi,
                'bmi_label' => $bmiLabel,
                'bmi_tone' => $bmiTone,
                'latest_screening' => $latestScreening,
                'reminders' => $reminders,
            ];
        }

        return view('profile.index', compact('stats'));
    }
}
