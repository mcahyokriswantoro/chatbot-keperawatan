<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomecareBooking;
use App\Models\HomecarePackage;
use App\Services\OrderWhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHomecareController extends Controller
{
    public function __construct(
        private OrderWhatsAppNotifier $notifier,
    ) {}
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $packages = HomecarePackage::orderBy('price')->get();

        $bookingsQuery = HomecareBooking::with(['user', 'package'])->latest();
        if ($status !== 'all') {
            $bookingsQuery->where('status', $status);
        }
        $bookings = $bookingsQuery->get();

        $pendingCount = HomecareBooking::where('status', 'pending')->count();
        $paidCount = HomecareBooking::where('status', 'paid')->count();
        $completedCount = HomecareBooking::where('status', 'completed')->count();
        $rejectedCount = HomecareBooking::where('status', 'rejected')->count();

        $transportFeePerKm = \App\Models\Setting::getValue('homecare_transport_fee_per_km', 5000);

        return view('admin.homecare.index', [
            'packages' => $packages,
            'bookings' => $bookings,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'paidCount' => $paidCount,
            'completedCount' => $completedCount,
            'rejectedCount' => $rejectedCount,
            'transportFeePerKm' => (int) $transportFeePerKm,
        ]);
    }

    public function create(): View
    {
        return view('admin.homecare.form', [
            'package' => new HomecarePackage(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'icon' => ['required', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $validated['active'] = $request->has('active');

        HomecarePackage::create($validated);

        return redirect()->route('admin.homecare.index')->with('status', 'Paket homecare baru berhasil ditambahkan.');
    }

    public function edit(HomecarePackage $package): View
    {
        return view('admin.homecare.form', [
            'package' => $package,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, HomecarePackage $package)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'icon' => ['required', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $validated['active'] = $request->has('active');

        $package->update($validated);

        return redirect()->route('admin.homecare.index')->with('status', 'Paket homecare berhasil diperbarui.');
    }

    public function destroy(HomecarePackage $package)
    {
        $package->delete();

        return redirect()->route('admin.homecare.index')->with('status', 'Paket homecare berhasil dihapus.');
    }

    public function approveBooking(HomecareBooking $booking)
    {
        abort_unless($booking->isPending(), 404);

        $booking->load('user', 'package');
        $booking->update(['status' => 'paid']);

        rescue(fn () => $this->notifier->notifyHomecareApproved($booking));

        return back()->with('status', "Booking {$booking->reference_code} berhasil disetujui.");
    }

    public function completeBooking(HomecareBooking $booking)
    {
        abort_unless($booking->isPaid(), 404);

        $booking->load('user', 'package');
        $booking->update(['status' => 'completed']);

        rescue(fn () => $this->notifier->notifyHomecareCompleted($booking));

        return back()->with('status', "Kunjungan homecare {$booking->reference_code} ditandai Selesai.");
    }

    public function rejectBooking(Request $request, HomecareBooking $booking)
    {
        abort_unless($booking->isPending(), 404);

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'max:255'],
        ]);

        $booking->load('user', 'package');
        $booking->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
        ]);

        rescue(fn () => $this->notifier->notifyHomecareRejected($booking));

        return back()->with('status', "Booking {$booking->reference_code} berhasil ditolak.");
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'transport_fee_per_km' => ['required', 'integer', 'min:0'],
        ]);

        \App\Models\Setting::setValue('homecare_transport_fee_per_km', $validated['transport_fee_per_km']);

        return redirect()->route('admin.homecare.index')->with('status', 'Tarif transportasi homecare per KM berhasil diperbarui.');
    }
}
