@extends('layouts.admin')

@section('title', 'Tenaga Kesehatan')

@section('content')
    <x-admin.page-banner
        title="Tenaga Kesehatan"
        subtitle="Kelola perawat, dokter, dan profil tampilan di laman user"
        tone="sky"
        :back="route('admin.dashboard')"
    />

    @include('admin.partials.consultation-tabs')

    @if ($setupRequired ?? false)
        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
            <p class="font-bold">Database belum siap</p>
            <p class="mt-1 text-xs leading-relaxed">Tabel tenaga kesehatan belum ada. Jalankan setup sekali di server:</p>
            <ol class="mt-2 list-inside list-decimal text-xs leading-relaxed">
                <li>Upload <code class="rounded bg-amber-100 px-1">deploy/setup-once.php</code> ke <code class="rounded bg-amber-100 px-1">public/setup-once.php</code></li>
                <li>Buka <code class="rounded bg-amber-100 px-1">/setup-once.php?key=ck2026setup</code></li>
                <li>Tunggu "SELESAI", lalu refresh halaman ini</li>
            </ol>
        </div>
    @endif

    <div class="mb-4">
        <a
            href="{{ route('admin.consultations.providers.create') }}"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 py-3 text-sm font-bold text-white shadow-sm hover:bg-sky-700"
        >
            + Tambah tenaga kesehatan
        </a>
    </div>

    <div class="space-y-3">
        @forelse ($providers as $item)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex gap-3">
                    <div class="h-20 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
                        <img src="{{ $item->photoUrl() }}" alt="{{ $item->short_name }}" class="h-full w-full object-cover object-top">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-900">{{ $item->short_name }}</p>
                                <p class="text-xs text-slate-500">{{ $item->specialty ?? $item->title }}</p>
                                <p class="mt-1 text-[10px] text-slate-400">{{ $item->categoryLabel() }} · {{ $item->key }}</p>
                            </div>
                            <span @class([
                                'shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase',
                                'bg-emerald-100 text-emerald-800' => $item->active,
                                'bg-slate-100 text-slate-500' => ! $item->active,
                            ])>
                                {{ $item->active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <dl class="mt-2 grid gap-1 text-[11px] text-slate-600">
                            <div class="flex justify-between gap-2">
                                <dt>WhatsApp</dt>
                                <dd class="font-medium">{{ $item->whatsapp }}</dd>
                            </div>
                            @if ($item->price)
                                <div class="flex justify-between gap-2">
                                    <dt>Harga</dt>
                                    <dd class="font-medium">{{ 'Rp '.number_format($item->price, 0, ',', '.') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('admin.consultations.providers.edit', $item) }}" class="rounded-lg bg-brand-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-brand-700">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('admin.consultations.providers.toggle', $item) }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-[11px] font-semibold text-slate-700 hover:bg-slate-50">
                            {{ $item->active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.consultations.providers.destroy', $item) }}" onsubmit="return confirm('Hapus {{ $item->short_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-sky-200 bg-sky-50/30 px-4 py-10 text-center text-sm text-slate-500">
                Belum ada tenaga kesehatan. Tambah perawat atau dokter untuk ditampilkan ke user.
            </div>
        @endforelse
    </div>

    @if ($providers->hasPages())
        <div class="mt-4">{{ $providers->links() }}</div>
    @endif
@endsection
