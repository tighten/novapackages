<?php

use App\Models\Favorite;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('a guest user can not favorite a package', function () {
    $package = Package::factory()->create();

    $response = $this->json('POST', route('internalapi.package.favorites.store', $package->id));

    $response->assertStatus(401);
    $this->assertCount(0, Favorite::where('package_id', $package->id)->get());
});

test('an authenticated user can add a package to their favorites', function () {
    $user = User::factory()->create();
    $package = Package::factory()->create();

    $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

    $this->assertCount(1, $user->favorites);
    $this->assertTrue($user->favorites()->first()->package->is($package));
});

test('a user can not favorite the same package twice', function () {
    $user = User::factory()->create();
    $package = Package::factory()->create();
    $user->favoritePackage($package->id);

    $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

    $this->assertCount(1, $user->favorites);
    $this->assertTrue($user->favorites()->first()->package->is($package));
});

test('a user can remove a favorite', function () {
    $user = User::factory()->create();
    $packageA = Package::factory()->create();
    $packageB = Package::factory()->create();
    $user->favoritePackage($packageA->id);
    $user->favoritePackage($packageB->id);

    $response = $this->actingAs($user)->json('DELETE', route('internalapi.package.favorites.destroy', $packageB));

    $this->assertCount(1, $user->favorites);
    $this->assertTrue($user->favorites()->first()->package->is($packageA));
});
