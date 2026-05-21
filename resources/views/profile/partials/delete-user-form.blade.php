<section class="space-y-4">
    <header>
        <h2 class="text-base font-bold text-slate-900">Hapus Akun</h2>
        <p class="mt-1 text-sm text-slate-500">
            Setelah akun dihapus, semua data akan dihapus permanen. Pastikan Anda telah menyimpan data penting sebelum melanjutkan.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full rounded-full border border-rose-300 bg-rose-50 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-100 active:scale-[0.98]"
    >
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable maxWidth="md">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-slate-900">
                Yakin ingin menghapus akun?
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Masukkan kata sandi untuk mengonfirmasi penghapusan akun secara permanen.
            </p>

            <div class="mt-4">
                <label for="password" class="sr-only">Kata Sandi</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Kata sandi"
                    class="w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('password', 'userDeletion')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5 flex flex-col gap-2">
                <button
                    type="submit"
                    class="w-full rounded-full bg-rose-600 py-3 text-sm font-semibold text-white transition hover:bg-rose-700 active:scale-[0.98]"
                >
                    Hapus Akun
                </button>
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="w-full rounded-full border border-brand-200 py-3 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 active:scale-[0.98]"
                >
                    Batal
                </button>
            </div>
        </form>
    </x-modal>
</section>
