<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->provider_key && ! $user->isAdmin() && ! $user->isApproved()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'login' => 'Akun mitra Anda belum diverifikasi oleh Admin. Silakan tunggu verifikasi admin sebelum masuk.',
            ]);
        }

        $default = match (true) {
            $user?->isAdmin() => route('admin.dashboard', absolute: false),
            $user?->provider_key === 'perawat',
            $user?->provider_key === 'dokter' => route('admin.consultations.chat.index', absolute: false),
            $user?->provider_key === 'apotek' => route('admin.medicines.index', absolute: false),
            $user?->provider_key === 'homecare' => route('admin.homecare.index', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($default);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
