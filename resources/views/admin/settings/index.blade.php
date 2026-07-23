@extends('layouts.admin')

@section('title', 'Pengaturan Mitra & WA')

@section('content')
    <x-admin.page-banner title="Pengaturan Mitra & WA" />

    <div class="px-4 py-6 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-50 p-4 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            @csrf

            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-800">Nomor WhatsApp Notifikasi</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Sistem akan mengirimkan pesan notifikasi pesanan dan booking baru ke nomor-nomor di bawah ini.
                </p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Admin -->
                <div>
                    <label for="order_admin_phone" class="block text-sm font-medium leading-6 text-slate-900">Admin Utama (Penerima Semua Notif Baru)</label>
                    <div class="mt-2">
                        <input type="tel" name="order_admin_phone" id="order_admin_phone" value="{{ old('order_admin_phone', $settings['order_admin_phone']) }}" required class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm sm:leading-6">
                        @error('order_admin_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr class="border-slate-100">

                <h3 class="text-md font-semibold text-slate-800">Mitra Apotek (Pemesanan Obat)</h3>

                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <label for="umla_farma1_phone" class="block text-sm font-medium leading-6 text-slate-900">UMLA FARMA 1 (Kampus 1)</label>
                        <div class="mt-2">
                            <input type="tel" name="umla_farma1_phone" id="umla_farma1_phone" value="{{ old('umla_farma1_phone', $settings['umla_farma1_phone']) }}" required class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm sm:leading-6">
                            @error('umla_farma1_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="umla_farma2_phone" class="block text-sm font-medium leading-6 text-slate-900">UMLA FARMA 2 (Kembangbahu)</label>
                        <div class="mt-2">
                            <input type="tel" name="umla_farma2_phone" id="umla_farma2_phone" value="{{ old('umla_farma2_phone', $settings['umla_farma2_phone']) }}" required class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm sm:leading-6">
                            @error('umla_farma2_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <h3 class="text-md font-semibold text-slate-800">Mitra Homecare (Medical Center)</h3>

                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                    <div>
                        <label for="medical_center1_phone" class="block text-sm font-medium leading-6 text-slate-900">Medical Center UMLA 1 (Plosowahyu)</label>
                        <div class="mt-2">
                            <input type="tel" name="medical_center1_phone" id="medical_center1_phone" value="{{ old('medical_center1_phone', $settings['medical_center1_phone']) }}" required class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm sm:leading-6">
                            @error('medical_center1_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="medical_center2_phone" class="block text-sm font-medium leading-6 text-slate-900">Medical Center UMLA 2 (Paciran)</label>
                        <div class="mt-2">
                            <input type="tel" name="medical_center2_phone" id="medical_center2_phone" value="{{ old('medical_center2_phone', $settings['medical_center2_phone']) }}" required class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-brand-500 sm:text-sm sm:leading-6">
                            @error('medical_center2_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-x-4 border-t border-slate-100">
                <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
