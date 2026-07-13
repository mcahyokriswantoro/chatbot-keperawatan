@extends('layouts.chat')

@section('content')
@php
    $photoUrl = $provider['photo'] ?? '/images/avatars/male.svg';
    $providerName = $provider['short_name'] ?? $provider['name'];
@endphp
<div
    x-data="consultationChat(@js([
        'providerKey' => $providerKey,
        'provider' => $provider,
        'messagesUrl' => $messagesUrl,
        'sendUrl' => $sendUrl,
        'backUrl' => $checkoutUrl,
        'expiresAt' => $expiresAt,
        'initialMessages' => $initialMessages,
        'csrf' => csrf_token(),
    ]))"
    class="flex h-full flex-col"
>
    <header class="shrink-0 border-b border-brand-100 bg-white/90 px-4 pt-[max(0.75rem,env(safe-area-inset-top))] pb-3 backdrop-blur-md">
        @if (session('status'))
            <div class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                {{ session('status') }}
            </div>
        @endif
        <div class="flex items-center gap-3">
            <a
                href="{{ $checkoutUrl }}"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-brand-600 transition hover:bg-brand-50"
                aria-label="Kembali"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>

            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-emerald-50 ring-2 ring-emerald-100">
                <img src="{{ $photoUrl }}" alt="{{ $providerName }}" class="h-full w-full object-cover object-top">
            </div>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-sm font-bold text-slate-900">{{ $provider['name'] }}</h1>
                <p class="flex items-center gap-1.5 text-xs text-emerald-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Online · {{ $provider['title'] ?? 'Tenaga Kesehatan' }}
                </p>
            </div>
        </div>
        <p class="mt-2 text-[10px] text-slate-400">
            Sesi aktif {{ $sessionHours }} jam · balasan dari tenaga kesehatan muncul di chat ini
        </p>
    </header>

    <div x-ref="messageList" class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-4 py-4 pb-2">
        <template x-for="msg in messages" :key="msg.id">
            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start gap-2'">
                <template x-if="msg.role !== 'user'">
                    <div class="mt-1 h-8 w-8 shrink-0 overflow-hidden rounded-full bg-emerald-50 ring-2 ring-emerald-100">
                        <img src="{{ $photoUrl }}" alt="" class="h-full w-full object-cover object-top">
                    </div>
                </template>
                <div
                    :class="{
                        'max-w-[82%] rounded-2xl px-4 py-2.5 text-sm leading-relaxed shadow-sm': true,
                        'rounded-tl-sm border border-brand-100 bg-white text-slate-700': msg.role !== 'user',
                        'rounded-tr-sm bg-brand-600 text-white': msg.role === 'user',
                    }"
                >
                    <p x-show="msg.sender_name && msg.role === 'provider'" x-cloak class="mb-1 text-[10px] font-bold text-emerald-700" x-text="msg.sender_name"></p>
                    <p x-text="msg.text" class="whitespace-pre-wrap"></p>
                </div>
            </div>
        </template>

        <div x-show="sending" x-cloak class="flex justify-end">
            <div class="rounded-2xl rounded-tr-sm bg-brand-600/80 px-4 py-3 shadow-sm">
                <span class="inline-flex gap-1">
                    <span class="h-2 w-2 animate-bounce rounded-full bg-white/80 [animation-delay:0ms]"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-white/80 [animation-delay:150ms]"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-white/80 [animation-delay:300ms]"></span>
                </span>
            </div>
        </div>
    </div>

    <footer class="shrink-0 border-t border-brand-100 bg-white px-4 py-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
        <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
            <textarea
                x-model="draft"
                x-ref="input"
                rows="1"
                placeholder="Tulis keluhan atau pertanyaan..."
                class="max-h-28 min-h-[2.75rem] flex-1 resize-none rounded-2xl border border-brand-200 px-4 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-100"
                @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
            ></textarea>
            <button
                type="submit"
                :disabled="sending || ! draft.trim() || expired"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-50"
                aria-label="Kirim"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
            </button>
        </form>
        <p class="mt-2 text-center text-[10px] text-slate-400">Tenaga kesehatan akan membalas di chat ini · notifikasi dikirim ke WhatsApp mereka</p>
    </footer>
</div>
@endsection
