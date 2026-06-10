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

            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-soft ring-2 ring-brand-100">
                <x-screening-bot-icon class="h-8 w-8" />
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
        class="flex-1 overflow-y-auto overscroll-contain px-4 py-4 pb-2 space-y-4"
    >
        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2'">
                {{-- Bot avatar --}}
                <template x-if="msg.role === 'bot'">
                    <div class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-brand-100">
                        <x-screening-bot-icon class="h-6 w-6" />
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
                    <div x-show="msg.isResult" class="space-y-3">
                        <p class="font-bold text-brand-700" x-text="config.result.title"></p>
                        <div
                            x-show="config.scoring && totalScore !== null"
                            x-cloak
                            class="space-y-2"
                        >
                            <div class="rounded-xl bg-brand-50 border border-brand-200 px-3 py-3 text-center">
                                <p class="text-[10px] font-medium uppercase tracking-wide text-brand-600">Jumlah Skor</p>
                                <p class="text-2xl font-bold text-brand-700" x-text="totalScore + ' / ' + maxScore"></p>
                            </div>
                            <div
                                class="rounded-xl border px-4 py-3 text-center"
                                :class="{
                                    'border-rose-200 bg-rose-50 text-rose-800': hasilKategori === 'Tinggi',
                                    'border-amber-200 bg-amber-50 text-amber-800': hasilKategori === 'Sedang',
                                    'border-emerald-200 bg-emerald-50 text-emerald-800': hasilKategori === 'Rendah',
                                }"
                            >
                                <p class="text-[10px] font-medium uppercase tracking-wide opacity-80">Klasifikasi Risiko</p>
                                <p class="text-xl font-bold" x-text="risikoLabel ?? (hasilKategori ? 'Risiko ' + hasilKategori : '')"></p>
                                <p
                                    x-show="hasWarningSigns"
                                    x-cloak
                                    class="mt-1 text-[10px] font-semibold text-rose-700"
                                >
                                    Terdapat tanda peringatan (warning signs)
                                </p>
                                <p class="mt-1 text-[10px] opacity-70" x-text="config.scoring_legend ?? '≥11 Tinggi · 6–10 Sedang · 0–5 Rendah'"></p>
                            </div>
                        </div>
                        <div x-show="config.scoring && scoreRows.length" x-cloak class="overflow-x-auto rounded-xl border border-brand-100">
                            <table class="w-full min-w-[280px] text-left text-[10px]">
                                <thead class="bg-brand-50 text-brand-800">
                                    <tr>
                                        <th class="px-2 py-1.5 font-semibold">No</th>
                                        <th class="px-2 py-1.5 font-semibold">Item</th>
                                        <th class="px-2 py-1.5 font-semibold text-center">Jawaban</th>
                                        <th class="px-2 py-1.5 font-semibold text-center">Skor (Ya)</th>
                                        <th class="px-2 py-1.5 font-semibold text-center">Skor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-50 text-slate-700">
                                    <template x-for="row in scoreRows" :key="row.no">
                                        <tr>
                                            <td class="px-2 py-1.5 align-top" x-text="row.no"></td>
                                            <td class="px-2 py-1.5 align-top" x-text="row.text"></td>
                                            <td
                                                class="px-2 py-1.5 text-center align-top font-medium"
                                                :class="row.jawaban === 'Ya' ? 'text-emerald-700' : 'text-slate-600'"
                                                x-text="row.jawaban"
                                            ></td>
                                            <td class="px-2 py-1.5 text-center align-top" x-text="row.skor_ya"></td>
                                            <td
                                                class="px-2 py-1.5 text-center align-top font-bold"
                                                :class="row.skor_didapat > 0 ? 'text-brand-700' : 'text-slate-400'"
                                                x-text="row.skor_didapat"
                                            ></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-brand-50 font-bold text-brand-800">
                                    <tr>
                                        <td colspan="3" class="px-2 py-2 text-right">Jumlah</td>
                                        <td class="px-2 py-2 text-center" x-text="maxScore"></td>
                                        <td class="px-2 py-2 text-center" x-text="totalScore"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div
                            x-show="activeSelfManagement"
                            x-cloak
                            class="rounded-xl border border-brand-200 bg-brand-50/80 px-3 py-3 text-left"
                        >
                            <p class="text-xs font-bold text-brand-800" x-text="'Panduan Self-Management — ' + (activeSelfManagement?.label ?? '')"></p>
                            <p
                                x-show="activeSelfManagement?.intro"
                                x-cloak
                                class="mt-2 text-[10px] leading-relaxed text-slate-600"
                                x-text="activeSelfManagement?.intro"
                            ></p>
                            <template x-for="(section, sIdx) in (activeSelfManagement?.sections ?? [])" :key="sIdx">
                                <div class="mt-3">
                                    <p class="text-[11px] font-semibold text-slate-800" x-text="section.title"></p>
                                    <ul class="mt-1 list-inside list-disc space-y-0.5 text-[10px] leading-relaxed text-slate-600">
                                        <template x-for="(item, iIdx) in section.items" :key="iIdx">
                                            <li x-text="item"></li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                            <div x-show="config.self_management?.emergency" x-cloak class="mt-3 rounded-lg border border-rose-200 bg-rose-50 px-2 py-2">
                                <p class="text-[10px] font-bold text-rose-800" x-text="config.self_management?.emergency?.title"></p>
                                <ul class="mt-1 list-inside list-disc space-y-0.5 text-[10px] text-rose-900">
                                    <template x-for="(item, eIdx) in (config.self_management?.emergency?.items ?? [])" :key="eIdx">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <pre
                            x-show="!config.scoring"
                            class="whitespace-pre-wrap font-sans text-xs leading-relaxed text-slate-600"
                            x-text="msg.text"
                        ></pre>
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
            <div class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-brand-100">
                <x-screening-bot-icon class="h-6 w-6" />
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

    {{-- Footer — floating dock --}}
    <footer class="shrink-0 px-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-2">
        <div class="rounded-[1.75rem] border border-white/80 bg-white/95 shadow-[0_-4px_24px_-4px_rgba(0,102,255,0.12),0_8px_32px_-8px_rgba(15,23,42,0.12)] backdrop-blur-xl ring-1 ring-brand-100/60">
            <div class="px-4 py-4">

                {{-- Quick reply (single choice) --}}
                <div x-show="activeOptions.length > 0 && currentStep >= 0 && config.questions[currentStep]?.type !== 'multi'" x-cloak>
                    <div
                        class="flex gap-2 rounded-2xl bg-slate-50/80 p-1.5"
                        :class="config.scoring ? 'grid grid-cols-2' : 'flex-wrap justify-center'"
                    >
                        <template x-for="opt in activeOptions" :key="opt.value">
                            <button
                                type="button"
                                @click="selectOption(opt)"
                                :class="config.scoring && opt.value === 'ya'
                                    ? 'w-full rounded-xl bg-emerald-600 py-3 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700 active:scale-[0.98]'
                                    : config.scoring && opt.value === 'tidak'
                                        ? 'w-full rounded-xl border border-slate-200/80 bg-white py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-[0.98]'
                                        : 'rounded-xl border border-brand-200/80 bg-white px-4 py-2.5 text-sm font-medium text-brand-700 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 active:scale-95'"
                                x-text="opt.label"
                            ></button>
                        </template>
                    </div>
                </div>

                {{-- Welcome quick reply --}}
                <div x-show="activeOptions.length > 0 && currentStep < 0" x-cloak class="flex flex-col gap-2">
                    <template x-for="opt in activeOptions" :key="opt.value">
                        <button
                            type="button"
                            @click="selectOption(opt)"
                            :class="opt.value === 'start'
                                ? 'w-full rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:from-brand-700 hover:to-brand-600 active:scale-[0.98]'
                                : 'w-full rounded-2xl border border-brand-100 bg-white py-3 text-sm font-medium text-slate-600 shadow-sm transition hover:border-brand-200 hover:bg-brand-50 active:scale-[0.98]'"
                            x-text="opt.label"
                        ></button>
                    </template>
                </div>

                {{-- Multi choice --}}
                <div x-show="activeOptions.length > 0 && currentStep >= 0 && config.questions[currentStep]?.type === 'multi'" x-cloak class="space-y-3">
                    <div class="flex flex-wrap gap-2 rounded-2xl bg-slate-50/80 p-2">
                        <template x-for="opt in activeOptions" :key="opt.value">
                            <button
                                type="button"
                                @click="toggleMulti(opt.value, opt.label)"
                                :class="isMultiSelected(opt.value)
                                    ? 'rounded-xl bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-md shadow-brand-600/20'
                                    : 'rounded-xl border border-brand-200/80 bg-white px-4 py-2 text-sm font-medium text-brand-700 shadow-sm hover:bg-brand-50'"
                                x-text="opt.label"
                            ></button>
                        </template>
                    </div>
                    <button
                        type="button"
                        @click="submitMulti()"
                        :disabled="multiSelected.length === 0"
                        class="w-full rounded-2xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40 disabled:shadow-none"
                    >
                        Kirim jawaban
                    </button>
                </div>

                {{-- Text input --}}
                <div x-show="showInput && !finished" x-cloak class="flex items-end gap-2 rounded-2xl bg-slate-50/80 p-1.5">
                    <textarea
                        x-ref="textInput"
                        x-model="textInput"
                        rows="1"
                        :placeholder="config.questions[currentStep]?.placeholder ?? 'Ketik pesan...'"
                        @keydown.enter.prevent="if (!$event.shiftKey) submitText()"
                        class="max-h-28 flex-1 resize-none rounded-xl border-0 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    ></textarea>
                    <button
                        type="button"
                        @click="submitText()"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-700 active:scale-95"
                        aria-label="Kirim"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
                        </svg>
                    </button>
                </div>

                {{-- Emergency banner --}}
                <div x-show="finished && isEmergency" x-cloak class="rounded-2xl bg-gradient-to-r from-rose-600 to-rose-500 px-4 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-rose-500/25">
                    <a href="{{ route('emergency') }}" class="underline underline-offset-2">Buka halaman Peringatan Darurat →</a>
                </div>

                {{-- Finished actions --}}
                <div x-show="finished" x-cloak class="space-y-2">
                    <a
                        x-show="config.self_management"
                        x-cloak
                        href="{{ $screening['self_management_url'] ?? route('login') }}"
                        class="mb-3 flex w-full items-center justify-center gap-2 rounded-2xl border-2 border-brand-500 bg-white py-3.5 text-sm font-semibold text-brand-600 shadow-sm transition hover:bg-brand-50 active:scale-[0.98]"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Self-Management
                    </a>
                    <div x-show="!isEmergency" x-cloak class="space-y-2">
                    <a
                        href="{{ route('detection.chat.session', $screening['disease']) }}"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:from-brand-700 hover:to-brand-600 active:scale-[0.98]"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7m0 0 3.182 3.183"/>
                        </svg>
                        Ulangi Skrining
                    </a>
                    <div class="grid gap-2 {{ auth()->check() ? 'grid-cols-2' : 'grid-cols-1' }}">
                        <a
                            href="{{ route('home') }}"
                            class="rounded-2xl border border-brand-100 bg-white py-3 text-center text-sm font-semibold text-brand-600 shadow-sm transition hover:border-brand-200 hover:bg-brand-50 active:scale-[0.98]"
                        >
                            Beranda
                        </a>
                        @auth
                            <a
                                href="{{ route('history') }}"
                                class="rounded-2xl border border-brand-100 bg-white py-3 text-center text-sm font-semibold text-brand-600 shadow-sm transition hover:border-brand-200 hover:bg-brand-50 active:scale-[0.98]"
                            >
                                Riwayat
                            </a>
                        @endauth
                    </div>
                    </div>
                </div>
                <div x-show="finished && isEmergency" x-cloak class="grid grid-cols-2 gap-2">
                    <a
                        href="{{ route('home') }}"
                        class="rounded-2xl border border-brand-100 bg-white py-3 text-center text-sm font-semibold text-brand-600 shadow-sm transition hover:bg-brand-50"
                    >
                        Beranda
                    </a>
                    @auth
                        <a
                            href="{{ route('history') }}"
                            class="rounded-2xl border border-brand-100 bg-white py-3 text-center text-sm font-semibold text-brand-600 shadow-sm transition hover:bg-brand-50"
                        >
                            Riwayat
                        </a>
                    @endauth
                </div>

                {{-- Idle hint --}}
                <p
                    x-show="!showInput && activeOptions.length === 0 && !finished && !isTyping && messages.length > 0"
                    x-cloak
                    class="rounded-xl bg-brand-50/80 px-3 py-2 text-center text-[11px] font-medium text-slate-500"
                >
                    Pilih salah satu opsi untuk melanjutkan
                </p>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
