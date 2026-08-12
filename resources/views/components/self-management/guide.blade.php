@props(['guide', 'highlight' => null])

@php
    $levels = config('self_management_diseases.risk_levels', ['Rendah', 'Sedang', 'Tinggi']);
    $emergency = $guide['emergency'] ?? null;
    $defaultTab = in_array($highlight, $levels, true) ? $highlight : 'Rendah';
@endphp

<div x-data="{ activeTab: '{{ $defaultTab }}' }" class="mb-5 space-y-4">
    <!-- Risk Level Tabs -->
    <div class="flex items-center justify-between gap-1 rounded-2xl bg-slate-100 p-1.5 ring-1 ring-slate-200/60">
        @foreach ($levels as $lvl)
            @php
                $isUserLevel = $highlight === $lvl;
            @endphp
            <button
                type="button"
                @click="activeTab = '{{ $lvl }}'"
                :class="activeTab === '{{ $lvl }}'
                    ? 'bg-white text-brand-700 shadow-sm font-bold'
                    : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="relative flex-1 rounded-xl py-2 text-center text-xs transition-all duration-150"
            >
                <span>Risiko {{ $lvl }}</span>
                @if ($isUserLevel)
                    <span class="ml-1 rounded-full bg-brand-600 px-1.5 py-0.5 text-[9px] font-bold text-white">Hasil Anda</span>
                @endif
            </button>
        @endforeach
    </div>

    <!-- Emergency Warning Section -->
    @if ($emergency)
        <div x-show="activeTab === 'Tinggi'" x-transition class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <div class="flex items-start gap-2.5">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-rose-100 text-sm font-bold text-rose-600">⚠️</span>
                <div>
                    <h2 class="text-sm font-bold text-rose-900">{{ $emergency['title'] }}</h2>
                    <ul class="mt-2 space-y-1.5 text-xs text-rose-950">
                        @foreach ($emergency['items'] as $item)
                            <li class="flex gap-2">
                                <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-rose-500"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Blocks per Risk Level -->
    @foreach ($levels as $level)
        @php $block = $guide[$level] ?? null; @endphp
        @if ($block)
            <section
                x-show="activeTab === '{{ $level }}'"
                x-transition
                @class([
                    'rounded-2xl border p-4 transition-all',
                    'border-brand-300 bg-brand-50/70 ring-2 ring-brand-200' => $highlight === $level,
                    'border-slate-200 bg-white' => $highlight !== $level,
                ])
            >
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h2 class="text-sm font-bold text-slate-900">{{ $block['label'] }}</h2>
                    @if ($highlight === $level)
                        <span class="rounded-full bg-brand-600 px-2.5 py-0.5 text-[10px] font-bold text-white shadow-xs">Hasil Skrining Anda</span>
                    @endif
                </div>

                @if (! empty($block['intro']))
                    <p class="mb-3.5 text-xs leading-relaxed text-slate-600">{{ $block['intro'] }}</p>
                @endif

                <div class="space-y-4">
                    @foreach ($block['sections'] as $section)
                        <div class="rounded-xl border border-slate-100 bg-white/80 p-3 shadow-xs">
                            <h3 class="text-xs font-bold text-brand-800">{{ $section['title'] }}</h3>
                            <ul class="mt-2 space-y-1.5 text-xs leading-relaxed text-slate-700">
                                @foreach ($section['items'] as $item)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-0.5 font-bold text-brand-500">•</span>
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
</div>

