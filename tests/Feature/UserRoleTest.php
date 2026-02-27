<?php

use App\Models\User;

test('user default role is user', function () {
    $user = User::factory()->create();

    expect($user->fresh()->role_name)->toEqual('user');
    expect($user->fresh()->role)->toEqual(User::USER_ROLE);
});

test('user correctly reports if admin', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => User::ADMIN_ROLE]);

    expect($user->fresh()->isAdmin())->toBeFalse();
    expect($admin->fresh()->isAdmin())->toBeTrue();
});
