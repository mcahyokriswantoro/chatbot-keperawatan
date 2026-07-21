<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Akses admin diperlukan.');
        }

        // Jika user adalah penyedia layanan / mitra dan bukan super admin
        if ($user->provider_key && ! $user->isAdmin()) {
            $currentRouteName = $request->route()?->getName();
            
            if ($user->provider_key === 'apotek') {
                // Mitra Apotek hanya boleh mengakses rute obat/apotek
                if (! str_starts_with($currentRouteName, 'admin.medicines.')) {
                    return redirect()->route('admin.medicines.index');
                }
            } elseif ($user->provider_key === 'homecare') {
                // Mitra Homecare hanya boleh mengakses rute homecare
                if (! str_starts_with($currentRouteName, 'admin.homecare.')) {
                    return redirect()->route('admin.homecare.index');
                }
            } else {
                // Mitra Konsultasi Chat (Dokter/Perawat/Dosen)
                $allowedRoutes = [
                    'admin.consultations.chat.index',
                    'admin.consultations.chat.show',
                    'admin.consultations.chat.messages',
                    'admin.consultations.chat.reply',
                ];

                if (! in_array($currentRouteName, $allowedRoutes, true)) {
                    return redirect()->route('admin.consultations.chat.index');
                }
            }
        } elseif (! $user->isAdmin()) {
            abort(403, 'Akses admin diperlukan.');
        }

        return $next($request);
    }
}
