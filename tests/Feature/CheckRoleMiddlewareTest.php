<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_cannot_visit_admin_protected_routes()
    {
        $user = User::factory()->create();

        $response = $this->be($user->fresh())->get(route('app.admin.index'));

        $response->assertStatus(302);
    }

    /** @test */
    public function admins_can_visit_admin_protected_routes()
    {
        $user = User::factory()->admin()->create();

        $response = $this->be($user->fresh())->get(route('app.admin.index'));

        $response->assertStatus(200);
    }
}
