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
}
