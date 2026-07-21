@extends('layouts.mobile')

@php
    $detectionUrl = auth()->check() ? route('detection.start') : route('login');

    $quickTopics = [
        ['id' => 'skrining', 'label' => 'Cara skrining', 'emoji' => '🔍'],
        ['id' => 'self-mgmt', 'label' => 'Self management', 'emoji' => '📋'],
        ['id' => 'risiko', 'label' => 'Arti hasil risiko', 'emoji' => '📊'],
        ['id' => 'akun', 'label' => 'Login & akun', 'emoji' => '👤'],
        ['id' => 'darurat', 'label' => 'Darurat', 'emoji' => '🚨'],
    ];

    $chatReplies = [
        'skrining' => [
            'user' => 'Bagaimana cara mulai skrining?',
            'bot' => "Mudah! Tap menu Deteksi di bawah → pilih jenis penyakit → jawab pertanyaan chatbot dengan Ya/Tidak. Di akhir Anda dapat skor, tingkat risiko, dan arahan self management.",
            'action' => ['label' => 'Mulai Skrining', 'url' => $detectionUrl],
        ],
        'self-mgmt' => [
            'user' => 'Apa itu self management?',
            'bot' => "Self management adalah panduan perawatan mandiri di rumah sesuai tingkat risiko hasil skrining (Rendah, Sedang, Tinggi). Mencakup diet, aktivitas, pantau gejala, dan kapan perlu ke dokter.",
            'action' => ['label' => 'Buka Self Management', 'url' => auth()->check() ? route('self-management') : route('login')],
        ],
        'risiko' => [
            'user' => 'Apa arti hasil risiko rendah, sedang, tinggi?',
            'bot' => "Rendah — pertahankan gaya hidup sehat. Sedang — perbaiki pola hidup & pantau rutin. Tinggi — segera konsultasi tenaga kesehatan + ikuti panduan self management. Hasil skrining bersifat informatif, bukan diagnosis medis.",
            'action' => ['label' => 'Lihat Riwayat', 'url' => auth()->check() ? route('history') : route('login')],
        ],
        'akun' => [
            'user' => 'Apakah harus login?',
            'bot' => "Skrining bisa dicoba tanpa akun, tapi login diperlukan untuk menyimpan riwayat, monitoring kesehatan, dan self management. Daftar gratis dengan data profil lengkap.",
            'action' => ['label' => auth()->check() ? 'Profil Saya' : 'Daftar / Masuk', 'url' => auth()->check() ? route('profile.page') : route('register')],
        ],
        'darurat' => [
            'user' => 'Kapan harus ke IGD?',
            'bot' => "Segera ke fasilitas kesehatan jika ada nyeri dada hebat, sesak napas berat, gejala stroke (FAST), atau tanda bahaya lain. Hubungi 119 atau IGD terdekat. Jangan tunggu skrining selesai.",
            'action' => ['label' => 'Hotline Darurat', 'url' => route('emergency')],
        ],
    ];

    $faqs = [
        [
            'category' => 'Skrining',
            'icon' => '🔍',
            'items' => [
                ['q' => 'Penyakit apa saja yang bisa diskrining?', 'a' => 'TB Paru, DHF, PPOK, Penyakit Ginjal, Stroke, Jantung Koroner, Diabetes Melitus, dan Hipertensi — masing-masing dengan kuesioner khusus.'],
                ['q' => 'Berapa lama skrining selesai?', 'a' => 'Rata-rata 5–15 menit tergantung jenis penyakit (19–26 pertanyaan). Jawab jujur sesuai kondisi Anda.'],
                ['q' => 'Apakah hasil skrining adalah diagnosis?', 'a' => 'Tidak. Hasil bersifat informatif untuk deteksi dini. Konfirmasi medis harus melalui pemeriksaan tenaga kesehatan.'],
            ],
        ],
        [
            'category' => 'Self Management',
            'icon' => '📋',
            'items' => [
                ['q' => 'Kapan self management muncul?', 'a' => 'Setelah skrining selesai, atau dari menu Self Management / Riwayat Skrining. Panduan disesuaikan tingkat risiko hasil skor Anda.'],
                ['q' => 'Apakah menggantikan obat dari dokter?', 'a' => 'Tidak. Self management melengkapi pengobatan. Minum obat tetap sesuai resep dokter.'],
            ],
        ],
        [
            'category' => 'Monitoring & Riwayat',
            'icon' => '📈',
            'items' => [
                ['q' => 'Apa yang bisa dicatat di monitoring?', 'a' => 'Keluhan, obat, aktivitas, diet, tekanan darah, nadi, suhu, gula darah, SpO₂, berat badan, dan catatan lain.'],
                ['q' => 'Di mana lihat riwayat skrining?', 'a' => 'Menu Profil → Riwayat, atau Dashboard. Setiap hasil menampilkan skor, risiko, dan link self management.'],
            ],
        ],
        [
            'category' => 'Akun',
            'icon' => '👤',
            'items' => [
                ['q' => 'Bisa login pakai nomor HP?', 'a' => 'Ya. Di halaman masuk, pilih tab Nomor HP lalu masukkan nomor yang terdaftar.'],
                ['q' => 'Data saya aman?', 'a' => 'Data profil dan riwayat kesehatan tersimpan di akun Anda dan hanya bisa diakses setelah login.'],
            ],
        ],
    ];

    $faqFlat = collect($faqs)->flatMap(function ($group) {
        return collect($group['items'])->map(fn ($item) => [
            'category' => $group['category'],
            'q' => $item['q'],
            'a' => $item['a'],
        ]);
    })->values();

    $steps = [
        ['num' => '1', 'title' => 'Pilih Deteksi', 'desc' => 'Tap menu Deteksi & pilih jenis skrining', 'color' => 'bg-brand-500'],
        ['num' => '2', 'title' => 'Jawab Chatbot', 'desc' => 'Respons pertanyaan dengan Ya / Tidak', 'color' => 'bg-violet-500'],
        ['num' => '3', 'title' => 'Lihat Hasil', 'desc' => 'Skor, risiko & temuan jawaban Anda', 'color' => 'bg-amber-500'],
        ['num' => '4', 'title' => 'Self Management', 'desc' => 'Ikuti panduan sesuai tingkat risiko', 'color' => 'bg-emerald-500'],
    ];

    $shortcuts = [
        ['label' => 'Skrining', 'desc' => 'Deteksi via chatbot', 'url' => $detectionUrl, 'bg' => 'from-brand-600 to-brand-500', 'auth' => false],
        ['label' => 'Edukasi', 'desc' => 'Video edukasi kesehatan', 'url' => route('education.index'), 'bg' => 'from-violet-600 to-purple-500', 'auth' => false],
        ['label' => 'Monitoring', 'desc' => 'Catat vital signs', 'url' => route('monitoring'), 'bg' => 'from-emerald-600 to-teal-500', 'auth' => true],
        ['label' => 'Darurat', 'desc' => 'Hotline & peringatan', 'url' => route('emergency'), 'bg' => 'from-rose-600 to-red-500', 'auth' => false],
    ];
