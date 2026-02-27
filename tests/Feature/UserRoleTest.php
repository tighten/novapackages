<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('user default role is user', function () {
    $user = User::factory()->create();

    $this->assertEquals('user', $user->fresh()->role_name);
    $this->assertEquals(User::USER_ROLE, $user->fresh()->role);
});

test('user correctly reports if admin', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => User::ADMIN_ROLE]);

    $this->assertFalse($user->fresh()->isAdmin());
    $this->assertTrue($admin->fresh()->isAdmin());
});
