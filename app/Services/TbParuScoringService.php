<?php

namespace App\Services;

class TbParuScoringService
{
    /**
     * @return list<array{id: string, no: int, text: string, type: string, score_ya: int, options: list<array{value: string, label: string}>}>
     */
    public function questions(): array
    {
        $options = config('tb_paru_skrining.yes_no_options');

        return array_map(function (array $item) use ($options) {
            return [
                'id' => $item['id'],
                'no' => $item['no'],
                'text' => $item['text'],
                'type' => 'choice',
                'score_ya' => $item['score_ya'],
                'options' => $options,
            ];
        }, config('tb_paru_skrining.items'));
    }

    public function maxScore(): int
    {
        return (int) array_sum(array_column(config('tb_paru_skrining.items'), 'score_ya'));
    }

    public function hasilKategori(int $total): string
    {
        return match (true) {
            $total >= 11 => 'Tinggi',
            $total >= 6 => 'Sedang',
            default => 'Rendah',
        };
    }

    public function riskLevelFromTotal(int $total): string
    {
        return match (true) {
            $total >= 11 => 'high',
            $total >= 6 => 'medium',
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

        foreach (config('tb_paru_skrining.items') as $item) {
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
        }, config('tb_paru_skrining.items'));
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public function buildSummary(array $answers): string
    {
        $rows = $this->scoreRows($answers);
        $total = $this->calculateTotal($answers);
        $max = $this->maxScore();

        $hasil = $this->hasilKategori($total);

        $lines = [
            '📋 Hasil Skrining TB Paru',
            '',
            "⭐ JUMLAH NILAI AKHIR: {$total} / {$max}",
            "📌 HASIL: {$hasil}",
            '',
            'No | Item | Jawaban | Skor (Ya) | Skor Didapat',
            str_repeat('-', 60),
        ];

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
     * @return array{total: int, max: int, risk_level: string, is_emergency: bool, emergency_symptoms: list<string>}
     */
    public function evaluate(array $answers): array
    {
        $total = $this->calculateTotal($answers);
        $max = $this->maxScore();

        $emergencySymptoms = [];
        if (($answers['q07'] ?? '') === 'ya') {
            $emergencySymptoms[] = 'Batuk berdarah';
        }

        $isEmergency = count($emergencySymptoms) > 0;

        $hasilKategori = $this->hasilKategori($total);

        $riskLevel = $isEmergency ? 'emergency' : $this->riskLevelFromTotal($total);

        return [
            'total' => $total,
            'max' => $max,
            'hasil_kategori' => $hasilKategori,
            'risk_level' => $riskLevel,
            'is_emergency' => $isEmergency,
            'emergency_symptoms' => $emergencySymptoms,
        ];
    }
}
