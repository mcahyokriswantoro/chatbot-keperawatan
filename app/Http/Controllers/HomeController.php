<?php

namespace App\Http\Controllers;

use App\Services\HealthStatusService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(HealthStatusService $healthStatusService): View
    {
        $activeOrders = [];
        if (auth()->check()) {
            $user = auth()->user();

            try {
                $medOrders = \App\Models\MedicineOrder::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'paid', 'delivered'])
                    ->latest()
                    ->take(3)
                    ->get();
                foreach ($medOrders as $mo) {
                    $hasProof = !empty($mo->payment_proof);
                    $statusLabel = match($mo->status) {
                        'paid' => '✅ Disetujui (Lunas - Siap Kirim)',
                        'delivered' => '🛵 Sedang Dalam Pengiriman',
                        default => $hasProof ? '⏳ Menunggu Verifikasi Admin' : '💳 Belum Dibayar',
                    };
                    $activeOrders[] = [
                        'icon' => '📦',
                        'title' => "Pesanan Obat ({$mo->reference_code})",
                        'status' => $statusLabel,
                        'url' => route('medicines.status', $mo),
                    ];
                }
            } catch (\Throwable) {}

            try {
                $hcBookings = \App\Models\HomecareBooking::where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'paid'])
                    ->latest()
                    ->take(3)
                    ->get();
                foreach ($hcBookings as $hb) {
                    $hasProof = !empty($hb->payment_proof);
                    $statusLabel = match($hb->status) {
                        'paid' => '🗓️ Disetujui (Jadwal Terkonfirmasi)',
                        default => $hasProof ? '⏳ Menunggu Verifikasi Admin' : '💳 Belum Dibayar',
                    };
                    $activeOrders[] = [
                        'icon' => '🏠',
                        'title' => "Booking Homecare ({$hb->reference_code})",
                        'status' => $statusLabel,
                        'url' => route('homecare.status', $hb),
                    ];
                }
            } catch (\Throwable) {}
        }

        return view('home', [
            'tips' => config('health.chatbot_tips'),
            'healthStatus' => $healthStatusService->forUser(auth()->user()),
            'activeOrders' => $activeOrders,
        ]);
    }
}
