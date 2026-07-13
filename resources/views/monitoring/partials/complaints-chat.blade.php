@props([
    'disease',
    'diseaseInfo',
    'symptoms',
    'severityOptions',
])

@php
    $symptomItems = collect($symptoms)->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values()->all();
    $oldComplaints = old('complaint', []);
@endphp

<x-monitoring.section
    letter="a"
    title="Keluhan"
    :subtitle="\App\Support\MonitoringCopy::complaintsSectionSubtitle($diseaseInfo['label'])"
    accent="rose"
    class="!shadow-card"
>
    <div
        x-data="monitoringComplaintsChat(@js([
            'diseaseLabel' => $diseaseInfo['label'],
            'intro' => \App\Support\MonitoringCopy::complaintsIntro($diseaseInfo['label']),
            'symptoms' => $symptomItems,
            'options' => $severityOptions,
            'oldAnswers' => $oldComplaints,
        ]))"
        class="monitoring-complaints-chat"
    >
        <div class="mb-3" x-show="started || finished" x-cloak>
            <div class="mb-1 flex items-center justify-between text-[10px] font-medium text-slate-400">
                <span x-text="progressLabel"></span>
                <span x-text="progress + '%'"></span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-rose-100">
                <div class="h-full rounded-full bg-brand-600 transition-all duration-300" :style="`width: ${progress}%`"></div>
            </div>
        </div>

        <div
            x-ref="messageList"
            x-show="messages.length > 0"
            x-cloak
            class="monitoring-complaints-chat__messages max-h-[min(24rem,55vh)] space-y-3 overflow-y-auto overscroll-contain rounded-2xl bg-slate-50/80 p-3 ring-1 ring-slate-100"
        >
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2'">
                    <div
                        x-show="msg.role === 'bot'"
                        class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-brand-100"
                    >
                        <x-screening-bot-icon class="h-5 w-5" />
                    </div>
                    <div
                        :class="{
                            'max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs leading-relaxed shadow-sm': true,
                            'rounded-tl-sm border border-brand-100 bg-white text-slate-700': msg.role === 'bot',
                            'rounded-tr-sm bg-brand-600 font-semibold text-white': msg.role === 'user',
                        }"
                        x-text="msg.text"
                    ></div>
                </div>
            </template>
        </div>
        <p x-show="messages.length > 4" x-cloak class="mt-1.5 text-center text-[10px] text-slate-400">Gulir ke atas untuk melihat jawaban sebelumnya</p>

        <div class="mt-3" :class="{ 'mt-0': !started && !finished }">
            <div x-show="!started && !finished" x-cloak>
                <button
                    type="button"
                    @click="start()"
                    class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
                >
                    Mulai catat keluhan
                </button>
            </div>

            <div x-show="started && !introDone && !finished" x-cloak>
                <button
                    type="button"
                    @click="continueAfterIntro()"
                    class="monitoring-btn-primary w-full rounded-2xl py-3 text-sm font-bold"
                >
                    Lanjut
                </button>
            </div>

            <div x-show="started && introDone && !finished && currentSymptom" x-cloak>
                <p class="mb-2 text-center text-[10px] font-medium text-slate-500">Pilih tingkat keluhan</p>
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-50/80 p-1.5">
                    <template x-for="opt in options" :key="opt.value">
                        <button
                            type="button"
                            @click="selectOption(opt)"
                            class="monitoring-complaints-chat__option rounded-xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-brand-300 hover:bg-brand-50 active:scale-[0.98]"
                            x-text="opt.label"
                        ></button>
                    </template>
                </div>
            </div>

            <div x-show="finished" x-cloak class="space-y-3">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Total skor keluhan</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-800" x-text="totalScore"></p>
                </div>
                <x-monitoring.section-save section="complaints" label="Simpan keluhan" ready="finished" />
            </div>
        </div>

        <template x-for="symptom in symptoms" :key="symptom.key">
            <input type="hidden" :name="`complaint[${symptom.key}]`" :value="answers[symptom.key] ?? ''">
        </template>
    </div>
</x-monitoring.section>

<style>[x-cloak]{display:none!important}</style>
