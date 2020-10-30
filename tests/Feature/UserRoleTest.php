<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_default_role_is_user()
    {
        $user = User::factory()->create();

        $this->assertEquals('user', $user->fresh()->role_name);
        $this->assertEquals(User::USER_ROLE, $user->fresh()->role);
    }

    /** @test */
    public function user_correctly_reports_if_admin()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => User::ADMIN_ROLE]);

        $this->assertFalse($user->fresh()->isAdmin());
        $this->assertTrue($admin->fresh()->isAdmin());
    }
}
