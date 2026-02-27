<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;

test('unauthenticated users cant post ratings', function () {
    $package = Package::factory()->create();
    $response = $this->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    expect($response->getTargetUrl())->toEqual(route('login'));
});

test('posting a rating increases the packages overall rating', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->be($user)->post(route('internalapi.ratings.store'), [
        'package_id' => $package->id,
        'rating' => 4,
    ]);

    expect($package->average_rating)->toEqual(4);
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

    expect($package->ratings()->count())->toEqual(1);
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

    expect((int) $package->user_average_rating)->toEqual(2);
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

    expect($package->ratings()->count())->toEqual(0);
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

    expect($package->ratings()->count())->toEqual(0);
    $request->assertStatus(422);
    $request->assertJson([
        'status' => 'error',
        'message' => 'A package cannot be rated by its author',
    ]);
});
