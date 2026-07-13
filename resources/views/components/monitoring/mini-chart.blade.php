@props(['title', 'data', 'field' => 'complaint', 'max' => 30, 'color' => 'brand'])

@php
    $colors = [
        'brand' => ['stroke' => '#0066ff', 'fill' => 'rgba(0, 102, 255, 0.12)'],
        'emerald' => ['stroke' => '#059669', 'fill' => 'rgba(5, 150, 105, 0.12)'],
        'violet' => ['stroke' => '#7c3aed', 'fill' => 'rgba(124, 58, 237, 0.12)'],
    ];
    $palette = $colors[$color] ?? $colors['brand'];

    $count = count($data);
    $yMax = in_array($field, ['compliance', 'self_management'], true)
        ? 100
        : max(1, (int) $max);
    $steps = 4;
    $yTicks = collect(range($steps, 0))
        ->map(fn (int $i) => (int) round($yMax * $i / $steps))
        ->values()
        ->all();

    $leftPct = fn (int $index) => $count > 1 ? ($index / ($count - 1)) * 100 : 50;
    $bottomPct = fn ($value) => $value === null ? 0 : min(100, max(0, ((float) $value / $yMax) * 100));
    $svgY = fn ($value) => 100 - $bottomPct($value);

    $points = [];
    foreach ($data as $i => $point) {
        $value = $point[$field] ?? null;
        $points[] = [
            'value' => $value,
            'date' => $point['date'],
            'left' => $leftPct($i),
            'bottom' => $bottomPct($value),
            'svgY' => $svgY($value),
        ];
    }

    $linePoints = collect($points)->map(fn ($p) => "{$p['left']},{$p['svgY']}")->join(' ');
    $areaPath = $count > 0
        ? 'M '.collect($points)->map(fn ($p) => "{$p['left']},{$p['svgY']}")->join(' L ')
            .' L '.$points[$count - 1]['left'].',100 L '.$points[0]['left'].',100 Z'
        : '';
@endphp

@if ($count > 0)
    <div class="monitoring-line-chart-card">
        <div class="monitoring-line-chart-card__head">
            <h3 class="monitoring-line-chart-card__title">{{ $title }}</h3>
            <span class="monitoring-line-chart-card__meta">{{ $count }} hari</span>
        </div>
        <div class="monitoring-line-chart-card__body">
            <div class="monitoring-line-chart-card__y-axis" aria-hidden="true">
                @foreach ($yTicks as $tick)
                    <span>{{ $tick }}</span>
                @endforeach
            </div>
            <div class="monitoring-line-chart-card__plot-wrap">
                <div class="monitoring-line-chart-card__plot">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        @foreach ($yTicks as $tick)
                            @if ($tick > 0)
                                <line
                                    x1="0"
                                    y1="{{ 100 - (($tick / $yMax) * 100) }}"
                                    x2="100"
                                    y2="{{ 100 - (($tick / $yMax) * 100) }}"
                                    class="monitoring-line-chart-card__grid"
                                />
                            @endif
                        @endforeach
                        @if ($count > 1 && $areaPath !== '')
                            <path d="{{ $areaPath }}" fill="{{ $palette['fill'] }}" stroke="none" />
                            <polyline
                                points="{{ $linePoints }}"
                                fill="none"
                                stroke="{{ $palette['stroke'] }}"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                vector-effect="non-scaling-stroke"
                            />
                        @endif
                    </svg>
                    @foreach ($points as $point)
                        @if ($point['value'] !== null)
                            <div
                                class="monitoring-line-chart-card__point"
                                style="left: {{ $point['left'] }}%; bottom: {{ $point['bottom'] }}%; --point-color: {{ $palette['stroke'] }};"
                            >
                                <span class="monitoring-line-chart-card__value">{{ $point['value'] }}</span>
                                <span class="monitoring-line-chart-card__dot"></span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="monitoring-line-chart-card__x-axis">
                    @foreach ($points as $point)
                        <span style="left: {{ $point['left'] }}%;">{{ $point['date'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
