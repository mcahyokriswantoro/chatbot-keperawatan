@props([
    'complaintMax',
])

<div x-show="preview.chart_data && preview.chart_data.length > 0" x-cloak class="monitoring-monthly-charts space-y-2">
    <h2 class="text-sm font-bold text-slate-900">Grafik per hari</h2>
    <p class="text-[11px] text-slate-500">Catatan harian bulan <span x-text="month"></span></p>

    <template x-for="chart in [
        { title: 'Keluhan', field: 'complaint', color: 'brand', max: @js($complaintMax) },
        { title: 'Kepatuhan minum obat (%)', field: 'compliance', color: 'violet', max: 100 },
        { title: 'Self management (%)', field: 'self_management', color: 'emerald', max: 100 },
    ]" :key="chart.field">
        <div class="monitoring-line-chart-card">
            <div class="monitoring-line-chart-card__head">
                <h3 class="monitoring-line-chart-card__title" x-text="chart.title"></h3>
                <span class="monitoring-line-chart-card__meta" x-text="(preview.chart_data?.length ?? 0) + ' hari'"></span>
            </div>
            <div class="monitoring-line-chart-card__body">
                <div class="monitoring-line-chart-card__y-axis" aria-hidden="true">
                    <template x-for="tick in chartYTicks(chart.field, chart.max)" :key="chart.field + '-y-' + tick">
                        <span x-text="tick"></span>
                    </template>
                </div>
                <div class="monitoring-line-chart-card__plot-wrap">
                    <div class="monitoring-line-chart-card__plot">
                        <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                            <template x-for="tick in chartYTicks(chart.field, chart.max)" :key="chart.field + '-grid-' + tick">
                                <line
                                    x-show="tick > 0"
                                    x1="0"
                                    :y1="chartGridSvgY(tick, chart.field, chart.max)"
                                    x2="100"
                                    :y2="chartGridSvgY(tick, chart.field, chart.max)"
                                    class="monitoring-line-chart-card__grid"
                                />
                            </template>
                            <path
                                x-show="(preview.chart_data?.length ?? 0) > 1"
                                :d="chartAreaPath(chart.field, chart.max)"
                                :fill="chartFill(chart.color)"
                                stroke="none"
                            />
                            <polyline
                                x-show="(preview.chart_data?.length ?? 0) > 1"
                                :points="chartLinePointsSvg(chart.field, chart.max)"
                                fill="none"
                                :stroke="chartStroke(chart.color)"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                vector-effect="non-scaling-stroke"
                            />
                        </svg>
                        <template x-for="(point, index) in preview.chart_data" :key="chart.field + '-' + point.date">
                            <div
                                x-show="point[chart.field] !== null && point[chart.field] !== undefined"
                                class="monitoring-line-chart-card__point"
                                :style="`left: ${chartPlotLeft(index)}%; bottom: ${chartPlotBottom(chart.field, point[chart.field], chart.max)}%; --point-color: ${chartStroke(chart.color)};`"
                            >
                                <span class="monitoring-line-chart-card__value" x-text="point[chart.field]"></span>
                                <span class="monitoring-line-chart-card__dot"></span>
                            </div>
                        </template>
                    </div>
                    <div class="monitoring-line-chart-card__x-axis">
                        <template x-for="(point, index) in preview.chart_data" :key="'x-' + chart.field + '-' + point.date">
                            <span :style="`left: ${chartPlotLeft(index)}%;`" x-text="point.date"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
