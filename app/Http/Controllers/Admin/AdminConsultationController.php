<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationOrder;
use App\Services\ConsultationAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminConsultationController extends Controller
{
    public function __construct(
        private ConsultationAccessService $access,
    ) {}

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');

        $orders = ConsultationOrder::query()
            ->with(['user'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->where('payment_method', 'dana')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $pendingCount = ConsultationOrder::query()
            ->where('status', 'pending')
            ->where('payment_method', 'dana')
            ->count();

        $paidCount = ConsultationOrder::query()
            ->where('status', 'paid')
            ->where('payment_method', 'dana')
            ->count();

        $rejectedCount = ConsultationOrder::query()
            ->where('status', 'rejected')
            ->where('payment_method', 'dana')
            ->count();

        return view('admin.consultations.index', [
            'orders' => $orders,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'paidCount' => $paidCount,
            'rejectedCount' => $rejectedCount,
        ]);
    }

    public function approve(ConsultationOrder $order): RedirectResponse
    {
        $this->access->approveOrder($order, auth()->user());

        return back()->with('status', 'Pembayaran '.$order->reference_code.' disetujui. User bisa chat live.');
    }

    public function reject(Request $request, ConsultationOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->access->rejectOrder($order, auth()->user(), $validated['admin_note'] ?? null);

        return back()->with('status', 'Pembayaran '.$order->reference_code.' ditolak.');
    }

    public function toggleFreeMode(?string $category = null): RedirectResponse
    {
        if ($category) {
            $key = "consultation_free_{$category}";
            $current = \App\Models\Setting::getValue($key, '0');
            $new = $current === '1' ? '0' : '1';
            \App\Models\Setting::setValue($key, $new);

            // Also sync global if all categories become free/paid
            $categories = collect(config('consultation.categories', []));
            $labelName = $categories->firstWhere('key', $category)['label'] ?? ucfirst($category);
            $statusLabel = $new === '1' ? 'Gratis 100%' : 'Berbayar (Harus Bayar)';

            return back()->with('status', "Status biaya konsultasi [{$labelName}] berhasil diubah menjadi: {$statusLabel}.");
        }

        $current = \App\Models\Setting::getValue('consultation_is_free', '0');
        $new = $current === '1' ? '0' : '1';
        \App\Models\Setting::setValue('consultation_is_free', $new);

        foreach (['perawat', 'dokter_umum', 'penyakit_dalam'] as $cKey) {
            \App\Models\Setting::setValue("consultation_free_{$cKey}", $new);
        }

        $label = $new === '1' ? 'Gratis 100%' : 'Berbayar (Harus Bayar)';

        return back()->with('status', "Status biaya seluruh konsultasi berhasil diubah menjadi: {$label}.");
    }
}
