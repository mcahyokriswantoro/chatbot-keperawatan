<?php

namespace App\Http\Controllers;

use App\Models\ScreeningSession;
use App\Services\ScreeningRiskEvaluator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    public function store(Request $request, ScreeningRiskEvaluator $evaluator): JsonResponse
    {
        $validated = $request->validate([
            'disease' => ['required', 'string', 'max:50'],
            'answers' => ['required', 'array'],
            'summary' => ['required', 'string', 'max:5000'],
            'screening_identity_id' => ['nullable', 'integer', 'exists:screening_identities,id'],
        ]);

        abort_unless(isset(config('diseases')[$validated['disease']]), 422, 'Jenis skrining tidak valid.');

        $risk = $evaluator->evaluate($validated['answers'], $validated['disease']);

        $identityId = $validated['screening_identity_id']
            ?? session("screening_identity.{$validated['disease']}");

        $session = ScreeningSession::create([
            'user_id' => $request->user()?->id,
            'screening_identity_id' => $identityId,
            'disease' => $validated['disease'],
            'answers' => $validated['answers'],
            'summary' => $validated['summary'],
            'risk_level' => $risk['risk_level'],
            'is_emergency' => $risk['is_emergency'],
        ]);

        if ($identityId) {
            session()->forget("screening_identity.{$validated['disease']}");
        }

        return response()->json([
            'id' => $session->id,
            'risk_level' => $risk['risk_level'],
            'is_emergency' => $risk['is_emergency'],
            'emergency_symptoms' => $risk['emergency_symptoms'],
            'emergency_url' => route('emergency'),
        ]);
    }
}
