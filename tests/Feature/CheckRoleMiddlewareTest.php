<?php

use App\Models\User;

test('users cannot visit admin protected routes', function () {
    $user = User::factory()->create();

    $response = $this->be($user->fresh())->get(route('app.admin.index'));

    $response->assertStatus(302);
});

test('admins can visit admin protected routes', function () {
    $user = User::factory()->admin()->create();

    $response = $this->be($user->fresh())->get(route('app.admin.index'));

    $response->assertStatus(200);
});
