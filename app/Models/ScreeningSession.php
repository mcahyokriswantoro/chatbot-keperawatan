<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningSession extends Model
{
    protected $fillable = [
        'user_id',
        'screening_identity_id',
        'disease',
        'answers',
        'summary',
        'risk_level',
        'is_emergency',
    ];

    public function diseaseLabel(): ?string
    {
        if (! $this->disease) {
            return null;
        }

        return config("diseases.{$this->disease}.label");
    }

    /**
     * Level risiko untuk tampilan (TB Paru lama bisa tersimpan sebagai emergency → ditampilkan Tinggi).
     */
    public function displayRiskLevel(): string
    {
        $level = $this->risk_level;

        if ($this->disease === 'tb_paru' && $level === 'emergency') {
            return 'high';
        }

        return $level;
    }

    public function displayRiskLabel(): string
    {
        return match ($this->displayRiskLevel()) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            'emergency' => 'Darurat',
            default => $this->risk_level,
        };
    }

    public function showsEmergencyUi(): bool
    {
        if ($this->disease === 'tb_paru') {
            return false;
        }

        return $this->is_emergency || $this->risk_level === 'emergency';
    }

    /**
     * @return array{total: ?int, max: ?int, hasil_kategori: ?string, risiko_label: ?string}
     */
    public function scoreData(): array
    {
        $answers = $this->answers ?? [];

        if (isset($answers['_total_score'])) {
            return [
                'total' => (int) $answers['_total_score'],
                'max' => isset($answers['_max_score']) ? (int) $answers['_max_score'] : null,
                'hasil_kategori' => $answers['_hasil_kategori'] ?? null,
                'risiko_label' => $answers['_risiko_label'] ?? null,
            ];
        }

        if ($this->disease) {
            $risk = app(\App\Services\ScreeningRiskEvaluator::class)->evaluate($answers, $this->disease);

            return [
                'total' => $risk['total_score'] ?? null,
                'max' => $risk['max_score'] ?? null,
                'hasil_kategori' => $risk['hasil_kategori'] ?? null,
                'risiko_label' => $risk['risiko_label'] ?? null,
            ];
        }

        return [
            'total' => null,
            'max' => null,
            'hasil_kategori' => null,
            'risiko_label' => null,
        ];
    }

    public function scoreLabel(): string
    {
        $score = $this->scoreData();

        if ($score['risiko_label']) {
            return $score['risiko_label'];
        }

        if ($score['hasil_kategori']) {
            return 'Risiko '.$score['hasil_kategori'];
        }

        if ($this->showsEmergencyUi()) {
            return 'Darurat';
        }

        return 'Risiko '.$this->displayRiskLabel();
    }

    public function scoreSummary(): ?string
    {
        $score = $this->scoreData();

        if ($score['total'] === null) {
            return null;
        }

        $parts = ["Skor {$score['total']}"];
        if ($score['max']) {
            $parts[0] .= "/{$score['max']}";
        }
        $parts[] = $this->scoreLabel();

        return implode(' · ', $parts);
    }

    public function selfManagementRiskKey(): ?string
    {
        if ($this->showsEmergencyUi()) {
            return 'Tinggi';
        }

        return match ($this->displayRiskLevel()) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high', 'emergency' => 'Tinggi',
            default => null,
        };
    }

    public function hasSelfManagement(): bool
    {
        return $this->disease !== null
            && isset(config('self_management_diseases.list')[$this->disease]);
    }

    public function selfManagementUrl(): ?string
    {
        if (! $this->hasSelfManagement()) {
            return null;
        }

        $params = ['disease' => $this->disease];
        $riskKey = $this->selfManagementRiskKey();
        if ($riskKey) {
            $params['risk'] = $riskKey;
        }

        return route('self-management.show', $params);
    }

    /**
     * @return array{label: string, intro: string, sections: array<int, array{title: string, items: list<string>}>}|null
     */
    public function selfManagementGuideBlock(): ?array
    {
        if (! $this->hasSelfManagement()) {
            return null;
        }

        $registry = config('self_management_diseases.list');
        $guide = config($registry[$this->disease]['config']);
        $key = $this->selfManagementRiskKey();

        return $key ? ($guide[$key] ?? null) : null;
    }

    public function nextStepMessage(): string
    {
        if ($this->showsEmergencyUi()) {
            return 'Segera ke fasilitas kesehatan atau IGD terdekat. Setelah kondisi stabil, lanjutkan dengan panduan self management.';
        }

        $block = $this->selfManagementGuideBlock();
        if ($block && ! empty($block['intro'])) {
            return $block['intro'];
        }

        return match ($this->displayRiskLevel()) {
            'low' => 'Pertahankan gaya hidup sehat dan lakukan skrining berkala.',
            'medium' => 'Perbaiki pola hidup dan pantau gejala secara rutin.',
            'high' => 'Konsultasi ke tenaga kesehatan dan ikuti panduan perawatan mandiri.',
            default => 'Ikuti panduan self management sesuai tingkat risiko skrining Anda.',
        };
    }

    public function screeningConfigKey(): ?string
    {
        if ($this->disease === null) {
            return null;
        }

        $key = "{$this->disease}_skrining";

        return config()->has($key) ? $key : null;
    }

    /**
     * @return list<array{no: int, text: string, answer: string, answer_label: string, is_positive: bool}>
     */
    public function answerBreakdown(): array
    {
        $configKey = $this->screeningConfigKey();
        if ($configKey === null) {
            return [];
        }

        $items = config("{$configKey}.items", []);
        $answers = $this->answers ?? [];
        $labelMap = ['ya' => 'Ya', 'tidak' => 'Tidak'];

        $rows = [];
        foreach ($items as $item) {
            $raw = (string) ($answers[$item['id']] ?? '');
            if ($raw === '') {
                continue;
            }

            $scoreYa = (int) ($item['score_ya'] ?? 1);
            $isPositive = $raw === 'ya' && $scoreYa > 0;

            $rows[] = [
                'no' => (int) $item['no'],
                'text' => $item['text'],
                'answer' => $raw,
                'answer_label' => $labelMap[$raw] ?? ($raw !== '-' ? ucfirst($raw) : '-'),
                'is_positive' => $isPositive,
            ];
        }

        return $rows;
    }

    public function scoringLegend(): ?string
    {
        $configKey = $this->screeningConfigKey();

        return $configKey ? config("{$configKey}.scoring_legend") : null;
    }

    public function scoreProgressPercent(): ?int
    {
        $score = $this->scoreData();
        if ($score['total'] === null || ! $score['max']) {
            return null;
        }

        return (int) min(100, round(($score['total'] / $score['max']) * 100));
    }

    /**
     * @return array{border: string, bg: string, text: string, ring: string, accent: string}
     */
    public function riskTheme(): array
    {
        if ($this->showsEmergencyUi()) {
            return [
                'border' => 'border-rose-300',
                'bg' => 'bg-rose-50',
                'text' => 'text-rose-800',
                'ring' => 'ring-rose-200',
                'accent' => 'bg-rose-500',
            ];
        }

        return match ($this->displayRiskLevel()) {
            'low' => [
                'border' => 'border-emerald-200',
                'bg' => 'bg-emerald-50',
                'text' => 'text-emerald-800',
                'ring' => 'ring-emerald-100',
                'accent' => 'bg-emerald-500',
            ],
            'medium' => [
                'border' => 'border-amber-200',
                'bg' => 'bg-amber-50',
                'text' => 'text-amber-900',
                'ring' => 'ring-amber-100',
                'accent' => 'bg-amber-500',
            ],
            'high' => [
                'border' => 'border-orange-200',
                'bg' => 'bg-orange-50',
                'text' => 'text-orange-900',
                'ring' => 'ring-orange-100',
                'accent' => 'bg-orange-500',
            ],
            default => [
                'border' => 'border-slate-200',
                'bg' => 'bg-slate-50',
                'text' => 'text-slate-800',
                'ring' => 'ring-slate-100',
                'accent' => 'bg-slate-400',
            ],
        };
    }

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'is_emergency' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identity(): BelongsTo
    {
        return $this->belongsTo(ScreeningIdentity::class, 'screening_identity_id');
    }
}
