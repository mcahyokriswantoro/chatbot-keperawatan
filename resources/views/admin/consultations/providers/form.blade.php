@extends('layouts.admin')

@section('title', $provider->exists ? 'Edit Tenaga Kesehatan' : 'Tambah Tenaga Kesehatan')

@section('content')
    <x-admin.page-banner
        :title="$provider->exists ? 'Edit Tenaga Kesehatan' : 'Tambah Tenaga Kesehatan'"
        :subtitle="$provider->exists ? $provider->short_name : 'Lengkapi profil untuk tampilan user'"
        tone="sky"
        :back="route('admin.consultations.providers.index')"
        :show-actions="false"
    />

    <form
        method="POST"
        action="{{ $provider->exists ? route('admin.consultations.providers.update', $provider) : route('admin.consultations.providers.store') }}"
        enctype="multipart/form-data"
        class="space-y-4 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm"
        x-data="{ photoPreview: @js($provider->exists ? $provider->photoUrl() : '/images/avatars/male.svg') }"
    >
        @csrf

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
                <p class="font-semibold">Perubahan belum tersimpan:</p>
                <ul class="mt-1 list-inside list-disc text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        {{-- Foto --}}
        <div>
            <label class="mb-2 block text-xs font-medium text-slate-600">Foto profil</label>
            <div class="flex items-start gap-4">
                <div class="h-28 w-24 shrink-0 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
                    <img
                        src="{{ $provider->exists ? $provider->photoUrl() : '/images/avatars/male.svg' }}"
                        :src="photoPreview"
                        alt=""
                        class="h-full w-full object-cover object-top"
                    >
                </div>
                <div class="min-w-0 flex-1 space-y-2">
                    <input
                        type="file"
                        name="photo"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-sky-700"
                        @change="
                            const file = $event.target.files?.[0];
                            if (! file) return;
                            const reader = new FileReader();
                            reader.onload = (e) => photoPreview = e.target.result;
                            reader.readAsDataURL(file);
                        "
                    >
                    <p class="text-[10px] text-slate-400">JPG, PNG, WEBP, atau GIF — maks. 5 MB. Rasio portrait 3:4 disarankan.</p>
                    @if ($provider->exists && $provider->photo)
                        <label class="inline-flex items-center gap-2 text-[11px] text-slate-600">
                            <input type="checkbox" name="remove_photo" value="1" class="rounded border-slate-300">
                            Hapus foto saat ini
                        </label>
                    @endif
                    @error('photo')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-medium text-slate-600">Nama lengkap + gelar</label>
                <input type="text" name="name" value="{{ old('name', $provider->name) }}" required placeholder="Abdul Aziz Alimul Hidayat, S.Kep., N.s" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
                @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Nama tampilan (pendek)</label>
                <input type="text" name="short_name" value="{{ old('short_name', $provider->short_name) }}" required class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
                @error('short_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Kode unik (URL)</label>
                <input type="text" name="key" value="{{ old('key', $provider->key) }}" placeholder="perawat-abdul-aziz" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
                <p class="mt-1 text-[10px] text-slate-400">Kosongkan untuk otomatis dari nama.</p>
                @error('key')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Kategori</label>
                <select name="category_key" required class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat['key'] }}" @selected(old('category_key', $provider->category_key) === $cat['key'])>{{ $cat['label'] }}</option>
                    @endforeach
                </select>
                @error('category_key')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Spesialisasi / jabatan</label>
                <input type="text" name="specialty" value="{{ old('specialty', $provider->specialty) }}" placeholder="Perawat (Ners)" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Judul profesi</label>
                <input type="text" name="title" value="{{ old('title', $provider->title) }}" placeholder="Perawat Profesional" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Gelar / credential</label>
                <input type="text" name="credential" value="{{ old('credential', $provider->credential) }}" placeholder="S.Kep., N.s" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Pengalaman (tahun)</label>
                <input type="number" name="experience_years" value="{{ old('experience_years', $provider->experience_years) }}" min="0" max="60" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Rating (%)</label>
                <input type="number" name="rating_percent" value="{{ old('rating_percent', $provider->rating_percent ?? 100) }}" min="0" max="100" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Harga (opsional)</label>
                <input type="number" name="price" value="{{ old('price', $provider->price) }}" min="0" step="500" placeholder="25000" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
                <p class="mt-1 text-[10px] text-slate-400">Kosong = harga kategori.</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">WhatsApp</label>
                <input type="tel" name="whatsapp" value="{{ old('whatsapp', $provider->whatsapp) }}" required placeholder="085645527751" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
                @error('whatsapp')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">WhatsApp intl (opsional)</label>
                <input type="text" name="whatsapp_intl" value="{{ old('whatsapp_intl', $provider->whatsapp_intl) }}" placeholder="6285645527751" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
            </div>
        </div>

        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Urutan tampil</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $provider->sort_order ?? 0) }}" min="0" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm">
        </div>

        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Sapaan WhatsApp (opsional)</label>
            <textarea name="greeting" rows="3" class="w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm" placeholder="Pesan pembuka saat user chat...">{{ old('greeting', $provider->greeting) }}</textarea>
        </div>

        <input type="hidden" name="active" value="0">
        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm">
            <input type="checkbox" name="active" value="1" @checked(old('active', $provider->active ?? true)) class="rounded border-slate-300 text-sky-600">
            <span>Tampilkan di laman user (aktif)</span>
        </label>

        <button type="submit" class="w-full rounded-xl bg-sky-600 py-3 text-sm font-bold text-white hover:bg-sky-700">
            {{ $provider->exists ? 'Simpan perubahan' : 'Tambah tenaga kesehatan' }}
        </button>
    </form>
@endsection
