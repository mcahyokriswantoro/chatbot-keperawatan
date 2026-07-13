@php
    $detectionUrl = auth()->check() ? route('detection.start') : route('login');
    $selfManagementUrl = auth()->check() ? route('self-management') : route('login');
    $consultationUrl = route('consultation.index');

    $items = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => request()->routeIs('home')],
        ['label' => 'Deteksi', 'route' => 'detection-start', 'url' => $detectionUrl, 'active' => request()->routeIs('detection.*')],
        ['label' => 'Self Mgmt', 'route' => 'self-management', 'url' => $selfManagementUrl, 'active' => request()->routeIs('self-management*')],
        ['label' => 'Konsultasi', 'route' => 'consultation.index', 'url' => $consultationUrl, 'active' => request()->routeIs('consultation.*')],
        ['label' => 'Edukasi', 'route' => 'education.index', 'active' => request()->routeIs('education.*')],
        ['label' => 'Profil', 'route' => 'profile.page', 'active' => request()->routeIs('profile.*')],
    ];
@endphp

<nav class="ck-bottom-nav fixed bottom-0 left-0 right-0 z-50 border-t border-brand-100 bg-white/95 shadow-[0_-4px_24px_rgba(0,80,180,0.06)] backdrop-blur-md">
    <div class="mx-auto max-w-md px-1 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
        <ul class="flex items-start justify-between gap-0">
            @foreach ($items as $item)
                <li class="min-w-0 flex-1">
                    <a
                        href="{{ $item['url'] ?? (Route::has($item['route']) ? route($item['route']) : '#') }}"
                        @class([
                            'flex flex-col items-center gap-1 py-1 transition-colors',
                            'text-brand-600' => $item['active'],
                            'text-slate-400 hover:text-brand-500' => ! $item['active'],
                        ])
                    >
                        <span @class([
                            'flex h-8 w-8 items-center justify-center rounded-full transition-all sm:h-9 sm:w-9',
                            'bg-brand-600 text-white shadow-soft' => $item['active'],
                            'bg-white text-slate-400' => ! $item['active'],
                        ])>
                            @include('components.mobile.icons.' . str_replace('.', '-', $item['route']))
                        </span>
                        <span @class([
                            'w-full px-0.5 text-center text-[9px] font-medium leading-tight sm:text-[10px]',
                            'font-semibold text-brand-600' => $item['active'],
                        ])>
                            {{ $item['label'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
