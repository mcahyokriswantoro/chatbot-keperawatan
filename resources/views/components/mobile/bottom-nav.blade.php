@php
    $items = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => request()->routeIs('home')],
        ['label' => 'Profil', 'route' => 'profile.page', 'active' => request()->routeIs('profile.page')],
        ['label' => 'Riwayat', 'route' => 'history', 'active' => request()->routeIs('history')],
        // Sementara disembunyikan:
        // ['label' => 'Self Management', 'route' => 'self-management', 'active' => request()->routeIs('self-management')],
        // ['label' => 'Monitoring', 'route' => 'monitoring', 'active' => request()->routeIs('monitoring')],
        ['label' => 'Bantuan', 'route' => 'help', 'active' => request()->routeIs('help')],
    ];
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-brand-100 shadow-[0_-4px_24px_rgba(0,80,180,0.06)]">
    <div class="mx-auto max-w-md px-2 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
        <ul class="flex items-start justify-between gap-0.5">
            @foreach ($items as $item)
                <li class="flex-1 min-w-0">
                    <a
                        href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                        @class([
                            'flex flex-col items-center gap-1 py-1 transition-colors',
                            'text-brand-600' => $item['active'],
                            'text-slate-400 hover:text-brand-500' => ! $item['active'],
                        ])
                    >
                        <span @class([
                            'flex h-10 w-10 items-center justify-center rounded-full transition-all',
                            'bg-brand-600 text-white shadow-soft' => $item['active'],
                            'bg-white text-slate-400' => ! $item['active'],
                        ])>
                            @include('components.mobile.icons.' . str_replace('.', '-', $item['route']))
                        </span>
                        <span @class([
                            'text-[9px] font-medium leading-tight text-center truncate w-full px-0.5',
                            'text-brand-600 font-semibold' => $item['active'],
                        ])>
                            {{ $item['label'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
