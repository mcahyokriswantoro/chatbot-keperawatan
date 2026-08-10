<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Valid registration roles.
     */
    private const VALID_ROLES = ['pasien', 'perawat', 'dokter', 'apotek', 'homecare'];

    public function create(Request $request): View
    {
        $role = $request->query('role', 'pasien');

        if (! in_array($role, self::VALID_ROLES, true)) {
            $role = 'pasien';
        }

        return view('auth.register', compact('role'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'phone' => PhoneNumber::normalize($request->string('phone')->toString()),
        ]);

        $role = $request->input('role', 'pasien');

        if (! in_array($role, self::VALID_ROLES, true)) {
            $role = 'pasien';
        }

        // Validate based on role
        $validated = match ($role) {
            'pasien' => $this->validatePasien($request),
            'perawat', 'dokter' => $this->validateNakes($request),
            'apotek' => $this->validateApotek($request),
            'homecare' => $this->validateHomecare($request),
        };

        if (! PhoneNumber::isValid($validated['phone'])) {
            throw ValidationException::withMessages([
                'phone' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx.',
            ]);
        }

        // Build user data based on role
        $userData = match ($role) {
            'pasien' => $this->buildPasienData($validated),
            'perawat', 'dokter' => $this->buildNakesData($validated, $role),
            'apotek' => $this->buildApotekData($validated),
            'homecare' => $this->buildHomecareData($validated),
        };

        // Filter user data to only include columns that actually exist in the database table
        $userData = array_filter($userData, static function ($key) {
            return Schema::hasColumn('users', $key);
        }, ARRAY_FILTER_USE_KEY);

        $user = User::create($userData);

        if ($user->provider_key === 'apotek') {
            $key = str_contains(strtolower($user->name), '2') ? 'umla_farma2_phone' : 'umla_farma1_phone';
            \App\Models\Setting::setValue($key, $user->phone);
        } elseif ($user->provider_key === 'homecare') {
            $key = str_contains(strtolower($user->name), '2') ? 'medical_center2_phone' : 'medical_center1_phone';
            \App\Models\Setting::setValue($key, $user->phone);
        }

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::error('Registered event error: '.$e->getMessage());
        }

        // Pendaftaran selain pasien membutuhkan verifikasi admin terlebih dahulu
        if ($role !== 'pasien') {
            return redirect()->route('login')->with(
                'status',
                'Pendaftaran mitra berhasil! Akun Anda sedang dalam proses verifikasi oleh Admin. Silakan tunggu verifikasi admin sebelum masuk.'
            );
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function validatePasien(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'gender' => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'weight' => ['required', 'numeric', 'min:1', 'max:500'],
            'height' => ['required', 'numeric', 'min:30', 'max:300'],
            'address' => ['required', 'string', 'max:1000'],
            'occupation' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'gender.required' => 'Silakan pilih jenis kelamin.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        ]);
    }

    private function validateNakes(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title_front' => ['nullable', 'string', 'max:50'],
            'title_back' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'gender' => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'str_number' => ['required', 'string', 'max:100'],
            'specialty' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'address' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'gender.required' => 'Silakan pilih jenis kelamin.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'str_number.required' => 'Nomor STR wajib diisi.',
            'specialty.required' => 'Spesialisasi wajib diisi.',
            'experience_years.required' => 'Pengalaman kerja wajib diisi.',
        ]);
    }

    private function validateApotek(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'license_number' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'license_number.required' => 'Nomor SIPA/SIA wajib diisi.',
        ]);
    }

    private function validateHomecare(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'license_number' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'license_number.required' => 'Nomor izin usaha wajib diisi.',
        ]);
    }

    private function buildPasienData(array $validated): array
    {
        $dateOfBirth = \Carbon\Carbon::parse($validated['date_of_birth']);

        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'date_of_birth' => $dateOfBirth->toDateString(),
            'age' => (int) $dateOfBirth->age,
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'address' => $validated['address'],
            'occupation' => $validated['occupation'],
            'password' => Hash::make($validated['password']),
            'is_approved' => true,
            'email_verified_at' => now(),
        ];
    }

    private function buildNakesData(array $validated, string $role): array
    {
        $titleFront = trim((string) ($validated['title_front'] ?? ''));
        $titleBack = trim((string) ($validated['title_back'] ?? ''));
        $fullName = trim($validated['name']);

        if ($titleFront !== '') {
            $fullName = $titleFront.' '.$fullName;
        }
        if ($titleBack !== '') {
            $fullName = $fullName.', '.$titleBack;
        }

        return [
            'name' => $fullName,
            'email' => $validated['email'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'occupation' => $validated['specialty'].' — STR: '.$validated['str_number'],
            'password' => Hash::make($validated['password']),
            'provider_key' => $role,
            'is_approved' => false,
            'email_verified_at' => now(),
        ];
    }

    private function buildApotekData(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'occupation' => 'Apotek — SIPA/SIA: '.$validated['license_number'],
            'password' => Hash::make($validated['password']),
            'provider_key' => 'apotek',
            'is_approved' => false,
            'email_verified_at' => now(),
        ];
    }

    private function buildHomecareData(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'occupation' => 'Homecare — Izin: '.$validated['license_number'],
            'password' => Hash::make($validated['password']),
            'provider_key' => 'homecare',
            'is_approved' => false,
            'email_verified_at' => now(),
        ];
    }
}
