@extends('layouts.admin')

@section('title', 'Detail Monitoring')

@section('content')
    <x-admin.page-banner
        title="Detail Monitoring"
        :subtitle="($entry->recorded_at ?? $entry->created_at)->translatedFormat('d F Y')"
        :back="route('admin.monitoring.index')"
        tone="violet"
        :show-actions="false"
    />

    <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-bold text-slate-900">Tanda vital</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Tekanan darah</dt><dd class="font-bold text-brand-700">{{ $entry->bloodPressureLabel() ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Nadi</dt><dd class="font-medium">{{ $entry->heart_rate ? $entry->heart_rate.' bpm' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Suhu</dt><dd class="font-medium">{{ $entry->temperature ? $entry->temperature.' °C' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">SpO₂</dt><dd class="font-medium">{{ $entry->oxygen_saturation ? $entry->oxygen_saturation.'%' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Gula darah</dt><dd class="font-medium">{{ $entry->blood_sugar ? $entry->blood_sugar.' mg/dL' : '—' }}</dd></div>
            <div class="flex justify-between gap-4 border-b border-slate-50 pb-2"><dt class="text-slate-500">Berat</dt><dd class="font-medium">{{ $entry->weight ? $entry->weight.' kg' : '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Patuh diet</dt><dd class="font-medium">{{ $entry->dietCompliantLabel() ?? '—' }}</dd></div>
        </dl>
    </section>

    @if ($entry->complaints || $entry->medication_name || $entry->activities || $entry->notes)
        <section class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm space-y-4 text-sm">
            @if ($entry->complaints)
                <div><p class="text-[11px] font-semibold text-slate-500">Keluhan</p><p class="mt-1 leading-relaxed">{{ $entry->complaints }}</p></div>
            @endif
            @if ($entry->medication_name)
                <div><p class="text-[11px] font-semibold text-slate-500">Obat</p><p class="mt-1">{{ $entry->medication_name }} — {{ $entry->medication_dose }} ({{ $entry->medication_schedule }})</p></div>
            @endif
            @if ($entry->activities)
                <div><p class="text-[11px] font-semibold text-slate-500">Aktivitas</p><p class="mt-1 whitespace-pre-wrap">{{ $entry->activities }}</p></div>
            @endif
            @if ($entry->notes)
                <div><p class="text-[11px] font-semibold text-slate-500">Catatan</p><p class="mt-1 whitespace-pre-wrap">{{ $entry->notes }}</p></div>
            @endif
        </section>
    @endif

    @if ($entry->user)
        <section class="rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
            <p class="text-[10px] font-semibold uppercase text-slate-500">Pengguna</p>
            <p class="mt-1 font-bold text-slate-900">{{ $entry->user->name }}</p>
            <p class="text-xs text-slate-500">{{ $entry->user->email }}</p>
            <a href="{{ route('admin.users.show', $entry->user) }}" class="mt-3 inline-block rounded-full bg-brand-600 px-4 py-2 text-xs font-semibold text-white">Profil pengguna</a>
        </section>
    @endif
@endsection
