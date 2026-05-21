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
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['laki_laki', 'perempuan'])],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'weight_kg' => ['required', 'numeric', 'min:1', 'max:500'],
            'height_cm' => ['required', 'integer', 'min:30', 'max:300'],
            'domicile_address' => ['required', 'string', 'max:1000'],
            'occupation' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'province_kode' => ['required', 'string', 'exists:wilayah,kode'],
            'regency_kode' => ['required', 'string', 'exists:wilayah,kode'],
            'district_kode' => ['required', 'string', 'exists:wilayah,kode'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
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
                $validator->errors()->add('regency_kode', 'Kabupaten tidak valid untuk provinsi yang dipilih.');
            }

            if (! $district?->isKecamatan() || $district->parent_kode !== $regency?->kode) {
                $validator->errors()->add('district_kode', 'Kecamatan tidak valid untuk kabupaten yang dipilih.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.before_or_equal' => 'Tanggal lahir tidak valid.',
            'weight_kg.required' => 'Berat badan wajib diisi.',
            'height_cm.required' => 'Tinggi badan wajib diisi.',
            'domicile_address.required' => 'Alamat domisili wajib diisi.',
            'occupation.required' => 'Pekerjaan wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'province_kode.required' => 'Provinsi wajib dipilih.',
            'province_kode.exists' => 'Provinsi tidak valid.',
            'regency_kode.required' => 'Kabupaten/Kota wajib dipilih.',
            'regency_kode.exists' => 'Kabupaten/Kota tidak valid.',
            'district_kode.required' => 'Kecamatan wajib dipilih.',
            'district_kode.exists' => 'Kecamatan tidak valid.',
        ];
    }
}
