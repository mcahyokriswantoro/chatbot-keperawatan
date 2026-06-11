@extends('layouts.admin')

@section('title', 'Akses Admin')

@section('content')
    <x-admin.page-banner
        title="Kelola Akses Admin"
        subtitle="Tambah admin baru lewat email terdaftar"
        tone="rose"
    />

    @if ($errors->has('access'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ $errors->first('access') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.access.store') }}" class="mb-6 space-y-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        @csrf
        <div>
            <label for="email" class="mb-1 block text-xs font-medium text-slate-600">Email pengguna</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                placeholder="nama@email.com"
                class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-[11px] leading-relaxed text-slate-500">
                Email harus sudah terdaftar di aplikasi. Setelah ditambahkan, pengguna login via <strong>/login</strong> lalu otomatis masuk panel admin.
            </p>
        </div>
        <button type="submit" class="w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700">
            Tambah sebagai admin
        </button>
    </form>

    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Admin aktif ({{ $admins->count() }})</h2>
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
