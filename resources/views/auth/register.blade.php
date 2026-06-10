@extends('layouts.auth')

@section('content')
    <x-auth.health-branding
        title="Daftar Akun Baru"
        subtitle="Lengkapi data diri Anda untuk memulai perawatan mandiri dan skrining kesehatan."
    />

    <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
        <form
            method="POST"
            action="{{ route('register') }}"
            class="space-y-4"
            x-data="{
                dob: @js(old('date_of_birth', '')),
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
                }
            }"
        >
            @csrf

            <div>
                <label for="name" class="text-xs font-medium text-slate-600">Nama <span class="text-rose-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                @error('name')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="text-xs font-medium text-slate-600">Email <span class="text-rose-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                @error('email')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="gender" class="text-xs font-medium text-slate-600">Jenis Kelamin <span class="text-rose-500">*</span></label>
                <select id="gender" name="gender" required
                    class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                    <option value="laki-laki" @selected(old('gender') === 'laki-laki')>Laki-laki</option>
                    <option value="perempuan" @selected(old('gender') === 'perempuan')>Perempuan</option>
                </select>
                @error('gender')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="text-xs font-medium text-slate-600">No HP <span class="text-rose-500">*</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" inputmode="tel" autocomplete="tel"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                <p class="mt-1 text-[11px] text-slate-400">Nomor ini bisa dipakai untuk masuk ke akun Anda.</p>
                @error('phone')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="date_of_birth" class="text-xs font-medium text-slate-600">Tanggal Lahir <span class="text-rose-500">*</span></label>
                    <input type="date" id="date_of_birth" name="date_of_birth" x-model="dob" value="{{ old('date_of_birth') }}" required max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('date_of_birth')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-600">Usia</label>
                    <input type="text" readonly :value="age !== '' ? age + ' tahun' : 'Otomatis'" tabindex="-1"
                        class="mt-1 w-full rounded-xl border border-brand-100 bg-slate-50 px-3 py-2.5 text-sm text-slate-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="weight" class="text-xs font-medium text-slate-600">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" id="weight" name="weight" value="{{ old('weight') }}" required min="1" max="500"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('weight')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="height" class="text-xs font-medium text-slate-600">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" id="height" name="height" value="{{ old('height') }}" required min="30" max="300"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('height')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="address" class="text-xs font-medium text-slate-600">Alamat Domisili <span class="text-rose-500">*</span></label>
                <textarea id="address" name="address" rows="2" required
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">{{ old('address') }}</textarea>
                @error('address')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="occupation" class="text-xs font-medium text-slate-600">Pekerjaan <span class="text-rose-500">*</span></label>
                <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" required
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                @error('occupation')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="text-xs font-medium text-slate-600">Kata Sandi <span class="text-rose-500">*</span></label>
                <input type="password" id="password" name="password" required autocomplete="new-password"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                @error('password')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-xs font-medium text-slate-600">Ulangi Kata Sandi <span class="text-rose-500">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                    class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
            </div>

            <p class="rounded-xl bg-brand-50 px-3 py-2 text-[11px] leading-relaxed text-brand-800">
                Semua kolom wajib diisi. Tidak ada bagian yang boleh dilewati.
            </p>

            <button type="submit" class="w-full rounded-full bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:bg-brand-700 active:scale-[0.98]">
                Daftar Sekarang
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Masuk</a>
    </p>

    <a href="{{ route('home') }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50">
        Kembali ke Beranda
    </a>
@endsection
