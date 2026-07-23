@extends('layouts.admin')

@section('title', $isEdit ? 'Edit Obat' : 'Tambah Obat')

@section('content')
<div class="space-y-4">
    <x-admin.page-banner
        :title="$isEdit ? 'Edit Obat' : 'Tambah Obat Baru'"
        :subtitle="$isEdit ? 'Perbarui informasi obat dalam katalog' : 'Masukkan data obat baru untuk dijual online'"
        tone="blue"
        :back="route('admin.medicines.index')"
    />

    <form method="POST" action="{{ $isEdit ? route('admin.medicines.update', $medicine) : route('admin.medicines.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Nama Obat</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $medicine->name) }}"
                    required
                    placeholder="Contoh: Paracetamol 500 mg (1 Strip)"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                @error('name')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Kategori</label>
                <select
                    name="category"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >
                    <option value="" disabled {{ !old('category', $medicine->category) ? 'selected' : '' }}>Pilih Kategori</option>
                    @foreach (['Obat Bebas', 'Vitamin & Suplemen', 'Obat Keras'] as $opt)
                        <option value="{{ $opt }}" {{ old('category', $medicine->category) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Harga (Rupiah)</label>
                    <input
                        type="number"
                        name="price"
                        value="{{ old('price', $medicine->price) }}"
                        required
                        min="0"
                        placeholder="Contoh: 15000"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    @error('price')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Jumlah Stok</label>
                    <input
                        type="number"
                        name="stock"
                        value="{{ old('stock', $medicine->stock) }}"
                        required
                        min="0"
                        placeholder="Contoh: 50"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    @error('stock')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Keterangan / Deskripsi</label>
                <textarea
                    name="description"
                    rows="4"
                    placeholder="Meredakan gejala batuk, pilek, atau demam..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                >{{ old('description', $medicine->description) }}</textarea>
                @error('description')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Foto Obat</label>
                @if ($medicine->photo)
                    <div class="mb-2 flex h-20 w-20 items-center justify-center rounded-lg bg-slate-50 border border-slate-100 overflow-hidden">
                        <img src="{{ $medicine->photoUrl() }}" alt="" class="max-h-18 max-w-full object-contain">
                    </div>
                @endif
                <input
                    type="file"
                    name="photo"
                    accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#00529c] hover:file:bg-blue-100"
                >
                <p class="text-[9px] text-slate-400 mt-1">Format: JPG, PNG, WEBP, GIF. Maks: 2 MB. Kosongkan jika tidak diubah.</p>
                @error('photo')
                    <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input
                    type="checkbox"
                    name="active"
                    value="1"
                    id="active"
                    {{ old('active', $medicine->active ?? true) ? 'checked' : '' }}
                    class="rounded border-slate-300 text-[#00529c] focus:ring-[#00529c]/20"
                >
                <label for="active" class="text-xs font-semibold text-slate-700">Tampilkan obat ini di katalog pasien (Aktif)</label>
            </div>
        </section>

        <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#00529c] py-4 text-sm font-bold text-white shadow-lg shadow-[#00529c]/25 transition hover:bg-[#004787] active:scale-[0.98]"
        >
            {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Obat' }}
        </button>
    </form>
</div>
@endsection
