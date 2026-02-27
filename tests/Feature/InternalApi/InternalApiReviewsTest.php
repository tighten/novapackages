<?php

use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('unauthenticated users cant post review', function () {
    $package = Package::factory()->create();
    $review = Review::factory()->make();

    $response = $this->post(route('internalapi.reviews.store'), [
        'package_id' => $package->id,
        'review' => $review->content,
    ]);

    expect($response->getTargetUrl())->toEqual(route('login'));
});

test('authenticated user cannot see link to post review before reviewing package', function () {
    Http::fake([
        'https://packagist.org/packages/*.json' => Http::response(),
    ]);

    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->be($user)
        ->get('/packages/' . $package->composer_name)
        ->assertDontSee('Write Your Review Here');
});

test('the same user cant add two reviews to a package', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();
    $review = Review::factory()->make();

    $user->ratePackage($package->id, 3);

    $this->be($user)->post(route('internalapi.reviews.store'), [
        'package_id' => $package->id,
        'review' => $review->content,
    ]);

    $this->be($user)->post(route('internalapi.reviews.store'), [
        'package_id' => $package->id,
        'review' => $review->content,
    ]);

    expect($package->reviews()->count())->toEqual(1);
});

test('users can modify their reviews', function () {
    $package = Package::factory()->create();
    $user = User::factory()->create();

    $user->ratePackage($package->id, 3);

    $this->be($user)->post(route('internalapi.reviews.store'), [
        'package_id' => $package->id,
        'review' => 'Old Review Content',
    ]);

    $response = $this->be($user)->post(route('internalapi.reviews.store'), [
        'package_id' => $package->id,
        'review' => 'New Review Content',
    ]);

    expect($package->reviews->first()->content)->toEqual('New Review Content');
});

test('a user can delete their review', function () {
    $review = Review::factory()->create();

    $this->be($review->user)
        ->delete(route('internalapi.reviews.delete', [$review->id]))
        ->assertSuccessful();

    $this->assertDatabaseMissing('reviews', [
        'id' => $review->id,
    ]);
});

test('a user cannot delete a review belonging to another user', function () {
    $review = Review::factory()->create();
    $otherUser = User::factory()->create();

    $this->be($otherUser)
        ->delete(route('internalapi.reviews.delete', [$review->id]))
        ->assertForbidden();

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
    ]);
});

test('an admin can delete another users review', function () {
    $review = Review::factory()->create();
    $adminUser = User::factory()->admin()->create();

    $this->be($adminUser)
        ->delete(route('internalapi.reviews.delete', [$review->id]))
        ->assertSuccessful();

    $this->assertDatabaseMissing('reviews', [
        'id' => $review->id,
    ]);
});
