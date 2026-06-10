@props(['guide', 'highlight' => null])

@php
    $levels = config('self_management_diseases.risk_levels');
    $emergency = $guide['emergency'] ?? null;
@endphp

@if ($emergency)
    <section class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
        <h2 class="text-sm font-bold text-rose-800">{{ $emergency['title'] }}</h2>
        <ul class="mt-2 space-y-1.5 text-sm text-rose-900">
            @foreach ($emergency['items'] as $item)
                <li class="flex gap-2">
                    <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-rose-500"></span>
                    <span>{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    </section>
@endif

@foreach ($levels as $level)
    @php $block = $guide[$level] ?? null; @endphp
    @if ($block)
        <section @class([
            'mb-4 rounded-2xl border p-4',
            'border-brand-400 bg-brand-50 ring-2 ring-brand-200' => $highlight === $level,
            'border-brand-100 bg-white shadow-card' => $highlight !== $level,
        ])>
            <div class="mb-3 flex items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-slate-900">{{ $block['label'] }}</h2>
                @if ($highlight === $level)
                    <span class="rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-semibold text-white">Hasil skrining Anda</span>
                @endif
            </div>

            @if (! empty($block['intro']))
                <p class="mb-3 text-xs leading-relaxed text-slate-600">{{ $block['intro'] }}</p>
            @endif

            <div class="space-y-4">
                @foreach ($block['sections'] as $section)
                    <div>
                        <h3 class="text-xs font-semibold text-brand-800">{{ $section['title'] }}</h3>
                        <ul class="mt-1.5 space-y-1.5 text-sm leading-relaxed text-slate-700">
                            @foreach ($section['items'] as $item)
                                <li class="flex gap-2">
                                    <span class="text-brand-400">•</span>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endforeach
