<?php

namespace App\Http\Controllers;

use App\Models\HomecareBooking;
use App\Models\HomecarePackage;
use App\Services\OrderWhatsAppNotifier;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomecareController extends Controller
{
    public function __construct(
        private OrderWhatsAppNotifier $notifier,
    ) {}
    public function index(): View
    {
        $packages = HomecarePackage::where('active', true)->orderBy('price')->get();

        return view('homecare.index', [
            'packages' => $packages,
        ]);
    }

    public function book(HomecarePackage $package): View
    {
        abort_unless($package->active, 404);

        $transportFeePerKm = (int) \App\Models\Setting::getValue('homecare_transport_fee_per_km', 5000);

        return view('homecare.book', [
            'package' => $package,
            'priceLabel' => 'Rp ' . number_format($package->price, 0, ',', '.'),
            'transportFeePerKm' => $transportFeePerKm,
        ]);
    }

    public function storeBooking(Request $request, HomecarePackage $package)
    {
        abort_unless($package->active, 404);

        $validated = $request->validate([
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:40'],
            'booking_date_only' => ['required', 'date', 'after:today'],
            'booking_time_only' => ['required', 'string', 'regex:/^((09|1[0-6]):(00|30))|(17:00)$/'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'distance_km' => ['nullable', 'numeric', 'min:0'],
        ], [
            'patient_name.required' => 'Nama pasien wajib diisi.',
            'patient_phone.required' => 'Nomor HP pasien wajib diisi.',
            'booking_date_only.required' => 'Tanggal kunjungan wajib diisi.',
            'booking_date_only.after' => 'Tanggal kunjungan minimal esok hari.',
            'booking_time_only.required' => 'Waktu kunjungan wajib diisi.',
            'booking_time_only.regex' => 'Waktu kunjungan harus di antara pukul 09:00 s.d 17:00 WIB (interval 30 menit).',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat lengkap minimal 10 karakter.',
        ]);

        $bookingTime = \Illuminate\Support\Carbon::parse($validated['booking_date_only'] . ' ' . $validated['booking_time_only']);

        $distanceKm = isset($validated['distance_km']) ? (float) $validated['distance_km'] : null;
        $transportFee = null;
        if ($distanceKm !== null) {
            $feePerKm = (int) \App\Models\Setting::getValue('homecare_transport_fee_per_km', 5000);
            $transportFee = (int) round($distanceKm * $feePerKm);
        }

        $booking = HomecareBooking::create([
            'user_id' => auth()->id(),
            'homecare_package_id' => $package->id,
            'reference_code' => 'BK-HC-' . now()->format('ymdHis') . rand(10, 99),
            'patient_name' => $validated['patient_name'],
            'patient_phone' => $validated['patient_phone'],
            'booking_date' => $bookingTime,
            'address' => $validated['address'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'distance_km' => $distanceKm,
            'transport_fee' => $transportFee,
            'status' => 'pending',
        ]);

        return redirect()->route('homecare.payment', $booking);
    }

    public function payment(HomecareBooking $booking): View
    {
        abort_unless($booking->user_id == auth()->id(), 403);
        abort_unless($booking->isPending() && !$booking->payment_proof, 404);

        $booking->load('package');

        $totalPrice = $booking->package->price + ($booking->transport_fee ?? 0);

        return view('homecare.payment', [
            'booking' => $booking,
            'priceLabel' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
        ]);
    }

    public function confirmPayment(Request $request, HomecareBooking $booking)
    {
        abort_unless($booking->user_id == auth()->id(), 403);
        abort_unless($booking->isPending() && !$booking->payment_proof, 404);

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

        $proofPath = $request->file('payment_proof')->store('homecare-payment-proofs', 'public');

        $booking->load('user', 'package');
        $booking->update([
            'sender_identity' => $request->input('sender_identity'),
            'payment_proof'   => $proofPath,
        ]);

        rescue(fn () => $this->notifier->notifyAdminNewHomecareBooking($booking));
        rescue(fn () => $this->notifier->notifyHomecareProviderNewBooking($booking));

        return redirect()
            ->route('homecare.status', $booking)
            ->with('status', 'Bukti pembayaran homecare berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function status(HomecareBooking $booking): View
    {
        abort_unless($booking->user_id == auth()->id(), 403);
        $booking->load('package');

        $totalPrice = $booking->package->price + ($booking->transport_fee ?? 0);

        return view('homecare.status', [
            'booking' => $booking,
            'priceLabel' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
        ]);
    }
}
