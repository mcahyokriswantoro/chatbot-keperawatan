<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Services\OrderWhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminMedicineController extends Controller
{
    public function __construct(
        private OrderWhatsAppNotifier $notifier,
    ) {}
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $medicines = Medicine::orderBy('category')->orderBy('name')->get();

        $ordersQuery = MedicineOrder::with(['user', 'items.medicine'])->latest();
        if ($status !== 'all') {
            $ordersQuery->where('status', $status);
        }
        $orders = $ordersQuery->get();

        $pendingCount = MedicineOrder::where('status', 'pending')->count();
        $paidCount = MedicineOrder::where('status', 'paid')->count();
        $deliveredCount = MedicineOrder::where('status', 'delivered')->count();
        $rejectedCount = MedicineOrder::where('status', 'rejected')->count();

        $shippingFeePerKm = (int) \App\Models\Setting::getValue('medicine_shipping_fee_per_km', 3000);

        return view('admin.medicines.index', [
            'medicines' => $medicines,
            'orders' => $orders,
            'status' => $status,
            'pendingCount' => $pendingCount,
            'paidCount' => $paidCount,
            'deliveredCount' => $deliveredCount,
            'rejectedCount' => $rejectedCount,
            'shippingFeePerKm' => $shippingFeePerKm,
        ]);
    }

    public function create(): View
    {
        return view('admin.medicines.form', [
            'medicine' => new Medicine(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('medicines', 'public');
        }

        $validated['active'] = $request->has('active');

        Medicine::create($validated);

        return redirect()->route('admin.medicines.index')->with('status', 'Obat baru berhasil ditambahkan.');
    }

    public function edit(Medicine $medicine): View
    {
        return view('admin.medicines.form', [
            'medicine' => $medicine,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($medicine->photo) {
                Storage::disk('public')->delete($medicine->photo);
            }
            $validated['photo'] = $request->file('photo')->store('medicines', 'public');
        }

        $validated['active'] = $request->has('active');

        $medicine->update($validated);

        return redirect()->route('admin.medicines.index')->with('status', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->photo) {
            Storage::disk('public')->delete($medicine->photo);
        }

        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('status', 'Obat berhasil dihapus.');
    }

    public function approveOrder(MedicineOrder $order)
    {
        abort_unless($order->isPending(), 404);

        $order->load('user', 'items.medicine');
        $order->update(['status' => 'paid']);

        rescue(fn () => $this->notifier->notifyMedicineApproved($order));

        return back()->with('status', "Pesanan {$order->reference_code} berhasil disetujui (Lunas).");
    }

    public function deliverOrder(MedicineOrder $order)
    {
        abort_unless($order->isPaid(), 404);

        $order->load('user', 'items.medicine');
        $order->update(['status' => 'delivered']);

        rescue(fn () => $this->notifier->notifyMedicineDelivered($order));

        return back()->with('status', "Pesanan {$order->reference_code} telah ditandai sebagai Dikirim.");
    }

    public function rejectOrder(Request $request, MedicineOrder $order)
    {
        abort_unless($order->isPending(), 404);

        $validated = $request->validate([
            'admin_note' => ['required', 'string', 'max:255'],
        ]);

        $order->load('user', 'items.medicine');

        DB::transaction(function () use ($order, $validated) {
            // Kembalikan stok obat
            foreach ($order->items as $item) {
                $item->medicine()->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'rejected',
                'admin_note' => $validated['admin_note'],
            ]);
        });

        rescue(fn () => $this->notifier->notifyMedicineRejected($order));

        return back()->with('status', "Pesanan {$order->reference_code} berhasil ditolak.");
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'medicine_shipping_fee_per_km' => ['required', 'integer', 'min:0'],
        ]);

        \App\Models\Setting::setValue('medicine_shipping_fee_per_km', $validated['medicine_shipping_fee_per_km']);

        return back()->with('status', 'Tarif pengiriman obat berhasil diperbarui.');
    }
}
