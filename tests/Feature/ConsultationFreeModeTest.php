<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationFreeModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultation_index_reflects_category_free_status(): void
    {
        Setting::setValue('consultation_is_free', '0');
        Setting::setValue('consultation_free_perawat', '1');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('consultation.index'));

        $response->assertOk();
        $response->assertSee('Gratis · Chat aktif');
    }

    public function test_consultation_provider_page_reflects_category_free_status(): void
    {
        Setting::setValue('consultation_is_free', '0');
        Setting::setValue('consultation_free_perawat', '1');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('consultation.category', 'perawat'));

        $response->assertOk();
        $response->assertSee('Layanan konsultasi chat gratis disetujui Admin');
        $response->assertDontSee('💳 Chat Berbayar');
    }

    public function test_consultation_provider_page_reflects_paid_status_when_set_to_0(): void
    {
        Setting::setValue('consultation_is_free', '0');
        Setting::setValue('consultation_free_perawat', '0');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('consultation.category', 'perawat'));

        $response->assertOk();
        $response->assertSee('Pilih tenaga kesehatan');
        $response->assertDontSee('Layanan konsultasi chat gratis disetujui Admin');
    }
}
