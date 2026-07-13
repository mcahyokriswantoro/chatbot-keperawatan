@props([
    'diseaseInfo',
    'selfItems',
    'selfManagementOptions',
])

@php
    $items = collect($selfItems)->map(fn ($item, $index) => [
        'index' => $index,
        'question' => \App\Support\MonitoringCopy::selfManagementPrompt($item),
    ])->values()->all();
    $oldAnswers = old('self_management', []);
@endphp

<x-monitoring.section
    letter="d"
    title="Self management hari ini"
    :subtitle="\App\Support\MonitoringCopy::selfManagementSectionSubtitle($diseaseInfo['risk'])"
    accent="amber"
    class="!shadow-card"
>
    <div
        x-data="monitoringSelfManagementChat(@js([
            'riskLevel' => $diseaseInfo['risk'],
            'items' => $items,
            'options' => $selfManagementOptions,
            'oldAnswers' => $oldAnswers,
        ]))"
        class="monitoring-self-management-chat"
    >
        <div class="mb-3" x-show="started || finished" x-cloak>
            <div class="mb-1 flex items-center justify-between text-[10px] font-medium text-slate-400">
                <span x-text="progressLabel"></span>
                <span x-text="progress + '%'"></span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-amber-100">
                <div class="h-full rounded-full bg-amber-500 transition-all duration-300" :style="`width: ${progress}%`"></div>
            </div>
        </div>

        <div
            x-ref="messageList"
            x-show="messages.length > 0"
            x-cloak
            class="monitoring-complaints-chat__messages max-h-[min(24rem,55vh)] space-y-3 overflow-y-auto overscroll-contain rounded-2xl bg-amber-50/30 p-3 ring-1 ring-amber-100"
        >
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2'">
                    <div
                        x-show="msg.role === 'bot'"
                        class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-amber-100"
                    >
                        <x-screening-bot-icon class="h-5 w-5" />
                    </div>
                    <div
                        :class="{
                            'max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs leading-relaxed shadow-sm': true,
                            'rounded-tl-sm border border-amber-100 bg-white text-slate-700': msg.role === 'bot',
                            'rounded-tr-sm bg-amber-500 font-semibold text-white': msg.role === 'user',
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
                    class="w-full rounded-2xl bg-amber-500 py-3 text-sm font-bold text-white shadow-lg transition active:scale-[0.99]"
                >
                    Mulai evaluasi Self management
                </button>
            </div>

            <div x-show="started && !finished && currentItem" x-cloak>
                <p class="mb-2 text-center text-[10px] font-medium text-slate-500">Pilih jawaban Anda</p>
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-amber-50/80 p-1.5">
                    <template x-for="opt in options" :key="opt.value">
                        <button
                            type="button"
                            @click="selectOption(opt)"
                            class="monitoring-complaints-chat__option rounded-xl border border-amber-200 bg-white py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-amber-400 hover:bg-amber-50 active:scale-[0.98]"
                            x-text="opt.label"
                        ></button>
                    </template>
                </div>
            </div>

            <div x-show="finished" x-cloak class="space-y-3">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Self management</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-800" x-text="totalPercent + '%'"></p>
                </div>
                <x-monitoring.section-save section="self_management" label="Simpan self management" ready="finished" />
            </div>
        </div>

        <template x-for="item in items" :key="item.index">
            <input type="hidden" :name="`self_management[${item.index}]`" :value="answers[item.index] ?? ''">
        </template>
    </div>
</x-monitoring.section>

<style>[x-cloak]{display:none!important}</style>
