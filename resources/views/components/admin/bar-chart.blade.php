@props(['title', 'items' => [], 'suffix' => '', 'max' => null, 'hint' => null])

@php
    $values = collect($items)->pluck('value')->filter(fn ($v) => $v !== null);
    $peak = $max ?? max(1, (float) $values->max());
    $usePercentScale = collect($items)->contains(fn ($item) => array_key_exists('percent', $item));
    $categoryColors = [
        'Baik' => 'admin-bar-chart__badge--baik',
        'Cukup' => 'admin-bar-chart__badge--cukup',
        'Kurang' => 'admin-bar-chart__badge--kurang',
    ];
@endphp

<div class="admin-chart-card">
    <div class="admin-chart-card__head">
        <div>
            <h3 class="admin-chart-card__title">{{ $title }}</h3>
            @if ($hint)
                <p class="admin-chart-card__hint">{{ $hint }}</p>
            @endif
        </div>
    </div>
    @if (count($items) > 0)
        <div class="admin-bar-chart">
            @foreach ($items as $item)
                @php
                    $value = $item['value'] ?? 0;
                    $width = $usePercentScale
                        ? min(100, (float) ($item['percent'] ?? 0))
                        : ($peak > 0 ? min(100, round(($value / $peak) * 100, 1)) : 0);
                    $color = $item['color'] ?? '#0066ff';
                    $category = $item['category'] ?? null;
                @endphp
                <div class="admin-bar-chart__block">
                    <div class="admin-bar-chart__row">
                        <div class="admin-bar-chart__label">{{ $item['label'] }}</div>
                        <div class="admin-bar-chart__track">
                            <div class="admin-bar-chart__fill" style="width: {{ $width }}%; background: {{ $color }};"></div>
                        </div>
                        <div class="admin-bar-chart__value">
                            @if (! empty($item['display']))
                                <span class="admin-bar-chart__metric">{{ $item['display'] }}</span>
                            @else
                                <span class="admin-bar-chart__metric">{{ is_float($value) ? number_format($value, 1) : $value }}{{ $suffix }}</span>
                            @endif
                            @if ($category)
                                <span @class(['admin-bar-chart__badge', $categoryColors[$category] ?? ''])>{{ $category }}</span>
                            @endif
                        </div>
                    </div>
                    @if (! empty($item['breakdown']))
                        <div class="admin-bar-chart__breakdown">
                            @foreach ($item['breakdown'] as $row)
                                <span class="admin-bar-chart__breakdown-item">{{ $row['label'] }} · {{ $row['value'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="admin-chart-card__empty">Belum ada data.</p>
    @endif
</div>
