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
            'answers' => ['required', 'array'],
            'summary' => ['required', 'string', 'max:5000'],
        ]);

        $risk = $evaluator->evaluate($validated['answers']);

        $session = ScreeningSession::create([
            'user_id' => $request->user()?->id,
            'answers' => $validated['answers'],
            'summary' => $validated['summary'],
            'risk_level' => $risk['risk_level'],
            'is_emergency' => $risk['is_emergency'],
        ]);

        return response()->json([
            'id' => $session->id,
            'risk_level' => $risk['risk_level'],
            'is_emergency' => $risk['is_emergency'],
            'emergency_symptoms' => $risk['emergency_symptoms'],
            'emergency_url' => route('emergency'),
        ]);
    }
}
