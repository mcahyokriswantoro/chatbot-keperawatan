<?php

namespace Tests\Feature\Admin;

use App\Models\ScreeningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_lists(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin');

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.screenings.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.monitoring.index'))->assertOk();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@example.com',
        ]);

        $this->post(route('login'), [
            'login_method' => 'email',
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_view_screening_detail(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $session = ScreeningSession::query()->create([
            'user_id' => User::factory()->create()->id,
            'disease' => 'hipertensi',
            'answers' => [],
            'summary' => 'Test summary',
            'risk_level' => 'medium',
            'is_emergency' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.screenings.show', $session))
            ->assertOk()
            ->assertSee('Test summary');
    }
}
