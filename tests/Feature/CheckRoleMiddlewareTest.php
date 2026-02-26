<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckRoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function users_cannot_visit_admin_protected_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->be($user->fresh())->get(route('app.admin.index'));

        $response->assertStatus(302);
    }

    #[Test]
    public function admins_can_visit_admin_protected_routes(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->be($user->fresh())->get(route('app.admin.index'));

        $response->assertStatus(200);
    }
}
