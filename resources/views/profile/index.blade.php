@extends('layouts.mobile')

@php
    $tips = config('health.chatbot_tips');
@endphp

@section('content')
<div
    x-data="{
        tipIndex: 0,
        tips: @js($tips),
        init() {
            setInterval(() => {
                this.tipIndex = (this.tipIndex + 1) % this.tips.length;
            }, 5000);
        },
    }"
    class="space-y-6"
>
    @auth
        @php
            $user = auth()->user();
            $firstName = explode(' ', trim($user->name))[0];
            $genderLabel = $user->genderLabel();
        @endphp

        {{-- Hero profil --}}
        <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 via-white to-brand-100/80 px-4 pb-5 pt-4 shadow-soft ring-1 ring-brand-100/60">
            <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-brand-200/30"></div>
            <div class="pointer-events-none absolute -bottom-4 left-1/4 h-20 w-20 rounded-full bg-brand-100/50"></div>

            <div class="relative flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-sm ring-2 ring-brand-100">
                    <x-app.medical-note-icon class="h-8 w-8" />
                </div>
                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-xs font-medium text-slate-500">Profil Pasien 👋</p>
                    <h1 class="text-xl font-bold leading-tight text-slate-900">Halo, {{ $firstName }}!</h1>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Data Anda aman dan siap mendukung layanan chatbot keperawatan 💙
                    </p>
                </div>
                <div class="relative h-20 w-20 shrink-0 animate-[float_3s_ease-in-out_infinite]">
                    <img
                        src="{{ asset('images/robot.png') }}"
                        alt=""
                        class="h-full w-full object-contain drop-shadow-md"
                    />
                </div>
            </div>

            {{-- Kartu identitas --}}
            <div class="relative mt-4 overflow-hidden rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-brand-100/80 backdrop-blur-sm">
                <div class="flex items-center gap-4">
                    <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-white shadow-lg ring-4 ring-brand-50">
                        <img
                            src="{{ $user->profilePhotoUrl() }}"
                            alt="Foto profil {{ $user->name }}"
                            class="h-full w-full object-cover"
                        />
                        <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs shadow-md ring-2 ring-brand-100" aria-hidden="true">🩺</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                        @if ($user->phone)
                            <p class="mt-0.5 truncate text-xs text-brand-600">{{ $user->phone }}</p>
                        @endif
                    </div>
                </div>

                @if ($genderLabel || $user->age || $user->occupation)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($genderLabel)
                            <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100">
                                {{ $genderLabel }}
                            </span>
                        @endif
                        @if ($user->age)
                            <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-semibold text-violet-700 ring-1 ring-violet-100">
                                {{ $user->age }} tahun
                            </span>
                        @endif
                        @if ($user->occupation)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                {{ $user->occupation }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        {{-- Statistik kesehatan --}}
        <section>
            <h2 class="mb-3 text-base font-bold text-slate-900">Ringkasan Kesehatan</h2>
            <div class="grid grid-cols-3 gap-2">
                <div class="rounded-2xl border border-brand-50 bg-white p-3 text-center shadow-sm">
                    <span class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50">
                        <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                    </span>
                    <p class="text-[10px] font-medium text-slate-500">Skrining</p>
                    <p class="mt-0.5 text-lg font-bold text-slate-900">{{ $stats['screening_count'] }}</p>
                </div>

                <div class="rounded-2xl border border-brand-50 bg-white p-3 text-center shadow-sm">
                    <span class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50">
                        <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                    </span>
                    <p class="text-[10px] font-medium text-slate-500">Monitoring</p>
                    <p class="mt-0.5 text-lg font-bold text-slate-900">{{ $stats['monitoring_count'] }}</p>
                </div>

                <div class="rounded-2xl border border-brand-50 bg-white p-3 text-center shadow-sm">
                    <span class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50">
                        <svg class="h-5 w-5 text-rose-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219z"/>
                        </svg>
                    </span>
                    <p class="text-[10px] font-medium text-slate-500">IMT</p>
                    @if ($stats['bmi'])
                        <p class="mt-0.5 text-lg font-bold {{ $stats['bmi_tone'] }}">{{ $stats['bmi'] }}</p>
                        <p class="text-[9px] font-semibold {{ $stats['bmi_tone'] }}">{{ $stats['bmi_label'] }}</p>
                    @else
                        <p class="mt-0.5 text-sm font-bold text-slate-400">—</p>
                    @endif
                </div>
            </div>

            @if ($stats['latest_screening'])
                @php $latest = $stats['latest_screening']; @endphp
                <a
                    href="{{ route('history.show', $latest->id) }}"
                    class="mt-3 flex items-center gap-3 rounded-2xl border border-brand-50 bg-gradient-to-r from-brand-50/80 to-white p-3 shadow-sm transition hover:shadow-md active:scale-[0.99]"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm ring-1 ring-brand-100">
                        <img src="{{ asset('images/unggulan_deteksi.png') }}" alt="" class="h-7 w-7 object-contain" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-brand-600">Skrining Terakhir</p>
                        <p class="truncate text-sm font-bold text-slate-900">{{ $latest->diseaseLabel() ?? 'Deteksi Kesehatan' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $latest->formattedDateTime('d M Y') }} · Risiko {{ $latest->displayRiskLabel() }}</p>
                    </div>
                    <svg class="h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </a>
            @endif
        </section>

        {{-- Data tubuh --}}
        @if ($user->weight || $user->height)
            <section class="overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-brand-50">
                <h2 class="mb-3 text-sm font-bold text-slate-900">Data Antropometri</h2>
                <div class="grid grid-cols-2 gap-3">
                    @if ($user->weight)
                        <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                            <p class="text-[10px] font-medium text-slate-500">Berat Badan</p>
                            <p class="text-sm font-bold text-slate-900">{{ number_format((float) $user->weight, 1) }} <span class="text-xs font-normal text-slate-500">kg</span></p>
                        </div>
                    @endif
                    @if ($user->height)
                        <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                            <p class="text-[10px] font-medium text-slate-500">Tinggi Badan</p>
                            <p class="text-sm font-bold text-slate-900">{{ number_format((float) $user->height, 0) }} <span class="text-xs font-normal text-slate-500">cm</span></p>
                        </div>
                    @endif
                </div>
                @unless ($stats['bmi'])
                    <p class="mt-2 text-[11px] text-slate-500">Lengkapi berat & tinggi badan di edit profil untuk hitung IMT.</p>
                @endunless
            </section>
        @endif

        {{-- Menu akun --}}
        <section>
            <h2 class="mb-3 text-base font-bold text-slate-900">Menu Akun</h2>
            <div class="space-y-2">
                @php
                    $menus = [
                        [
                            'url' => route('dashboard'),
                            'label' => 'Dashboard',
                            'desc' => 'Ringkasan kesehatan Anda',
                            'icon' => 'brand',
                            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>',
                        ],
                        [
                            'url' => route('profile.edit'),
                            'label' => 'Edit Profil',
                            'desc' => 'Perbarui data pribadi',
                            'icon' => 'violet',
                            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>',
                        ],
                        [
                            'url' => route('history'),
                            'label' => 'Riwayat Skrining',
                            'desc' => 'Hasil deteksi sebelumnya',
                            'icon' => 'emerald',
                            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'url' => route('monitoring'),
                            'label' => 'Monitoring Kesehatan',
                            'desc' => 'Catat tanda vital & keluhan',
                            'icon' => 'rose',
                            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z"/>',
                        ],
                        [
                            'url' => route('self-management'),
                            'label' => 'Self Management',
                            'desc' => 'Panduan perawatan mandiri',
                            'icon' => 'amber',
                            'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
                        ],
                    ];

                    $iconBg = [
                        'brand' => 'bg-brand-50 text-brand-600',
                        'violet' => 'bg-violet-50 text-violet-600',
                        'emerald' => 'bg-emerald-50 text-emerald-600',
                        'rose' => 'bg-rose-50 text-rose-600',
                        'amber' => 'bg-amber-50 text-amber-600',
                    ];
                @endphp

                @foreach ($menus as $menu)
                    <a
                        href="{{ $menu['url'] }}"
                        class="group flex items-center gap-3 rounded-2xl border border-brand-50 bg-white px-4 py-3.5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md active:scale-[0.99]"
                    >
                        <span @class(['flex h-11 w-11 shrink-0 items-center justify-center rounded-xl transition group-hover:scale-105', $iconBg[$menu['icon']]])>
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">{!! $menu['svg'] !!}</svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">{{ $menu['label'] }}</p>
                            <p class="text-[11px] text-slate-500">{{ $menu['desc'] }}</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-slate-300 transition group-hover:text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endforeach

                @if ($user->isAdmin())
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="group flex items-center gap-3 rounded-2xl bg-slate-800 px-4 py-3.5 shadow-md transition hover:bg-slate-900 active:scale-[0.99]"
                    >
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-700 text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.217.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-white">Admin Panel</p>
                            <p class="text-[11px] text-slate-400">Kelola konten aplikasi</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="group flex w-full items-center gap-3 rounded-2xl border border-rose-100 bg-gradient-to-r from-rose-50 to-white px-4 py-3.5 shadow-sm transition hover:border-rose-200 hover:shadow-md active:scale-[0.99]"
                    >
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        </span>
                        <div class="min-w-0 flex-1 text-left">
                            <p class="font-semibold text-rose-700">Keluar</p>
                            <p class="text-[11px] text-rose-500">Akhiri sesi chatbot keperawatan</p>
                        </div>
                    </button>
                </form>
            </div>
        </section>

    @else
        {{-- Tamu --}}
        <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 via-white to-brand-100/80 px-4 pb-5 pt-4 shadow-soft ring-1 ring-brand-100/60">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white shadow-sm ring-2 ring-brand-100">
                    <x-app.medical-note-icon class="h-8 w-8" />
                </div>
                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-xs font-medium text-slate-500">Hi, Saya Chatbot 👋</p>
                    <h1 class="text-xl font-bold leading-tight text-slate-900">Profil Keperawatan</h1>
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                        Masuk untuk menyimpan riwayat skrining dan data kesehatan Anda 💙
                    </p>
                </div>
                <div class="relative h-20 w-20 shrink-0 animate-[float_3s_ease-in-out_infinite]">
                    <img src="{{ asset('images/robot.png') }}" alt="" class="h-full w-full object-contain drop-shadow-md" />
                </div>
            </div>
        </header>

        <div class="relative overflow-hidden rounded-3xl bg-white p-6 shadow-sm ring-1 ring-brand-50">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-white shadow-lg shadow-brand-600/15 ring-4 ring-brand-50">
                <x-app.medical-note-icon class="h-14 w-14" />
            </div>
            <p class="mb-5 text-center text-sm leading-relaxed text-slate-600">
                Buat akun gratis untuk akses riwayat deteksi, monitoring kesehatan, dan panduan self management dari chatbot keperawatan.
            </p>
            <a href="{{ route('login') }}" class="mb-2 block w-full rounded-full bg-gradient-to-r from-brand-600 to-brand-500 py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition active:scale-[0.98]">
                Masuk ke Akun
            </a>
            <a href="{{ route('register') }}" class="block w-full rounded-full border-2 border-brand-200 bg-white py-3.5 text-center text-sm font-semibold text-brand-600 transition hover:bg-brand-50 active:scale-[0.98]">
                Daftar Gratis
            </a>
        </div>

        <section class="grid grid-cols-2 gap-2">
            @foreach ([
                ['label' => 'Deteksi Dini', 'icon' => '🔍'],
                ['label' => 'Riwayat Aman', 'icon' => '📋'],
                ['label' => 'Monitoring', 'icon' => '📊'],
                ['label' => 'Edukasi', 'icon' => '📚'],
            ] as $benefit)
                <div class="rounded-2xl border border-brand-50 bg-white p-3 text-center shadow-sm">
                    <span class="text-2xl" aria-hidden="true">{{ $benefit['icon'] }}</span>
                    <p class="mt-1 text-[11px] font-semibold text-slate-700">{{ $benefit['label'] }}</p>
                </div>
            @endforeach
        </section>
    @endauth

    {{-- Tips chatbot --}}
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 ring-1 ring-emerald-100">
        <div class="flex items-center gap-3 p-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center">
                <img src="{{ asset('images/idea.png') }}" alt="" class="h-full w-full object-contain" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Tips dari Chatbot</p>
                <p
                    x-text="tips[tipIndex]"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-0.5 text-xs leading-relaxed text-slate-700"
                ></p>
            </div>
        </div>
        <div class="flex justify-center gap-1.5 pb-3">
            <template x-for="(_, i) in tips" :key="i">
                <button
                    type="button"
                    @click="tipIndex = i"
                    :class="tipIndex === i ? 'w-4 bg-emerald-500' : 'w-1.5 bg-emerald-200'"
                    class="h-1.5 rounded-full transition-all"
                    :aria-label="'Tip ' + (i + 1)"
                ></button>
            </template>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
</style>
@endpush
