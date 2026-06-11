@props(['user'])

<a href="{{ route('admin.users.show', $user) }}" class="block rounded-2xl border border-brand-100 bg-white p-4 shadow-sm transition active:scale-[0.99]">
    <div class="flex items-start gap-3">
        <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-2xl object-cover ring-2 ring-brand-100">
        <div class="min-w-0 flex-1">
            <p class="font-bold text-slate-900">{{ $user->name }}</p>
            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $user->email }}</p>
            <p class="mt-1 text-[11px] text-slate-400">{{ $user->phone ?? '—' }} · Daftar {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
        <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
    </div>
    <div class="mt-3 flex gap-2">
        <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100">{{ $user->screening_sessions_count }} skrining</span>
        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 ring-1 ring-emerald-100">{{ $user->health_monitorings_count }} monitor</span>
    </div>
</a>
