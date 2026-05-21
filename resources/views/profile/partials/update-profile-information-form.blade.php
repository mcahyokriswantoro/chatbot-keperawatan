<section>
    <header class="mb-4">
        <h2 class="text-base font-bold text-slate-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui nama dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="text-xs font-medium text-slate-600">Nama</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('name')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="text-xs font-medium text-slate-600">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm text-amber-800">
                    <p>Alamat email belum diverifikasi.</p>
                    <button
                        form="send-verification"
                        type="submit"
                        class="mt-1 font-semibold text-brand-600 underline hover:text-brand-700"
                    >
                        Kirim ulang email verifikasi
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-700">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98] sm:w-auto sm:px-8"
            >
                Simpan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-600"
                >
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>
