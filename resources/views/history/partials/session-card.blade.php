@props(['session', 'compact' => false])

@php
    $theme = $session->riskTheme();
    $score = $session->scoreData();
    $guide = $session->selfManagementGuideBlock();
@endphp

<article class="overflow-hidden rounded-2xl border bg-white shadow-sm {{ $theme['border'] }}">
    <div class="flex">
        <div @class(['w-1.5 shrink-0', $theme['accent']])></div>
        <div class="min-w-0 flex-1 p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-900">{{ $session->diseaseLabel() ?? 'Skrining Kesehatan' }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $session->created_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <span @class(['shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase ring-1', $theme['bg'], $theme['text'], $theme['ring']])>
                    {{ $session->displayRiskLabel() }}
                </span>
            </div>

            {{-- Hasil skor --}}
            <div @class(['mt-3 rounded-xl px-3 py-2.5 ring-1', $theme['bg'], $theme['ring']])>
                @if ($score['total'] !== null)
                    <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                        <p @class(['text-lg font-bold', $theme['text']])>{{ $score['total'] }}@if($score['max'])<span class="text-sm font-semibold opacity-70">/{{ $score['max'] }}</span>@endif</p>
                        <p @class(['text-xs font-semibold', $theme['text']])>{{ $session->scoreLabel() }}</p>
                    </div>
                @else
                    <p @class(['text-sm font-bold', $theme['text']])>{{ $session->scoreLabel() }}</p>
                @endif
                @if ($session->showsEmergencyUi())
                    <p class="mt-1 text-[11px] font-semibold text-rose-700">⚠ Gejala darurat terdeteksi — segera ke fasilitas kesehatan</p>
                @endif
            </div>

            @unless ($compact)
                <p class="mt-3 text-xs leading-relaxed text-slate-600">
                    <span class="font-semibold text-slate-800">Langkah selanjutnya:</span>
                    {{ $session->nextStepMessage() }}
                </p>

                @if ($guide && ! empty($guide['sections'][0]['items']))
                    <ul class="mt-2 space-y-1">
                        @foreach (array_slice($guide['sections'][0]['items'], 0, 2) as $item)
                            <li class="flex gap-2 text-[11px] text-slate-600">
                                <span @class(['mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full', $theme['accent']])></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endunless

            <div class="mt-4 flex flex-wrap gap-2">
                @if ($session->selfManagementUrl())
                    <a
                        href="{{ $session->selfManagementUrl() }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-4 py-2 text-[11px] font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-[0.98]"
                    >
                        Self Management
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif
                @if ($session->showsEmergencyUi())
                    <a
                        href="{{ route('emergency') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-rose-600 px-4 py-2 text-[11px] font-semibold text-white shadow-sm transition hover:bg-rose-700"
                    >
                        Darurat
                    </a>
                @endif
                <a
                    href="{{ route('history.show', $session->id) }}"
                    class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-4 py-2 text-[11px] font-semibold text-slate-600 transition hover:border-brand-200 hover:text-brand-600"
                >
                    Detail
                </a>
            </div>
        </div>
    </div>
</article>
