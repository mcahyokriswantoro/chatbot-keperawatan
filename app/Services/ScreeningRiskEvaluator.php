<?php

namespace App\Services;

class ScreeningRiskEvaluator
{
    /**
     * @param  array<string, mixed>  $answers
     * @return array{risk_level: string, is_emergency: bool, emergency_symptoms: list<string>}
     */
    public function evaluate(array $answers): array
    {
        $symptoms = $answers['symptoms'] ?? [];
        if (! is_array($symptoms)) {
            $symptoms = [$symptoms];
        }

        $emergencySymptoms = [];
        $emergencyMap = [
            'sesak' => 'Sesak napas',
            'nyeri_dada' => 'Nyeri dada',
        ];

        foreach ($emergencyMap as $key => $label) {
            if (in_array($key, $symptoms, true)) {
                $emergencySymptoms[] = $label;
            }
        }

        $isEmergency = count($emergencySymptoms) > 0;

        $riskLevel = 'low';

        if ($isEmergency) {
            $riskLevel = 'emergency';
        } elseif (
            in_array('demam', $symptoms, true)
            || in_array('batuk', $symptoms, true)
            || ($answers['duration'] ?? null) === '>7'
        ) {
            $riskLevel = 'medium';
        }

        if (
            ! $isEmergency
            && in_array($answers['chronic'] ?? '', ['diabetes', 'hipertensi', 'jantung'], true)
            && ! in_array('tidak_ada', $symptoms, true)
        ) {
            $riskLevel = 'high';
        }

        return [
            'risk_level' => $riskLevel,
            'is_emergency' => $isEmergency,
            'emergency_symptoms' => $emergencySymptoms,
        ];
    }
}
