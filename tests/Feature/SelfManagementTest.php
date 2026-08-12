<?php

namespace Tests\Feature;

use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelfManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_self_management_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('self-management'));

        $response->assertStatus(200);
        $response->assertSee('Self Management');
        $response->assertSee('Hipertensi');
    }

    public function test_user_without_screening_can_view_all_diseases_self_management_education(): void
    {
        $user = User::factory()->create();
        $diseases = array_keys(config('self_management_diseases.list', []));

        foreach ($diseases as $disease) {
            $response = $this->actingAs($user)->get(route('self-management.show', $disease));

            $response->assertStatus(200);
            $response->assertSee('Panduan Edukasi Self-Management');
            $response->assertSee('Risiko Rendah');
            $response->assertSee('Risiko Sedang');
            $response->assertSee('Risiko Tinggi');
        }
    }

    public function test_user_with_screening_has_personalized_risk_highlighted(): void
    {
        $user = User::factory()->create();

        ScreeningSession::create([
            'user_id' => $user->id,
            'disease' => 'hipertensi',
            'status' => 'completed',
            'answers' => ['q01' => 'ya', 'q02' => 'ya', 'q03' => 'ya', 'q04' => 'ya', 'q05' => 'ya', 'q06' => 'ya', 'q07' => 'ya', 'q08' => 'ya', 'q09' => 'ya', 'q10' => 'ya', 'q11' => 'ya'],
            'summary' => 'Skrining Hipertensi Risiko Tinggi',
            'risk_level' => 'high',
            'is_emergency' => false,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('self-management.show', 'hipertensi'));

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Personal');
        $response->assertSee('Hasil Skrining Anda');
        $response->assertSee('Minum obat antihipertensi');
    }
}
