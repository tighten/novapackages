<?php

use App\Models\Package;
use App\Models\User;

test('packages show on main page', function () {
    $user = User::factory()->admin()->create();
    $package = Package::factory()->create();

    $response = $this->be($user)->get(route('home'));
    $response->assertSee($package->name);
});

test('disabled packages dont show on main page', function () {
    $user = User::factory()->admin()->create();
    $package = Package::factory()->disabled()->create();

    $response = $this->be($user)->get(route('home'));
    $response->assertDontSee($package->name);
});

test('disabled packages dont return from api', function () {
    $this->markTestIncomplete();
});
