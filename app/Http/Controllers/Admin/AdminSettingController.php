<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function index(): View
    {
        $getMitraPhone = function (string $settingKey, string $configKey, string $providerKey, string $searchName) {
            $value = Setting::getValue($settingKey);
            if (! empty($value)) {
                return $value;
            }

            // Fallback ke akun mitra terdaftar jika ada
            $userPhone = \App\Models\User::where('provider_key', $providerKey)
                ->where(function ($q) use ($searchName) {
                    $q->where('name', 'like', "%{$searchName}%")->orWhere('email', 'like', "%{$searchName}%");
                })
                ->value('phone');

            if (empty($userPhone)) {
                $userPhone = \App\Models\User::where('provider_key', $providerKey)->value('phone');
            }

            return ! empty($userPhone) ? $userPhone : config($configKey, '');
        };

        $settings = [
            'consultation_is_free'             => Setting::getValue('consultation_is_free', '0'),
            'consultation_free_perawat'        => Setting::getValue('consultation_free_perawat', '0'),
            'consultation_free_dokter_umum'    => Setting::getValue('consultation_free_dokter_umum', '0'),
            'consultation_free_penyakit_dalam' => Setting::getValue('consultation_free_penyakit_dalam', '0'),
            'order_admin_phone'                => Setting::getValue('order_admin_phone', config('consultation.notification.admin_phone')),
            'umla_farma1_phone'                => $getMitraPhone('umla_farma1_phone', 'consultation.notification.umla_farma1_phone', 'apotek', '1'),
            'umla_farma2_phone'                => $getMitraPhone('umla_farma2_phone', 'consultation.notification.umla_farma2_phone', 'apotek', '2'),
            'medical_center1_phone'            => $getMitraPhone('medical_center1_phone', 'consultation.notification.medical_center1_phone', 'homecare', '1'),
            'medical_center2_phone'            => $getMitraPhone('medical_center2_phone', 'consultation.notification.medical_center2_phone', 'homecare', '2'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'consultation_free_perawat'        => ['nullable', 'in:0,1'],
            'consultation_free_dokter_umum'    => ['nullable', 'in:0,1'],
            'consultation_free_penyakit_dalam' => ['nullable', 'in:0,1'],
            'order_admin_phone'                => ['required', 'string', 'max:20'],
            'umla_farma1_phone'                => ['required', 'string', 'max:20'],
            'umla_farma2_phone'                => ['required', 'string', 'max:20'],
            'medical_center1_phone'            => ['required', 'string', 'max:20'],
            'medical_center2_phone'            => ['required', 'string', 'max:20'],
        ]);

        foreach (['perawat', 'dokter_umum', 'penyakit_dalam'] as $catKey) {
            Setting::setValue("consultation_free_{$catKey}", $request->input("consultation_free_{$catKey}", '0'));
        }

        foreach (['order_admin_phone', 'umla_farma1_phone', 'umla_farma2_phone', 'medical_center1_phone', 'medical_center2_phone'] as $phoneKey) {
            if (isset($validated[$phoneKey])) {
                Setting::setValue($phoneKey, $validated[$phoneKey]);
            }
        }

        // Sinkronisasi nomor ke akun user mitra terdaftar jika ada
        $syncToUser = function (string $providerKey, string $searchName, string $phone) {
            $user = \App\Models\User::where('provider_key', $providerKey)
                ->where(function ($q) use ($searchName) {
                    $q->where('name', 'like', "%{$searchName}%")->orWhere('email', 'like', "%{$searchName}%");
                })
                ->first();

            if (! $user) {
                $user = \App\Models\User::where('provider_key', $providerKey)->first();
            }

            if ($user) {
                $user->update(['phone' => $phone]);
            }
        };

        if (isset($validated['umla_farma1_phone'])) {
            $syncToUser('apotek', '1', $validated['umla_farma1_phone']);
        }
        if (isset($validated['umla_farma2_phone'])) {
            $syncToUser('apotek', '2', $validated['umla_farma2_phone']);
        }
        if (isset($validated['medical_center1_phone'])) {
            $syncToUser('homecare', '1', $validated['medical_center1_phone']);
        }
        if (isset($validated['medical_center2_phone'])) {
            $syncToUser('homecare', '2', $validated['medical_center2_phone']);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan aplikasi, notifikasi, dan akun mitra berhasil diperbarui & disinkronkan.');
    }
}
