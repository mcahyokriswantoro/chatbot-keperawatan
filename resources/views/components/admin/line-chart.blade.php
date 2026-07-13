@props(['title', 'data' => [], 'color' => '#0066ff', 'meta' => null])

@php
    $count = count($data);
    $values = collect($data)->pluck('value')->filter(fn ($v) => $v !== null);
    $yMax = max(1, (int) $values->max());
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
        $value = $point['value'] ?? null;
        $points[] = [
            'value' => $value,
            'date' => $point['date'] ?? '',
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
    $fill = $color.'1f';
@endphp

<div class="admin-chart-card">
    <div class="admin-chart-card__head">
        <h3 class="admin-chart-card__title">{{ $title }}</h3>
        @if ($meta)
            <span class="admin-chart-card__meta">{{ $meta }}</span>
        @endif
    </div>
    @if ($count > 0)
        <div class="admin-line-chart">
            <div class="admin-line-chart__y-axis" aria-hidden="true">
                @foreach ($yTicks as $tick)
                    <span>{{ $tick }}</span>
                @endforeach
            </div>
            <div class="admin-line-chart__plot-wrap">
                <div class="admin-line-chart__plot">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        @foreach ($yTicks as $tick)
                            @if ($tick > 0)
                                <line
                                    x1="0"
                                    y1="{{ 100 - (($tick / $yMax) * 100) }}"
                                    x2="100"
                                    y2="{{ 100 - (($tick / $yMax) * 100) }}"
                                    class="admin-line-chart__grid"
                                />
                            @endif
                        @endforeach
                        @if ($count > 1 && $areaPath !== '')
                            <path d="{{ $areaPath }}" fill="{{ $fill }}" stroke="none" />
                            <polyline
                                points="{{ $linePoints }}"
                                fill="none"
                                stroke="{{ $color }}"
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
                                class="admin-line-chart__point"
                                style="left: {{ $point['left'] }}%; bottom: {{ $point['bottom'] }}%; --point-color: {{ $color }};"
                            >
                                <span class="admin-line-chart__value">{{ $point['value'] }}</span>
                                <span class="admin-line-chart__dot"></span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="admin-line-chart__x-axis">
                    @foreach ($points as $point)
                        <span style="left: {{ $point['left'] }}%;">{{ $point['date'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <p class="admin-chart-card__empty">Belum ada data.</p>
    @endif
</div>
