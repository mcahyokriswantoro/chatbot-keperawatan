<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAccessController extends Controller
{
    public function __construct(
        private AdminAccessService $access,
    ) {}

    public function index(): View
    {
        $providers = \App\Models\ConsultationProvider::tableReady()
            ? \App\Models\ConsultationProvider::query()->where('active', true)->orderBy('short_name')->get()
            : collect();

        $providerAdmins = User::query()
            ->whereNotNull('provider_key')
            ->where('is_admin', false)
            ->latest()
            ->get();

        $eligibleUsers = User::query()
            ->where('is_admin', false)
            ->whereNull('provider_key')
            ->orderBy('name')
            ->get();

        return view('admin.access.index', [
            'admins' => User::query()->where('is_admin', true)->latest()->get(),
            'providers' => $providers,
            'providerAdmins' => $providerAdmins,
            'eligibleUsers' => $eligibleUsers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $user = $this->access->grantByEmail($validated['email']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('admin.access.index')
            ->with('status', "{$user->name} ({$user->email}) sekarang bisa login sebagai admin.");
    }

    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->access->revoke($user, auth()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['access' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.access.index')
            ->with('status', "Akses admin untuk {$user->email} telah dicabut.");
    }

    public function storeProvider(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'provider_key' => ['required', 'string', 'max:60'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'provider_key.required' => 'Tenaga kesehatan wajib dipilih.',
        ]);

        try {
            $user = $this->access->grantProviderAccess($validated['email'], $validated['provider_key']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['email_provider' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('admin.access.index')
            ->with('status', "Akses chat untuk {$user->name} ({$user->email}) berhasil diaktifkan.");
    }

    public function destroyProvider(User $user): RedirectResponse
    {
        $this->access->revokeProviderAccess($user);

        return redirect()
            ->route('admin.access.index')
            ->with('status', "Akses chat untuk {$user->email} telah dicabut.");
    }
}
