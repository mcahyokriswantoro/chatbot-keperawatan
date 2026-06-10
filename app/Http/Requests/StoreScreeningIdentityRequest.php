<?php

namespace App\Http\Requests;

use App\Models\Wilayah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreScreeningIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isSelf = $this->input('screening_target') === 'self';

        $rules = [
            'screening_target' => ['required', Rule::in(['self', 'other'])],
            'province_kode' => ['required', 'string', 'exists:wilayah,kode'],
            'regency_kode' => ['required', 'string', 'exists:wilayah,kode'],
            'district_kode' => ['required', 'string', 'exists:wilayah,kode'],
        ];

        if (! $isSelf) {
            $rules += [
                'name' => ['required', 'string', 'max:255'],
                'gender' => ['required', Rule::in(['laki_laki', 'perempuan'])],
                'phone' => ['required', 'string', 'max:20'],
                'date_of_birth' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
                'weight_kg' => ['required', 'numeric', 'min:1', 'max:500'],
                'height_cm' => ['required', 'integer', 'min:30', 'max:300'],
                'domicile_address' => ['required', 'string', 'max:1000'],
                'occupation' => ['required', 'string', 'max:255'],
            ];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('screening_target') === 'self') {
                $user = $this->user();

                if (! $user) {
                    $validator->errors()->add('screening_target', 'Deteksi diri sendiri hanya tersedia untuk pengguna yang sudah masuk.');
                } elseif (! filled($user->name) || ! filled($user->gender) || ! filled($user->phone)
                    || ! filled($user->date_of_birth) || ! filled($user->weight) || ! filled($user->height)
                    || ! filled($user->address) || ! filled($user->occupation)) {
                    $validator->errors()->add('screening_target', 'Profil akun Anda belum lengkap. Lengkapi data registrasi atau pilih deteksi orang lain.');
                }
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $province = Wilayah::find($this->input('province_kode'));
            $regency = Wilayah::find($this->input('regency_kode'));
            $district = Wilayah::find($this->input('district_kode'));

            if (! $province?->isProvinsi()) {
                $validator->errors()->add('province_kode', 'Provinsi tidak valid.');
            }

            if (! $regency?->isKabupaten() || $regency->parent_kode !== $province?->kode) {
                $validator->errors()->add('regency_kode', 'Kabupaten/Kota tidak valid untuk provinsi yang dipilih.');
            }

            if (! $district?->isKecamatan() || $district->parent_kode !== $regency?->kode) {
                $validator->errors()->add('district_kode', 'Kecamatan tidak valid untuk kabupaten/kota yang dipilih.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'screening_target.required' => 'Silakan pilih jenis deteksi.',
            'name.required' => 'Nama wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.before_or_equal' => 'Tanggal lahir tidak valid.',
            'weight_kg.required' => 'Berat badan wajib diisi.',
            'height_cm.required' => 'Tinggi badan wajib diisi.',
            'domicile_address.required' => 'Alamat domisili wajib diisi.',
            'occupation.required' => 'Pekerjaan wajib diisi.',
            'province_kode.required' => 'Provinsi wajib dipilih.',
            'regency_kode.required' => 'Kabupaten/Kota wajib dipilih.',
            'district_kode.required' => 'Kecamatan wajib dipilih.',
        ];
    }
}
