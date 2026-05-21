<?php

namespace App\Services;

class PpokScoringService
{
    /**
     * @return list<array{id: string, no: int, text: string, type: string, score_ya: int, options: list<array{value: string, label: string}>}>
     */
    public function questions(): array
    {
        $options = config('ppok_skrining.yes_no_options');
        $prefix = config('ppok_skrining.question_prefix');

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
        }, config('ppok_skrining.items'));
    }

    public function maxScore(): int
    {
        return (int) array_sum(array_column(config('ppok_skrining.items'), 'score_ya'));
    }

    public function hasilKategori(int $total): string
    {
        if ($total >= 9) {
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

    public function riskLevelFromTotal(int $total): string
    {
        return match ($this->hasilKategori($total)) {
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

        foreach (config('ppok_skrining.items') as $item) {
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
        }, config('ppok_skrining.items'));
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
        $risiko = $this->risikoLabel($hasil);

        $lines = [
            '📋 Hasil Skrining PPOK',
            '',
            "⭐ JUMLAH NILAI AKHIR: {$total} / {$max}",
            "📌 KLASIFIKASI: {$risiko}",
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
     * @return array{total: int, max: int, hasil_kategori: string, risiko_label: string, risk_level: string, is_emergency: bool, emergency_symptoms: list<string>}
     */
    public function evaluate(array $answers): array
    {
        $total = $this->calculateTotal($answers);
        $max = $this->maxScore();
        $hasilKategori = $this->hasilKategori($total);
        $risikoLabel = $this->risikoLabel($hasilKategori);

        return [
            'total' => $total,
            'max' => $max,
            'hasil_kategori' => $hasilKategori,
            'risiko_label' => $risikoLabel,
            'risk_level' => $this->riskLevelFromTotal($total),
            'is_emergency' => false,
            'emergency_symptoms' => [],
        ];
    }
}
