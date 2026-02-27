<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use willvincent\Rateable\Rating;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('deleting self authored package ratings', function () {
    $packageAuthor = User::factory()->create();
    // We are creating two collaborators here and setting the
    // second one on the author to ensure the user_id on the rating
    // is different from the author_id on the package.
    $collaborators = Collaborator::factory(2)->create();
    $packageAuthor->collaborators()->save($collaborators->last());
    $otherUser = User::factory()->create();
    $package = Package::factory()->create([
        'author_id' => $collaborators->last()->id,
    ]);

    $invalidRating = new Rating(['rating' => 5]);
    $invalidRating->user_id = $packageAuthor->id;

    $validRating = new Rating(['rating' => 5]);
    $validRating->user_id = $otherUser->id;

    $package->ratings()->save($invalidRating);
    $package->ratings()->save($validRating);
    expect($package->fresh()->ratings()->count())->toEqual(2);

    $this->artisan('purge:self-authored-package-ratings');

    tap($package->fresh(), function ($package) use ($otherUser) {
        expect($package->ratings()->count())->toEqual(1);
        expect($package->ratings->first()->user_id)->toEqual($otherUser->id);
    });
});

test('deleting self contributed package ratings', function () {
    $packageAuthor = User::factory()->create();
    $packageContributor = User::factory()->create();
    $collaborator = Collaborator::factory()->create([
        'user_id' => $packageContributor->id,
    ]);
    $otherUser = User::factory()->create();
    $package = Package::factory()->create([
        'author_id' => $packageAuthor->id,
    ]);
    $package->contributors()->sync([$collaborator->id]);

    $invalidRating = new Rating(['rating' => 5]);
    $invalidRating->user_id = $packageContributor->id;

    $validRating = new Rating(['rating' => 5]);
    $validRating->user_id = $otherUser->id;

    $package->ratings()->save($invalidRating);
    $package->ratings()->save($validRating);
    expect($package->fresh()->ratings()->count())->toEqual(2);

    $this->artisan('purge:self-authored-package-ratings');

    tap($package->fresh(), function ($package) use ($otherUser) {
        expect($package->ratings()->count())->toEqual(1);
        expect($package->ratings->first()->user_id)->toEqual($otherUser->id);
    });
});
