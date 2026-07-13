@extends('layouts.admin')

@section('title', 'Balas Chat')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? '/images/avatars/male.svg';
@endphp
<div
    x-data="adminConsultationChat(@js([
        'messagesUrl' => route('admin.consultations.chat.messages', $order),
        'replyUrl' => route('admin.consultations.chat.reply', $order),
        'initialMessages' => $messages,
        'csrf' => csrf_token(),
    ]))"
    class="flex min-h-[70vh] flex-col rounded-2xl border border-slate-200 bg-white shadow-sm"
>
    <div class="border-b border-slate-100 px-4 py-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.consultations.chat.index') }}" class="text-brand-600" aria-label="Kembali">←</a>
            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-slate-100">
                <img src="{{ $photoUrl }}" alt="" class="h-full w-full object-cover object-top">
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-bold text-slate-900">{{ $patientName }}</p>
                <p class="text-xs text-slate-500">{{ $providerName }} · aktif s/d {{ $order->expires_at?->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mx-4 mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">{{ session('status') }}</div>
    @endif

    <div x-ref="messageList" class="flex-1 space-y-3 overflow-y-auto px-4 py-4" style="max-height: 55vh;">
        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.role === 'user' ? 'flex justify-start' : 'flex justify-end'">
                <div
                    :class="{
                        'max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed': true,
                        'rounded-tl-sm border border-slate-200 bg-slate-50 text-slate-700': msg.role === 'user',
                        'rounded-tr-sm bg-emerald-600 text-white': msg.role !== 'user' && msg.role !== 'system',
                        'rounded-tr-sm border border-sky-100 bg-sky-50 text-sky-900': msg.role === 'system',
                    }"
                >
                    <p x-show="msg.role === 'user'" class="mb-0.5 text-[10px] font-bold text-slate-500">Pasien</p>
                    <p x-text="msg.text" class="whitespace-pre-wrap"></p>
                </div>
            </div>
        </template>
    </div>

    <form @submit.prevent="sendReply()" class="border-t border-slate-100 p-4">
        <div class="flex gap-2">
            <textarea
                x-model="draft"
                rows="2"
                placeholder="Tulis balasan untuk pasien..."
                class="min-h-[3rem] flex-1 resize-none rounded-xl border border-brand-200 px-3 py-2 text-sm"
            ></textarea>
            <button
                type="submit"
                :disabled="sending || ! draft.trim()"
                class="shrink-0 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-bold text-white disabled:opacity-50"
            >Kirim</button>
        </div>
    </form>
</div>
@endsection
