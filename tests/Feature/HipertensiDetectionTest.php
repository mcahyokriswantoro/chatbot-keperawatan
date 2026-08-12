<?php

namespace Tests\Feature;

use App\Models\ScreeningIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HipertensiDetectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_hipertensi_detection_chat_session(): void
    {
        $user = User::factory()->create();
        $identity = ScreeningIdentity::create([
            'user_id' => $user->id,
            'screening_target' => 'self',
            'disease' => 'hipertensi',
            'name' => $user->name,
            'age' => 30,
            'gender' => 'male',
            'phone' => '08123456789',
            'date_of_birth' => '1995-01-01',
            'weight_kg' => 65,
            'height_cm' => 170,
            'address' => 'Jl. Test No. 123',
            'domicile_address' => 'Jl. Test No. 123',
            'province' => 'Jawa Barat',
            'province_kode' => '32',
            'regency' => 'Kota Bandung',
            'regency_kode' => '3273',
            'district' => 'Coblong',
            'district_kode' => '327301',
            'occupation' => 'Karyawan',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['screening_identity_id' => $identity->id])
            ->get(route('detection.chat.session', 'hipertensi'));

        $response->assertStatus(200);
        $response->assertSee('Hipertensi');
    }
}
