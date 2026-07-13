@extends('layouts.admin')

@section('title', 'Chat Konsultasi')

@section('content')
    <x-admin.page-banner
        title="Chat Konsultasi"
        subtitle="Balas pesan pasien — notifikasi WA terkirim saat pasien menulis"
        tone="emerald"
        :back="route('admin.dashboard')"
    />

    @include('admin.partials.consultation-tabs')

    @if ($unreadTotal > 0)
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900">
            {{ $unreadTotal }} pesan belum dibaca
        </div>
    @endif

    @if ($providers->isNotEmpty())
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            <a
                href="{{ route('admin.consultations.chat.index') }}"
                @class([
                    'shrink-0 rounded-lg px-3 py-1.5 text-[11px] font-semibold',
                    'bg-emerald-600 text-white' => ! $providerKey,
                    'border border-slate-200 bg-white text-slate-600' => $providerKey,
                ])
            >Semua</a>
            @foreach ($providers as $item)
                <a
                    href="{{ route('admin.consultations.chat.index', ['provider' => $item->key]) }}"
                    @class([
                        'shrink-0 rounded-lg px-3 py-1.5 text-[11px] font-semibold',
                        'bg-emerald-600 text-white' => $providerKey === $item->key,
                        'border border-slate-200 bg-white text-slate-600' => $providerKey !== $item->key,
                    ])
                >{{ $item->short_name }}</a>
            @endforeach
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($threads as $thread)
            <a
                href="{{ route('admin.consultations.chat.show', $thread) }}"
                class="block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-900">{{ $thread->user?->name ?? 'Pasien' }}</p>
                        <p class="text-xs text-slate-500">{{ $thread->provider_key }} · {{ $thread->reference_code ?? 'Tanpa ref' }}</p>
                        <p class="mt-2 line-clamp-2 text-xs text-slate-600">{{ $thread->message_preview }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if (($thread->unread_count ?? 0) > 0)
                            <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                {{ $thread->unread_count }}
                            </span>
                        @endif
                        <p class="mt-1 text-[10px] text-slate-400">s/d {{ $thread->expires_at?->format('d M H:i') }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/30 px-4 py-10 text-center text-sm text-slate-500">
                Belum ada chat aktif. Chat muncul setelah pasien bayar dan mulai menulis pesan.
            </div>
        @endforelse
    </div>
@endsection
