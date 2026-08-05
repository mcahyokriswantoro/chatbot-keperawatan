<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => PhoneNumber::normalize($this->string('phone')->toString()),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isPasien = ! $user->isAdmin() && ! $user->provider_key;
        $isApotekOrHomecare = in_array($user->provider_key, ['apotek', 'homecare'], true);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'gender' => [
                $isApotekOrHomecare ? 'nullable' : 'required',
                Rule::in(['laki-laki', 'perempuan']),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class, 'phone')->ignore($user->id),
            ],
            'date_of_birth' => $isPasien ? ['required', 'date', 'before:today'] : ['nullable', 'date', 'before:today'],
            'weight' => $isPasien ? ['required', 'numeric', 'min:1', 'max:500'] : ['nullable', 'numeric', 'min:1', 'max:500'],
            'height' => $isPasien ? ['required', 'numeric', 'min:30', 'max:300'] : ['nullable', 'numeric', 'min:30', 'max:300'],
            'address' => ['required', 'string', 'max:1000'],
            'occupation' => $isPasien ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remove_profile_photo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gender.required' => 'Silakan pilih jenis kelamin.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah digunakan akun lain.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'profile_photo.image' => 'Foto profil harus berupa gambar (JPEG, PNG, WEBP).',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! PhoneNumber::isValid($this->input('phone', ''))) {
                $validator->errors()->add(
                    'phone',
                    'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx.'
                );
            }
        });
    }
}
