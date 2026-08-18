@extends('layouts.admin')

@section('title', 'Rekapitulasi Data Pasien & Skrining')

@section('content')
    <x-admin.page-banner
        title="Rekapitulasi Data Pasien"
        subtitle="Data skrining lengkap, pengukuran antropometri (TB, BB, IMT), skor &amp; tingkat risiko"
        :back="route('admin.users.index')"
        tone="brand"
        :show-actions="false"
    />

    {{-- Stats Cards --}}
    <div class="mb-5 grid grid-cols-3 gap-3">
        <div class="rounded-2xl border border-brand-100 bg-white p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-brand-600">{{ $totalPatients }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Pasien Terdaftar</p>
        </div>
        <div class="rounded-2xl border border-brand-100 bg-white p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-emerald-600">{{ $totalScreenings }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Total Skrining</p>
        </div>
        <div class="rounded-2xl border border-rose-100 bg-white p-4 text-center shadow-sm">
            <p class="text-2xl font-bold text-rose-600">{{ $totalHighRisk }}</p>
            <p class="mt-1 text-[11px] text-slate-500">Risiko Tinggi / Darurat</p>
        </div>
    </div>

    {{-- Action Bar & Filters --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] }}"
                placeholder="Cari pasien, akun, email..."
                class="min-w-[180px] flex-1 rounded-xl border border-brand-200 bg-white px-3 py-2 text-xs focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >

            <select
                name="disease"
                class="rounded-xl border border-brand-200 bg-white px-3 py-2 text-xs text-slate-700 focus:border-brand-400 focus:outline-none"
            >
                <option value="">Semua Penyakit</option>
                @foreach ($diseases as $d)
                    <option value="{{ $d }}" @selected($filters['disease'] === $d)>
                        {{ config("diseases.{$d}.label", $d) }}
                    </option>
                @endforeach
            </select>

            <select
                name="risk"
                class="rounded-xl border border-brand-200 bg-white px-3 py-2 text-xs text-slate-700 focus:border-brand-400 focus:outline-none"
            >
                <option value="">Semua Risiko</option>
                <option value="low" @selected($filters['risk'] === 'low')>Rendah</option>
                <option value="medium" @selected($filters['risk'] === 'medium')>Sedang</option>
                <option value="high" @selected($filters['risk'] === 'high')>Tinggi</option>
                <option value="emergency" @selected($filters['risk'] === 'emergency')>Darurat</option>
            </select>

            <button type="submit" class="rounded-xl bg-brand-600 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-brand-700">
                Filter
            </button>

            @if ($filters['q'] || $filters['disease'] || $filters['risk'])
                <a href="{{ route('admin.users.rekap') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('admin.users.export') }}"
           class="flex shrink-0 items-center gap-1.5 rounded-full border border-emerald-300 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-100 hover:border-emerald-400 active:scale-95">
            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
            </svg>
            Download Excel (.xls)
        </a>
    </div>

    {{-- Recap Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-slate-100 bg-slate-50 text-[11px] font-semibold text-slate-600">
                    <tr>
                        <th class="px-3 py-3 text-center">No</th>
                        <th class="px-3 py-3">Pasien &amp; Akun</th>
                        <th class="px-3 py-3 text-center">Target</th>
                        <th class="px-3 py-3">Data Fisik (TB / BB / IMT)</th>
                        <th class="px-3 py-3">Wilayah</th>
                        <th class="px-3 py-3">Skrining &amp; Waktu</th>
                        <th class="px-3 py-3 text-center">Skor &amp; Kategori</th>
                        <th class="px-3 py-3 text-center">Risiko</th>
                        <th class="px-3 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @php $patientNo = 1; @endphp
                    @forelse ($groupedPatients as $patientKey => $patientSessions)
                        @php
                            $totalSessions = $patientSessions->count();
                            $firstSession = $patientSessions->first();
                            $user = $firstSession->user;
                            $identity = $firstSession->identity;
                            $patientName = $identity?->name ?? ($user?->name ?? 'Pasien');
                            $isSelf = $identity ? $identity->screening_target === 'self' : true;
                            
                            $tb = $identity?->height_cm ?? ($user?->height ? (float)$user->height : null);
                            $bb = $identity?->weight_kg ? (float)$identity->weight_kg : ($user?->weight ? (float)$user->weight : null);
                            $bmi = null;
                            $statusGizi = null;
                            if ($tb && $bb && $tb > 0) {
                                $hM = $tb / 100;
                                $bmi = round($bb / ($hM * $hM), 1);
                                if ($bmi < 18.5) $statusGizi = 'Kurus';
                                elseif ($bmi < 25) $statusGizi = 'Normal';
                                elseif ($bmi < 30) $statusGizi = 'Kelebihan BB';
                                else $statusGizi = 'Obesitas';
                            }
                        @endphp

                        @foreach ($patientSessions->values() as $idx => $s)
                            @php
                                $scoreData = $s->scoreData();
                            @endphp
                            <tr class="transition hover:bg-slate-50/75 {{ $idx === 0 ? 'border-t-2 border-slate-200 bg-white' : 'bg-slate-50/30' }}">
                                @if ($idx === 0)
                                    <td rowspan="{{ $totalSessions }}" class="border-r border-slate-200 px-3 py-3 text-center font-bold text-slate-500 align-top bg-white">
                                        {{ $patientNo }}
                                    </td>
                                    <td rowspan="{{ $totalSessions }}" class="border-r border-slate-200 px-3 py-3 align-top bg-white">
                                        <p class="font-bold text-slate-900 text-sm">{{ $patientName }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            Akun: {{ $user?->name ?? 'Tamu' }}
                                            @if($user?->phone) · {{ $user->phone }}@endif
                                        </p>
                                        <p class="mt-1 text-[10px] font-semibold text-brand-600">
                                            {{ $totalSessions }} riwayat skrining
                                        </p>
                                    </td>
                                    <td rowspan="{{ $totalSessions }}" class="border-r border-slate-200 px-3 py-3 text-center align-top bg-white">
                                        @if ($isSelf)
                                            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100 whitespace-nowrap">
                                                Diri Sendiri
                                            </span>
                                        @else
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-700 ring-1 ring-amber-100 whitespace-nowrap">
                                                Orang Lain
                                            </span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $totalSessions }}" class="border-r border-slate-200 px-3 py-3 align-top bg-white">
                                        @if ($tb || $bb)
                                            <div class="space-y-0.5">
                                                <p class="font-semibold text-slate-800">
                                                    {{ $tb ? $tb . ' cm' : '—' }} / {{ $bb ? $bb . ' kg' : '—' }}
                                                </p>
                                                @if ($bmi)
                                                    <p class="text-[10px] text-slate-500">
                                                        IMT: <span class="font-bold text-slate-700">{{ $bmi }}</span> ({{ $statusGizi }})
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $totalSessions }}" class="border-r border-slate-200 px-3 py-3 text-[11px] text-slate-600 align-top bg-white">
                                        <p class="font-medium text-slate-800">{{ $identity?->regency ?? ($user?->address ? Str::limit($user->address, 25) : '—') }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $identity?->district ?? '' }}</p>
                                    </td>
                                @endif

                                {{-- Screening Specific Columns --}}
                                <td class="px-3 py-2.5">
                                    <p class="font-semibold text-slate-800">{{ $s->diseaseLabel() ?? 'Skrining' }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $s->formattedDateTime() }}</p>
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @if ($scoreData['total'] !== null)
                                        <p class="font-bold text-slate-900">
                                            {{ $scoreData['total'] }}{{ $scoreData['max'] ? '/' . $scoreData['max'] : '' }}
                                        </p>
                                        @if ($scoreData['hasil_kategori'])
                                            <p class="text-[10px] font-medium text-slate-500">{{ $scoreData['hasil_kategori'] }}</p>
                                        @endif
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    @include('admin.partials.risk-badge', ['session' => $s])
                                </td>
                                <td class="px-3 py-2.5 text-center">
                                    <a href="{{ route('admin.screenings.show', $s) }}"
                                       class="inline-flex items-center gap-1 rounded-lg border border-brand-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-brand-700 shadow-xs transition hover:bg-brand-50">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @php $patientNo++; @endphp
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Tidak ada data rekap skrining ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
