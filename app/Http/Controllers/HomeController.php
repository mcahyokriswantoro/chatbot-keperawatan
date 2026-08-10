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

            $medOrders = \App\Models\MedicineOrder::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'paid', 'delivered'])
                ->latest()
                ->take(3)
                ->get();
            foreach ($medOrders as $mo) {
                $statusLabel = match($mo->status) {
                    'paid' => '✅ Disetujui (Lunas - Siap Kirim)',
                    'delivered' => '🛵 Sedang Dalam Pengiriman',
                    default => $mo->payment_proof ? '⏳ Menunggu Verifikasi Admin' : '💳 Belum Dibayar',
                };
                $activeOrders[] = [
                    'icon' => '📦',
                    'title' => "Pesanan Obat ({$mo->reference_code})",
                    'status' => $statusLabel,
                    'url' => route('medicines.status', $mo),
                ];
            }

            $hcBookings = \App\Models\HomecareBooking::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'paid'])
                ->latest()
                ->take(3)
                ->get();
            foreach ($hcBookings as $hb) {
                $statusLabel = match($hb->status) {
                    'paid' => '🗓️ Disetujui (Jadwal Terkonfirmasi)',
                    default => $hb->payment_proof ? '⏳ Menunggu Verifikasi Admin' : '💳 Belum Dibayar',
                };
                $activeOrders[] = [
                    'icon' => '🏠',
                    'title' => "Booking Homecare ({$hb->reference_code})",
                    'status' => $statusLabel,
                    'url' => route('homecare.status', $hb),
                ];
            }
        }

        return view('home', [
            'tips' => config('health.chatbot_tips'),
            'healthStatus' => $healthStatusService->forUser(auth()->user()),
            'activeOrders' => $activeOrders,
        ]);
    }
}
