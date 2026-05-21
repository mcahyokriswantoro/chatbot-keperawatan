@props(['type' => 'success'])

@php
    $styles = match($type) {
        'danger' => 'bg-rose-50 border-rose-200 text-rose-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        default => 'bg-emerald-50 border-emerald-200 text-emerald-800',
    };
@endphp

@if (session('status'))
    <div {{ $attributes->merge(['class' => "mb-4 rounded-2xl border px-4 py-3 text-sm {$styles}"]) }}>
        {{ session('status') }}
    </div>
@endif
