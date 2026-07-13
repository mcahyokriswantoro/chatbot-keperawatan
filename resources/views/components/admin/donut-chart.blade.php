@props(['title', 'items' => [], 'centerLabel' => 'total'])

@php
    $total = max(1, collect($items)->sum('value'));
    $radius = 36;
    $circumference = 2 * M_PI * $radius;
    $offset = 0;
@endphp

<div class="admin-chart-card">
    <div class="admin-chart-card__head">
        <h3 class="admin-chart-card__title">{{ $title }}</h3>
    </div>
    @if (collect($items)->sum('value') > 0)
        <div class="admin-donut-chart">
            <div class="admin-donut-chart__ring">
                <svg viewBox="0 0 100 100" aria-hidden="true">
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#f1f5f9" stroke-width="14" />
                    @foreach ($items as $item)
                        @php
                            $value = (int) ($item['value'] ?? 0);
                            if ($value <= 0) {
                                continue;
                            }
                            $segment = ($value / $total) * $circumference;
                            $dash = "{$segment} {$circumference}";
                            $color = $item['color'] ?? '#0066ff';
                        @endphp
                        <circle
                            cx="50"
                            cy="50"
                            r="{{ $radius }}"
                            fill="none"
                            stroke="{{ $color }}"
                            stroke-width="14"
                            stroke-dasharray="{{ $dash }}"
                            stroke-dashoffset="{{ -$offset }}"
                            transform="rotate(-90 50 50)"
                            stroke-linecap="butt"
                        />
                        @php $offset += $segment; @endphp
                    @endforeach
                </svg>
                <div class="admin-donut-chart__center">
                    <span class="admin-donut-chart__total">{{ collect($items)->sum('value') }}</span>
                    <span class="admin-donut-chart__meta">{{ $centerLabel }}</span>
                </div>
            </div>
            <div class="admin-donut-chart__legend">
                @foreach ($items as $item)
                    <div class="admin-donut-chart__legend-block">
                        <div class="admin-donut-chart__legend-row">
                            <span class="admin-donut-chart__dot" style="background: {{ $item['color'] ?? '#0066ff' }};"></span>
                            <span class="admin-donut-chart__legend-label">{{ $item['label'] }}</span>
                            <span class="admin-donut-chart__legend-value">
                                {{ $item['value'] ?? 0 }}@if (isset($item['percent'])) ({{ number_format($item['percent'], 1) }}%)@endif
                            </span>
                        </div>
                        @if (! empty($item['breakdown']))
                            <div class="admin-donut-chart__breakdown">
                                @foreach ($item['breakdown'] as $row)
                                    <span class="admin-donut-chart__breakdown-item">{{ $row['label'] }} · {{ $row['value'] }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="admin-chart-card__empty">Belum ada data.</p>
    @endif
</div>
