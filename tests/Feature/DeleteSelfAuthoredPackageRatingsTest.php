<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use willvincent\Rateable\Rating;

class DeleteSelfAuthoredPackageRatingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function deleting_self_authored_package_ratings()
    {
        $packageAuthor = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $package = factory(Package::class)->create([
            'author_id' => $packageAuthor->id,
        ]);

        $invalidRating = new Rating(['rating' => 5]);
        $invalidRating->user_id = $packageAuthor->id;

        $validRating = new Rating(['rating' => 5]);
        $validRating->user_id = $otherUser->id;

        $package->ratings()->save($invalidRating);
        $package->ratings()->save($validRating);
        $this->assertEquals(2, $package->fresh()->ratings()->count());

        $this->artisan('purge:self-authored-package-ratings');

        tap($package->fresh(), function ($package) use ($otherUser) {
            $this->assertEquals(1, $package->ratings()->count());
            $this->assertEquals($otherUser->id, $package->ratings->first()->user_id);
        });
    }

    /** @test */
    function deleting_self_contributed_package_ratings()
    {
        $packageAuthor = factory(User::class)->create();
        $packageContributer = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->create([
            'user_id' => $packageContributer->id,
        ]);
        $otherUser = factory(User::class)->create();
        $package = factory(Package::class)->create([
            'author_id' => $packageAuthor->id,
        ]);
        $package->contributors()->sync([$collaborator->id]);

        $invalidRating = new Rating(['rating' => 5]);
        $invalidRating->user_id = $packageContributer->id;

        $validRating = new Rating(['rating' => 5]);
        $validRating->user_id = $otherUser->id;

        $package->ratings()->save($invalidRating);
        $package->ratings()->save($validRating);
        $this->assertEquals(2, $package->fresh()->ratings()->count());

        $this->artisan('purge:self-authored-package-ratings');

        tap($package->fresh(), function ($package) use ($otherUser) {
            $this->assertEquals(1, $package->ratings()->count());
            $this->assertEquals($otherUser->id, $package->ratings->first()->user_id);
        });
    }
}
