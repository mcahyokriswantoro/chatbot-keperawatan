<?php

namespace App\Services;

class ScreeningRiskEvaluator
{
    /**
     * @param  array<string, mixed>  $answers
     * @return array{risk_level: string, is_emergency: bool, emergency_symptoms: list<string>}
     */
    public function evaluate(array $answers, ?string $disease = null): array
    {
        if ($disease === 'tb_paru') {
            $result = app(TbParuScoringService::class)->evaluate($answers);

            return [
                'risk_level' => $result['risk_level'],
                'is_emergency' => $result['is_emergency'],
                'emergency_symptoms' => $result['emergency_symptoms'],
                'total_score' => $result['total'],
                'max_score' => $result['max'],
                'hasil_kategori' => $result['hasil_kategori'],
            ];
        }

        if ($disease === 'dhf') {
            $result = app(DhfScoringService::class)->evaluate($answers);

            return [
                'risk_level' => $result['risk_level'],
                'is_emergency' => $result['is_emergency'],
                'emergency_symptoms' => $result['emergency_symptoms'],
                'total_score' => $result['total'],
                'max_score' => $result['max'],
                'hasil_kategori' => $result['hasil_kategori'],
                'risiko_label' => $result['risiko_label'],
            ];
        }

        $emergencySymptoms = $this->detectEmergency($answers, $disease);
        $isEmergency = count($emergencySymptoms) > 0;
        $riskLevel = 'low';

        if ($isEmergency) {
            $riskLevel = 'emergency';
        } elseif ($this->hasHighRiskAnswers($answers, $disease)) {
            $riskLevel = 'high';
        } elseif ($this->hasMediumRiskAnswers($answers, $disease)) {
            $riskLevel = 'medium';
        }

        return [
            'risk_level' => $riskLevel,
            'is_emergency' => $isEmergency,
            'emergency_symptoms' => $emergencySymptoms,
        ];
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<string>
     */
    private function detectEmergency(array $answers, ?string $disease): array
    {
        $flags = [];

        $multiKeys = ['fast', 'gejala_jantung', 'gejala_dhf', 'gejala_tb', 'gejala_ginjal', 'gejala_dm', 'gejala_ht'];
        foreach ($multiKeys as $key) {
            $values = $answers[$key] ?? [];
            if (! is_array($values)) {
                $values = [$values];
            }
            if (in_array('tidak_ada', $values, true)) {
                continue;
            }

            if ($key === 'fast' && array_intersect(['wajah', 'lengan', 'bicara'], $values)) {
                $flags[] = 'Gejala stroke (FAST)';
            }
            if ($key === 'gejala_jantung' && array_intersect(['sesak', 'kebas'], $values)) {
                $flags[] = 'Gejala jantung berat';
            }
            if ($key === 'gejala_jantung' && ($answers['nyeri_dada'] ?? '') === 'ya_berat') {
                $flags[] = 'Nyeri dada hebat';
            }
        }

        if (($answers['nyeri_dada'] ?? '') === 'ya_berat') {
            $flags[] = 'Nyeri dada hebat';
        }
        if (($answers['tanda_berat'] ?? '') === 'ya') {
            $flags[] = 'Tanda bahaya DHF';
        }
        if (($answers['dahak_darah'] ?? '') === 'ya') {
            $flags[] = 'Batuk berdarah';
        }
        if (($answers['sesak'] ?? '') === 'ya_berat') {
            $flags[] = 'Sesak napas berat';
        }
        if ($disease === 'stroke' && ($answers['waktu_mulai'] ?? '') === '<24jam') {
            $fast = $answers['fast'] ?? [];
            if (is_array($fast) && ! in_array('tidak_ada', $fast, true) && count($fast) > 0) {
                $flags[] = 'Stroke mendadak < 24 jam';
            }
        }

        return array_values(array_unique($flags));
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    private function hasHighRiskAnswers(array $answers, ?string $disease): bool
    {
        if (($answers['batuk_lama'] ?? '') === 'ya' && ($answers['dahak_darah'] ?? '') === 'ya') {
            return true;
        }
        if (($answers['gula_terukur'] ?? '') === 'ya' || ($answers['tekanan_terukur'] ?? '') === 'ya') {
            return true;
        }
        if (($answers['nyeri_dada'] ?? '') === 'ya_berat') {
            return true;
        }

        return match ($disease) {
            'diabetes_melitus' => in_array('keluarga', (array) ($answers['faktor_risiko'] ?? []), true)
                && ! in_array('tidak_ada', (array) ($answers['gejala_dm'] ?? []), true),
            'hipertensi' => ($answers['tekanan_terukur'] ?? '') === 'ya',
            default => false,
        };
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    private function hasMediumRiskAnswers(array $answers, ?string $disease): bool
    {
        if (($answers['batuk_lama'] ?? '') === 'ya') {
            return true;
        }
        if (($answers['demam_mendadak'] ?? '') === 'ya') {
            return true;
        }
        if (($answers['batuk_kronis'] ?? '') === 'ya') {
            return true;
        }

        foreach (['gejala_tb', 'gejala_dhf', 'gejala_dm', 'gejala_ht', 'gejala_ginjal', 'faktor_risiko'] as $key) {
            $values = $answers[$key] ?? [];
            if (! is_array($values)) {
                continue;
            }
            if (count(array_diff($values, ['tidak_ada'])) > 0) {
                return true;
            }
        }

        return false;
    }
}
