<?php

namespace App\Services;

class InitialScreeningService
{
    /**
     * @return list<array{id: string, no: int|string, text: string, type: string, options: list<array{value: string, label: string}>}>
     */
    public function questions(): array
    {
        $options = config('skrining_awal.yes_no_options');

        return array_map(function (array $item) use ($options) {
            return [
                'id' => $item['id'],
                'no' => $item['no'],
                'text' => $item['text'],
                'type' => 'choice',
                'options' => $options,
            ];
        }, config('skrining_awal.items'));
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<string>
     */
    public function recommendedSlugs(array $answers): array
    {
        $recommended = [];

        foreach (config('skrining_awal.routes') as $questionId => $diseases) {
            if (($answers[$questionId] ?? '') !== 'ya') {
                continue;
            }

            foreach ($diseases as $disease) {
                $recommended[] = $disease;
            }
        }

        $unique = array_values(array_unique($recommended));
        $order = config('skrining_awal.disease_order');

        usort($unique, function (string $a, string $b) use ($order) {
            $posA = array_search($a, $order, true);
            $posB = array_search($b, $order, true);

            return ($posA === false ? 999 : $posA) <=> ($posB === false ? 999 : $posB);
        });

        return $unique;
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<array{slug: string, label: string, icon: string, description: string, url: string, triggers: list<string>}>
     */
    public function recommendedDiseases(array $answers): array
    {
        $diseases = config('diseases');
        $items = collect(config('skrining_awal.items'))->keyBy('id');
        $bySlug = [];

        foreach (config('skrining_awal.routes') as $questionId => $slugs) {
            if (($answers[$questionId] ?? '') !== 'ya') {
                continue;
            }

            $item = $items->get($questionId);
            $trigger = $item
                ? "{$item['no']}. {$item['text']}"
                : $questionId;

            foreach ($slugs as $slug) {
                if (! isset($bySlug[$slug])) {
                    $bySlug[$slug] = ['triggers' => []];
                }

                if (! in_array($trigger, $bySlug[$slug]['triggers'], true)) {
                    $bySlug[$slug]['triggers'][] = $trigger;
                }
            }
        }

        return array_values(array_filter(array_map(function (string $slug) use ($diseases, $bySlug) {
            $config = $diseases[$slug] ?? null;
            if (! $config || ($config['advanced'] ?? true) === false) {
                return null;
            }

            return [
                'slug' => $slug,
                'label' => $config['label'],
                'icon' => $config['icon'] ?? '📋',
                'description' => $config['description'] ?? '',
                'url' => route('detection.chat.session', $slug),
                'triggers' => $bySlug[$slug]['triggers'] ?? [],
            ];
        }, $this->recommendedSlugs($answers))));
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return list<array{no: int|string, text: string, jawaban: string}>
     */
    public function answerRows(array $answers): array
    {
        return array_map(function (array $item) use ($answers) {
            $raw = (string) ($answers[$item['id']] ?? '');
            $label = match ($raw) {
                'ya' => 'Ya',
                'tidak' => 'Tidak',
                default => '-',
            };

            return [
                'no' => $item['no'],
                'text' => $item['text'],
                'jawaban' => $label,
            ];
        }, config('skrining_awal.items'));
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    public function buildSummary(array $answers): string
    {
        $rows = $this->answerRows($answers);
        $recommended = $this->recommendedDiseases($answers);

        $lines = [
            '📋 Hasil Skrining Awal',
            '',
        ];

        foreach ($rows as $row) {
            $lines[] = "{$row['no']}. {$row['text']} — {$row['jawaban']}";
        }

        $lines[] = '';
        $lines[] = '📌 Rekomendasi Skrining Lanjut:';

        if ($recommended === []) {
            $lines[] = 'Tidak ada skrining lanjut spesifik berdasarkan jawaban ya. Anda tetap dapat memilih skrining lanjut sesuai kebutuhan.';
        } else {
            foreach ($recommended as $item) {
                $lines[] = '• '.$item['label'];
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>  $answers
     * @return array{
     *     risk_level: string,
     *     is_emergency: bool,
     *     emergency_symptoms: list<string>,
     *     recommended_slugs: list<string>,
     *     recommended_diseases: list<array{slug: string, label: string, icon: string, description: string, url: string}>
     * }
     */
    public function evaluate(array $answers): array
    {
        $recommended = $this->recommendedDiseases($answers);
        $emergencySymptoms = [];

        if (($answers['q18'] ?? '') === 'ya') {
            $emergencySymptoms[] = 'Gejala stroke mendadak (FAST)';
        }

        if (($answers['q14'] ?? '') === 'ya' && ($answers['q13'] ?? '') === 'ya') {
            $emergencySymptoms[] = 'Demam dengan tanda perdarahan (DHF)';
        }

        $isEmergency = $emergencySymptoms !== [];

        return [
            'risk_level' => $isEmergency ? 'emergency' : 'low',
            'is_emergency' => $isEmergency,
            'emergency_symptoms' => $emergencySymptoms,
            'recommended_slugs' => array_column($recommended, 'slug'),
            'recommended_diseases' => $recommended,
        ];
    }
}
