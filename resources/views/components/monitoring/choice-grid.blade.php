@props(['name', 'options', 'columns' => 4, 'selected' => null])

@php
    $gridClass = match ((int) $columns) {
        3 => 'monitoring-choice-grid--3',
        2 => 'monitoring-choice-grid--2',
        default => 'monitoring-choice-grid--4',
    };
@endphp

<div @class(['monitoring-choice-grid', $gridClass])>
    @foreach ($options as $option)
        @php $value = $option['value'] ?? ''; @endphp
        <label class="monitoring-choice cursor-pointer">
            <input
                type="radio"
                name="{{ $name }}"
                value="{{ $value }}"
                @checked($selected === $value)
                class="sr-only"
            >
            <span class="monitoring-choice-pill">
                {{ $option['label'] }}
            </span>
        </label>
    @endforeach
</div>
