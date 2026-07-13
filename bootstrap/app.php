<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'screening.completed' => \App\Http\Middleware\EnsureScreeningCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ukuran upload terlalu besar untuk server.'], 413);
            }

            $maxLabel = '120 MB';
            try {
                $maxLabel = round(max(1, (int) config('education.video_max_upload_kb', 122880)) / 1024).' MB';
            } catch (Throwable) {
            }

            return redirect()->back()->withInput()->with(
                'upload_error',
                'Upload gagal: ukuran file melebihi batas server (post_max_size). Maks. aplikasi '.$maxLabel.'. Naikkan limit PHP di hosting, atau gunakan opsi Sudah di server / Link video.'
            );
        });
    })->create();
