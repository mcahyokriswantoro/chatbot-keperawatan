<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineOrder;
use App\Models\MedicineOrderItem;
use App\Services\OrderWhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function __construct(
        private OrderWhatsAppNotifier $notifier,
    ) {}
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $query = Medicine::query()->where('active', true);

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        $medicines = $query->orderBy('name')->get();
        $categories = Medicine::query()->where('active', true)->distinct()->pluck('category')->all();

        $cart = session()->get('medicine_cart', []);
        $cartCount = array_sum($cart);

        return view('medicines.index', [
            'medicines' => $medicines,
            'categories' => $categories,
            'currentCategory' => $category,
            'search' => $search,
            'cartCount' => $cartCount,
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $medicine = Medicine::find($validated['medicine_id']);
        if ($medicine->stock < $validated['quantity']) {
            return back()->with('error', "Stok {$medicine->name} tidak mencukupi.");
        }

        $cart = session()->get('medicine_cart', []);
        $id = $validated['medicine_id'];

        if (isset($cart[$id])) {
            $cart[$id] += $validated['quantity'];
        } else {
            $cart[$id] = $validated['quantity'];
        }

        session()->put('medicine_cart', $cart);

        return back()->with('status', "{$medicine->name} berhasil ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'integer', 'min:0'],
        ]);

        $cart = session()->get('medicine_cart', []);

        foreach ($validated['quantities'] as $id => $qty) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $medicine = Medicine::find($id);
                if ($medicine && $medicine->stock < $qty) {
                    return back()->with('error', "Stok {$medicine->name} tidak mencukupi.");
                }
                $cart[$id] = $qty;
            }
        }

        session()->put('medicine_cart', $cart);

        return back()->with('status', 'Keranjang berhasil diperbarui.');
    }

    public function removeFromCart(int $id)
    {
        $cart = session()->get('medicine_cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('medicine_cart', $cart);
        }

        return back()->with('status', 'Item berhasil dihapus dari keranjang.');
    }

    public function cart(): View
    {
        $cart = session()->get('medicine_cart', []);
        $items = [];
        $total = 0;

        if (!empty($cart)) {
            $medicines = Medicine::whereIn('id', array_keys($cart))->get();
            foreach ($medicines as $med) {
                $qty = $cart[$med->id];
                $subtotal = $med->price * $qty;
                $total += $subtotal;
                $items[] = [
                    'medicine' => $med,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        $shippingFeePerKm = (int) \App\Models\Setting::getValue('medicine_shipping_fee_per_km', 3000);

        return view('medicines.cart', [
            'items' => $items,
            'total' => $total,
            'totalLabel' => 'Rp ' . number_format($total, 0, ',', '.'),
            'shippingFeePerKm' => $shippingFeePerKm,
        ]);
    }

    public function checkout(Request $request)
    {
        $cart = session()->get('medicine_cart', []);
        if (empty($cart)) {
            return redirect()->route('medicines.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $validated = $request->validate([
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'closest_pharmacy' => ['nullable', 'string', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ], [
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'address.min' => 'Alamat pengiriman minimal 10 karakter.',
            'distance_km.required' => 'Jarak pengiriman wajib ditentukan.',
        ]);

        $order = DB::transaction(function () use ($cart, $validated) {
            $total = 0;
            $itemsToCreate = [];

            foreach ($cart as $id => $qty) {
                $medicine = Medicine::lockForUpdate()->find($id);
                if (!$medicine || $medicine->stock < $qty) {
                    throw new \Exception("Stok obat {$medicine->name} tidak mencukupi.");
                }

                // Potong stok
                $medicine->decrement('stock', $qty);

                $subtotal = $medicine->price * $qty;
                $total += $subtotal;

                $itemsToCreate[] = [
                    'medicine_id' => $id,
                    'quantity' => $qty,
                    'price' => $medicine->price,
                ];
            }

            $distanceKm = (float) $validated['distance_km'];
            $feePerKm = (int) \App\Models\Setting::getValue('medicine_shipping_fee_per_km', 3000);
            $shippingFee = (int) round($distanceKm * $feePerKm);
            $grandTotal = $total + $shippingFee;

            $order = MedicineOrder::create([
                'user_id' => auth()->id(),
                'reference_code' => 'ORD-MED-' . now()->format('ymdHis') . rand(10, 99),
                'total_amount' => $grandTotal,
                'address' => $validated['address'],
                'closest_pharmacy' => $validated['closest_pharmacy'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'distance_km' => $distanceKm,
                'shipping_fee' => $shippingFee,
                'status' => 'pending',
            ]);

            foreach ($itemsToCreate as $item) {
                $item['medicine_order_id'] = $order->id;
                MedicineOrderItem::create($item);
            }

            return $order;
        });

        // Clear cart
        session()->forget('medicine_cart');

        return redirect()->route('medicines.payment', $order);
    }

    public function payment(MedicineOrder $order): View
    {
        abort_unless($order->user_id == auth()->id(), 403);
        abort_unless($order->isPending() && !$order->payment_proof, 404);

        $order->load('items.medicine');

        return view('medicines.payment', [
            'order' => $order,
            'priceLabel' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
        ]);
    }

    public function confirmPayment(Request $request, MedicineOrder $order)
    {
        abort_unless($order->user_id == auth()->id(), 403);
        abort_unless($order->isPending() && !$order->payment_proof, 404);

        $request->validate([
            'sender_identity' => ['required', 'string', 'min:5', 'max:120'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'payment_confirmed' => ['accepted'],
        ], [
            'sender_identity.required' => 'Identitas pengirim wajib diisi.',
            'payment_proof.required' => 'Upload bukti transfer wajib.',
            'payment_proof.mimes' => 'Bukti transfer harus JPG, PNG, WEBP, atau PDF.',
            'payment_confirmed.accepted' => 'Centang konfirmasi bahwa Anda sudah transfer.',
        ]);

        $proofPath = $request->file('payment_proof')->store('medicine-payment-proofs', 'public');

        $order->load('user', 'items.medicine');
        $order->update([
            'sender_identity' => $request->input('sender_identity'),
            'payment_proof'   => $proofPath,
        ]);

        rescue(fn () => $this->notifier->notifyAdminNewMedicineOrder($order));
        rescue(fn () => $this->notifier->notifyPharmacyNewOrder($order));

        return redirect()
            ->route('medicines.status', $order)
            ->with('status', 'Bukti pembayaran obat berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function status(MedicineOrder $order): View
    {
        abort_unless($order->user_id == auth()->id(), 403);
        $order->load('items.medicine');

        return view('medicines.status', [
            'order' => $order,
            'priceLabel' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
        ]);
    }
}
