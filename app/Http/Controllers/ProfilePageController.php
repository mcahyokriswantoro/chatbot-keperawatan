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
                    'cancel_url' => route('medicines.cart.clear'),
                    'cancel_method' => 'POST',
                    'can_cancel' => true,
                    'icon' => '🛒',
                    'color' => 'blue',
                ];
            }

            // 2. Consultation Orders
            $activeConsultations = \App\Models\ConsultationOrder::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'paid'])
                ->latest()
                ->take(5)
                ->get();
            foreach ($activeConsultations as $cOrder) {
                $hasProof = !empty($cOrder->payment_proof);
                $providerLabel = $cOrder->provider_key === 'perawat' ? 'Perawat' : 'Dokter';

                if ($cOrder->status === 'paid') {
                    $reminders[] = [
                        'type' => 'consultation',
                        'title' => "Konsultasi Aktif ({$providerLabel})",
                        'description' => "Pembayaran {$cOrder->reference_code} disetujui! Chat aktif, silakan konsultasi.",
                        'action_label' => 'Buka Chat 💬',
                        'action_url' => route('consultation.chat', $cOrder->provider_key),
                        'cancel_url' => null,
                        'cancel_method' => null,
                        'can_cancel' => false,
                        'icon' => '💬',
                        'color' => 'blue',
                    ];
                } else {
                    $reminders[] = [
                        'type' => 'consultation',
                        'title' => "Pembayaran Konsultasi ({$providerLabel})",
                        'description' => $hasProof 
                            ? "Pembayaran {$cOrder->reference_code} sedang diverifikasi oleh admin." 
                            : "Konsultasi ({$cOrder->reference_code}) menanti transfer & upload bukti.",
                        'action_label' => $hasProof ? 'Lihat Detail' : 'Kirim Bukti Transfer',
                        'action_url' => route('consultation.payment', $cOrder->provider_key),
                        'cancel_url' => route('consultation.order.cancel', $cOrder->provider_key),
                        'cancel_method' => 'DELETE',
                        'can_cancel' => !$hasProof,
                        'icon' => '💬',
                        'color' => $hasProof ? 'amber' : 'rose',
                    ];
                }
            }

            // 3. Medicine Orders (Pending, Paid/Approved, Delivered, Rejected)
            $activeMedicineOrders = \App\Models\MedicineOrder::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'paid', 'delivered', 'rejected'])
                ->latest()
                ->take(5)
                ->get();
            foreach ($activeMedicineOrders as $mOrder) {
                $hasProof = !empty($mOrder->payment_proof);

                if ($mOrder->status === 'paid') {
                    $pharmacyLabel = $mOrder->closest_pharmacy ? " ({$mOrder->closest_pharmacy})" : '';
                    $description = "Pembayaran disetujui! Obat sedang disiapkan oleh apotek{$pharmacyLabel}.";
                    $actionLabel = 'Lacak Pesanan 📦';
                    $color = 'blue';
                } elseif ($mOrder->status === 'delivered') {
                    $description = "Pesanan {$mOrder->reference_code} dalam proses pengiriman ke alamat Anda 🛵.";
                    $actionLabel = 'Lacak Pengiriman 🛵';
                    $color = 'blue';
                } elseif ($mOrder->status === 'rejected') {
                    $description = "Pembayaran {$mOrder->reference_code} ditolak oleh admin. Silakan periksa atau upload ulang.";
                    $actionLabel = 'Lihat Detail ❌';
                    $color = 'rose';
                } else {
                    $description = $hasProof 
                        ? "Pembayaran {$mOrder->reference_code} sedang dalam verifikasi admin." 
                        : "Pemesanan obat {$mOrder->reference_code} belum dibayar. Silakan lakukan transfer.";
                    $actionLabel = $hasProof ? 'Lihat Status ⏳' : 'Bayar Sekarang';
                    $color = $hasProof ? 'amber' : 'rose';
                }

                $reminders[] = [
                    'type' => 'medicine_order',
                    'title' => "Pesanan Obat ({$mOrder->reference_code})",
                    'description' => $description,
                    'action_label' => $actionLabel,
                    'action_url' => route('medicines.status', $mOrder),
                    'cancel_url' => route('medicines.order.cancel', $mOrder),
                    'cancel_method' => 'DELETE',
                    'can_cancel' => $mOrder->status === 'pending' && !$hasProof,
                    'icon' => '💊',
                    'color' => $color,
                ];
            }

            // 4. Homecare Bookings (Pending, Paid/Approved, Completed, Rejected)
            $activeHomecareBookings = \App\Models\HomecareBooking::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'paid', 'completed', 'rejected'])
                ->latest()
                ->take(5)
                ->get();
            foreach ($activeHomecareBookings as $hBooking) {
                $hasProof = !empty($hBooking->payment_proof);
                $bookingDateStr = $hBooking->booking_date ? $hBooking->booking_date->format('d/m/Y H:i') : '';

                if ($hBooking->status === 'paid') {
                    $description = "Booking disetujui! Perawat akan berkunjung sesuai jadwal ({$bookingDateStr}).";
                    $actionLabel = 'Lihat Jadwal 🗓️';
                    $color = 'blue';
                } elseif ($hBooking->status === 'completed') {
                    $description = "Kunjungan perawat untuk booking {$hBooking->reference_code} telah selesai.";
                    $actionLabel = 'Lihat Detail ✅';
                    $color = 'blue';
                } elseif ($hBooking->status === 'rejected') {
                    $description = "Booking homecare {$hBooking->reference_code} ditolak oleh admin.";
                    $actionLabel = 'Lihat Detail ❌';
                    $color = 'rose';
                } else {
                    $description = $hasProof 
                        ? "Bukti pembayaran {$hBooking->reference_code} sedang diverifikasi admin." 
                        : "Layanan homecare {$hBooking->reference_code} belum dibayar. Silakan transfer.";
                    $actionLabel = $hasProof ? 'Lihat Status ⏳' : 'Bayar Sekarang';
                    $color = $hasProof ? 'amber' : 'rose';
                }

                $reminders[] = [
                    'type' => 'homecare_booking',
                    'title' => "Booking Homecare ({$hBooking->reference_code})",
                    'description' => $description,
                    'action_label' => $actionLabel,
                    'action_url' => route('homecare.status', $hBooking),
                    'cancel_url' => route('homecare.booking.cancel', $hBooking),
                    'cancel_method' => 'DELETE',
                    'can_cancel' => $hBooking->status === 'pending' && !$hasProof,
                    'icon' => '🏠',
                    'color' => $color,
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
