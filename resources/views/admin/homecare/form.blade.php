@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Paket Homecare' : 'Tambah Paket Homecare')

@section('content')
<div class="space-y-4">
    <x-admin.page-banner
        :title="$isEdit ? 'Edit Paket' : 'Tambah Paket Baru'"
        :subtitle="$isEdit ? 'Perbarui detail paket layanan homecare' : 'Masukkan paket kunjungan perawat baru ke sistem'"
        tone="blue"
        :back="route('admin.homecare.index')"
    />

    <form method="POST" action="{{ $isEdit ? route('admin.homecare.update', $package) : route('admin.homecare.store') }}" class="space-y-4">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Nama Paket Layanan</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $package->name) }}"
                    required
                    placeholder="Contoh: Perawatan Luka Pasca Operasi"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                @error('name')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tarif Kunjungan (Rupiah)</label>
                    <input
                        type="number"
                        name="price"
                        value="{{ old('price', $package->price) }}"
                        required
                        min="0"
                        placeholder="Contoh: 150000"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    @error('price')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Icon (Emoji)</label>
                    <select
                        name="icon"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        @foreach (['🩹' => 'Plester (🩹)', '🩺' => 'Stetoskop (🩺)', '💧' => 'Infus (💧)', '💉' => 'Suntik (💉)', '🏃' => 'Fisioterapi (🏃)', '👴' => 'Lansia (👴)', '👩‍⚕️' => 'Perawat (👩‍⚕️)'] as $emoji => $label)
                            <option value="{{ $emoji }}" {{ old('icon', $package->icon) === $emoji ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('icon')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Deskripsi Paket Layanan</label>
                <textarea
                    name="description"
                    rows="4"
                    placeholder="Tindakan pembersihan luka steril, penggantian perban, dan pengecekan infeksi..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >{{ old('description', $package->description) }}</textarea>
                @error('description')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input
                    type="checkbox"
                    name="active"
                    value="1"
                    id="active"
                    {{ old('active', $package->active ?? true) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-[#00529c] focus:ring-[#00529c]/20"
                >
                <label for="active" class="text-xs font-semibold text-slate-700">Tampilkan paket ini di daftar pemesanan (Aktif)</label>
            </div>
        </section>

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#00529c] py-4 text-sm font-bold text-white shadow-lg shadow-[#00529c]/25 transition hover:bg-[#004787] active:scale-[0.98]"
        >
            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Paket' }}
        </button>
    </form>
</div>
@endsection
