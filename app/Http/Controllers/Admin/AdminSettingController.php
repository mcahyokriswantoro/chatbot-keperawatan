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
        // Pengaturan nomor WA default fallback ambil dari config/env,
        // namun prioritas utama diambil dari tabel settings.
        
        $settings = [
            'order_admin_phone'       => Setting::getValue('order_admin_phone', config('consultation.notification.admin_phone')),
            'umla_farma1_phone'       => Setting::getValue('umla_farma1_phone', config('consultation.notification.umla_farma1_phone')),
            'umla_farma2_phone'       => Setting::getValue('umla_farma2_phone', config('consultation.notification.umla_farma2_phone')),
            'medical_center1_phone'   => Setting::getValue('medical_center1_phone', config('consultation.notification.medical_center1_phone')),
            'medical_center2_phone'   => Setting::getValue('medical_center2_phone', config('consultation.notification.medical_center2_phone')),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'order_admin_phone'     => ['required', 'string', 'max:20'],
            'umla_farma1_phone'     => ['required', 'string', 'max:20'],
            'umla_farma2_phone'     => ['required', 'string', 'max:20'],
            'medical_center1_phone' => ['required', 'string', 'max:20'],
            'medical_center2_phone' => ['required', 'string', 'max:20'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan notifikasi WhatsApp berhasil diperbarui.');
    }
}
