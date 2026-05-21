<section>
    <header class="mb-4">
        <h2 class="text-base font-bold text-slate-900">Ubah Kata Sandi</h2>
        <p class="mt-1 text-sm text-slate-500">
            Gunakan kata sandi yang kuat dan unik untuk keamanan akun.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="text-xs font-medium text-slate-600">Kata Sandi Saat Ini</label>
            <input
                type="password"
                id="update_password_current_password"
                name="current_password"
                autocomplete="current-password"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('current_password', 'updatePassword')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="text-xs font-medium text-slate-600">Kata Sandi Baru</label>
            <input
                type="password"
                id="update_password_password"
                name="password"
                autocomplete="new-password"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('password', 'updatePassword')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="text-xs font-medium text-slate-600">Konfirmasi Kata Sandi Baru</label>
            <input
                type="password"
                id="update_password_password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('password_confirmation', 'updatePassword')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98] sm:w-auto sm:px-8"
            >
                Simpan
            </button>

            @if (session('status') === 'password-updated')
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
