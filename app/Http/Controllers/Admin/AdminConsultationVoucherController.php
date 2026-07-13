<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationProvider;
use App\Models\ConsultationVoucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminConsultationVoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = ConsultationVoucher::query()
            ->latest('created_at')
            ->paginate(20);

        $providers = ConsultationProvider::query()
            ->orderBy('short_name')
            ->get()
            ->map(fn ($p) => ['key' => $p->key, 'label' => $p->short_name]);

        return view('admin.consultations.vouchers', [
            'vouchers' => $vouchers,
            'providers' => $providers,
            'discountOptions' => [100, 50, 25],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:consultation_vouchers,code'],
            'discount_percent' => ['required', 'integer', 'in:100,50,25'],
            'provider_key' => ['nullable', 'string', 'max:50'],
            'max_uses' => ['required', 'integer', 'min:1', 'max:100000'],
            'expires_at' => ['nullable', 'date'],
        ], [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.unique' => 'Kode voucher sudah dipakai.',
            'discount_percent.in' => 'Diskon harus 100%, 50%, atau 25%.',
        ]);

        ConsultationVoucher::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_percent' => (int) $validated['discount_percent'],
            'provider_key' => $validated['provider_key'] ?: null,
            'max_uses' => (int) $validated['max_uses'],
            'uses_count' => 0,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Voucher '.strtoupper(trim($validated['code'])).' berhasil dibuat.');
    }

    public function update(Request $request, ConsultationVoucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'discount_percent' => ['required', 'integer', 'in:100,50,25'],
            'provider_key' => ['nullable', 'string', 'max:50'],
            'max_uses' => ['required', 'integer', 'min:1', 'max:100000'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $voucher->update([
            'discount_percent' => (int) $validated['discount_percent'],
            'provider_key' => $validated['provider_key'] ?: null,
            'max_uses' => (int) $validated['max_uses'],
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Voucher '.$voucher->code.' diperbarui.');
    }

    public function toggle(ConsultationVoucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('status', 'Voucher '.$voucher->code.' '.($voucher->is_active ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function destroy(ConsultationVoucher $voucher): RedirectResponse
    {
        $code = $voucher->code;
        $voucher->delete();

        return back()->with('status', 'Voucher '.$code.' dihapus.');
    }
}
