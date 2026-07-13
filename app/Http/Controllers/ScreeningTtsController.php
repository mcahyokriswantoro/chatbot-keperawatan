<?php

namespace App\Http\Controllers;

use App\Services\ScreeningTtsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RuntimeException;

class ScreeningTtsController extends Controller
{
    public function store(Request $request, ScreeningTtsService $tts): Response|JsonResponse
    {
        set_time_limit((int) config('screening_tts.timeout_seconds', 180) + 30);

        $validated = $request->validate([
            'text' => ['required', 'string', 'max:8000'],
            'gender' => ['nullable', 'string', 'max:30'],
        ]);

        $gender = $validated['gender'] ?? $request->user()?->gender;

        try {
            $audio = $tts->synthesize($validated['text'], $gender);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }

        return response($audio, 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
