<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('unauthenticated users cant post ratings', function () {
    $package = Package::factory()->create();
    $response = $this->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    $this->assertEquals(route('login'), $response->getTargetUrl());
});

test('posting a rating increases the packages overall rating', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    $this->assertEquals(4, $package->average_rating);
});

test('the same user cant add two ratings to a package', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 2,
    ]);

    $this->assertEquals(1, $package->ratings()->count());
});

test('users can modify their ratings', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 2,
    ]);

    $this->assertEquals(2, (int) $package->user_average_rating);
});

test('a user cannot rate a package they authored', function () {
    $user = User::factory()->create();
    $package = Package::factory()->create([
        'author_id' => Collaborator::factory()->create([
            'user_id' => $user->id,
        ]),
    ]);

    $request = $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 5,
    ]);

    $this->assertEquals(0, $package->ratings()->count());
    $request->assertStatus(422);
    $request->assertJson([
        'status' => 'error',
        'message' => 'A package cannot be rated by its author',
    ]);
});

test('a user cannot rate a package they collaborated on', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->make();
    $user->collaborators()->save($collaborator);
    $package = Package::factory()->create();
    $package->contributors()->save($collaborator);

    $request = $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 5,
    ]);

    $this->assertEquals(0, $package->ratings()->count());
    $request->assertStatus(422);
    $request->assertJson([
        'status' => 'error',
        'message' => 'A package cannot be rated by its author',
    ]);
});
