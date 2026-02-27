<?php

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


it('counts average rating', function () {
    $package = Package::factory()->create();
    $users = User::factory(4)->create();

    $i = 1;
    foreach ($users as $user) {
        $user->ratePackage($package, $i++);
    }

    expect($package->fresh()->average_rating)->toEqual((1 + 2 + 3 + 4) / 4);
});

it('counts each rating correctly', function () {
    $package = Package::factory()->create();
    $users = User::factory(15)->create();

    // Generate 1 5-star ratings, 2 4-star, etc.
    foreach (range(1, 5) as $i) {
        $users->shift()->ratePackage($package, 1);
    }

    foreach (range(1, 4) as $i) {
        $users->shift()->ratePackage($package, 2);
    }

    foreach (range(1, 3) as $i) {
        $users->shift()->ratePackage($package, 3);
    }

    foreach (range(1, 2) as $i) {
        $users->shift()->ratePackage($package, 4);
    }

    $users->shift()->ratePackage($package, 5);

    expect($package->countFiveStarRatings())->toEqual(1);
    expect($package->countFourStarRatings())->toEqual(2);
    expect($package->countThreeStarRatings())->toEqual(3);
    expect($package->countTwoStarRatings())->toEqual(4);
    expect($package->countOneStarRatings())->toEqual(5);
});

it('counts each rating correctly when eager loaded', function () {
    $package = Package::factory()->create();
    $users = User::factory(15)->create();

    // Generate 1 5-star ratings, 2 4-star, etc.
    foreach (range(1, 5) as $i) {
        $users->shift()->ratePackage($package, 1);
    }

    foreach (range(1, 4) as $i) {
        $users->shift()->ratePackage($package, 2);
    }

    foreach (range(1, 3) as $i) {
        $users->shift()->ratePackage($package, 3);
    }

    foreach (range(1, 2) as $i) {
        $users->shift()->ratePackage($package, 4);
    }

    $users->shift()->ratePackage($package, 5);

    $package = Package::with('ratings')->find($package->id);

    expect($package->countFiveStarRatings())->toEqual(1);
    expect($package->countFourStarRatings())->toEqual(2);
    expect($package->countThreeStarRatings())->toEqual(3);
    expect($package->countTwoStarRatings())->toEqual(4);
    expect($package->countOneStarRatings())->toEqual(5);
});

test('average rating rounded to 1 decimal', function () {
    $package = Package::factory()->create();
    $users = User::factory(3)->create();
    $ratings = [3, 5, 5];

    $i = 0;
    foreach ($users as $user) {
        $user->ratePackage($package, $ratings[$i]);
        $i++;
    }

    expect($package->fresh()->average_rating)->toEqual(4.3);
});
