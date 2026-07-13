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

        // Jika user adalah penyedia layanan konsultasi (dokter/perawat) dan bukan super admin
        if ($user->provider_key && ! $user->isAdmin()) {
            $currentRouteName = $request->route()?->getName();
            
            $allowedRoutes = [
                'admin.consultations.chat.index',
                'admin.consultations.chat.show',
                'admin.consultations.chat.messages',
                'admin.consultations.chat.reply',
            ];

            if (! in_array($currentRouteName, $allowedRoutes, true)) {
                // Jika mengakses base admin (/admin), redirect ke chat konsultasi
                if ($request->is('admin') || $request->is('admin/')) {
                    return redirect()->route('admin.consultations.chat.index');
                }
                
                abort(403, 'Anda hanya memiliki akses untuk menu Chat Konsultasi.');
            }
        } elseif (! $user->isAdmin()) {
            abort(403, 'Akses admin diperlukan.');
        }

        return $next($request);
    }
}
