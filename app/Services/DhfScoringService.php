<?php

namespace App\Services;

class DhfScoringService
{
    /**
     * @return list<array{id: string, no: int, text: string, type: string, score_ya: int, options: list<array{value: string, label: string}>}>
     */
    public function questions(): array
    {
        $options = config('dhf_skrining.yes_no_options');
        $prefix = config('dhf_skrining.question_prefix');

        return array_map(function (array $item) use ($options, $prefix) {
            return [
                'id' => $item['id'],
                'no' => $item['no'],
                'text' => $item['text'],
                'type' => 'choice',
                'score_ya' => $item['score_ya'],
                'options' => $options,
                'prompt_prefix' => $prefix,
            ];
        }, config('dhf_skrining.items'));
    }

    public function maxScore(): int
    {
        return (int) array_sum(array_column(config('dhf_skrining.items'), 'score_ya'));
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public function hasWarningSigns(array $answers): bool
    {
        foreach (config('dhf_skrining.warning_sign_ids') as $id) {
            if (($answers[$id] ?? '') === 'ya') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<string>
     */
    public function warningSignLabels(array $answers): array
    {
        $labels = [];

        foreach (config('dhf_skrining.items') as $item) {
            if (! in_array($item['id'], config('dhf_skrining.warning_sign_ids'), true)) {
                continue;
            }
            if (($answers[$item['id']] ?? '') === 'ya') {
                $labels[] = $item['text'];
            }
        }

        return $labels;
    }

    public function hasilKategori(int $total, bool $hasWarningSigns): string
    {
        if ($hasWarningSigns || $total >= 9) {
            return 'Tinggi';
        }

        if ($total >= 5) {
            return 'Sedang';
        }

        return 'Rendah';
    }

    public function risikoLabel(string $hasilKategori): string
    {
        return match ($hasilKategori) {
            'Tinggi' => 'Risiko Tinggi',
            'Sedang' => 'Risiko Sedang',
            default => 'Risiko Rendah',
        };
    }

    public function riskLevelFromResult(int $total, bool $hasWarningSigns, bool $isEmergency): string
    {
        if ($isEmergency) {
            return 'emergency';
        }

        $hasil = $this->hasilKategori($total, $hasWarningSigns);

        return match ($hasil) {
            'Tinggi' => 'high',
            'Sedang' => 'medium',
            default => 'low',
        };
    }

    public function scoreForAnswer(string $answer, int $scoreYa): int
    {
        return $answer === 'ya' ? $scoreYa : 0;
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public function calculateTotal(array $answers): int
    {
        $total = 0;

        foreach (config('dhf_skrining.items') as $item) {
            $total += $this->scoreForAnswer(
                (string) ($answers[$item['id']] ?? ''),
                $item['score_ya'],
            );
        }

        return $total;
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<array{no: int, text: string, jawaban: string, skor_didapat: int, skor_ya: int}>
     */
    public function scoreRows(array $answers): array
    {
        return array_map(function (array $item) use ($answers) {
            $jawaban = (string) ($answers[$item['id']] ?? '');
            $label = $jawaban === 'ya' ? 'Ya' : ($jawaban === 'tidak' ? 'Tidak' : '-');

            return [
                'no' => $item['no'],
                'text' => $item['text'],
                'jawaban' => $label,
                'skor_didapat' => $this->scoreForAnswer($jawaban, $item['score_ya']),
                'skor_ya' => $item['score_ya'],
            ];
        }, config('dhf_skrining.items'));
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public function buildSummary(array $answers): string
    {
        $rows = $this->scoreRows($answers);
        $total = $this->calculateTotal($answers);
        $max = $this->maxScore();
        $hasWarning = $this->hasWarningSigns($answers);
        $hasil = $this->hasilKategori($total, $hasWarning);
        $risiko = $this->risikoLabel($hasil);

        $lines = [
            '📋 Hasil Skrining DHF',
            '',
            "⭐ JUMLAH NILAI AKHIR: {$total} / {$max}",
            "📌 KLASIFIKASI: {$risiko}",
        ];

        if ($hasWarning) {
            $lines[] = '⚠️ Terdapat tanda peringatan (warning signs)';
        }

        $lines[] = '';
        $lines[] = 'No | Gejala | Jawaban | Skor (Ya) | Skor Didapat';
        $lines[] = str_repeat('-', 60);

        foreach ($rows as $row) {
            $lines[] = sprintf(
                '%d | %s | %s | %d | %d',
                $row['no'],
                $row['text'],
                $row['jawaban'],
                $row['skor_ya'],
                $row['skor_didapat'],
            );
        }

        $lines[] = str_repeat('-', 60);
        $lines[] = "TOTAL | | | {$max} | {$total}";

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return array{total: int, max: int, hasil_kategori: string, risiko_label: string, risk_level: string, is_emergency: bool, emergency_symptoms: list<string>, has_warning_signs: bool}
     */
    public function evaluate(array $answers): array
    {
        $total = $this->calculateTotal($answers);
        $max = $this->maxScore();
        $hasWarning = $this->hasWarningSigns($answers);
        $warningLabels = $this->warningSignLabels($answers);

        $isEmergency = $hasWarning;
        $hasilKategori = $this->hasilKategori($total, $hasWarning);
        $risikoLabel = $this->risikoLabel($hasilKategori);

        $riskLevel = $this->riskLevelFromResult($total, $hasWarning, $isEmergency);

        return [
            'total' => $total,
            'max' => $max,
            'hasil_kategori' => $hasilKategori,
            'risiko_label' => $risikoLabel,
            'risk_level' => $riskLevel,
            'is_emergency' => $isEmergency,
            'emergency_symptoms' => $warningLabels,
            'has_warning_signs' => $hasWarning,
        ];
    }
}
