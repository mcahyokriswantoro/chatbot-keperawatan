@props([
    'text',
    'gender' => null,
    'class' => '',
])

@php
    $btnClass = 'inline-flex flex-1 items-center justify-center gap-1.5 rounded-2xl border px-3 py-2.5 text-sm font-semibold shadow-sm transition active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40';
    $playClass = $btnClass.' border-brand-200 bg-white text-brand-700 hover:bg-brand-50';
    $controlClass = $btnClass.' border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100';
    $resumeClass = $btnClass.' border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 enabled:ring-2 enabled:ring-emerald-300';
@endphp

<style>
    [data-screening-tts-player][data-tts-state="paused"] [data-tts-action="resume"]:not(:disabled) {
        opacity: 1;
        box-shadow: 0 0 0 2px rgb(110 231 183);
    }
</style>

<div
    data-screening-tts-player
    data-tts-gender="{{ $gender ?? auth()->user()?->gender }}"
    data-tts-state="idle"
    @class(['w-full', $class])
>
    <textarea data-tts-text-content hidden readonly>{{ $text }}</textarea>

    <div class="flex flex-wrap gap-2">
        <button type="button" data-tts-action="play" class="{{ $playClass }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25h4.875c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125H6.75a.75.75 0 01-.75-.75v-4.125a.75.75 0 00-.75-.75H4.5a.75.75 0 010-1.5h1.125a.75.75 0 00.75-.75V9.375c0-.621.504-1.125 1.125-1.125z"/>
            </svg>
            Dengarkan Panduan
        </button>
        <button type="button" data-tts-action="pause" disabled class="{{ $controlClass }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/>
            </svg>
            Jeda
        </button>
        <button type="button" data-tts-action="resume" disabled class="{{ $resumeClass }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.397 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/>
            </svg>
            Lanjutkan
        </button>
        <button type="button" data-tts-action="stop" disabled class="{{ $controlClass }}">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z"/>
            </svg>
            Stop
        </button>
    </div>
</div>
