@extends('layouts.mobile')

@section('title', 'Booking Kunjungan Homecare')

@section('content')
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
            transportFeePerKm: {{ $transportFeePerKm }},
            distanceKm: null,
            closestCampus: '',
            transportFee: 0,
            totalPrice: {{ $package->price }},
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
                if (this.distanceKm === null) {
                    this.transportFee = 0;
                    this.totalPrice = this.packagePrice;
                    return;
                }
                this.transportFee = Math.round(this.distanceKm * this.transportFeePerKm);
                this.totalPrice = this.packagePrice + this.transportFee;
            },
            init() {
                window.alpineForm = this;
            }
        }"
        method="POST"
        action="{{ route('homecare.store-booking', $package) }}"
        class="space-y-4"
    >
        @csrf

        <input type="hidden" name="latitude" x-model="latitude">
        <input type="hidden" name="longitude" x-model="longitude">
        <input type="hidden" name="distance_km" x-model="distanceKm">

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

        <section class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm space-y-2">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Alamat Kunjungan Perawat</h2>
            <textarea
                name="address"
                rows="3"
                required
                placeholder="Masukkan alamat lengkap lokasi kunjungan perawat (Nomor rumah, RT/RW, kelurahan, kecamatan, kota, patokan jalan)..."
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs focus:border-[#00529c] focus:outline-none focus:ring-2 focus:ring-[#00529c]/15 shadow-inner"
            >{{ old('address', auth()->user()->address) }}</textarea>
            @error('address')
                <p class="text-[10px] text-rose-600 font-semibold">{{ $message }}</p>
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
                
                <div x-show="distanceKm !== null" class="rounded-xl bg-[#00529c]/5 border border-[#00529c]/10 p-3 space-y-1 text-xs" x-cloak>
                    <p class="flex justify-between">
                        <span class="text-slate-500">Medical Center UMLA Terdekat:</span>
                        <span class="font-bold text-slate-800" x-text="closestCampus"></span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-slate-500">Jarak Kunjungan:</span>
                        <span class="font-bold text-slate-800" x-text="distanceKm + ' km'"></span>
                    </p>
                    <p class="flex justify-between">
                        <span class="text-slate-500">Tarif Transport (Rp <span x-text="transportFeePerKm.toLocaleString('id-ID')"></span>/km):</span>
                        <span class="font-bold text-[#00529c]" x-text="formatRupiah(transportFee)"></span>
                    </p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-[#00529c]/20 bg-[#00529c]/5 p-4 space-y-3">
            <h3 class="text-sm font-bold text-slate-900">Bayar via Transfer Bank (Giro BRI)</h3>
            <div class="space-y-1.5 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span>Biaya Layanan:</span>
                    <span class="font-semibold text-slate-850" x-text="formatRupiah(packagePrice)"></span>
                </div>
                <div class="flex justify-between" x-show="distanceKm !== null" x-cloak>
                    <span>Biaya Transport:</span>
                    <span class="font-semibold text-[#00529c]" x-text="formatRupiah(transportFee)"></span>
                </div>
                <div class="flex justify-between border-t border-slate-250 pt-1.5 text-sm font-extrabold text-slate-900">
                    <span>Total Transfer:</span>
                    <span class="text-lg text-[#00529c]" x-text="formatRupiah(totalPrice)"></span>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Total transfer akan diverifikasi otomatis setelah Anda mengunggah bukti pembayaran.</p>
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-full bg-[#00529c] py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#004787] active:scale-[0.98]"
            >
                Lanjutkan Pembayaran
            </button>
        </section>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Coordinates for UMLA campuses
    const umla1 = [-7.10444, 112.38778]; // Kampus 1 (Utama - Plosowahyu)
    const umla2 = [-6.8703, 112.3397]; // Kampus 2 (Paciran)

    // Initialize map
    const map = L.map('map').setView([-7.05, 112.40], 10); // Center around Lamongan

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors © CARTO'
    }).addTo(map);

    setTimeout(function() {
        map.invalidateSize();
    }, 200);

    // UMLA campus markers
    const markerUmla1 = L.marker(umla1, {
        icon: L.divIcon({
            html: '🏫',
            className: 'text-2xl flex items-center justify-center',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        })
    }).addTo(map).bindPopup('<b>Medical Center UMLA 1</b><br>Jl. Raya Plalangan, Plosowahyu');

    const markerUmla2 = L.marker(umla2, {
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

    // Function to place marker and calculate distance
    window.updateLocation = function(lat, lon) {
        const alpine = window.alpineForm;
        alpine.latitude = lat;
        alpine.longitude = lon;

        if (userMarker) {
            userMarker.setLatLng([lat, lon]);
        } else {
            userMarker = L.marker([lat, lon], { draggable: true }).addTo(map);
            userMarker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                window.updateLocation(position.lat, position.lng);
            });
        }

        map.setView([lat, lon], 13);

        // Query both campuses in parallel via OSRM
        const osrmUrl1 = `https://router.project-osrm.org/route/v1/driving/${lon},${lat};${umla1[1]},${umla1[0]}?overview=full&geometries=geojson`;
        const osrmUrl2 = `https://router.project-osrm.org/route/v1/driving/${lon},${lat};${umla2[1]},${umla2[0]}?overview=full&geometries=geojson`;

        Promise.all([
            fetch(osrmUrl1).then(res => res.json()).catch(() => null),
            fetch(osrmUrl2).then(res => res.json()).catch(() => null)
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

                // Draw actual road route
                if (distanceLine) {
                    map.removeLayer(distanceLine);
                }
                distanceLine = L.geoJSON(selectedRoute.geometry, {
                    style: {
                        color: '#00529c',
                        weight: 4,
                        opacity: 0.8
                    }
                }).addTo(map);
            } else {
                useFallback();
            }
        }).catch(() => {
            useFallback();
        });

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

            // Draw straight dashed line as fallback
            if (distanceLine) {
                map.removeLayer(distanceLine);
            }
            distanceLine = L.polyline([[lat, lon], closestCoords], {
                color: '#00529c',
                weight: 3,
                dashArray: '5, 10'
            }).addTo(map);
        }
    };

    // Click on map to select location
    map.on('click', function(e) {
        window.updateLocation(e.latlng.lat, e.latlng.lng);
    });

    // Geocoding via Nominatim with robust progressive fallback and cleaning
    window.searchAddressOnMap = function() {
        const alpine = window.alpineForm;
        const originalAddress = document.querySelector('textarea[name="address"]').value;

        if (!originalAddress || originalAddress.trim().length < 5) {
            alpine.geocodingError = 'Silakan tulis alamat lengkap terlebih dahulu.';
            return;
        }

        alpine.geocoding = true;
        alpine.geocodingError = '';

        // Clean query of noise details like RT/RW, No., Blok, Gang that are not indexed in OSM
        function cleanQuery(str) {
            let clean = str;
            clean = clean.replace(/rt\s*\.?\s*\d+\s*[\/\-]?\s*rw\s*\.?\s*\d+/gi, '');
            clean = clean.replace(/rt\s*\.?\s*\d+/gi, '');
            clean = clean.replace(/rw\s*\.?\s*\d+/gi, '');
            clean = clean.replace(/(?:no|nomor)\s*\.?\s*\d+[a-z]?/gi, '');
            clean = clean.replace(/blok\s*[a-z0-9\-\/]+/gi, '');
            clean = clean.replace(/(?:gang|gg\.)\s*[a-z0-9]+/gi, '');
            clean = clean.replace(/,\s*,/g, ',');
            clean = clean.replace(/^\s*,|,\s*$/g, '');
            return clean.trim();
        }

        const cleanedAddress = cleanQuery(originalAddress);

        // Split into progressive query levels
        let parts = [];
        const hasCommas = cleanedAddress.includes(',');
        if (hasCommas) {
            parts = cleanedAddress.split(',').map(p => p.trim()).filter(Boolean);
        } else {
            parts = cleanedAddress.split(/\s+/).filter(Boolean);
        }

        // Create target search queries to avoid multiple rapid API calls:
        // Query A: Drop the first specific component if address is long
        // Query B: Drop first 2 specific components
        // Query C: Broad fallback (Lamongan, Jawa Timur)
        let queries = [];
        queries.push(cleanedAddress); // Always try the full cleaned address first!

        if (parts.length > 2) {
            queries.push(parts.slice(1).join(hasCommas ? ', ' : ' '));
            if (parts.length > 3) {
                queries.push(parts.slice(2).join(hasCommas ? ', ' : ' '));
            }
        }
        
        if (queries[queries.length - 1].toLowerCase() !== 'lamongan, jawa timur, indonesia') {
            queries.push('Lamongan, Jawa Timur, Indonesia');
        }

        function tryGeocode(index) {
            if (index >= queries.length) {
                alpine.geocodingError = 'Alamat spesifik tidak ditemukan di peta. Silakan plot lokasi secara manual dengan mengeklik peta.';
                alpine.geocoding = false;
                return;
            }

            let query = queries[index];

            // If it's the broad fallback, just use Alun-Alun Lamongan to avoid jumping 13km away
            if (query.toLowerCase() === 'lamongan, jawa timur, indonesia') {
                window.updateLocation(-7.1126, 112.4150);
                alpine.geocoding = false;
                alpine.geocodingError = '📍 Alamat spesifik tidak ditemukan di database peta. Silakan GESER PIN (ikon biru) di peta ke lokasi rumah Anda yang tepat.';
                return;
            }

            // Ensure search stays within Lamongan
            if (!query.toLowerCase().includes('lamongan')) {
                query += ', Lamongan, Jawa Timur';
            }

            const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=1&email=admin@nersia.com`;

            fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    window.updateLocation(lat, lon);
                    alpine.geocoding = false;
                    
                    if (index > 0) {
                        alpine.geocodingError = '📍 Menampilkan lokasi perkiraan. Silakan GESER PIN (ikon biru) di peta ke lokasi rumah Anda yang tepat.';
                    }
                } else {
                    // Rate limit compliance: wait 600ms before retry
                    setTimeout(() => tryGeocode(index + 1), 600);
                }
            })
            .catch(err => {
                // Rate limit compliance: wait 600ms before retry
                setTimeout(() => tryGeocode(index + 1), 600);
            });
        }
        tryGeocode(0);
    };

    // Geolocation API
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

    // Auto-search address on load if pre-filled address is valid
    const initialAddress = document.querySelector('textarea[name="address"]').value;
    if (initialAddress && initialAddress.trim().length >= 10) {
        setTimeout(function() {
            window.searchAddressOnMap();
        }, 1000);
    }
});
</script>
@endpush

