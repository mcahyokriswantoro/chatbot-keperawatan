@extends('layouts.mobile')

@section('title', 'Booking Kunjungan Homecare')

@section('content')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<div class="space-y-4">
    {{-- Header --}}
    <header class="relative -mx-4 overflow-hidden bg-gradient-to-br from-[#00529c] via-[#004787] to-[#003366] px-5 py-4 text-white shadow-md sm:mx-0 sm:rounded-3xl">
        <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="relative flex items-center gap-3">
            <a href="{{ route('homecare.index') }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30" aria-label="Kembali">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-100">Layanan Homecare</p>
                <h1 class="text-base font-bold">Booking Kunjungan</h1>
            </div>
        </div>
    </header>

    {{-- Package Summary --}}
    <div class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm flex items-center gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#00529c]/5 text-xl">
            {{ $package->icon }}
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Paket yang dipilih</p>
            <h2 class="text-xs font-bold text-slate-800">{{ $package->name }}</h2>
            <p class="text-xs font-bold text-[#00529c] mt-0.5">{{ $priceLabel }}</p>
        </div>
    </div>

    {{-- Booking Form --}}
    <form
        x-data="{
            packagePrice: {{ $package->price }},
            serviceFee: 3000,
            transportFeePerKm: {{ $transportFeePerKm }},
            distanceKm: null,
            closestCampus: '',
            transportFee: 0,
            totalPrice: {{ $package->price + 3000 }},
            latitude: '',
            longitude: '',
            geocoding: false,
            geocodingError: '',
            bookingDateOnly: '{{ old('booking_date_only', now()->addDay()->format('Y-m-d')) }}',
            bookingTimeOnly: '{{ old('booking_time_only', '09:00') }}',
            
            formatRupiah(amount) {
                return 'Rp ' + amount.toLocaleString('id-ID');
            },
            calculateTransport() {
                if (this.distanceKm === null || this.distanceKm > 25) {
                    this.transportFee = 0;
                    this.totalPrice = this.packagePrice + this.serviceFee;
                    return;
                }
                this.transportFee = Math.round(this.distanceKm * this.transportFeePerKm);
                this.totalPrice = this.packagePrice + this.serviceFee + this.transportFee;
            },
            init() {
                window.alpineForm = this;
            }
        }"
        method="POST"
        action="{{ route('homecare.store-booking', $package) }}"
    >
        @csrf
        <input type="hidden" name="latitude" x-model="latitude">
        <input type="hidden" name="longitude" x-model="longitude">
        <input type="hidden" name="distance_km" x-model="distanceKm">

        <div class="space-y-4 lg:grid lg:grid-cols-12 lg:gap-6 lg:space-y-0">
            <div class="lg:col-span-7 space-y-4">
                <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Data Pasien</h2>
            
            <div class="space-y-1">
                <label class="block text-[11px] font-medium text-slate-500">Nama Lengkap Pasien</label>
                <input
                    type="text"
                    name="patient_name"
                    value="{{ old('patient_name', auth()->user()->name) }}"
                    required
                    placeholder="Contoh: Budi Santoso"
                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15"
                >
                @error('patient_name')
                    <p class="text-[10px] text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-[11px] font-medium text-slate-500">Nomor Handphone Pasien (WhatsApp)</label>
                <input
                    type="text"
                    name="patient_phone"
                    value="{{ old('patient_phone', auth()->user()->phone) }}"
                    required
                    placeholder="Contoh: 081234567890"
                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15"
                >
                @error('patient_phone')
                    <p class="text-[10px] text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-[11px] font-medium text-slate-500">Tanggal Kunjungan</label>
                    <input
                        type="date"
                        name="booking_date_only"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        x-model="bookingDateOnly"
                        required
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15 text-slate-700"
                    >
                    @error('booking_date_only')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-medium text-slate-500">Jam Kunjungan (WIB)</label>
                    <select
                        name="booking_time_only"
                        x-model="bookingTimeOnly"
                        required
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15 text-slate-750 font-semibold"
                    >
                        <option value="09:00">09:00 WIB</option>
                        <option value="09:30">09:30 WIB</option>
                        <option value="10:00">10:00 WIB</option>
                        <option value="10:30">10:30 WIB</option>
                        <option value="11:00">11:00 WIB</option>
                        <option value="11:30">11:30 WIB</option>
                        <option value="12:00">12:00 WIB</option>
                        <option value="12:30">12:30 WIB</option>
                        <option value="13:00">13:00 WIB</option>
                        <option value="13:30">13:30 WIB</option>
                        <option value="14:00">14:00 WIB</option>
                        <option value="14:30">14:30 WIB</option>
                        <option value="15:00">15:00 WIB</option>
                        <option value="15:30">15:30 WIB</option>
                        <option value="16:00">16:00 WIB</option>
                        <option value="16:30">16:30 WIB</option>
                        <option value="17:00">17:00 WIB</option>
                    </select>
                    @error('booking_time_only')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="space-y-1 mt-1 text-[9px] leading-normal text-slate-400">
                <p>• Minimal pemesanan H-1 kunjungan perawat.</p>
                <p>• Jam pelayanan kunjungan homecare: <strong>09:00 s.d 17:00 WIB</strong>.</p>
            </div>
        </section>

        <section
            class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-3"
            x-data="{
                editing: false,
                saving: false,
                saved: false,
                savedAddress: @js(old('address', auth()->user()?->address ?? '')),
                draftAddress: @js(old('address', auth()->user()?->address ?? '')),
                saveAddress() {
                    if (!this.draftAddress || this.draftAddress.trim().length < 10) {
                        alert('Alamat pengiriman minimal 10 karakter.');
                        return;
                    }
                    this.saving = true;
                    this.saved = false;
                    fetch('{{ route('profile.update.address') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ address: this.draftAddress.trim() })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.savedAddress = this.draftAddress.trim();
                            this.editing = false;
                            this.saved = true;
                            setTimeout(() => this.saved = false, 3000);
                            this.$nextTick(() => {
                                if (typeof window.searchAddressOnMap === 'function') {
                                    window.searchAddressOnMap();
                                }
                            });
                        } else {
                            alert(data.message || 'Gagal menyimpan alamat.');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan jaringan. Coba lagi.'))
                    .finally(() => this.saving = false);
                },
                cancelEdit() {
                    this.draftAddress = this.savedAddress;
                    this.editing = false;
                }
            }"
        >
            <input type="hidden" name="address" :value="savedAddress">

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-[#00529c]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    Alamat Kunjungan Perawat
                </h2>
                <button
                    type="button"
                    x-show="!editing"
                    @click="editing = true"
                    class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-bold text-[#00529c] shadow-sm transition hover:bg-slate-50 hover:border-[#00529c]/30 active:scale-[0.97]"
                >
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    Ubah Alamat
                </button>
            </div>

            {{-- Saved message --}}
            <div
                x-show="saved"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="flex items-center gap-1.5 rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-[11px] font-semibold text-emerald-700"
                x-cloak
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Alamat berhasil disimpan!
            </div>

            {{-- Display mode --}}
            <div x-show="!editing" x-cloak>
                <template x-if="savedAddress && savedAddress.trim().length > 0">
                    <div class="rounded-xl bg-slate-50 border border-slate-100 px-3.5 py-3 text-xs text-slate-700 leading-relaxed">
                        <span x-text="savedAddress"></span>
                    </div>
                </template>
                <template x-if="!savedAddress || savedAddress.trim().length === 0">
                    <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 px-3.5 py-4 text-center">
                        <p class="text-[11px] text-slate-400 font-medium">Belum ada alamat tersimpan.</p>
                        <button
                            type="button"
                            @click="editing = true"
                            class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-[#00529c] hover:underline"
                        >
                            + Tambah Alamat Kunjungan
                        </button>
                    </div>
                </template>
            </div>

            {{-- Edit mode --}}
            <div x-show="editing" x-cloak class="space-y-2.5">
                <textarea
                    x-model="draftAddress"
                    rows="3"
                    placeholder="Masukkan alamat lokasi kunjungan perawat (Nomor rumah, RT/RW, kelurahan, kecamatan, kota, patokan jalan)..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15 shadow-inner transition"
                ></textarea>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="saveAddress()"
                        :disabled="saving"
                        class="inline-flex items-center gap-1.5 rounded-full bg-[#00529c] px-4 py-2 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#004787] active:scale-[0.97] disabled:opacity-50"
                    >
                        <svg x-show="!saving" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span x-show="!saving">Simpan Alamat</span>
                        <span x-show="saving">Menyimpan...</span>
                    </button>
                    <button
                        type="button"
                        @click="cancelEdit()"
                        :disabled="saving"
                        class="rounded-full border border-slate-200 bg-white px-3.5 py-2 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                    >
                        Batal
                    </button>
                </div>
            </div>

            @error('address')
                <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
            @enderror

            {{-- Maps Area --}}
            <div class="space-y-2 pt-2 border-t border-slate-100">
                <p class="text-[11px] font-medium text-slate-500">Tentukan Lokasi Anda di Peta untuk Menghitung Transport:</p>
                <div class="flex gap-2">
                    <button
                        type="button"
                        @click="searchAddressOnMap()"
                        :disabled="geocoding"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:opacity-50"
                    >
                        <span x-show="!geocoding">🔍 Cari Alamat di Peta</span>
                        <span x-show="geocoding">⏳ Mencari lokasi...</span>
                    </button>
                    <button
                        type="button"
                        @click="getCurrentLocation()"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-[#00529c] shadow-sm transition hover:bg-slate-50"
                    >
                        📍 Gunakan GPS Saya
                    </button>
                </div>
                <p x-show="geocodingError" x-text="geocodingError" class="text-[10px] font-semibold text-rose-650" x-cloak></p>
                
                <div id="map" class="h-48 w-full rounded-2xl border border-slate-200 shadow-inner z-0" style="min-height: 220px;"></div>
                
                <div x-show="distanceKm !== null" class="rounded-xl border p-3 space-y-1 text-xs" :class="distanceKm > 25 ? 'bg-rose-50/70 border-rose-200' : 'bg-[#00529c]/5 border-[#00529c]/10'" x-cloak>
                    <p class="flex justify-between">
                        <span class="text-slate-500">Medical Center UMLA Terdekat:</span>
                        <span class="font-bold text-slate-800" x-text="closestCampus"></span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-slate-500">Jarak Kunjungan:</span>
                        <span class="font-bold" :class="distanceKm > 25 ? 'text-rose-600 font-extrabold' : 'text-slate-800'" x-text="distanceKm + ' km'"></span>
                    </p>
                    <template x-if="distanceKm <= 25">
                        <p class="flex justify-between">
                            <span class="text-slate-500">Tarif Transport:</span>
                            <span class="font-bold text-[#00529c]" x-text="formatRupiah(transportFee)"></span>
                        </p>
                    </template>
                    <template x-if="distanceKm > 25">
                        <div class="mt-2 rounded-lg bg-rose-100/80 border border-rose-300 p-2.5 text-xs text-rose-800 space-y-0.5">
                            <p class="font-bold flex items-center gap-1">
                                <span>⚠️</span> Melebihi Jarak Maksimal (25 KM)
                            </p>
                            <p class="text-[11px] leading-relaxed text-rose-700">
                                Mohon maaf, lokasi kunjungan Anda (<span class="font-bold" x-text="distanceKm + ' km'"></span>) melebihi jangkauan maksimal 25 km dari <span class="font-bold" x-text="closestCampus"></span> sehingga layanan Homecare tidak dapat diproses.
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </section>

            </div>

            {{-- Column 2: Summary Card & Submit --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Summary Card --}}
        <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-slate-800 mb-3">Ringkasan Pembayaran</h2>
            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Harga Paket Homecare</span>
                    <span class="font-semibold text-slate-800" x-text="formatRupiah(packagePrice)">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                </div>
                <div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Biaya Administrasi</span>
                        <template x-if="distanceKm === null">
                            <span class="font-semibold text-slate-800" x-text="formatRupiah(serviceFee + transportFee)">Rp 3.000</span>
                        </template>
                        <template x-if="distanceKm !== null && distanceKm <= 25">
                            <span class="font-semibold text-slate-800" x-text="formatRupiah(serviceFee + transportFee)">Rp 3.000</span>
                        </template>
                        <template x-if="distanceKm !== null && distanceKm > 25">
                            <span class="font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200 text-[11px]">
                                Tidak tersedia (> 25 km)
                            </span>
                        </template>
                    </div>
                    <p class="mt-0.5 text-[11px] text-slate-400">
                        Termasuk biaya layanan (<span x-text="formatRupiah(serviceFee)">Rp 3.000</span>) & transportasi (<span x-text="distanceKm === null ? '📍 Tentukan lokasi di peta' : (distanceKm > 25 ? 'Melebihi 25 km' : formatRupiah(transportFee))">Rp 0</span>)
                    </p>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-2.5 text-sm font-bold">
                    <span class="text-slate-800">Total Tagihan</span>
                    <span class="text-[#00529c] font-black" x-text="formatRupiah(totalPrice)">Rp {{ number_format($package->price + 3000, 0, ',', '.') }}</span>
                </div>
            </div>
        </section>

        {{-- Payment Action Card --}}
        <section class="rounded-2xl border border-[#00529c]/20 bg-[#00529c]/5 p-4 space-y-3">
            <h3 class="text-sm font-bold text-slate-900">Bayar via Transfer Bank (Giro BRI)</h3>
            <p class="text-xs text-slate-600">Transfer + upload bukti transfer, lalu tunggu verifikasi admin.</p>
            <button
                type="submit"
                :disabled="distanceKm !== null && distanceKm > 25"
                :class="(distanceKm !== null && distanceKm > 25) ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-[#00529c] hover:bg-[#004787] active:scale-[0.98]'"
                class="flex w-full items-center justify-center gap-2 rounded-full py-3.5 text-sm font-bold text-white shadow-sm transition"
            >
                <template x-if="distanceKm === null || distanceKm <= 25">
                    <span>Bayar <span x-text="formatRupiah(totalPrice)">Rp {{ number_format($package->price, 0, ',', '.') }}</span></span>
                </template>
                <template x-if="distanceKm !== null && distanceKm > 25">
                    <span>⚠️ Jarak Kunjungan > 25 KM (Tidak Dapat Diproses)</span>
                </template>
            </button>
        </section>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Leaflet Map Implementation
    document.addEventListener('DOMContentLoaded', function () {
        // Coordinates for UMLA campuses [lat, lng]
        const umla1 = [-7.10444, 112.38778]; // Kampus 1 (Utama - Plosowahyu)
        const umla2 = [-6.8703, 112.3397];   // Kampus 2 (Paciran)

        // Initialize Leaflet map
        const map = L.map('map').setView([-7.05, 112.40], 10);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors © CARTO'
        }).addTo(map);

        setTimeout(function() {
            map.invalidateSize();
        }, 300);

        // UMLA campus markers
        L.marker(umla1, {
            icon: L.divIcon({
                html: '🏫',
                className: 'text-2xl flex items-center justify-center',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        }).addTo(map).bindPopup('<b>Medical Center UMLA 1</b><br>Jl. Raya Plalangan, Plosowahyu');

        L.marker(umla2, {
            icon: L.divIcon({
                html: '🏥',
                className: 'text-2xl flex items-center justify-center',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        }).addTo(map).bindPopup('<b>Medical Center UMLA 2</b><br>Paciran, Lamongan');

        let userMarker = null;
        let distanceLine = null;

        // Helper: Haversine distance
        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of earth in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        window.updateLocation = function(lat, lon) {
            const alpine = window.alpineForm;
            if (!alpine) return;
            alpine.latitude = lat;
            alpine.longitude = lon;

            if (userMarker) {
                userMarker.setLatLng([lat, lon]);
            } else {
                userMarker = L.marker([lat, lon], { draggable: true }).addTo(map);
                userMarker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    window.updateLocation(pos.lat, pos.lng);
                });
            }

            map.setView([lat, lon], 13);

            // Query OSRM for driving route
            const osrmUrl1 = `https://router.project-osrm.org/route/v1/driving/${lon},${lat};${umla1[1]},${umla1[0]}?overview=full&geometries=geojson`;
            const osrmUrl2 = `https://router.project-osrm.org/route/v1/driving/${lon},${lat};${umla2[1]},${umla2[0]}?overview=full&geometries=geojson`;

            Promise.all([
                fetch(osrmUrl1).then(r => r.json()).catch(() => null),
                fetch(osrmUrl2).then(r => r.json()).catch(() => null)
            ]).then(([data1, data2]) => {
                let route1 = (data1 && data1.code === 'Ok' && data1.routes && data1.routes.length > 0) ? data1.routes[0] : null;
                let route2 = (data2 && data2.code === 'Ok' && data2.routes && data2.routes.length > 0) ? data2.routes[0] : null;

                let selectedRoute = null;
                let selectedName = '';
                let selectedCoords = null;
                let selectedDist = null;

                if (route1 && route2) {
                    const d1 = route1.distance / 1000;
                    const d2 = route2.distance / 1000;
                    if (d1 <= d2) {
                        selectedRoute = route1;
                        selectedName = 'Medical Center UMLA 1';
                        selectedCoords = umla1;
                        selectedDist = d1;
                    } else {
                        selectedRoute = route2;
                        selectedName = 'Medical Center UMLA 2';
                        selectedCoords = umla2;
                        selectedDist = d2;
                    }
                } else if (route1) {
                    selectedRoute = route1;
                    selectedName = 'Medical Center UMLA 1';
                    selectedCoords = umla1;
                    selectedDist = route1.distance / 1000;
                } else if (route2) {
                    selectedRoute = route2;
                    selectedName = 'Medical Center UMLA 2';
                    selectedCoords = umla2;
                    selectedDist = route2.distance / 1000;
                }

                if (selectedRoute) {
                    alpine.distanceKm = parseFloat(selectedDist.toFixed(2));
                    alpine.closestCampus = selectedName;
                    alpine.calculateTransport();

                    if (distanceLine) map.removeLayer(distanceLine);
                    distanceLine = L.geoJSON(selectedRoute.geometry, {
                        style: { color: '#00529c', weight: 5, opacity: 0.9 }
                    }).addTo(map);
                } else {
                    useFallback();
                }
            }).catch(() => useFallback());

            function useFallback() {
                const dist1 = getDistance(lat, lon, umla1[0], umla1[1]);
                const dist2 = getDistance(lat, lon, umla2[0], umla2[1]);

                let closestCoords = umla1;
                let closestName = 'Medical Center UMLA 1';
                let fallbackDist = dist1;

                if (dist2 < dist1) {
                    closestCoords = umla2;
                    closestName = 'Medical Center UMLA 2';
                    fallbackDist = dist2;
                }

                const correctedDist = fallbackDist * 1.3;
                alpine.distanceKm = parseFloat(correctedDist.toFixed(2));
                alpine.closestCampus = closestName;
                alpine.calculateTransport();

                if (distanceLine) map.removeLayer(distanceLine);
                distanceLine = L.polyline([[lat, lon], closestCoords], {
                    color: '#00529c', weight: 4, dashArray: '5, 10'
                }).addTo(map);
            }
        };

        // Click map
        map.on('click', function(e) {
            window.updateLocation(e.latlng.lat, e.latlng.lng);
        });

        // Geocoding via Nominatim with progressive fallback & keyword cleaning
        window.searchAddressOnMap = function() {
            const alpine = window.alpineForm;
            const inputElem = document.querySelector('textarea[name="address"]') || document.querySelector('input[name="address"]');
            const originalAddress = inputElem ? inputElem.value : '';

            if (!originalAddress || originalAddress.trim().length < 3) {
                alpine.geocodingError = 'Silakan tulis alamat lengkap terlebih dahulu.';
                return;
            }

            alpine.geocoding = true;
            alpine.geocodingError = '';

            function cleanQuery(str) {
                let clean = str;
                clean = clean.replace(/\bkecamatan\b|\bkec\b\.?/gi, '');
                clean = clean.replace(/\bkelurahan\b|\bkel\b\.?/gi, '');
                clean = clean.replace(/\bdesa\b|\bds\b\.?/gi, '');
                clean = clean.replace(/\bkabupaten\b|\bkab\b\.?/gi, '');
                clean = clean.replace(/\bkota\b/gi, '');
                clean = clean.replace(/rt\s*\.?\s*\d+\s*[\/\-]?\s*rw\s*\.?\s*\d+/gi, '');
                clean = clean.replace(/rt\s*\.?\s*\d+/gi, '');
                clean = clean.replace(/rw\s*\.?\s*\d+/gi, '');
                clean = clean.replace(/(?:no|nomor)\s*\.?\s*\d+[a-z]?/gi, '');
                clean = clean.replace(/blok\s*[a-z0-9\-\/]+/gi, '');
                clean = clean.replace(/(?:gang|gg\.)\s*[a-z0-9]+/gi, '');
                clean = clean.replace(/\s+/g, ' ');
                clean = clean.replace(/,\s*,/g, ',');
                clean = clean.replace(/^\s*,|,\s*$/g, '');
                return clean.trim();
            }

            const cleaned = cleanQuery(originalAddress);
            let queries = [];

            function formatQ(s) {
                return s.toLowerCase().includes('indonesia') ? s : s + ', Indonesia';
            }

            if (cleaned.length > 0) queries.push(formatQ(cleaned));
            queries.push(formatQ(originalAddress.trim()));

            const words = cleaned.split(/\s+/).filter(Boolean);
            if (words.length > 2) {
                queries.push(formatQ(words.slice(1).join(' ')));
            }

            function tryQuery(idx) {
                if (idx >= queries.length) {
                    alpine.geocoding = false;
                    alpine.geocodingError = '📍 Alamat tidak ditemukan di database peta. Silakan KLIK atau GESER PIN (ikon biru) di peta ke lokasi rumah Anda yang tepat.';
                    return;
                }

                const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(queries[idx])}&format=json&limit=1&email=admin@nersia.com`;

                fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (data && data.length > 0) {
                        window.updateLocation(parseFloat(data[0].lat), parseFloat(data[0].lon));
                        alpine.geocoding = false;
                    } else {
                        setTimeout(() => tryQuery(idx + 1), 300);
                    }
                })
                .catch(() => {
                    setTimeout(() => tryQuery(idx + 1), 300);
                });
            }

            tryQuery(0);
        };

        // Geolocation
        window.getCurrentLocation = function() {
            const alpine = window.alpineForm;
            alpine.geocodingError = '';

            if (!navigator.geolocation) {
                alpine.geocodingError = 'Browser Anda tidak mendukung deteksi lokasi.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    window.updateLocation(position.coords.latitude, position.coords.longitude);
                },
                function(err) {
                    alpine.geocodingError = 'Gagal mengakses GPS perangkat Anda. Silakan pilih lokasi secara manual di peta.';
                }
            );
        };

        // Auto-search address on load
        const initialAddressInput = document.querySelector('input[name="address"]') || document.querySelector('textarea[name="address"]');
        if (initialAddressInput && initialAddressInput.value && initialAddressInput.value.trim().length >= 4) {
            setTimeout(function() {
                window.searchAddressOnMap();
            }, 600);
        }
    });
</script>
@endpush

