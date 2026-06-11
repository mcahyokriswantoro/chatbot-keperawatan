@props(['entry'])

<a href="{{ route('admin.monitoring.show', $entry) }}" class="block rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition active:scale-[0.99]">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="font-bold text-slate-900">{{ $entry->user?->name ?? 'Pengguna' }}</p>
            <p class="mt-0.5 text-[11px] text-slate-400">{{ ($entry->recorded_at ?? $entry->created_at)->translatedFormat('d M Y') }}</p>
        </div>
        <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </div>
    <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
        @if ($entry->bloodPressureLabel())
            <span class="rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-700">TD {{ $entry->bloodPressureLabel() }}</span>
        @endif
        @if ($entry->heart_rate)
            <span class="rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-700">Nadi {{ $entry->heart_rate }}</span>
        @endif
        @if ($entry->dietCompliantLabel())
            <span class="rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-700">Diet {{ $entry->dietCompliantLabel() }}</span>
        @endif
    </div>
    @if ($entry->complaints)
        <p class="mt-2 text-xs leading-relaxed text-slate-600">{{ Str::limit($entry->complaints, 80) }}</p>
    @endif
</a>
