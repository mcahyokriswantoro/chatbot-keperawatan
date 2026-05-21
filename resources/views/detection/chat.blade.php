@extends('layouts.chat')

@section('content')
<div
    x-data="screeningChat(@js($screening))"
    class="flex h-full flex-col"
>
    {{-- Chat header --}}
    <header class="shrink-0 border-b border-brand-100 bg-white/90 backdrop-blur-md px-4 pt-[max(0.75rem,env(safe-area-inset-top))] pb-3">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('detection.start') }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand-600 transition hover:bg-brand-50"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white shadow-soft">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V9h2v7zm4 0h-2V9h2v7z"/>
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-sm font-bold text-slate-900" x-text="config.disease_label ?? config.bot_name"></h1>
                <p class="flex items-center gap-1.5 text-xs text-emerald-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span x-text="'Online · Skrining ' + (config.disease_label ?? 'Kesehatan')"></span>
                </p>
            </div>
        </div>

        {{-- Progress --}}
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between text-[10px] font-medium text-slate-400">
                <span x-text="progressLabel"></span>
                <span x-text="progress + '%'"></span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-brand-100">
                <div
                    class="h-full rounded-full bg-brand-600 transition-all duration-500 ease-out"
                    :style="`width: ${progress}%`"
                ></div>
            </div>
        </div>
    </header>

    {{-- Messages --}}
    <div
        x-ref="messageList"
        class="flex-1 overflow-y-auto overscroll-contain px-4 py-4 space-y-4"
    >
        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2'">
                {{-- Bot avatar --}}
                <template x-if="msg.role === 'bot'">
                    <div class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7v1h-2v-1a5 5 0 00-5-5h-1v1.27A2 2 0 0112 4a2 2 0 00-2 2 2 2 0 104 0 2 2 0 00-2-2c0-1.1.9-2 2-2V3.73A2 2 0 0112 2z"/>
                        </svg>
                    </div>
                </template>

                <div
                    :class="{
                        'max-w-[82%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm': true,
                        'rounded-tl-sm bg-white text-slate-700 border border-brand-100': msg.role === 'bot' && !msg.isResult,
                        'rounded-tr-sm bg-brand-600 text-white': msg.role === 'user',
                        'rounded-tl-sm bg-white border border-brand-200 text-slate-700 w-full max-w-full': msg.isResult,
                    }"
                >
                    <p x-show="!msg.isResult" x-text="msg.text"></p>
                    <div x-show="msg.isResult" class="space-y-2">
                        <p class="font-bold text-brand-700" x-text="config.result.title"></p>
                        <pre class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-slate-600" x-text="msg.text"></pre>
                    </div>
                    <p
                        class="mt-1 text-[10px] opacity-60"
                        :class="msg.role === 'user' ? 'text-right text-blue-100' : 'text-slate-400'"
                        x-text="msg.time"
                    ></p>
                </div>
            </div>
        </template>

        {{-- Typing indicator --}}
        <div x-show="isTyping" x-cloak class="flex items-start gap-2">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7v1h-2v-1a5 5 0 00-5-5h-1v1.27A2 2 0 0112 4a2 2 0 00-2 2 2 2 0 104 0 2 2 0 00-2-2c0-1.1.9-2 2-2V3.73A2 2 0 0112 2z"/>
                </svg>
            </div>
            <div class="rounded-2xl rounded-tl-sm border border-brand-100 bg-white px-4 py-3 shadow-sm">
                <div class="flex gap-1">
                    <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400 [animation-delay:-0.3s]"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400 [animation-delay:-0.15s]"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Input area --}}
    <footer class="shrink-0 border-t border-brand-100 bg-white px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
        {{-- Quick reply (single choice) --}}
        <div x-show="activeOptions.length > 0 && currentStep >= 0 && config.questions[currentStep]?.type !== 'multi'" x-cloak class="mb-3 flex flex-wrap gap-2">
            <template x-for="opt in activeOptions" :key="opt.value">
                <button
                    type="button"
                    @click="selectOption(opt)"
                    class="rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-medium text-brand-700 transition hover:border-brand-400 hover:bg-brand-100 active:scale-95"
                    x-text="opt.label"
                ></button>
            </template>
        </div>

        {{-- Welcome quick reply --}}
        <div x-show="activeOptions.length > 0 && currentStep < 0" x-cloak class="mb-3 flex flex-wrap gap-2">
            <template x-for="opt in activeOptions" :key="opt.value">
                <button
                    type="button"
                    @click="selectOption(opt)"
                    :class="opt.value === 'start'
                        ? 'rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:bg-brand-700 active:scale-95'
                        : 'rounded-full border border-brand-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-brand-50 active:scale-95'"
                    x-text="opt.label"
                ></button>
            </template>
        </div>

        {{-- Multi choice --}}
        <div x-show="activeOptions.length > 0 && currentStep >= 0 && config.questions[currentStep]?.type === 'multi'" x-cloak class="mb-3">
            <div class="mb-2 flex flex-wrap gap-2">
                <template x-for="opt in activeOptions" :key="opt.value">
                    <button
                        type="button"
                        @click="toggleMulti(opt.value, opt.label)"
                        :class="isMultiSelected(opt.value)
                            ? 'rounded-full bg-brand-600 px-4 py-2 text-sm font-medium text-white'
                            : 'rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-medium text-brand-700 hover:bg-brand-100'"
                        x-text="opt.label"
                    ></button>
                </template>
            </div>
            <button
                type="button"
                @click="submitMulti()"
                :disabled="multiSelected.length === 0"
                class="w-full rounded-full bg-brand-600 py-2.5 text-sm font-semibold text-white disabled:opacity-40 disabled:cursor-not-allowed hover:bg-brand-700 active:scale-[0.98]"
            >
                Kirim jawaban
            </button>
        </div>

        {{-- Text input --}}
        <div x-show="showInput && !finished" x-cloak class="flex items-end gap-2">
            <textarea
                x-ref="textInput"
                x-model="textInput"
                rows="1"
                :placeholder="config.questions[currentStep]?.placeholder ?? 'Ketik pesan...'"
                @keydown.enter.prevent="if (!$event.shiftKey) submitText()"
                class="max-h-28 flex-1 resize-none rounded-2xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            ></textarea>
            <button
                type="button"
                @click="submitText()"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white shadow-soft hover:bg-brand-700 active:scale-95"
                aria-label="Kirim"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                </svg>
            </button>
        </div>

        {{-- Emergency banner --}}
        <div x-show="finished && isEmergency" x-cloak class="mb-3 rounded-2xl bg-rose-600 px-4 py-3 text-center text-sm font-semibold text-white">
            <a href="{{ route('emergency') }}" class="underline">Buka halaman Peringatan Darurat →</a>
        </div>

        {{-- Finished actions --}}
        <div x-show="finished" x-cloak class="flex gap-2">
            <a
                href="{{ route('home') }}"
                class="flex-1 rounded-full border border-brand-200 py-2.5 text-center text-sm font-semibold text-brand-600 hover:bg-brand-50"
            >
                Kembali ke Beranda
            </a>
            @auth
                <a
                    href="{{ route('history') }}"
                    class="flex-1 rounded-full border border-brand-200 py-2.5 text-center text-sm font-semibold text-brand-600 hover:bg-brand-50"
                >
                    Riwayat
                </a>
            @endauth
            <button
                type="button"
                @click="window.location.reload()"
                class="flex-1 rounded-full bg-brand-600 py-2.5 text-sm font-semibold text-white hover:bg-brand-700"
            >
                Ulangi Skrining
            </button>
        </div>

        {{-- Idle hint when waiting --}}
        <p
            x-show="!showInput && activeOptions.length === 0 && !finished && !isTyping && messages.length > 0"
            x-cloak
            class="text-center text-[11px] text-slate-400"
        >
            Pilih salah satu opsi di atas untuk melanjutkan
        </p>
    </footer>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
