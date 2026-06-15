<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScreeningCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->screeningSessions()->exists()) {
            return redirect()
                ->route('self-management')
                ->with('error', 'Anda perlu menyelesaikan skrining kesehatan terlebih dahulu sebelum mengakses fitur pemantauan dan rekomendasi personal.');
        }

        return $next($request);
    }
}
