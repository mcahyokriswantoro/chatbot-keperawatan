@props([
    'section',
    'label',
    'ready' => 'true',
])

<button
    type="submit"
    name="save_section"
    value="{{ $section }}"
    formnovalidate
    x-bind:disabled="!({{ $ready }})"
    :class="({{ $ready }})
        ? 'monitoring-btn-primary monitoring-btn-primary--ready mt-4 w-full rounded-2xl py-3 text-sm font-bold transition active:scale-[0.99]'
        : 'monitoring-btn-primary monitoring-btn-primary--pending mt-4 w-full rounded-2xl py-3 text-sm font-bold transition active:scale-[0.99]'"
>
    {{ $label }}
</button>
