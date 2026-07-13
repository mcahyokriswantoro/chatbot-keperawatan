@props([
    'disease',
    'diseaseInfo',
    'userMedications',
    'severityOptions',
    'selfManagementOptions',
])

@php
    $symptoms = config("monitoring_complaints.{$disease}", []);
    $selfItems = config("monitoring_self_management.{$disease}.{$diseaseInfo['risk']}.items", []);
    $inputClass = 'mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-100';
@endphp

<form
    method="POST"
    action="{{ route('monitoring.store') }}"
    class="space-y-4"
    novalidate
    x-data="monitoringDailyForm(@js(['hasSelfManagement' => $selfItems !== []]))"
    @monitoring-daily-section.window="handleSection($event)"
    @change="checkMedication()"
>
    @csrf
    <input type="hidden" name="monitor_type" value="daily">
    <input type="hidden" name="disease" value="{{ $disease }}">

    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-card">
        <label class="text-xs font-semibold text-slate-700">Tanggal Pencatatan</label>
        <input type="date" name="recorded_at" value="{{ old('recorded_at', date('Y-m-d')) }}" required class="{{ $inputClass }}">
    </div>

    @include('monitoring.partials.complaints-chat', [
        'disease' => $disease,
        'diseaseInfo' => $diseaseInfo,
        'symptoms' => $symptoms,
        'severityOptions' => $severityOptions,
    ])

    @include('monitoring.partials.medication-section', [
        'userMedications' => $userMedications,
        'inputClass' => $inputClass,
    ])

    @include('monitoring.partials.daily-vitals-section', [
        'inputClass' => $inputClass,
    ])

    @if ($selfItems !== [])
        @include('monitoring.partials.self-management-chat', [
            'diseaseInfo' => $diseaseInfo,
            'selfItems' => $selfItems,
            'selfManagementOptions' => $selfManagementOptions,
        ])
    @endif
</form>
