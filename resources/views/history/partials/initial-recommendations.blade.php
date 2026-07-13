@props(['session', 'compact' => false])

@php
    $recommended = $session->recommendedFollowUpDiseases();
@endphp

@if ($recommended !== [])
    <div class="space-y-2">
        <p class="text-xs font-bold text-brand-800">
            {{ $compact ? 'Skrining lanjut:' : 'Skrining Lanjut Direkomendasikan' }}
        </p>

        @foreach ($recommended as $item)
            <a
                href="{{ $item['url'] }}"
                class="flex items-start gap-3 rounded-xl border border-brand-200 bg-white px-3 py-3 transition hover:border-brand-400 hover:bg-brand-50 active:scale-[0.99]"
            >
                <span class="text-xl leading-none">{{ $item['icon'] }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-bold text-slate-900">{{ $item['label'] }}</span>
                    @unless ($compact)
                        <span class="mt-0.5 block text-[10px] text-slate-500">{{ $item['description'] }}</span>
                        @if (! empty($item['triggers']))
                            <ul class="mt-2 space-y-0.5">
                                @foreach (array_slice($item['triggers'], 0, 3) as $trigger)
                                    <li class="flex gap-1 text-[10px] text-brand-700">
                                        <span class="shrink-0">•</span>
                                        <span>{{ $trigger }}</span>
                                    </li>
                                @endforeach
                                @if (count($item['triggers']) > 3)
                                    <li class="text-[10px] text-slate-400">+{{ count($item['triggers']) - 3 }} indikator lainnya</li>
                                @endif
                            </ul>
                        @endif
                    @else
                        <span class="mt-0.5 block text-[10px] text-slate-500">
                            {{ count($item['triggers'] ?? []) }} indikator risiko
                        </span>
                    @endunless
                </span>
                <span class="shrink-0 rounded-full bg-brand-600 px-2.5 py-1 text-[10px] font-semibold text-white">
                    Mulai
                </span>
            </a>
        @endforeach
    </div>
@else
    <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
        Tidak ada skrining lanjut spesifik dari jawaban ya. Tetap waspada gejala baru.
    </p>
@endif