@endphp

@section('content')
<div
    x-data="{
        search: '',
        openFaq: null,
        activeTopic: null,
        chatMessages: [
            { role: 'bot', text: 'Halo! 👋 Saya Nersia Health. Tap topik di bawah — saya bantu jelaskan fitur aplikasi ini.' }
        ],
        replies: @js($chatReplies),
        faqItems: @js($faqFlat),
        typing: false,
        askTopic(id) {
            const item = this.replies[id];
            if (!item) return;
            this.activeTopic = id;
            this.chatMessages.push({ role: 'user', text: item.user });
            this.typing = true;
            this.scrollChat();
            setTimeout(() => {
                this.typing = false;
                this.chatMessages.push({ role: 'bot', text: item.bot, action: item.action ?? null });
                this.scrollChat();
            }, 700);
        },
        scrollChat() {
            this.$nextTick(() => {
                const el = this.$refs.chatBox;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },
        faqVisible(category, question, answer) {
            if (!this.search.trim()) return true;
            const q = this.search.toLowerCase();
            return category.toLowerCase().includes(q)
                || question.toLowerCase().includes(q)
                || answer.toLowerCase().includes(q);
        },
        hasFaqResults() {
            if (!this.search.trim()) return true;
            return this.faqItems.some(item => this.faqVisible(item.category, item.q, item.a));
        },
        toggleFaq(key) {
            this.openFaq = this.openFaq === key ? null : key;
        },
    }"
    class="space-y-5 pb-2"
>
    {{-- Hero --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-500 to-teal-500 px-4 pb-4 pt-4 text-white shadow-lg shadow-brand-600/20">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></div>
        <div class="flex items-center gap-3">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/20 ring-2 ring-white/30 backdrop-blur-sm">
                <svg class="h-9 w-9 text-white" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <rect x="14" y="18" width="36" height="28" rx="10" fill="currentColor" opacity="0.95"/>
                    <circle cx="24" cy="30" r="3.5" fill="#0ea5e9"/>
                    <circle cx="40" cy="30" r="3.5" fill="#0ea5e9"/>
                    <path d="M26 38c2 2 10 2 12 0" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="32" cy="6" r="3" fill="#f87171"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-blue-100">Pusat Bantuan</p>
                <h1 class="text-xl font-bold">Nersia Health</h1>
                <p class="mt-0.5 text-xs text-blue-100/90">Tanya apa saja tentang fitur aplikasi</p>
            </div>
        </div>
    </header>

    {{-- Chat interaktif --}}
    <section class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm ring-1 ring-brand-50">
        <div class="border-b border-slate-100 bg-slate-50/80 px-4 py-2.5">
            <p class="text-[11px] font-semibold text-slate-600">💬 Konsultasi Virtual</p>
        </div>

        <div x-ref="chatBox" class="max-h-64 space-y-3 overflow-y-auto px-4 py-4 scrollbar-none">
            <template x-for="(msg, idx) in chatMessages" :key="idx">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        class="max-w-[88%] rounded-2xl px-3.5 py-2.5 text-xs leading-relaxed"
                        :class="msg.role === 'user'
                            ? 'rounded-tr-sm bg-brand-600 text-white'
                            : 'rounded-tl-sm border border-brand-100 bg-brand-50 text-slate-700'"
                    >
                        <p x-text="msg.text"></p>
                        <a
                            x-show="msg.action"
                            x-cloak
                            :href="msg.action?.url"
                            class="mt-2 inline-flex items-center gap-1 rounded-full bg-brand-600 px-3 py-1.5 text-[10px] font-semibold text-white transition hover:bg-brand-700"
                        >
                            <span x-text="msg.action?.label"></span>
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                </div>
            </template>

            <div x-show="typing" x-cloak class="flex justify-start">
                <div class="rounded-2xl rounded-tl-sm border border-brand-100 bg-white px-4 py-3">
                    <div class="flex gap-1">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400 [animation-delay:-0.3s]"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400 [animation-delay:-0.15s]"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-brand-400"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 px-3 py-3">
            <p class="mb-2 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Pertanyaan cepat</p>
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">
                @foreach ($quickTopics as $topic)
                    <button
                        type="button"
                        @click="askTopic('{{ $topic['id'] }}')"
                        :class="activeTopic === '{{ $topic['id'] }}' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-brand-50 hover:text-brand-700'"
                        class="shrink-0 rounded-full px-3 py-2 text-[11px] font-semibold transition active:scale-95"
                    >
                        {{ $topic['emoji'] }} {{ $topic['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Panduan langkah --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Cara Pakai Aplikasi</h2>
        <div class="relative space-y-0 pl-4">
            <div class="absolute bottom-4 left-[7px] top-4 w-0.5 bg-brand-100"></div>
            @foreach ($steps as $step)
                <div class="relative flex gap-3 pb-4 last:pb-0">
                    <span class="relative z-10 flex h-4 w-4 shrink-0 items-center justify-center rounded-full {{ $step['color'] }} text-[9px] font-bold text-white ring-2 ring-white">{{ $step['num'] }}</span>
                    <div class="min-w-0 flex-1 rounded-xl border border-slate-100 bg-white px-3 py-2.5 shadow-sm">
                        <p class="text-sm font-bold text-slate-900">{{ $step['title'] }}</p>
                        <p class="text-[11px] text-slate-500">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Akses cepat --}}
    <section>
        <h2 class="mb-3 text-sm font-bold text-slate-900">Akses Cepat</h2>
        <div class="grid grid-cols-2 gap-2">
            @foreach ($shortcuts as $item)
                @if (empty($item['auth']) || auth()->check())
                    <a
                        href="{{ $item['url'] }}"
                        class="group relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $item['bg'] }} p-3.5 text-white shadow-md transition active:scale-[0.97] hover:shadow-lg"
                    >
                        <p class="text-sm font-bold">{{ $item['label'] }}</p>
                        <p class="mt-0.5 text-[10px] text-white/80">{{ $item['desc'] }}</p>
                        <svg class="absolute bottom-3 right-3 h-4 w-4 opacity-60 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section class="rounded-2xl border border-brand-100 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-4">
            <h2 class="text-sm font-bold text-slate-900">Pertanyaan Umum (FAQ)</h2>
            <div class="relative mt-3">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input
                    type="search"
                    x-model="search"
                    placeholder="Cari pertanyaan..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-100"
                />
            </div>
        </div>

        <div class="px-4">
            @foreach ($faqs as $groupIdx => $group)
                @foreach ($group['items'] as $itemIdx => $item)
                    @php $faqKey = "{$groupIdx}-{$itemIdx}"; @endphp
                    <div
                        x-show="faqVisible(@js($group['category']), @js($item['q']), @js($item['a']))"
                        class="border-b border-slate-100 last:border-0"
                    >
                        <button
                            type="button"
                            @click="toggleFaq('{{ $faqKey }}')"
                            class="flex w-full items-start gap-3 py-3.5 text-left"
                        >
                            <span class="mt-0.5 text-base" aria-hidden="true">{{ $group['icon'] }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-slate-900">{{ $item['q'] }}</span>
                                <span class="mt-0.5 block text-[10px] font-medium text-brand-600">{{ $group['category'] }}</span>
                            </span>
                            <svg
                                class="mt-1 h-4 w-4 shrink-0 text-slate-400 transition"
                                :class="openFaq === '{{ $faqKey }}' ? 'rotate-180' : ''"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            ><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <div
                            x-show="openFaq === '{{ $faqKey }}'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            x-cloak
                            class="pb-3 pl-9"
                        >
                            <p class="text-xs leading-relaxed text-slate-600">{{ $item['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            @endforeach

            <p x-show="!hasFaqResults()" x-cloak class="py-8 text-center text-sm text-slate-500">
                Tidak ada FAQ cocok. Coba kata kunci lain atau tanya via chat di atas.
            </p>
        </div>
    </section>

    {{-- Penyakit --}}
    <section class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-100">
        <h2 class="text-sm font-bold text-slate-900">8 Jenis Skrining Tersedia</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach (config('diseases') as $disease)
                <span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-1 text-[11px] font-medium text-slate-700 shadow-sm ring-1 ring-slate-100">
                    <span aria-hidden="true">{{ $disease['icon'] ?? '🩺' }}</span>
                    {{ $disease['label'] }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- Darurat --}}
    <div class="rounded-2xl border border-rose-100 bg-gradient-to-r from-rose-50 to-white p-4">
        <p class="text-sm font-bold text-rose-900">Butuh bantuan medis segera?</p>
        <p class="mt-1 text-xs text-rose-700">Jangan menunggu — hubungi layanan darurat atau ke IGD terdekat.</p>
        <a href="{{ route('emergency') }}" class="mt-3 inline-flex items-center gap-2 rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">
            Buka Peringatan Darurat
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
    </div>

    @guest
        <p class="text-center text-xs text-slate-500">
            <a href="{{ route('login') }}" class="font-semibold text-brand-600">Masuk</a>
            atau
            <a href="{{ route('register') }}" class="font-semibold text-brand-600">Daftar</a>
            untuk fitur lengkap.
        </p>
    @endguest
</div>
@endsection

@push('scripts')
<style>
    .scrollbar-none::-webkit-scrollbar { display: none; }
    .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush
