@php
    $isProvider = auth()->user()?->provider_key && ! auth()->user()?->isAdmin();

    $providerKey = auth()->user()?->provider_key;
    $items = [];

    if ($isProvider) {
        if ($providerKey === 'apotek') {
            $items = [
                [
                    'label' => 'Kelola Obat',
                    'route' => 'admin.medicines.index',
                    'active' => request()->routeIs('admin.medicines.*'),
                    'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z',
                ]
            ];
        } elseif ($providerKey === 'homecare') {
            $items = [
                [
                    'label' => 'Kelola Homecare',
                    'route' => 'admin.homecare.index',
                    'active' => request()->routeIs('admin.homecare.*'),
                    'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
                ]
            ];
        } else {
            $items = [
                [
                    'label' => 'Chat Konsul',
                    'route' => 'admin.consultations.chat.index',
                    'active' => request()->routeIs('admin.consultations.chat.*'),
                    'icon' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
                ]
            ];
        }
    } else {
        $items = [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => request()->routeIs('admin.dashboard'),
                'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            ],
            [
                'label' => 'Pengguna',
                'route' => 'admin.users.index',
                'active' => request()->routeIs('admin.users.*'),
                'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            ],
            [
                'label' => 'Skrining',
                'route' => 'admin.screenings.index',
                'active' => request()->routeIs('admin.screenings.*'),
                'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z',
            ],
            [
                'label' => 'Monitor',
                'route' => 'admin.monitoring.index',
                'active' => request()->routeIs('admin.monitoring.*'),
                'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
            ],
            [
                'label' => 'Konsul',
                'route' => 'admin.consultations.index',
                'active' => request()->routeIs('admin.consultations.*'),
                'icon' => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
            ],
            [
                'label' => 'Edukasi',
                'route' => 'admin.articles.index',
                'active' => request()->routeIs('admin.articles.*'),
                'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
            ],
        ];
    }
@endphp

<nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-brand-100 bg-white/95 shadow-[0_-4px_24px_rgba(0,80,180,0.06)] backdrop-blur-md">
    <div class="mx-auto max-w-md px-1 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
        <ul class="flex items-start justify-between gap-0.5">
            @foreach ($items as $item)
                <li class="min-w-0 flex-1">
                    <a
                        href="{{ route($item['route']) }}"
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
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                        </span>
                        <span @class([
                            'w-full truncate px-0.5 text-center text-[9px] font-medium leading-tight',
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
