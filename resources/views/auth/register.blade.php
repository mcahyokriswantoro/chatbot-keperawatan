@extends('layouts.auth')

@section('content')
    <x-auth.health-branding
        title="Daftar Akun Baru"
        subtitle="Pilih jenis akun yang ingin Anda daftarkan."
    />

    {{-- Role Picker --}}
    <div
        x-data="{
            role: @js(old('role', $role ?? 'pasien')),
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
        {{-- Role Selection Cards --}}
        <div class="mb-5 rounded-2xl border border-brand-100 bg-white p-4 shadow-card">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Daftar Sebagai</p>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                {{-- Pasien --}}
                <button
                    type="button"
                    @click="role = 'pasien'"
                    :style="{
                        backgroundColor: role === 'pasien' ? '#f0f9ff' : '#ffffff',
                        borderColor: role === 'pasien' ? '#0284c7' : '#e2e8f0',
                        color: role === 'pasien' ? '#0369a1' : '#475569'
                    }"
                    class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition-all duration-200 cursor-pointer"
                >
                    <span class="text-2xl" aria-hidden="true">🧑‍🦰</span>
                    <span class="text-xs font-semibold">Pasien</span>
                </button>

                {{-- Perawat --}}
                <button
                    type="button"
                    @click="role = 'perawat'"
                    :style="{
                        backgroundColor: role === 'perawat' ? '#ecfdf5' : '#ffffff',
                        borderColor: role === 'perawat' ? '#059669' : '#e2e8f0',
                        color: role === 'perawat' ? '#047857' : '#475569'
                    }"
                    class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition-all duration-200 cursor-pointer"
                >
                    <span class="text-2xl" aria-hidden="true">👩‍⚕️</span>
                    <span class="text-xs font-semibold">Perawat</span>
                </button>

                {{-- Dokter --}}
                <button
                    type="button"
                    @click="role = 'dokter'"
                    :style="{
                        backgroundColor: role === 'dokter' ? '#eff6ff' : '#ffffff',
                        borderColor: role === 'dokter' ? '#2563eb' : '#e2e8f0',
                        color: role === 'dokter' ? '#1d4ed8' : '#475569'
                    }"
                    class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition-all duration-200 cursor-pointer"
                >
                    <span class="text-2xl" aria-hidden="true">🩺</span>
                    <span class="text-xs font-semibold">Dokter</span>
                </button>

                {{-- Apotek --}}
                <button
                    type="button"
                    @click="role = 'apotek'"
                    :style="{
                        backgroundColor: role === 'apotek' ? '#fffbeb' : '#ffffff',
                        borderColor: role === 'apotek' ? '#d97706' : '#e2e8f0',
                        color: role === 'apotek' ? '#b45309' : '#475569'
                    }"
                    class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition-all duration-200 cursor-pointer"
                >
                    <span class="text-2xl" aria-hidden="true">💊</span>
                    <span class="text-xs font-semibold">Apotek</span>
                </button>

                {{-- Homecare --}}
                <button
                    type="button"
                    @click="role = 'homecare'"
                    :style="{
                        backgroundColor: role === 'homecare' ? '#f5f3ff' : '#ffffff',
                        borderColor: role === 'homecare' ? '#7c3aed' : '#e2e8f0',
                        color: role === 'homecare' ? '#6d28d9' : '#475569'
                    }"
                    class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-3 py-3 text-center transition-all duration-200 cursor-pointer"
                >
                    <span class="text-2xl" aria-hidden="true">🏠</span>
                    <span class="text-xs font-semibold">Homecare</span>
                </button>
            </div>
        </div>

        {{-- Role Description Badge --}}
        <div class="mb-4 flex items-center justify-center">
            <div
                x-show="role === 'pasien'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="inline-flex items-center gap-1.5 rounded-full bg-brand-100 px-3 py-1.5 text-xs font-medium text-brand-700"
            >
                <span>🧑‍🦰</span> Registrasi akun pasien untuk skrining & konsultasi kesehatan
            </div>
            <div
                x-show="role === 'perawat'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-medium text-emerald-700"
            >
                <span>👩‍⚕️</span> Registrasi akun perawat (Perlu Verifikasi Admin)
            </div>
            <div
                x-show="role === 'dokter'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700"
            >
                <span>🩺</span> Registrasi akun dokter (Perlu Verifikasi Admin)
            </div>
            <div
                x-show="role === 'apotek'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-medium text-amber-700"
            >
                <span>💊</span> Registrasi akun mitra apotek (Perlu Verifikasi Admin)
            </div>
            <div
                x-show="role === 'homecare'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="inline-flex items-center gap-1.5 rounded-full bg-violet-100 px-3 py-1.5 text-xs font-medium text-violet-700"
            >
                <span>🏠</span> Registrasi akun mitra homecare (Perlu Verifikasi Admin)
            </div>
        </div>

        {{-- Registration Form --}}
        <div class="rounded-2xl border border-brand-100 bg-white p-5 shadow-card">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="role" :value="role">

                {{-- ========== NAKES ONLY: Gelar Depan & Gelar Belakang ========== --}}
                <div x-show="role === 'perawat' || role === 'dokter'" x-transition class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="title_front" class="text-xs font-medium text-slate-600">Gelar Depan <span class="text-slate-400">(opsional)</span></label>
                        <input type="text" id="title_front" name="title_front" value="{{ old('title_front') }}"
                            :disabled="role !== 'perawat' && role !== 'dokter'"
                            :placeholder="role === 'perawat' ? 'Mis: Ns.' : 'Mis: dr.'"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>
                    <div>
                        <label for="title_back" class="text-xs font-medium text-slate-600">Gelar Belakang <span class="text-slate-400">(opsional)</span></label>
                        <input type="text" id="title_back" name="title_back" value="{{ old('title_back') }}"
                            :disabled="role !== 'perawat' && role !== 'dokter'"
                            :placeholder="role === 'perawat' ? 'Mis: S.Kep' : 'Mis: Sp.PD'"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    </div>
                </div>

                {{-- ========== COMMON: Nama ========== --}}
                <div>
                    <label for="name" class="text-xs font-medium text-slate-600">
                        <span x-show="role === 'apotek'">Nama Apotek</span>
                        <span x-show="role === 'homecare'">Nama Layanan</span>
                        <span x-show="role === 'perawat' || role === 'dokter'">Nama Lengkap (Tanpa Gelar)</span>
                        <span x-show="role === 'pasien'">Nama Lengkap</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        :placeholder="role === 'apotek' ? 'Apotek Sehat Jaya' : (role === 'homecare' ? 'Homecare Nusantara' : 'Nama lengkap Anda')"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('name')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== COMMON: Email ========== --}}
                <div>
                    <label for="email" class="text-xs font-medium text-slate-600">Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('email')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== Gender (Pasien, Perawat, Dokter) ========== --}}
                <div x-show="role === 'pasien' || role === 'perawat' || role === 'dokter'" x-transition>
                    <label for="gender" class="text-xs font-medium text-slate-600">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select id="gender" name="gender"
                        :required="role === 'pasien' || role === 'perawat' || role === 'dokter'"
                        :disabled="role === 'apotek' || role === 'homecare'"
                        class="mt-1 w-full rounded-xl border border-brand-200 bg-white px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                        <option value="laki-laki" @selected(old('gender') === 'laki-laki')>Laki-laki</option>
                        <option value="perempuan" @selected(old('gender') === 'perempuan')>Perempuan</option>
                    </select>
                    @error('gender')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== COMMON: No HP ========== --}}
                <div>
                    <label for="phone" class="text-xs font-medium text-slate-600">No HP <span class="text-rose-500">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" inputmode="tel" autocomplete="tel"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    <p class="mt-1 text-[11px] text-slate-400">Nomor ini bisa dipakai untuk masuk ke akun Anda.</p>
                    @error('phone')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== PASIEN ONLY: Tanggal Lahir & Usia ========== --}}
                <div x-show="role === 'pasien'" x-transition class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="date_of_birth" class="text-xs font-medium text-slate-600">Tanggal Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" id="date_of_birth" name="date_of_birth" x-model="dob" value="{{ old('date_of_birth') }}"
                            :required="role === 'pasien'"
                            :disabled="role !== 'pasien'"
                            max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('date_of_birth')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600">Usia</label>
                        <input type="text" readonly :value="age !== '' ? age + ' tahun' : 'Otomatis'" tabindex="-1"
                            class="mt-1 w-full rounded-xl border border-brand-100 bg-slate-50 px-3 py-2.5 text-sm text-slate-600">
                    </div>
                </div>

                {{-- ========== PASIEN ONLY: BB & TB ========== --}}
                <div x-show="role === 'pasien'" x-transition class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="weight" class="text-xs font-medium text-slate-600">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" id="weight" name="weight" value="{{ old('weight') }}"
                            :required="role === 'pasien'"
                            :disabled="role !== 'pasien'" min="1" max="500"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('weight')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="height" class="text-xs font-medium text-slate-600">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" id="height" name="height" value="{{ old('height') }}"
                            :required="role === 'pasien'"
                            :disabled="role !== 'pasien'" min="30" max="300"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('height')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- ========== PASIEN ONLY: Pekerjaan ========== --}}
                <div x-show="role === 'pasien'" x-transition>
                    <label for="occupation" class="text-xs font-medium text-slate-600">Pekerjaan <span class="text-rose-500">*</span></label>
                    <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}"
                        :required="role === 'pasien'"
                        :disabled="role !== 'pasien'"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('occupation')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== NAKES (Perawat/Dokter): STR, Spesialisasi, Pengalaman ========== --}}
                <div x-show="role === 'perawat' || role === 'dokter'" x-transition>
                    <label for="str_number" class="text-xs font-medium text-slate-600">
                        <span x-text="role === 'perawat' ? 'No. STR Perawat' : 'No. STR Dokter'"></span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="str_number" name="str_number" value="{{ old('str_number') }}"
                        :required="role === 'perawat' || role === 'dokter'"
                        :disabled="role !== 'perawat' && role !== 'dokter'"
                        placeholder="Masukkan nomor STR"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('str_number')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div x-show="role === 'perawat' || role === 'dokter'" x-transition class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="specialty" class="text-xs font-medium text-slate-600">Spesialisasi <span class="text-rose-500">*</span></label>
                        <input type="text" id="specialty" name="specialty" value="{{ old('specialty') }}"
                            :required="role === 'perawat' || role === 'dokter'"
                            :disabled="role !== 'perawat' && role !== 'dokter'"
                            :placeholder="role === 'perawat' ? 'Mis: Keperawatan Medikal Bedah' : 'Mis: Dokter Umum'"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('specialty')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="experience_years" class="text-xs font-medium text-slate-600">Pengalaman (thn) <span class="text-rose-500">*</span></label>
                        <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years') }}"
                            :required="role === 'perawat' || role === 'dokter'"
                            :disabled="role !== 'perawat' && role !== 'dokter'"
                            min="0" max="60" placeholder="5"
                            class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        @error('experience_years')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- ========== APOTEK & HOMECARE: SIPA/SIA & Izin Usaha ========== --}}
                <div x-show="role === 'apotek' || role === 'homecare'" x-transition>
                    <label for="license_number" class="text-xs font-medium text-slate-600">
                        <span x-show="role === 'apotek'">No. SIPA/SIA</span>
                        <span x-show="role === 'homecare'">No. Izin Usaha</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}"
                        :required="role === 'apotek' || role === 'homecare'"
                        :disabled="role !== 'apotek' && role !== 'homecare'"
                        :placeholder="role === 'apotek' ? 'Masukkan nomor SIPA atau SIA' : 'Masukkan nomor izin usaha'"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">
                    @error('license_number')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== COMMON: Alamat ========== --}}
                <div>
                    <label for="address" class="text-xs font-medium text-slate-600">
                        <span x-show="role === 'apotek'">Alamat Apotek</span>
                        <span x-show="role === 'homecare'">Alamat Kantor</span>
                        <span x-show="role !== 'apotek' && role !== 'homecare'">Alamat Domisili</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="2" required
                        :placeholder="role === 'apotek' ? 'Alamat lengkap apotek' : (role === 'homecare' ? 'Alamat kantor layanan' : 'Alamat domisili Anda')"
                        class="mt-1 w-full rounded-xl border border-brand-200 px-3 py-2.5 text-sm focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- ========== COMMON: Password ========== --}}
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

                {{-- Info box --}}
                <div
                    :style="{
                        backgroundColor: role === 'pasien' ? '#f0f9ff' : (role === 'perawat' ? '#ecfdf5' : (role === 'dokter' ? '#eff6ff' : (role === 'apotek' ? '#fffbeb' : '#f5f3ff'))),
                        color: role === 'pasien' ? '#075985' : (role === 'perawat' ? '#065f46' : (role === 'dokter' ? '#1e40af' : (role === 'apotek' ? '#92400e' : '#5b21b6')))
                    }"
                    class="rounded-xl px-3.5 py-3 text-xs leading-relaxed transition-all duration-200 border border-slate-100"
                >
                    <span x-show="role === 'pasien'">Semua kolom wajib diisi. Data Anda digunakan untuk skrining dan monitoring kesehatan. Akun pasien langsung aktif.</span>
                    <span x-show="role !== 'pasien'" x-cloak>⚠️ <strong>Perhatian:</strong> Registrasi mitra (Perawat, Dokter, Apotek, Homecare) membutuhkan <strong>verifikasi dan persetujuan Admin</strong> sebelum akun dapat digunakan untuk masuk.</span>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    :style="{
                        backgroundColor: role === 'pasien' ? '#0284c7' : (role === 'perawat' ? '#059669' : (role === 'dokter' ? '#2563eb' : (role === 'apotek' ? '#d97706' : '#7c3aed')))
                    }"
                    class="w-full rounded-full py-3.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:opacity-90 active:scale-[0.98] cursor-pointer"
                >
                    <span x-show="role === 'pasien'">Daftar Sebagai Pasien</span>
                    <span x-show="role === 'perawat'" x-cloak>Daftar Sebagai Perawat</span>
                    <span x-show="role === 'dokter'" x-cloak>Daftar Sebagai Dokter</span>
                    <span x-show="role === 'apotek'" x-cloak>Daftar Sebagai Apotek</span>
                    <span x-show="role === 'homecare'" x-cloak>Daftar Sebagai Homecare</span>
                </button>
            </form>
        </div>
    </div>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Masuk</a>
    </p>

    <a href="{{ route('home') }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full border border-brand-200 bg-white py-3 text-sm font-semibold text-brand-600 transition hover:bg-brand-50">
        Kembali ke Beranda
    </a>
@endsection
