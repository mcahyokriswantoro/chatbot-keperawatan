@extends('layouts.chat')

@section('content')
<div class="flex h-full flex-col">
    <header class="shrink-0 border-b border-brand-100 bg-white/90 backdrop-blur-md px-4 pt-[max(0.75rem,env(safe-area-inset-top))] pb-3">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('detection.start') }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand-600 transition hover:bg-brand-50"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-sm font-bold text-slate-900">Identitas Peserta — {{ $diseaseLabel }}</h1>
                <p class="text-xs text-slate-500">Lengkapi data sebelum memulai skrining</p>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto overscroll-contain px-4 py-4">
        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('detection.identity.store', $disease) }}"
            class="space-y-4"
            x-data="screeningIdentityForm(@js(['provinces' => $provinces, 'old' => $oldWilayah]))"
        >
            @csrf

            <div>
                <label for="name" class="text-xs font-medium text-slate-600">Nama <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
            </div>

            <div>
                <label for="gender" class="text-xs font-medium text-slate-600">Jenis Kelamin <span class="text-rose-500">*</span></label>
                <select
                    id="gender"
                    name="gender"
                    required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    <option value="">Pilih jenis kelamin</option>
                    <option value="laki_laki" @selected(old('gender') === 'laki_laki')>Laki-laki</option>
                    <option value="perempuan" @selected(old('gender') === 'perempuan')>Perempuan</option>
                </select>
            </div>

            <div>
                <label for="phone" class="text-xs font-medium text-slate-600">No HP <span class="text-rose-500">*</span></label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    placeholder="08xxxxxxxxxx"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="date_of_birth" class="text-xs font-medium text-slate-600">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                        required
                        max="{{ date('Y-m-d') }}"
                        @change="updateAge()"
                        x-ref="dob"
                        class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                </div>
                <div>
                    <label for="age_display" class="text-xs font-medium text-slate-600">Usia</label>
                    <input
                        type="text"
                        id="age_display"
                        x-ref="ageDisplay"
                        readonly
                        placeholder="Otomatis"
                        class="mt-1 w-full rounded-xl border border-brand-100 bg-brand-50 px-3 py-2.5 text-sm text-slate-600"
                    >
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="weight_kg" class="text-xs font-medium text-slate-600">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                    <input
                        type="number"
                        step="0.1"
                        id="weight_kg"
                        name="weight_kg"
                        value="{{ old('weight_kg') }}"
                        required
                        min="1"
                        class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                </div>
                <div>
                    <label for="height_cm" class="text-xs font-medium text-slate-600">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                    <input
                        type="number"
                        id="height_cm"
                        name="height_cm"
                        value="{{ old('height_cm') }}"
                        required
                        min="30"
                        max="300"
                        class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                    >
                </div>
            </div>

            <div>
                <label for="domicile_address" class="text-xs font-medium text-slate-600">Alamat Domisili <span class="text-rose-500">*</span></label>
                <textarea
                    id="domicile_address"
                    name="domicile_address"
                    rows="2"
                    required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >{{ old('domicile_address') }}</textarea>
            </div>

            <div>
                <label for="occupation" class="text-xs font-medium text-slate-600">Pekerjaan <span class="text-rose-500">*</span></label>
                <input
                    type="text"
                    id="occupation"
                    name="occupation"
                    value="{{ old('occupation') }}"
                    required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
            </div>

            <div>
                <label for="address" class="text-xs font-medium text-slate-600">Alamat <span class="text-rose-500">*</span></label>
                <textarea
                    id="address"
                    name="address"
                    rows="2"
                    required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="province_kode" class="text-xs font-medium text-slate-600">Provinsi <span class="text-rose-500">*</span></label>
                <select
                    id="province_kode"
                    name="province_kode"
                    required
                    x-model="provinceKode"
                    @change="onProvinceChange()"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                    <option value="">Pilih provinsi</option>
                    @foreach ($provinces as $province)
                        <option value="{{ $province->kode }}" @selected(old('province_kode', $oldWilayah['province_kode'] ?? '') === $province->kode)>
                            {{ $province->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="regency_kode" class="text-xs font-medium text-slate-600">Kabupaten/Kota <span class="text-rose-500">*</span></label>
                <select
                    id="regency_kode"
                    name="regency_kode"
                    required
                    x-model="regencyKode"
                    @change="onRegencyChange()"
                    :disabled="!provinceKode || loadingRegencies"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 disabled:bg-brand-50 disabled:text-slate-400"
                >
                    <option value="" x-text="loadingRegencies ? 'Memuat...' : 'Pilih kabupaten/kota'"></option>
                    <template x-for="item in regencies" :key="item.kode">
                        <option :value="item.kode" x-text="item.nama" :selected="item.kode === regencyKode"></option>
                    </template>
                </select>
            </div>

            <div>
                <label for="district_kode" class="text-xs font-medium text-slate-600">Kecamatan <span class="text-rose-500">*</span></label>
                <select
                    id="district_kode"
                    name="district_kode"
                    required
                    x-model="districtKode"
                    :disabled="!regencyKode || loadingDistricts"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200 disabled:bg-brand-50 disabled:text-slate-400"
                >
                    <option value="" x-text="loadingDistricts ? 'Memuat...' : 'Pilih kecamatan'"></option>
                    <template x-for="item in districts" :key="item.kode">
                        <option :value="item.kode" x-text="item.nama" :selected="item.kode === districtKode"></option>
                    </template>
                </select>
            </div>

            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft hover:bg-brand-700 active:scale-[0.98]"
            >
                Lanjut ke Skrining
            </button>
        </form>
    </div>
</div>

@endsection
