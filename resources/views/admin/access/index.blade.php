@extends('layouts.admin')

@section('title', 'Akses Admin & Verifikasi Mitra')

@section('content')
    <x-admin.page-banner
        title="Kelola Akses Admin & Mitra"
        subtitle="Verifikasi pendaftaran mitra baru dan kelola akses admin/tenaga kesehatan"
        tone="rose"
    />

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('access'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ $errors->first('access') }}
        </div>
    @endif

    {{-- Pending Mitra Verifications Section --}}
    <section class="mb-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span>Verifikasi Pendaftaran Mitra Baru</span>
                @if ($pendingProviders->isNotEmpty())
                    <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-bold text-white">
                        {{ $pendingProviders->count() }} Menunggu
                    </span>
                @endif
            </h2>
        </div>

        <div class="space-y-3">
            @if ($pendingProviders->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 py-6 text-center text-xs text-slate-400">
                    <p class="font-medium text-slate-500">Tidak ada permintaan verifikasi mitra baru.</p>
                    <p class="mt-1 text-[11px] text-slate-400">Pendaftaran mitra (Perawat, Dokter, Apotek, Homecare) yang perlu disetujui akan muncul di sini.</p>
                </div>
            @else
                @foreach ($pendingProviders as $pending)
                    @php
                        $roleLabel = match ($pending->provider_key) {
                            'perawat' => 'Perawat',
                            'dokter' => 'Dokter',
                            'apotek' => 'Mitra Apotek',
                            'homecare' => 'Mitra Homecare',
                            default => ucfirst($pending->provider_key),
                        };

                        $roleBadgeColor = match ($pending->provider_key) {
                            'perawat' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                            'dokter' => 'bg-blue-50 text-blue-700 ring-blue-200',
                            'apotek' => 'bg-amber-50 text-amber-700 ring-amber-200',
                            'homecare' => 'bg-violet-50 text-violet-700 ring-violet-200',
                            default => 'bg-slate-50 text-slate-700 ring-slate-200',
                        };
                    @endphp
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0 flex-1">
                                <img src="{{ $pending->profilePhotoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-2xl object-cover ring-2 ring-amber-200">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-bold text-slate-900">{{ $pending->name }}</p>
                                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-semibold ring-1 {{ $roleBadgeColor }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </div>
                                    <p class="truncate text-xs text-slate-600 mt-0.5">📧 {{ $pending->email }} | 📞 {{ $pending->phone }}</p>
                                    @if ($pending->occupation)
                                        <p class="mt-1 text-xs font-medium text-slate-700 bg-white/80 rounded-lg px-2 py-1 inline-block border border-amber-100">
                                            📋 {{ $pending->occupation }}
                                        </p>
                                    @endif
                                    @if ($pending->address)
                                        <p class="mt-1 text-[11px] text-slate-500">📍 {{ $pending->address }}</p>
                                    @endif
                                    <p class="mt-1.5 text-[10px] text-slate-400">Terdaftar: {{ $pending->created_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2 border-t border-amber-200/60 pt-3">
                            <form method="POST" action="{{ route('admin.access.approve-provider', $pending) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-emerald-600 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                    ✓ Verifikasi & Aktifkan
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.access.reject-provider', $pending) }}" onsubmit="return confirm('Tolak dan hapus pendaftaran mitra ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                    ✕ Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    {{-- Forms to Add Admin & Provider --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <form method="POST" action="{{ route('admin.access.store') }}" class="space-y-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            @csrf
            <h3 class="text-sm font-bold text-slate-900">Tambah Super Admin</h3>
            <div>
                <label for="email" class="mb-1 block text-xs font-medium text-slate-600">Pilih Pengguna Terdaftar</label>
                <select
                    id="email"
                    name="email"
                    required
                    class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm bg-white focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    @if ($eligibleUsers->isEmpty())
                        <option value="">-- Tidak ada pengguna yang tersedia --</option>
                    @else
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach ($eligibleUsers as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    @endif
                </select>
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-[11px] leading-relaxed text-slate-500">
                    Pengguna akan mendapatkan hak akses penuh sebagai Super Admin.
                </p>
            </div>
            <button type="submit" class="w-full rounded-full bg-brand-600 py-2.5 text-xs font-semibold text-white shadow-soft transition hover:bg-brand-700">
                Tambah sebagai Admin
            </button>
        </form>

        <form method="POST" action="{{ route('admin.access.store-provider') }}" class="space-y-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            @csrf
            <h3 class="text-sm font-bold text-slate-900">Hubungkan Akses Mitra Manual</h3>
            <div>
                <label for="email_provider" class="mb-1 block text-xs font-medium text-slate-600">Pilih Pengguna Terdaftar</label>
                <select
                    id="email_provider"
                    name="email"
                    required
                    class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm bg-white focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    @if ($eligibleUsers->isEmpty())
                        <option value="">-- Tidak ada pengguna yang tersedia --</option>
                    @else
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach ($eligibleUsers as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    @endif
                </select>
                @error('email_provider')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="provider_key" class="mb-1 block text-xs font-medium text-slate-600">Pilih Layanan Mitra</label>
                <select
                    id="provider_key"
                    name="provider_key"
                    required
                    class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm bg-white focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    <option value="">-- Pilih Provider --</option>
                    @foreach ($providers as $prov)
                        <option value="{{ $prov->key }}">{{ $prov->short_name }} ({{ $prov->categoryLabel() }})</option>
                    @endforeach
                    <optgroup label="Mitra Layanan">
                        <option value="perawat">Mitra Perawat</option>
                        <option value="dokter">Mitra Dokter</option>
                        <option value="apotek">Mitra Apotek</option>
                        <option value="homecare">Mitra Homecare</option>
                    </optgroup>
                </select>
                @error('provider_key')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-full bg-violet-600 py-2.5 text-xs font-semibold text-white shadow-soft transition hover:bg-violet-700">
                Hubungkan Akses Mitra
            </button>
        </form>
    </div>

    {{-- Active Providers Section --}}
    <section class="mb-6">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Mitra & Tenaga Kesehatan Aktif ({{ $providerAdmins->count() }})</h2>
        <div class="space-y-3">
            @if ($providerAdmins->isEmpty())
                <p class="text-xs text-slate-400 text-center py-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50">Belum ada mitra/tenaga kesehatan aktif.</p>
            @endif
            @foreach ($providerAdmins as $pa)
                @php
                    if ($pa->provider_key === 'apotek') {
                        $providerName = 'Mitra Apotek';
                    } elseif ($pa->provider_key === 'homecare') {
                        $providerName = 'Mitra Homecare';
                    } elseif ($pa->provider_key === 'perawat') {
                        $providerName = 'Mitra Perawat';
                    } elseif ($pa->provider_key === 'dokter') {
                        $providerName = 'Mitra Dokter';
                    } else {
                        $linkedProvider = collect($providers)->firstWhere('key', $pa->provider_key);
                        $providerName = $linkedProvider?->short_name ?? $pa->provider_key;
                    }
                @endphp
                <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <img src="{{ $pa->profilePhotoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-2xl object-cover ring-2 ring-brand-100">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900">{{ $pa->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $pa->email }} | {{ $pa->phone }}</p>
                            @if ($pa->occupation)
                                <p class="text-[11px] text-slate-600 mt-0.5">{{ $pa->occupation }}</p>
                            @endif
                            <span class="mt-2 inline-flex rounded-full bg-violet-50 px-2.5 py-0.5 text-[10px] font-semibold text-violet-700 ring-1 ring-violet-100">
                                Link: {{ $providerName }}
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.access.destroy-provider', $pa) }}" class="mt-3 border-t border-slate-100 pt-3" onsubmit="return confirm('Cabut akses mitra untuk {{ $pa->email }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-rose-600">Cabut akses mitra</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Super Admins Section --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Admin Aktif ({{ $admins->count() }})</h2>
        <div class="space-y-3">
            @foreach ($admins as $admin)
                <div class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <img src="{{ $admin->profilePhotoUrl() }}" alt="" class="h-11 w-11 shrink-0 rounded-2xl object-cover ring-2 ring-brand-100">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900">{{ $admin->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $admin->email }}</p>
                            @if ($admin->is(auth()->user()))
                                <span class="mt-2 inline-flex rounded-full bg-brand-50 px-2.5 py-0.5 text-[10px] font-semibold text-brand-700 ring-1 ring-brand-100">Anda</span>
                            @endif
                        </div>
                    </div>
                    @if (! $admin->is(auth()->user()))
                        <form method="POST" action="{{ route('admin.access.destroy', $admin) }}" class="mt-3 border-t border-slate-100 pt-3" onsubmit="return confirm('Cabut akses admin untuk {{ $admin->email }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-rose-600">Cabut akses admin</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endsection
