@props(['title', 'back' => route('admin.dashboard'), 'subtitle' => null, 'showBack' => true])

@if ($showBack)
    <header class="mb-5 flex items-center gap-3">
        <a href="{{ $back }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand-600 hover:bg-brand-50" aria-label="Kembali">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
        </a>
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-xl font-bold text-slate-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    </header>
@else
    <header class="mb-5">
        <h1 class="text-xl font-bold text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ $subtitle }}</p>
        @endif
    </header>
@endif
