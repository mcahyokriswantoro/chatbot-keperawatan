@props(['summary'])

@php
    $lines = [];

    if ($summary['initial_screenings_count'] > 0) {
        $lines[] = $summary['initial_screenings_count'].' skrining awal';
    }
    if ($summary['disease_screenings_count'] > 0) {
        $text = $summary['disease_screenings_count'].' skrining penyakit';
        if ($summary['screening_diseases'] !== '') {
            $text .= ' ('.$summary['screening_diseases'].')';
        }
        $lines[] = $text;
    }
    if ($summary['daily_monitorings_count'] > 0 || $summary['monthly_monitorings_count'] > 0) {
        $parts = [];
        if ($summary['daily_monitorings_count'] > 0) {
            $parts[] = $summary['daily_monitorings_count'].' harian';
        }
        if ($summary['monthly_monitorings_count'] > 0) {
            $parts[] = $summary['monthly_monitorings_count'].' bulanan';
        }
        $text = 'Monitoring '.implode(', ', $parts);
        if ($summary['monitoring_diseases'] !== '') {
            $text .= ' · '.$summary['monitoring_diseases'];
        }
        $lines[] = $text;
    }
    if ($summary['active_medications_count'] > 0) {
        $lines[] = $summary['active_medications_count'].' obat aktif';
    } elseif ($summary['medications_count'] > 0) {
        $lines[] = $summary['medications_count'].' obat terdaftar';
    }
    if ($summary['self_management_logs_count'] > 0) {
        $text = $summary['self_management_logs_count'].' catatan mandiri';
        if ($summary['completed_self_management_count'] > 0) {
            $text .= ' ('.$summary['completed_self_management_count'].' selesai)';
        }
        $lines[] = $text;
    }

    $subjectStyles = match ($summary['subject_type']) {
        'admin' => [
            'border' => 'border-amber-200',
            'bg' => 'bg-amber-50/50 hover:bg-amber-50',
            'badge' => 'bg-amber-600',
            'ring' => 'ring-amber-100',
        ],
        'guest' => [
            'border' => 'border-slate-200',
            'bg' => 'bg-slate-50/70 hover:bg-slate-50',
            'badge' => 'bg-slate-600',
            'ring' => 'ring-slate-100',
        ],
        default => [
            'border' => 'border-violet-200',
            'bg' => 'bg-violet-50/40 hover:bg-violet-50',
            'badge' => 'bg-violet-600',
            'ring' => 'ring-violet-100',
        ],
    };
@endphp

<a
    href="{{ $summary['detail_url'] }}"
    @class([
        'flex items-start gap-2.5 rounded-xl border px-3 py-2.5 text-left shadow-sm transition active:scale-[0.99]',
        $subjectStyles['border'],
        $subjectStyles['bg'],
    ])
>
    <img
        src="{{ $summary['photo_url'] }}"
        alt=""
        @class(['mt-0.5 h-8 w-8 shrink-0 rounded-lg object-cover ring-1', $subjectStyles['ring']])
    >

    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <p class="truncate text-xs font-semibold text-slate-900">{{ $summary['name'] }}</p>
            <span @class([
                'inline-flex shrink-0 rounded-md px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-white',
                $subjectStyles['badge'],
            ])>
                {{ $summary['subject_label'] }}
            </span>
        </div>
        @if ($lines !== [])
            <ul class="mt-1 space-y-0.5">
                @foreach ($lines as $line)
                    <li class="truncate text-[10px] leading-relaxed text-slate-600">· {{ $line }}</li>
                @endforeach
            </ul>
        @else
            <p class="mt-0.5 text-[10px] text-slate-500">Belum ada aktivitas tercatat.</p>
        @endif
    </div>

    <svg class="mt-1 h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
    </svg>
</a>
