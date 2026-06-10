<section>
    <header class="mb-4">
        <h2 class="text-base font-bold text-slate-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-slate-500">
            Perbarui data diri Anda seperti saat pendaftaran.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="space-y-4"
        x-data="{
            dob: @js(old('date_of_birth', $user->date_of_birth?->format('Y-m-d') ?? '')),
            preview: null,
            get age() {
                if (!this.dob) return '';
                const birth = new Date(this.dob + 'T00:00:00');
                if (isNaN(birth.getTime())) return '';
                const today = new Date();
                let years = today.getFullYear() - birth.getFullYear();
                const monthDiff = today.getMonth() - birth.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                    years--;
                }
                return years >= 0 ? years : '';
            },
            onPhotoChange(event) {
                const file = event.target.files?.[0];
                this.preview = file ? URL.createObjectURL(file) : null;
            },
        }"
    >
        @csrf
        @method('patch')

        {{-- Foto profil --}}
        <div class="rounded-2xl border border-brand-100 bg-brand-50/40 p-4">
            <p class="text-xs font-medium text-slate-600">Foto Profil</p>
            <div class="mt-3 flex items-center gap-4">
                <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-white ring-2 ring-brand-100">
                    <img
                        :src="preview || @js($user->profilePhotoUrl())"
                        alt="Foto profil"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div class="min-w-0 flex-1 space-y-2">
                    <label class="inline-flex cursor-pointer items-center justify-center rounded-full border border-brand-200 bg-white px-4 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                        Pilih Foto
                        <input
                            type="file"
                            name="profile_photo"
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                            @change="onPhotoChange($event)"
                        />
                    </label>
                    @if ($user->profile_photo)
                        <label class="flex items-center gap-2 text-[11px] text-slate-500">
                            <input type="checkbox" name="remove_profile_photo" value="1" class="rounded border-brand-200 text-brand-600 focus:ring-brand-200">
                            Hapus foto, pakai robot default
                        </label>
                    @else
                        <p class="text-[11px] leading-relaxed text-slate-500">
                            Belum upload? Robot {{ $user->isFemale() ? 'wanita' : 'pria' }} dipakai otomatis.
                        </p>
                    @endif
                    @error('profile_photo')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div>
            <label for="name" class="text-xs font-medium text-slate-600">Nama <span class="text-rose-500">*</span></label>
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
            <label for="email" class="text-xs font-medium text-slate-600">Email <span class="text-rose-500">*</span></label>
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

        <div>
            <label for="gender" class="text-xs font-medium text-slate-600">Jenis Kelamin <span class="text-rose-500">*</span></label>
            <select
                id="gender"
                name="gender"
                required
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
                <option value="" disabled {{ old('gender', $user->gender) ? '' : 'selected' }}>Pilih jenis kelamin</option>
                <option value="laki-laki" @selected(old('gender', $user->gender) === 'laki-laki')>Laki-laki</option>
                <option value="perempuan" @selected(old('gender', $user->gender) === 'perempuan')>Perempuan</option>
            </select>
            @error('gender')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="text-xs font-medium text-slate-600">No HP <span class="text-rose-500">*</span></label>
            <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone', $user->phone) }}"
                required
                placeholder="08xxxxxxxxxx"
                inputmode="tel"
                autocomplete="tel"
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            <p class="mt-1 text-[11px] text-slate-400">Nomor ini bisa dipakai untuk masuk ke akun Anda.</p>
            @error('phone')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="date_of_birth" class="text-xs font-medium text-slate-600">Tanggal Lahir <span class="text-rose-500">*</span></label>
                <input
                    type="date"
                    id="date_of_birth"
                    name="date_of_birth"
                    x-model="dob"
                    value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                    required
                    max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('date_of_birth')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-medium text-slate-600">Usia</label>
                <input
                    type="text"
                    readonly
                    :value="age !== '' ? age + ' tahun' : 'Otomatis'"
                    tabindex="-1"
                    class="mt-1 w-full rounded-xl border border-brand-100 bg-slate-50 px-3 py-2.5 text-sm text-slate-600"
                >
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="weight" class="text-xs font-medium text-slate-600">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                <input
                    type="number"
                    step="0.1"
                    id="weight"
                    name="weight"
                    value="{{ old('weight', $user->weight) }}"
                    required
                    min="1"
                    max="500"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('weight')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="height" class="text-xs font-medium text-slate-600">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                <input
                    type="number"
                    step="0.1"
                    id="height"
                    name="height"
                    value="{{ old('height', $user->height) }}"
                    required
                    min="30"
                    max="300"
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                >
                @error('height')
                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="address" class="text-xs font-medium text-slate-600">Alamat Domisili <span class="text-rose-500">*</span></label>
            <textarea
                id="address"
                name="address"
                rows="2"
                required
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >{{ old('address', $user->address) }}</textarea>
            @error('address')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="occupation" class="text-xs font-medium text-slate-600">Pekerjaan <span class="text-rose-500">*</span></label>
            <input
                type="text"
                id="occupation"
                name="occupation"
                value="{{ old('occupation', $user->occupation) }}"
                required
                class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
            @error('occupation')
                <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <button
                type="submit"
                class="w-full rounded-full bg-brand-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98] sm:w-auto sm:px-8"
            >
                Simpan Perubahan
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
